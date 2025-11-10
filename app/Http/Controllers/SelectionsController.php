<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\SelectionProcess;
use App\Models\Vacancy;
use App\Models\User;
use App\Models\Candidate;

class SelectionsController extends Controller
{
    /**
     * Permissões necessárias para aprovar processos
     */
    private function getApproverPermissions(): array
    {
        return ['admin', 'diretoria', 'gerente_rh'];
    }

    /**
     * Verificar se o usuário tem permissão para aprovar processos
     */
    private function canApprove(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->hasAnyPermission($this->getApproverPermissions());
    }

    /**
     * Exibir página principal de processos seletivos
     */
    public function index()
    {
        return view('selections.index');
    }

    /**
     * Exibir processos aguardando aprovação
     */
    public function awaiting()
    {
        $canApprove = $this->canApprove();
        return view('selections.awaiting', compact('canApprove'));
    }

    /**
     * Exibir processos em andamento
     */
    public function inProgress()
    {
        return view('selections.in-progress');
    }

    /**
     * Exibir processos encerrados e congelados
     */
    public function finished()
    {
        return view('selections.finished');
    }

    /**
     * Exibir formulário de criação de processo seletivo
     */
    public function create()
    {
        // Buscar apenas vagas abertas
        $vacancies = Vacancy::whereNull('deleted_at')
            ->where('status', 'aberta')
            ->orderBy('vacancy_title')
            ->get();

        // Buscar usuários que podem aprovar
        $approvers = User::whereHas('permissions', function($query) {
            $query->whereIn('permission_name', $this->getApproverPermissions());
        })->orderBy('user_name')->get();

        return view('selections.create', compact('vacancies', 'approvers'));
    }

    /**
     * Salvar novo processo seletivo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'process_number' => 'required|string|max:50|unique:selection_processes,process_number',
            'vacancy_id' => 'required|exists:vacancies,vacancy_id',
            'reason' => 'required|in:substituicao,aumento_quadro',
            'approver_id' => 'required|exists:users,users_id',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'observations' => 'nullable|string',
        ], [
            'process_number.required' => 'O número do processo é obrigatório.',
            'process_number.unique' => 'Este número de processo já está em uso.',
            'vacancy_id.required' => 'A vaga é obrigatória.',
            'vacancy_id.exists' => 'A vaga selecionada é inválida.',
            'reason.required' => 'O motivo do processo é obrigatório.',
            'reason.in' => 'O motivo deve ser: substituição ou aumento de quadro.',
            'approver_id.required' => 'O aprovador é obrigatório.',
            'approver_id.exists' => 'O aprovador selecionado é inválido.',
            'budget.numeric' => 'A verba deve ser um número válido.',
            'budget.min' => 'A verba não pode ser negativa.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
            'end_date.date' => 'A data de encerramento deve ser uma data válida.',
            'end_date.after_or_equal' => 'A data de encerramento deve ser igual ou posterior à data de início.',
        ]);

        // Verificar se a vaga está aberta
        $vacancy = Vacancy::findOrFail($validated['vacancy_id']);
        if ($vacancy->status !== 'aberta') {
            return back()
                ->withInput()
                ->with('error', 'Apenas vagas abertas podem ter processos seletivos criados.');
        }

        // Validar se as datas estão dentro do intervalo da vaga
        if (!empty($validated['start_date']) && $vacancy->opening_date) {
            $startDate = Carbon::parse($validated['start_date']);
            if ($startDate->lt($vacancy->opening_date)) {
                return back()
                    ->withInput()
                    ->withErrors(['start_date' => 'A data de início deve ser igual ou posterior à data de abertura da vaga (' . $vacancy->opening_date->format('d/m/Y') . ').']);
            }
        }

        if (!empty($validated['start_date']) && $vacancy->closing_date) {
            $startDate = Carbon::parse($validated['start_date']);
            if ($startDate->gt($vacancy->closing_date)) {
                return back()
                    ->withInput()
                    ->withErrors(['start_date' => 'A data de início deve ser igual ou anterior à data de fechamento da vaga (' . $vacancy->closing_date->format('d/m/Y') . ').']);
            }
        }

        if (!empty($validated['end_date']) && $vacancy->opening_date) {
            $endDate = Carbon::parse($validated['end_date']);
            if ($endDate->lt($vacancy->opening_date)) {
                return back()
                    ->withInput()
                    ->withErrors(['end_date' => 'A data de encerramento deve ser igual ou posterior à data de abertura da vaga (' . $vacancy->opening_date->format('d/m/Y') . ').']);
            }
        }

        if (!empty($validated['end_date']) && $vacancy->closing_date) {
            $endDate = Carbon::parse($validated['end_date']);
            if ($endDate->gt($vacancy->closing_date)) {
                return back()
                    ->withInput()
                    ->withErrors(['end_date' => 'A data de encerramento deve ser igual ou anterior à data de fechamento da vaga (' . $vacancy->closing_date->format('d/m/Y') . ').']);
            }
        }

        try {
            DB::beginTransaction();

            $validated['status'] = 'aguardando_aprovacao';
            $validated['created_by'] = Auth::user()->user_name ?? 'system';
            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            SelectionProcess::create($validated);

            DB::commit();

            return redirect()->route('selections.index')
                ->with('success', 'Processo seletivo criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar processo seletivo: ' . $e->getMessage());
        }
    }

    /**
     * Exibir formulário de edição de processo seletivo
     */
    public function edit($id)
    {
        $process = SelectionProcess::with(['vacancy', 'approver', 'candidates'])
            ->whereNull('deleted_at')
            ->findOrFail($id);

        $vacancies = Vacancy::whereNull('deleted_at')
            ->where('status', 'aberta')
            ->orWhere('vacancy_id', $process->vacancy_id) // Incluir a vaga atual mesmo se não estiver aberta
            ->orderBy('vacancy_title')
            ->get();

        $approvers = User::whereHas('permissions', function($query) {
            $query->whereIn('permission_name', $this->getApproverPermissions());
        })->orderBy('user_name')->get();

        return view('selections.edit', compact('process', 'vacancies', 'approvers'));
    }

    /**
     * Atualizar processo seletivo
     */
    public function update(Request $request, $id)
    {
        $process = SelectionProcess::whereNull('deleted_at')->findOrFail($id);

        $validated = $request->validate([
            'process_number' => 'required|string|max:50|unique:selection_processes,process_number,' . $process->selection_process_id . ',selection_process_id',
            'vacancy_id' => 'required|exists:vacancies,vacancy_id',
            'reason' => 'required|in:substituicao,aumento_quadro',
            'approver_id' => 'required|exists:users,users_id',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:aguardando_aprovacao,em_andamento,encerrado,congelado,reprovado',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'observations' => 'nullable|string',
            'approval_notes' => 'nullable|string',
            'approval_date' => 'nullable|date',
        ], [
            'process_number.required' => 'O número do processo é obrigatório.',
            'process_number.unique' => 'Este número de processo já está em uso.',
            'vacancy_id.required' => 'A vaga é obrigatória.',
            'vacancy_id.exists' => 'A vaga selecionada é inválida.',
            'reason.required' => 'O motivo do processo é obrigatório.',
            'reason.in' => 'O motivo deve ser: substituição ou aumento de quadro.',
            'approver_id.required' => 'O aprovador é obrigatório.',
            'approver_id.exists' => 'O aprovador selecionado é inválido.',
            'budget.numeric' => 'A verba deve ser um número válido.',
            'budget.min' => 'A verba não pode ser negativa.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser: aguardando aprovação, em andamento, encerrado, congelado ou reprovado.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
            'end_date.date' => 'A data de encerramento deve ser uma data válida.',
            'end_date.after_or_equal' => 'A data de encerramento deve ser igual ou posterior à data de início.',
            'approval_date.date' => 'A data de aprovação deve ser uma data válida.',
        ]);

        // Buscar a vaga selecionada
        $vacancy = Vacancy::findOrFail($validated['vacancy_id']);

        // Validar se as datas estão dentro do intervalo da vaga
        if (!empty($validated['start_date']) && $vacancy->opening_date) {
            $startDate = Carbon::parse($validated['start_date']);
            if ($startDate->lt($vacancy->opening_date)) {
                return back()
                    ->withInput()
                    ->withErrors(['start_date' => 'A data de início deve ser igual ou posterior à data de abertura da vaga (' . $vacancy->opening_date->format('d/m/Y') . ').']);
            }
        }

        if (!empty($validated['start_date']) && $vacancy->closing_date) {
            $startDate = Carbon::parse($validated['start_date']);
            if ($startDate->gt($vacancy->closing_date)) {
                return back()
                    ->withInput()
                    ->withErrors(['start_date' => 'A data de início deve ser igual ou anterior à data de fechamento da vaga (' . $vacancy->closing_date->format('d/m/Y') . ').']);
            }
        }

        if (!empty($validated['end_date']) && $vacancy->opening_date) {
            $endDate = Carbon::parse($validated['end_date']);
            if ($endDate->lt($vacancy->opening_date)) {
                return back()
                    ->withInput()
                    ->withErrors(['end_date' => 'A data de encerramento deve ser igual ou posterior à data de abertura da vaga (' . $vacancy->opening_date->format('d/m/Y') . ').']);
            }
        }

        if (!empty($validated['end_date']) && $vacancy->closing_date) {
            $endDate = Carbon::parse($validated['end_date']);
            if ($endDate->gt($vacancy->closing_date)) {
                return back()
                    ->withInput()
                    ->withErrors(['end_date' => 'A data de encerramento deve ser igual ou anterior à data de fechamento da vaga (' . $vacancy->closing_date->format('d/m/Y') . ').']);
            }
        }

        try {
            DB::beginTransaction();

            // Se o status mudou para "em_andamento" e ainda não foi aprovado, definir data de aprovação
            if ($validated['status'] === 'em_andamento' && !$process->approval_date && $this->canApprove()) {
                $validated['approval_date'] = now()->toDateString();
                if (empty($validated['approver_id'])) {
                    $validated['approver_id'] = Auth::user()->users_id;
                }
            }

            $validated['updated_by'] = Auth::user()->user_name ?? 'system';
            $validated['updated_at'] = now();

            $process->update($validated);

            DB::commit();

            return redirect()->route('selections.index')
                ->with('success', 'Processo seletivo atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar processo seletivo: ' . $e->getMessage());
        }
    }

    /**
     * Aprovar processo seletivo
     */
    public function approve(Request $request, $id)
    {
        if (!$this->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para aprovar processos seletivos.'
            ], 403);
        }

        $process = SelectionProcess::whereNull('deleted_at')->findOrFail($id);

        if ($process->status !== 'aguardando_aprovacao') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas processos aguardando aprovação podem ser aprovados.'
            ], 400);
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $process->update([
                'status' => 'em_andamento',
                'approver_id' => Auth::user()->users_id,
                'approval_date' => now()->toDateString(),
                'approval_notes' => $validated['approval_notes'] ?? null,
                'updated_by' => Auth::user()->user_name ?? 'system',
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Processo seletivo aprovado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar processo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reprovar processo seletivo
     */
    public function reject(Request $request, $id)
    {
        if (!$this->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para reprovar processos seletivos.'
            ], 403);
        }

        $process = SelectionProcess::whereNull('deleted_at')->findOrFail($id);

        if ($process->status !== 'aguardando_aprovacao') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas processos aguardando aprovação podem ser reprovados.'
            ], 400);
        }

        $validated = $request->validate([
            'approval_notes' => 'required|string|min:10',
        ], [
            'approval_notes.required' => 'O motivo da reprovação é obrigatório.',
            'approval_notes.min' => 'O motivo da reprovação deve ter pelo menos 10 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            $process->update([
                'status' => 'reprovado',
                'approver_id' => Auth::user()->users_id,
                'approval_date' => now()->toDateString(),
                'approval_notes' => $validated['approval_notes'],
                'updated_by' => Auth::user()->user_name ?? 'system',
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Processo seletivo reprovado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reprovar processo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir processo seletivo (soft delete)
     */
    public function destroy($id)
    {
        try {
            $process = SelectionProcess::whereNull('deleted_at')->findOrFail($id);
            
            $process->deleted_by = Auth::user()->user_name ?? 'system';
            $process->save();
            
            $process->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Processo seletivo excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir processo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna os dados dos processos aguardando aprovação para o DataTables
     */
    public function getAwaitingApprovalData(Request $request): JsonResponse
    {
        return $this->getDataByStatus($request, 'aguardando_aprovacao');
    }

    /**
     * Retorna os dados dos processos em andamento para o DataTables
     */
    public function getInProgressData(Request $request): JsonResponse
    {
        return $this->getDataByStatus($request, 'em_andamento');
    }

    /**
     * Retorna os dados dos processos encerrados para o DataTables
     */
    public function getFinishedData(Request $request): JsonResponse
    {
        return $this->getDataByStatus($request, ['encerrado', 'congelado', 'reprovado']);
    }

    /**
     * Buscar candidatos para vincular ao processo seletivo
     */
    public function searchCandidates(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $processId = $request->input('process_id');
        
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
        
        // Excluir candidatos já vinculados a este processo
        if ($processId) {
            $process = SelectionProcess::find($processId);
            if ($process) {
                // Usar query direta na tabela pivot para evitar ambiguidade
                $linkedCandidateIds = DB::table('selection_process_candidates')
                    ->where('selection_process_id', $processId)
                    ->pluck('candidate_id')
                    ->toArray();
                if (!empty($linkedCandidateIds)) {
                    $query->whereNotIn('candidate_id', $linkedCandidateIds);
                }
            }
        }
        
        $candidates = $query->orderBy('candidate_name')->limit(20)->get();
        
        $data = $candidates->map(function($candidate) {
            return [
                'id' => $candidate->candidate_id,
                'name' => $candidate->candidate_name,
                'email' => $candidate->candidate_email ?? '-',
                'phone' => $candidate->candidate_phone ?? '-',
                'document' => $candidate->candidate_document ?? '-',
                'experience' => $candidate->candidate_experience ? substr($candidate->candidate_experience, 0, 200) . '...' : '-',
                'education' => $candidate->candidate_education ? substr($candidate->candidate_education, 0, 200) . '...' : '-',
                'skills' => $candidate->candidate_skills ?? '-',
                'resume_pdf_url' => $candidate->resume_pdf_url,
                'has_pdf' => $candidate->has_resume_pdf,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Vincular candidato ao processo seletivo
     */
    public function attachCandidate(Request $request, $id): JsonResponse
    {
        $process = SelectionProcess::whereNull('deleted_at')->findOrFail($id);
        
        if ($process->status !== 'em_andamento') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas processos em andamento podem ter candidatos vinculados.'
            ], 400);
        }
        
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,candidate_id',
            'notes' => 'nullable|string',
        ], [
            'candidate_id.required' => 'O candidato é obrigatório.',
            'candidate_id.exists' => 'O candidato selecionado é inválido.',
        ]);
        
        // Verificar se o candidato já está vinculado (usando query direta para evitar ambiguidade)
        $alreadyLinked = DB::table('selection_process_candidates')
            ->where('selection_process_id', $id)
            ->where('candidate_id', $validated['candidate_id'])
            ->exists();
        
        if ($alreadyLinked) {
            return response()->json([
                'success' => false,
                'message' => 'Este candidato já está vinculado a este processo.'
            ], 400);
        }
        
        try {
            DB::beginTransaction();
            
            $process->candidates()->attach($validated['candidate_id'], [
                'status' => 'pendente',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::user()->user_name ?? 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Candidato vinculado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao vincular candidato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desvincular candidato do processo seletivo
     */
    public function detachCandidate(Request $request, $id): JsonResponse
    {
        $process = SelectionProcess::whereNull('deleted_at')->findOrFail($id);
        
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,candidate_id',
        ], [
            'candidate_id.required' => 'O candidato é obrigatório.',
            'candidate_id.exists' => 'O candidato selecionado é inválido.',
        ]);
        
        try {
            DB::beginTransaction();
            
            $process->candidates()->detach($validated['candidate_id']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Candidato desvinculado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao desvincular candidato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retornar candidatos vinculados ao processo
     */
    public function getProcessCandidates($id): JsonResponse
    {
        $process = SelectionProcess::with('candidates')->whereNull('deleted_at')->findOrFail($id);
        
        $candidates = $process->candidates->map(function($candidate) {
            return [
                'id' => $candidate->candidate_id,
                'name' => $candidate->candidate_name,
                'email' => $candidate->candidate_email ?? '-',
                'phone' => $candidate->candidate_phone ?? '-',
                'status' => $candidate->pivot->status ?? 'pendente',
                'notes' => $candidate->pivot->notes ?? null,
                'linked_at' => $candidate->pivot->created_at?->format('d/m/Y H:i') ?? '-',
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $candidates
        ]);
    }

    /**
     * Retorna os dados da vaga (opening_date e closing_date) para restringir datas do processo seletivo
     */
    public function getVacancyDates($id): JsonResponse
    {
        try {
            $vacancy = Vacancy::whereNull('deleted_at')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'opening_date' => $vacancy->opening_date?->format('Y-m-d'),
                'closing_date' => $vacancy->closing_date?->format('Y-m-d'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vaga não encontrada.'
            ], 404);
        }
    }

    /**
     * Método auxiliar para retornar dados por status
     */
    private function getDataByStatus(Request $request, $status): JsonResponse
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = [
            'selection_process_id',
            'process_number',
            'vacancy_title',
            'reason',
            'approver_name',
            'start_date',
            'status',
        ];
        
        $query = SelectionProcess::with(['vacancy', 'approver'])
            ->whereNull('deleted_at');

        if (is_array($status)) {
            $query->whereIn('status', $status);
        } else {
            $query->where('status', $status);
        }
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('process_number', 'like', "%{$search}%")
                  ->orWhereHas('vacancy', function($vacancyQuery) use ($search) {
                      $vacancyQuery->where('vacancy_title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('approver', function($approverQuery) use ($search) {
                      $approverQuery->where('user_name', 'like', "%{$search}%");
                  });
            });
        }
        
        $totalRecords = SelectionProcess::whereNull('deleted_at');
        if (is_array($status)) {
            $totalRecords->whereIn('status', $status);
        } else {
            $totalRecords->where('status', $status);
        }
        $totalRecords = $totalRecords->count();
        
        $countQuery = clone $query;
        $filteredCount = $countQuery->count();
        
        $orderByColumn = $columns[$orderColumn] ?? 'selection_process_id';
        
        if ($orderByColumn === 'vacancy_title') {
            $query->join('vacancies', 'selection_processes.vacancy_id', '=', 'vacancies.vacancy_id')
                  ->orderBy('vacancies.vacancy_title', $orderDir)
                  ->select('selection_processes.*');
        } elseif ($orderByColumn === 'approver_name') {
            $query->leftJoin('users', 'selection_processes.approver_id', '=', 'users.users_id')
                  ->orderBy('users.user_name', $orderDir)
                  ->select('selection_processes.*');
        } else {
            $query->orderBy($orderByColumn, $orderDir);
        }
        
        $processes = $query->skip($start)->take($length)->get();
        
        $canApprove = $this->canApprove();
        $data = $processes->map(function($process) use ($canApprove) {
            $reasonLabels = [
                'substituicao' => 'Substituição',
                'aumento_quadro' => 'Aumento de Quadro',
            ];

            $statusBadges = [
                'aguardando_aprovacao' => '<span class="badge bg-warning">Aguardando Aprovação</span>',
                'em_andamento' => '<span class="badge bg-success">Em Andamento</span>',
                'encerrado' => '<span class="badge bg-secondary">Encerrado</span>',
                'congelado' => '<span class="badge bg-info">Congelado</span>',
                'reprovado' => '<span class="badge bg-danger">Reprovado</span>',
            ];

            $budget = $process->budget 
                ? 'R$ ' . number_format($process->budget, 2, ',', '.') 
                : '-';

            return [
                'id' => $process->selection_process_id,
                'process_number' => $process->process_number,
                'vacancy_title' => $process->vacancy->vacancy_title ?? '-',
                'reason' => $reasonLabels[$process->reason] ?? $process->reason,
                'approver' => $process->approver->user_name ?? '-',
                'budget' => $budget,
                'start_date' => $process->start_date?->format('d/m/Y') ?? '-',
                'end_date' => $process->end_date?->format('d/m/Y') ?? '-',
                'status' => $statusBadges[$process->status] ?? '',
                'status_raw' => $process->status,
                'can_approve' => $canApprove,
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
