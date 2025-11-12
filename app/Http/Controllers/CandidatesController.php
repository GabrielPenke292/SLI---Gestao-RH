<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;
use App\Models\StepInteraction;

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
        
        // Construir atividades (logs do sistema)
        $activities = [];
        
        // Evento: Vinculação do candidato (buscar do log de atividades)
        $linkLog = \App\Models\ActivityLog::where('log_type', 'candidate_linked')
            ->where('entity_type', 'Candidate')
            ->where('entity_id', $candidateId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->first(function($log) use ($processId) {
                $metadata = $log->metadata ?? [];
                return isset($metadata['process_id']) && $metadata['process_id'] == $processId;
            });
        
        if ($linkLog && $linkLog->created_at) {
            $activities[] = [
                'date' => $linkLog->created_at->format('d/m/Y H:i'),
                'title' => 'Candidato Vinculado',
                'description' => $linkLog->description ?? 'Você foi vinculado a este processo seletivo',
                'icon' => 'fa-user-plus',
                'color' => 'success',
                'status' => 'completed'
            ];
        } elseif ($process->pivot->created_at) {
            // Fallback para usar a data do pivot se não houver log
            $activities[] = [
                'date' => $process->pivot->created_at->format('d/m/Y H:i'),
                'title' => 'Candidato Vinculado',
                'description' => 'Você foi vinculado a este processo seletivo',
                'icon' => 'fa-user-plus',
                'color' => 'success',
                'status' => 'completed'
            ];
        }
        
        // Eventos: Observações adicionadas (buscar do log de atividades)
        $noteLogs = \App\Models\ActivityLog::where('log_type', 'candidate_note_added')
            ->where('entity_type', 'Candidate')
            ->where('entity_id', $candidateId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->filter(function($log) use ($processId) {
                // Filtrar por process_id no metadata
                $metadata = $log->metadata ?? [];
                return isset($metadata['process_id']) && $metadata['process_id'] == $processId;
            });
        
        foreach ($noteLogs as $noteLog) {
            if (!$noteLog->created_at) {
                continue; // Pular logs sem data
            }
            
            $metadata = $noteLog->metadata ?? [];
            $note = $metadata['note'] ?? '';
            if (!empty($note)) {
                $activities[] = [
                    'date' => $noteLog->created_at->format('d/m/Y H:i'),
                    'title' => 'Observação Adicionada',
                    'description' => $note,
                    'icon' => 'fa-sticky-note',
                    'color' => 'info',
                    'status' => 'completed'
                ];
            }
        }
        
        // Eventos: Movimentação entre etapas (buscar do log de atividades)
        $stepMoveLogs = \App\Models\ActivityLog::where('log_type', 'candidate_step_moved')
            ->where('entity_type', 'Candidate')
            ->where('entity_id', $candidateId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->filter(function($log) use ($processId) {
                // Filtrar por process_id no metadata
                $metadata = $log->metadata ?? [];
                return isset($metadata['process_id']) && $metadata['process_id'] == $processId;
            });
        
        foreach ($stepMoveLogs as $moveLog) {
            if (!$moveLog->created_at) {
                continue; // Pular logs sem data
            }
            
            $metadata = $moveLog->metadata ?? [];
            $fromStep = $metadata['from_step'] ?? 'Etapa anterior';
            $toStep = $metadata['to_step'] ?? 'Etapa seguinte';
            
            // Determinar se foi avanço ou retorno baseado na ordem das etapas
            $processSteps = $process->steps ?? [];
            $fromIndex = array_search($fromStep, $processSteps);
            $toIndex = array_search($toStep, $processSteps);
            
            $isAdvance = $fromIndex !== false && $toIndex !== false && $toIndex > $fromIndex;
            $direction = $isAdvance ? 'avançou' : 'retornou';
            $icon = $isAdvance ? 'fa-arrow-right' : 'fa-arrow-left';
            $color = $isAdvance ? 'success' : 'warning';
            
            $activities[] = [
                'date' => $moveLog->created_at->format('d/m/Y H:i'),
                'title' => 'Movimentação entre Etapas',
                'description' => sprintf('Você %s da etapa "%s" para "%s"', $direction, $fromStep, $toStep),
                'icon' => $icon,
                'color' => $color,
                'status' => 'completed'
            ];
        }
        
        // Eventos: Aprovação e Reprovação (buscar do log de atividades)
        $approvalLogs = \App\Models\ActivityLog::where('log_type', 'candidate_approved')
            ->where('entity_type', 'Candidate')
            ->where('entity_id', $candidateId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->filter(function($log) use ($processId) {
                $metadata = $log->metadata ?? [];
                return isset($metadata['process_id']) && $metadata['process_id'] == $processId;
            });
        
        foreach ($approvalLogs as $approvalLog) {
            if (!$approvalLog->created_at) {
                continue;
            }
            
            $metadata = $approvalLog->metadata ?? [];
            $step = $metadata['step'] ?? 'Etapa desconhecida';
            $observation = $metadata['observation'] ?? null;
            
            $description = "<strong>Etapa:</strong> {$step}";
            if ($observation) {
                $description .= "<br><strong>Observação:</strong> \"{$observation}\"";
            }
            
            $activities[] = [
                'date' => $approvalLog->created_at->format('d/m/Y H:i'),
                'title' => '✅ Candidato Aprovado',
                'description' => $description,
                'icon' => 'fa-check-circle',
                'color' => 'success',
                'status' => 'completed',
                'highlight' => true // Destacar na timeline
            ];
        }
        
        $rejectionLogs = \App\Models\ActivityLog::where('log_type', 'candidate_rejected')
            ->where('entity_type', 'Candidate')
            ->where('entity_id', $candidateId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->filter(function($log) use ($processId) {
                $metadata = $log->metadata ?? [];
                return isset($metadata['process_id']) && $metadata['process_id'] == $processId;
            });
        
        foreach ($rejectionLogs as $rejectionLog) {
            if (!$rejectionLog->created_at) {
                continue;
            }
            
            $metadata = $rejectionLog->metadata ?? [];
            $step = $metadata['step'] ?? 'Etapa desconhecida';
            $observation = $metadata['observation'] ?? null;
            
            $description = "<strong>Etapa:</strong> {$step}";
            if ($observation) {
                $description .= "<br><strong>Observação:</strong> \"{$observation}\"";
            }
            
            $activities[] = [
                'date' => $rejectionLog->created_at->format('d/m/Y H:i'),
                'title' => '❌ Candidato Reprovado',
                'description' => $description,
                'icon' => 'fa-times-circle',
                'color' => 'danger',
                'status' => 'completed',
                'highlight' => true // Destacar na timeline
            ];
        }
        
        // Ordenar atividades por data
        usort($activities, function($a, $b) {
            return strtotime(str_replace('/', '-', $a['date'])) <=> strtotime(str_replace('/', '-', $b['date']));
        });
        
        // Construir interações (perguntas e observações das etapas)
        $interactions = [];
        $stepInteractions = StepInteraction::where('selection_process_id', $processId)
            ->where('candidate_id', $candidateId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        foreach ($stepInteractions as $interaction) {
            // Verificar se created_at existe antes de formatar
            // Como o modelo tem $timestamps = false, precisamos verificar se o campo existe e não é null
            $createdAt = $interaction->created_at;
            
            // Se created_at for null, tentar usar updated_at como fallback
            if (!$createdAt && $interaction->updated_at) {
                $createdAt = $interaction->updated_at;
            }
            
            // Se ainda não tiver data, pular esta interação
            if (!$createdAt) {
                continue;
            }
            
            // Garantir que é um objeto Carbon/DateTime
            if (is_string($createdAt)) {
                try {
                    $createdAt = \Carbon\Carbon::parse($createdAt);
                } catch (\Exception $e) {
                    continue; // Pular se não conseguir fazer parse
                }
            }
            
            if ($interaction->interaction_type === 'pergunta') {
                $description = "<strong>Etapa:</strong> {$interaction->step}<br>";
                $description .= "<strong>Pergunta:</strong> \"{$interaction->question}\"";
                if ($interaction->answer) {
                    $description .= "<br><strong>Resposta:</strong> \"{$interaction->answer}\"";
                }
                
                $interactions[] = [
                    'date' => $createdAt->format('d/m/Y H:i'),
                    'title' => 'Pergunta Registrada',
                    'description' => $description,
                    'icon' => 'fa-question-circle',
                    'color' => 'primary',
                    'status' => 'completed'
                ];
            } elseif ($interaction->interaction_type === 'observacao') {
                $description = "<strong>Etapa:</strong> {$interaction->step}<br>";
                $description .= "<strong>Observação:</strong> \"{$interaction->observation}\"";
                
                $interactions[] = [
                    'date' => $createdAt->format('d/m/Y H:i'),
                    'title' => 'Observação Registrada',
                    'description' => $description,
                    'icon' => 'fa-sticky-note',
                    'color' => 'warning',
                    'status' => 'completed'
                ];
            }
        }
        
        // Ordenar interações por data
        usort($interactions, function($a, $b) {
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
            'activities' => $activities,
            'interactions' => $interactions
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
