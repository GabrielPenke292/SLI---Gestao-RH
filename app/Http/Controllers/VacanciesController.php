<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Vacancy;
use App\Models\Department;

class VacanciesController extends Controller
{
    /**
     * Permissões necessárias para cadastrar/editar vagas
     */
    private function getRequiredPermissions(): array
    {
        return ['admin', 'diretoria', 'gerente_rh', 'rh_operacional'];
    }

    /**
     * Verificar se o usuário tem permissão para cadastrar/editar vagas
     */
    private function checkPermission(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->hasAnyPermission($this->getRequiredPermissions());
    }

    /**
     * Exibir página inicial de vagas
     */
    public function index()
    {
        return view('vacancies.index');
    }

    /**
     * Listar vagas abertas
     */
    public function open()
    {
        $canCreate = $this->checkPermission();
        return view('vacancies.open', compact('canCreate'));
    }

    /**
     * Exibir formulário de criação de vaga
     */
    public function create()
    {
        if (!$this->checkPermission()) {
            return redirect()->route('vacancies.open')
                ->with('error', 'Você não tem permissão para cadastrar vagas.');
        }

        $departments = Department::whereNull('deleted_at')
            ->where('department_status', 1)
            ->orderBy('department_name')
            ->get();

        return view('vacancies.create', compact('departments'));
    }

    /**
     * Salvar nova vaga
     */
    public function store(Request $request)
    {
        if (!$this->checkPermission()) {
            return redirect()->route('vacancies.open')
                ->with('error', 'Você não tem permissão para cadastrar vagas.');
        }

        $validated = $request->validate([
            'vacancy_title' => 'required|string|max:255',
            'vacancy_description' => 'required|string',
            'urgency_level' => 'required|in:baixa,media,alta,critica',
            'salary' => 'nullable|numeric|min:0',
            'work_type' => 'nullable|string|max:50',
            'work_schedule' => 'nullable|string|max:50',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,department_id',
            'status' => 'required|in:aberta,pausada,encerrada',
            'opening_date' => 'nullable|date',
            'closing_date' => 'nullable|date|after_or_equal:opening_date',
        ], [
            'vacancy_title.required' => 'O título da vaga é obrigatório.',
            'vacancy_description.required' => 'A descrição da vaga é obrigatória.',
            'urgency_level.required' => 'O grau de urgência é obrigatório.',
            'urgency_level.in' => 'O grau de urgência deve ser: baixa, media, alta ou critica.',
            'salary.numeric' => 'O salário deve ser um número válido.',
            'salary.min' => 'O salário não pode ser negativo.',
            'department_id.exists' => 'O departamento selecionado é inválido.',
            'status.required' => 'O status da vaga é obrigatório.',
            'status.in' => 'O status deve ser: aberta, pausada ou encerrada.',
            'opening_date.date' => 'A data de abertura deve ser uma data válida.',
            'closing_date.date' => 'A data de fechamento deve ser uma data válida.',
            'closing_date.after_or_equal' => 'A data de fechamento deve ser igual ou posterior à data de abertura.',
        ]);

        try {
            DB::beginTransaction();

            $validated['created_by'] = Auth::user()->user_name ?? 'system';
            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            // Se não foi informada a data de abertura, usar a data atual
            if (empty($validated['opening_date'])) {
                $validated['opening_date'] = now()->toDateString();
            }

            Vacancy::create($validated);

            DB::commit();

            return redirect()->route('vacancies.open')
                ->with('success', 'Vaga cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar vaga: ' . $e->getMessage());
        }
    }

    /**
     * Exibir formulário de edição de vaga
     */
    public function edit($id)
    {
        if (!$this->checkPermission()) {
            return redirect()->route('vacancies.open')
                ->with('error', 'Você não tem permissão para editar vagas.');
        }

        $vacancy = Vacancy::with('department')
            ->whereNull('deleted_at')
            ->findOrFail($id);

        $departments = Department::whereNull('deleted_at')
            ->where('department_status', 1)
            ->orderBy('department_name')
            ->get();

        return view('vacancies.edit', compact('vacancy', 'departments'));
    }

    /**
     * Atualizar vaga
     */
    public function update(Request $request, $id)
    {
        if (!$this->checkPermission()) {
            return redirect()->route('vacancies.open')
                ->with('error', 'Você não tem permissão para editar vagas.');
        }

        $vacancy = Vacancy::whereNull('deleted_at')->findOrFail($id);

        $validated = $request->validate([
            'vacancy_title' => 'required|string|max:255',
            'vacancy_description' => 'required|string',
            'urgency_level' => 'required|in:baixa,media,alta,critica',
            'salary' => 'nullable|numeric|min:0',
            'work_type' => 'nullable|string|max:50',
            'work_schedule' => 'nullable|string|max:50',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,department_id',
            'status' => 'required|in:aberta,pausada,encerrada',
            'opening_date' => 'nullable|date',
            'closing_date' => 'nullable|date|after_or_equal:opening_date',
        ], [
            'vacancy_title.required' => 'O título da vaga é obrigatório.',
            'vacancy_description.required' => 'A descrição da vaga é obrigatória.',
            'urgency_level.required' => 'O grau de urgência é obrigatório.',
            'urgency_level.in' => 'O grau de urgência deve ser: baixa, media, alta ou critica.',
            'salary.numeric' => 'O salário deve ser um número válido.',
            'salary.min' => 'O salário não pode ser negativo.',
            'department_id.exists' => 'O departamento selecionado é inválido.',
            'status.required' => 'O status da vaga é obrigatório.',
            'status.in' => 'O status deve ser: aberta, pausada ou encerrada.',
            'opening_date.date' => 'A data de abertura deve ser uma data válida.',
            'closing_date.date' => 'A data de fechamento deve ser uma data válida.',
            'closing_date.after_or_equal' => 'A data de fechamento deve ser igual ou posterior à data de abertura.',
        ]);

        try {
            DB::beginTransaction();

            $validated['updated_by'] = Auth::user()->user_name ?? 'system';
            $validated['updated_at'] = now();

            $vacancy->update($validated);

            DB::commit();

            return redirect()->route('vacancies.open')
                ->with('success', 'Vaga atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar vaga: ' . $e->getMessage());
        }
    }

    /**
     * Excluir vaga (soft delete)
     */
    public function destroy($id)
    {
        if (!$this->checkPermission()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir vagas.'
            ], 403);
        }

        try {
            $vacancy = Vacancy::whereNull('deleted_at')->findOrFail($id);
            
            $vacancy->deleted_by = Auth::user()->user_name ?? 'system';
            $vacancy->save();
            
            $vacancy->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Vaga excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir vaga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna os dados das vagas para o DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $status = $request->input('status', 'aberta'); // Filtro por status
        
        // Mapear colunas do DataTables para colunas do banco
        $columns = [
            'vacancy_id',
            'vacancy_title',
            'urgency_level',
            'department_name',
            'opening_date',
            'status',
        ];
        
        // Query base com eager loading
        $query = Vacancy::with('department')
            ->whereNull('deleted_at');

        // Filtrar por status se especificado
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        // Aplicar busca
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('vacancy_title', 'like', "%{$search}%")
                  ->orWhere('vacancy_description', 'like', "%{$search}%")
                  ->orWhere('urgency_level', 'like', "%{$search}%")
                  ->orWhereHas('department', function($deptQuery) use ($search) {
                      $deptQuery->where('department_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Contar total de registros antes da paginação
        $totalQuery = Vacancy::whereNull('deleted_at');
        if ($status && $status !== 'all') {
            $totalQuery->where('status', $status);
        }
        $totalRecords = $totalQuery->count();
        
        // Clonar query para contar filtrados
        $countQuery = clone $query;
        $filteredCount = $countQuery->count();
        
        // Aplicar ordenação
        $orderByColumn = $columns[$orderColumn] ?? 'vacancy_id';
        
        if (in_array($orderByColumn, ['vacancy_id', 'vacancy_title', 'urgency_level', 'opening_date', 'status'])) {
            $query->orderBy($orderByColumn, $orderDir);
        } else {
            // Ordenação por relacionamento (departamento)
            if ($orderByColumn === 'department_name') {
                $query->join('departments', 'vacancies.department_id', '=', 'departments.department_id')
                      ->orderBy('departments.department_name', $orderDir)
                      ->select('vacancies.*');
            } else {
                $query->orderBy('vacancy_id', $orderDir);
            }
        }
        
        // Aplicar paginação
        $vacancies = $query->skip($start)
            ->take($length)
            ->get();
        
        // Formatar dados para o DataTables
        $canEdit = $this->checkPermission();
        $data = $vacancies->map(function($vacancy) use ($canEdit) {
            // Formatar salário
            $salary = $vacancy->salary 
                ? 'R$ ' . number_format($vacancy->salary, 2, ',', '.') 
                : '-';
            
            // Badge de urgência
            $urgencyBadges = [
                'baixa' => '<span class="badge bg-success">Baixa</span>',
                'media' => '<span class="badge bg-info">Média</span>',
                'alta' => '<span class="badge bg-warning">Alta</span>',
                'critica' => '<span class="badge bg-danger">Crítica</span>',
            ];
            $urgencyBadge = $urgencyBadges[$vacancy->urgency_level] ?? '';

            // Badge de status
            $statusBadges = [
                'aberta' => '<span class="badge bg-success">Aberta</span>',
                'pausada' => '<span class="badge bg-warning">Pausada</span>',
                'encerrada' => '<span class="badge bg-secondary">Encerrada</span>',
            ];
            $statusBadge = $statusBadges[$vacancy->status] ?? '';

            return [
                'id' => $vacancy->vacancy_id,
                'title' => $vacancy->vacancy_title,
                'urgency' => $urgencyBadge,
                'urgency_raw' => $vacancy->urgency_level,
                'department' => $vacancy->department?->department_name ?? '-',
                'salary' => $salary,
                'opening_date' => $vacancy->opening_date?->format('d/m/Y') ?? '-',
                'status' => $statusBadge,
                'status_raw' => $vacancy->status,
                'can_edit' => $canEdit,
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
