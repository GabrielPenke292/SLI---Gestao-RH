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
        $candidate = Candidate::whereNull('deleted_at')->findOrFail($id);
        return view('candidates.show', compact('candidate'));
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
