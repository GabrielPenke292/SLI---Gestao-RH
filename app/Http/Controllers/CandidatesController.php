<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;

class CandidatesController extends Controller
{
    /**
     * Exibir página principal de candidatos
     */
    public function index()
    {
        return view('candidates.index');
    }

    /**
     * Exibir formulário de criação de candidato
     */
    public function create()
    {
        return view('candidates.create');
    }

    /**
     * Salvar novo candidato
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_name' => 'required|string|max:100',
            'candidate_email' => 'nullable|email|max:100',
            'candidate_phone' => 'nullable|string|max:20',
            'candidate_document' => 'nullable|string|max:14',
            'candidate_rg' => 'nullable|string|max:20',
            'candidate_birth_date' => 'nullable|date',
            'candidate_address' => 'nullable|string|max:255',
            'candidate_city' => 'nullable|string|max:100',
            'candidate_state' => 'nullable|string|max:2',
            'candidate_zipcode' => 'nullable|string|max:10',
            'candidate_experience' => 'nullable|string',
            'candidate_education' => 'nullable|string',
            'candidate_skills' => 'nullable|string',
            'candidate_resume_text' => 'nullable|string',
            'candidate_resume_pdf' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'candidate_notes' => 'nullable|string',
        ], [
            'candidate_name.required' => 'O nome do candidato é obrigatório.',
            'candidate_email.email' => 'O e-mail deve ser um endereço válido.',
            'candidate_resume_pdf.mimes' => 'O arquivo deve ser um PDF.',
            'candidate_resume_pdf.max' => 'O arquivo PDF não pode ser maior que 10MB.',
        ]);

        try {
            DB::beginTransaction();

            // Upload do PDF se fornecido
            if ($request->hasFile('candidate_resume_pdf')) {
                $file = $request->file('candidate_resume_pdf');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'resumes/' . $fileName;
                
                // Garantir que a pasta existe e salvar o arquivo
                Storage::disk('public')->makeDirectory('resumes');
                Storage::disk('public')->putFileAs('resumes', $file, $fileName);
                
                $validated['candidate_resume_pdf'] = $filePath;
            }

            $validated['created_by'] = Auth::user()->user_name ?? 'system';
            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            Candidate::create($validated);

            DB::commit();

            return redirect()->route('candidates.index')
                ->with('success', 'Candidato cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar candidato: ' . $e->getMessage());
        }
    }

    /**
     * Exibir perfil do candidato
     */
    public function show($id)
    {
        $candidate = Candidate::with('selectionProcesses.vacancy', 'selectionProcesses.approver')
            ->whereNull('deleted_at')
            ->findOrFail($id);
        return view('candidates.show', compact('candidate'));
    }

    /**
     * Retornar timeline de um processo seletivo específico para o candidato
     */
    public function getProcessTimeline(Request $request, $candidateId): JsonResponse
    {
        $candidate = Candidate::whereNull('deleted_at')->findOrFail($candidateId);
        $processId = $request->input('process_id');
        
        if (!$processId) {
            return response()->json([
                'success' => false,
                'message' => 'Processo seletivo não especificado.'
            ], 400);
        }
        
        // Verificar se o candidato está vinculado a este processo
        $process = $candidate->selectionProcesses()
            ->where('selection_processes.selection_process_id', $processId)
            ->whereNull('selection_processes.deleted_at')
            ->with(['vacancy', 'approver'])
            ->first();
        
        if (!$process) {
            return response()->json([
                'success' => false,
                'message' => 'Processo seletivo não encontrado ou candidato não está vinculado a este processo.'
            ], 404);
        }
        
        // Construir timeline com eventos do processo
        $timeline = [];
        
        // Evento: Criação do processo
        if ($process->created_at) {
            $timeline[] = [
                'date' => $process->created_at->format('d/m/Y H:i'),
                'title' => 'Processo Criado',
                'description' => 'Processo seletivo foi criado',
                'icon' => 'fa-plus-circle',
                'color' => 'primary',
                'status' => 'completed'
            ];
        }
        
        // Evento: Vinculação do candidato
        if ($process->pivot->created_at) {
            $timeline[] = [
                'date' => $process->pivot->created_at->format('d/m/Y H:i'),
                'title' => 'Candidato Vinculado',
                'description' => 'Você foi vinculado a este processo seletivo',
                'icon' => 'fa-user-plus',
                'color' => 'info',
                'status' => 'completed'
            ];
        }
        
        // Evento: Aguardando aprovação
        if ($process->status === 'aguardando_aprovacao') {
            $timeline[] = [
                'date' => $process->created_at?->format('d/m/Y H:i') ?? '-',
                'title' => 'Aguardando Aprovação',
                'description' => 'Processo aguardando aprovação do diretor',
                'icon' => 'fa-clock',
                'color' => 'warning',
                'status' => 'current'
            ];
        }
        
        // Evento: Aprovação
        if ($process->approval_date) {
            $timeline[] = [
                'date' => $process->approval_date->format('d/m/Y'),
                'title' => 'Processo Aprovado',
                'description' => 'Processo aprovado por ' . ($process->approver->user_name ?? 'N/A'),
                'icon' => 'fa-check-circle',
                'color' => 'success',
                'status' => 'completed'
            ];
        }
        
        // Evento: Reprovação
        if ($process->status === 'reprovado') {
            $timeline[] = [
                'date' => $process->approval_date?->format('d/m/Y') ?? $process->updated_at?->format('d/m/Y H:i') ?? '-',
                'title' => 'Processo Reprovado',
                'description' => $process->approval_notes ?? 'Processo foi reprovado',
                'icon' => 'fa-times-circle',
                'color' => 'danger',
                'status' => 'completed'
            ];
        }
        
        // Evento: Início do processo
        if ($process->start_date && $process->status === 'em_andamento') {
            $timeline[] = [
                'date' => $process->start_date->format('d/m/Y'),
                'title' => 'Processo Iniciado',
                'description' => 'Processo seletivo em andamento',
                'icon' => 'fa-play-circle',
                'color' => 'success',
                'status' => $process->status === 'em_andamento' ? 'current' : 'completed'
            ];
        }
        
        // Evento: Status do candidato no processo
        $candidateStatus = $process->pivot->status ?? 'pendente';
        $statusLabels = [
            'pendente' => ['Pendente', 'Aguardando avaliação'],
            'aprovado' => ['Aprovado', 'Você foi aprovado neste processo'],
            'reprovado' => ['Reprovado', 'Você foi reprovado neste processo'],
            'contratado' => ['Contratado', 'Você foi contratado!']
        ];
        
        if (isset($statusLabels[$candidateStatus])) {
            $timeline[] = [
                'date' => $process->pivot->updated_at?->format('d/m/Y H:i') ?? $process->pivot->created_at?->format('d/m/Y H:i') ?? '-',
                'title' => 'Status: ' . $statusLabels[$candidateStatus][0],
                'description' => $statusLabels[$candidateStatus][1],
                'icon' => $candidateStatus === 'aprovado' || $candidateStatus === 'contratado' ? 'fa-check' : ($candidateStatus === 'reprovado' ? 'fa-times' : 'fa-hourglass-half'),
                'color' => $candidateStatus === 'aprovado' || $candidateStatus === 'contratado' ? 'success' : ($candidateStatus === 'reprovado' ? 'danger' : 'warning'),
                'status' => 'current'
            ];
        }
        
        // Evento: Encerramento
        if ($process->end_date || in_array($process->status, ['encerrado', 'congelado'])) {
            $timeline[] = [
                'date' => $process->end_date?->format('d/m/Y') ?? $process->updated_at?->format('d/m/Y H:i') ?? '-',
                'title' => 'Processo Encerrado',
                'description' => $process->status === 'congelado' ? 'Processo foi congelado' : 'Processo seletivo encerrado',
                'icon' => 'fa-stop-circle',
                'color' => 'secondary',
                'status' => 'completed'
            ];
        }
        
        // Ordenar timeline por data
        usort($timeline, function($a, $b) {
            return strtotime(str_replace('/', '-', $a['date'])) <=> strtotime(str_replace('/', '-', $b['date']));
        });
        
        return response()->json([
            'success' => true,
            'process' => [
                'id' => $process->selection_process_id,
                'number' => $process->process_number,
                'vacancy' => $process->vacancy->vacancy_title ?? 'N/A',
                'status' => $process->status,
            ],
            'timeline' => $timeline
        ]);
    }

    /**
     * Exibir formulário de edição de candidato
     */
    public function edit($id)
    {
        $candidate = Candidate::whereNull('deleted_at')->findOrFail($id);
        return view('candidates.edit', compact('candidate'));
    }

    /**
     * Atualizar candidato
     */
    public function update(Request $request, $id)
    {
        $candidate = Candidate::whereNull('deleted_at')->findOrFail($id);

        $validated = $request->validate([
            'candidate_name' => 'required|string|max:100',
            'candidate_email' => 'nullable|email|max:100',
            'candidate_phone' => 'nullable|string|max:20',
            'candidate_document' => 'nullable|string|max:14',
            'candidate_rg' => 'nullable|string|max:20',
            'candidate_birth_date' => 'nullable|date',
            'candidate_address' => 'nullable|string|max:255',
            'candidate_city' => 'nullable|string|max:100',
            'candidate_state' => 'nullable|string|max:2',
            'candidate_zipcode' => 'nullable|string|max:10',
            'candidate_experience' => 'nullable|string',
            'candidate_education' => 'nullable|string',
            'candidate_skills' => 'nullable|string',
            'candidate_resume_text' => 'nullable|string',
            'candidate_resume_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'candidate_notes' => 'nullable|string',
        ], [
            'candidate_name.required' => 'O nome do candidato é obrigatório.',
            'candidate_email.email' => 'O e-mail deve ser um endereço válido.',
            'candidate_resume_pdf.mimes' => 'O arquivo deve ser um PDF.',
            'candidate_resume_pdf.max' => 'O arquivo PDF não pode ser maior que 10MB.',
        ]);

        try {
            DB::beginTransaction();

            // Upload do novo PDF se fornecido
            if ($request->hasFile('candidate_resume_pdf')) {
                // Deletar PDF antigo se existir
                if ($candidate->candidate_resume_pdf && Storage::disk('public')->exists($candidate->candidate_resume_pdf)) {
                    Storage::disk('public')->delete($candidate->candidate_resume_pdf);
                }

                $file = $request->file('candidate_resume_pdf');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'resumes/' . $fileName;
                
                // Garantir que a pasta existe e salvar o arquivo
                Storage::disk('public')->makeDirectory('resumes');
                Storage::disk('public')->putFileAs('resumes', $file, $fileName);
                
                $validated['candidate_resume_pdf'] = $filePath;
            }

            $validated['updated_by'] = Auth::user()->user_name ?? 'system';
            $validated['updated_at'] = now();

            $candidate->update($validated);

            DB::commit();

            return redirect()->route('candidates.show', $id)
                ->with('success', 'Candidato atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar candidato: ' . $e->getMessage());
        }
    }

    /**
     * Excluir candidato (soft delete)
     */
    public function destroy($id)
    {
        try {
            $candidate = Candidate::whereNull('deleted_at')->findOrFail($id);
            
            $candidate->deleted_by = Auth::user()->user_name ?? 'system';
            $candidate->save();
            
            $candidate->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Candidato excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir candidato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna os dados dos candidatos para o DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = [
            'candidate_id',
            'candidate_name',
            'candidate_email',
            'candidate_phone',
            'created_at',
        ];
        
        $query = Candidate::whereNull('deleted_at');
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('candidate_name', 'like', "%{$search}%")
                  ->orWhere('candidate_email', 'like', "%{$search}%")
                  ->orWhere('candidate_phone', 'like', "%{$search}%")
                  ->orWhere('candidate_document', 'like', "%{$search}%")
                  ->orWhere('candidate_resume_text', 'like', "%{$search}%");
            });
        }
        
        $totalRecords = Candidate::whereNull('deleted_at')->count();
        
        $countQuery = clone $query;
        $filteredCount = $countQuery->count();
        
        $orderByColumn = $columns[$orderColumn] ?? 'candidate_id';
        $query->orderBy($orderByColumn, $orderDir);
        
        $candidates = $query->skip($start)->take($length)->get();
        
        $data = $candidates->map(function($candidate) {
            return [
                'id' => $candidate->candidate_id,
                'name' => $candidate->candidate_name,
                'email' => $candidate->candidate_email ?? '-',
                'phone' => $candidate->candidate_phone ?? '-',
                'created_at' => $candidate->created_at?->format('d/m/Y H:i') ?? '-',
                'has_pdf' => $candidate->has_resume_pdf,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredCount,
            'data' => $data
        ]);
    }
}
