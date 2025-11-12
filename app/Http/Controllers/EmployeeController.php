<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Worker;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employees.index');
    }

    public function board()
    {
        return view('employees.board');
    }

    public function create()
    {
        $departments = Department::whereNull('deleted_at')
            ->where('department_status', 1)
            ->orderBy('department_name')
            ->get();
        
        $roles = Role::whereNull('deleted_at')
            ->where('role_status', 1)
            ->orderBy('role_name')
            ->get();
        
        return view('employees.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'worker_name' => 'required|string|max:75',
            'worker_email' => 'required|email|max:45|unique:workers,worker_email',
            'worker_document' => 'required|string|max:20',
            'worker_rg' => 'nullable|string|max:20',
            'worker_birth_date' => 'required|date',
            'worker_start_date' => 'required|date',
            'worker_status' => 'required|integer|in:0,1',
            'worker_salary' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,department_id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,role_id',
        ], [
            'worker_name.required' => 'O nome do funcionário é obrigatório.',
            'worker_email.required' => 'O email é obrigatório.',
            'worker_email.email' => 'O email deve ser válido.',
            'worker_email.unique' => 'Este email já está cadastrado.',
            'worker_document.required' => 'O CPF é obrigatório.',
            'worker_birth_date.required' => 'A data de nascimento é obrigatória.',
            'worker_start_date.required' => 'A data de admissão é obrigatória.',
            'worker_status.required' => 'O status é obrigatório.',
            'worker_salary.required' => 'O salário é obrigatório.',
            'department_id.required' => 'O departamento é obrigatório.',
            'roles.required' => 'Selecione pelo menos um cargo.',
            'roles.min' => 'Selecione pelo menos um cargo.',
        ]);

        try {
            DB::beginTransaction();

            $validated['created_by'] = Auth::user()->name ?? 'system';
            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            $roles = $validated['roles'];
            unset($validated['roles']);

            $worker = Worker::create($validated);

            // Associar roles ao funcionário
            foreach ($roles as $roleId) {
                $worker->roles()->attach($roleId, [
                    'worker_role_status' => 1,
                    'created_at' => now(),
                    'created_by' => Auth::user()->name ?? 'system',
                ]);
            }

            DB::commit();

            return redirect()->route('employees.board')
                ->with('success', 'Funcionário cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar funcionário: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $worker = Worker::with(['department', 'roles'])
            ->whereNull('deleted_at')
            ->findOrFail($id);
        
        return view('employees.view', compact('worker'));
    }

    /**
     * Retornar timeline de movimentações do funcionário
     */
    public function getWorkerTimeline(Request $request, $workerId): JsonResponse
    {
        $worker = Worker::whereNull('deleted_at')->findOrFail($workerId);
        
        $activities = [];
        
        // Buscar logs de movimentações
        $movementLogs = \App\Models\ActivityLog::where('log_type', 'worker_movement')
            ->where('entity_type', 'Worker')
            ->where('entity_id', $workerId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($movementLogs as $log) {
            if (!$log->created_at) {
                continue;
            }
            
            $metadata = $log->metadata ?? [];
            $oldDept = $metadata['old_department'] ?? null;
            $newDept = $metadata['new_department'] ?? null;
            $oldRole = $metadata['old_role'] ?? null;
            $newRole = $metadata['new_role'] ?? null;
            $observation = $metadata['observation'] ?? null;
            
            $description = '';
            if ($oldDept && $newDept && $oldDept !== $newDept) {
                $description .= "<strong>Departamento:</strong> {$oldDept} → {$newDept}<br>";
            }
            if ($oldRole && $newRole && $oldRole !== $newRole) {
                $description .= "<strong>Cargo:</strong> {$oldRole} → {$newRole}<br>";
            }
            if ($observation) {
                $description .= "<strong>Observação:</strong> {$observation}";
            }
            
            $activities[] = [
                'date' => $log->created_at->format('d/m/Y H:i'),
                'title' => 'Movimentação Solicitada',
                'description' => $description ?: 'Movimentação de cargo solicitada',
                'icon' => 'fa-exchange-alt',
                'color' => 'info',
                'status' => 'pending'
            ];
        }
        
        // Buscar logs de aprovação
        $approvalLogs = \App\Models\ActivityLog::where('log_type', 'worker_movement_approved')
            ->where('entity_type', 'Worker')
            ->where('entity_id', $workerId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($approvalLogs as $log) {
            if (!$log->created_at) {
                continue;
            }
            
            $metadata = $log->metadata ?? [];
            $oldDept = $metadata['old_department'] ?? null;
            $newDept = $metadata['new_department'] ?? null;
            $oldRole = $metadata['old_role'] ?? null;
            $newRole = $metadata['new_role'] ?? null;
            
            $description = '';
            if ($oldDept && $newDept && $oldDept !== $newDept) {
                $description .= "<strong>Departamento:</strong> {$oldDept} → {$newDept}<br>";
            }
            if ($oldRole && $newRole && $oldRole !== $newRole) {
                $description .= "<strong>Cargo:</strong> {$oldRole} → {$newRole}";
            }
            
            $activities[] = [
                'date' => $log->created_at->format('d/m/Y H:i'),
                'title' => '✅ Movimentação Aprovada',
                'description' => $description ?: 'Movimentação de cargo aprovada e aplicada',
                'icon' => 'fa-check-circle',
                'color' => 'success',
                'status' => 'completed',
                'highlight' => true
            ];
        }
        
        // Buscar logs de rejeição
        $rejectionLogs = \App\Models\ActivityLog::where('log_type', 'worker_movement_rejected')
            ->where('entity_type', 'Worker')
            ->where('entity_id', $workerId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($rejectionLogs as $log) {
            if (!$log->created_at) {
                continue;
            }
            
            $metadata = $log->metadata ?? [];
            $rejectionReason = $metadata['rejection_reason'] ?? null;
            
            $description = 'Movimentação de cargo rejeitada';
            if ($rejectionReason) {
                $description .= "<br><strong>Motivo:</strong> {$rejectionReason}";
            }
            
            $activities[] = [
                'date' => $log->created_at->format('d/m/Y H:i'),
                'title' => '❌ Movimentação Rejeitada',
                'description' => $description,
                'icon' => 'fa-times-circle',
                'color' => 'danger',
                'status' => 'rejected',
                'highlight' => true
            ];
        }
        
        // Ordenar por data (mais recente primeiro)
        usort($activities, function($a, $b) {
            return strtotime(str_replace('/', '-', $b['date'])) - strtotime(str_replace('/', '-', $a['date']));
        });
        
        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }

    public function edit($id)
    {
        $worker = Worker::with(['department', 'roles'])
            ->whereNull('deleted_at')
            ->findOrFail($id);
        
        $departments = Department::whereNull('deleted_at')
            ->where('department_status', 1)
            ->orderBy('department_name')
            ->get();
        
        $roles = Role::whereNull('deleted_at')
            ->where('role_status', 1)
            ->orderBy('role_name')
            ->get();
        
        return view('employees.edit', compact('worker', 'departments', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $worker = Worker::whereNull('deleted_at')->findOrFail($id);

        $validated = $request->validate([
            'worker_name' => 'required|string|max:75',
            'worker_email' => 'required|email|max:45|unique:workers,worker_email,' . $worker->worker_id . ',worker_id',
            'worker_document' => 'required|string|max:20',
            'worker_rg' => 'nullable|string|max:20',
            'worker_birth_date' => 'required|date',
            'worker_start_date' => 'required|date',
            'worker_status' => 'required|integer|in:0,1',
            'worker_salary' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,department_id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,role_id',
        ], [
            'worker_name.required' => 'O nome do funcionário é obrigatório.',
            'worker_email.required' => 'O email é obrigatório.',
            'worker_email.email' => 'O email deve ser válido.',
            'worker_email.unique' => 'Este email já está cadastrado.',
            'worker_document.required' => 'O CPF é obrigatório.',
            'worker_birth_date.required' => 'A data de nascimento é obrigatória.',
            'worker_start_date.required' => 'A data de admissão é obrigatória.',
            'worker_status.required' => 'O status é obrigatório.',
            'worker_salary.required' => 'O salário é obrigatório.',
            'department_id.required' => 'O departamento é obrigatório.',
            'roles.required' => 'Selecione pelo menos um cargo.',
            'roles.min' => 'Selecione pelo menos um cargo.',
        ]);

        try {
            DB::beginTransaction();

            $validated['updated_by'] = Auth::user()->name ?? 'system';
            $validated['updated_at'] = now();

            $roles = $validated['roles'];
            unset($validated['roles']);

            $worker->update($validated);

            // Remover todos os roles e adicionar os novos
            $worker->roles()->detach();
            foreach ($roles as $roleId) {
                $worker->roles()->attach($roleId, [
                    'worker_role_status' => 1,
                    'created_at' => now(),
                    'created_by' => Auth::user()->name ?? 'system',
                ]);
            }

            DB::commit();

            return redirect()->route('employees.view', $worker->worker_id)
                ->with('success', 'Funcionário atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar funcionário: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $worker = Worker::whereNull('deleted_at')->findOrFail($id);
            
            // Atualizar deleted_by antes de fazer o soft delete
            $worker->deleted_by = Auth::user()->name ?? 'system';
            $worker->save();
            
            // Fazer o soft delete
            $worker->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Funcionário excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir funcionário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna os dados dos funcionários para o DataTables
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getData(Request $request): JsonResponse
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        // Mapear colunas do DataTables para colunas do banco
        $columns = [
            'worker_id',
            'worker_name',
            'worker_email',
            'role_name',
            'department_name',
            'worker_start_date',
            'worker_status',
        ];
        
        // Query base com eager loading
        $query = Worker::with(['department', 'roles'])
            ->whereNull('deleted_at');
        
        // Aplicar busca
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('worker_name', 'like', "%{$search}%")
                  ->orWhere('worker_email', 'like', "%{$search}%")
                  ->orWhereHas('roles', function($roleQuery) use ($search) {
                      $roleQuery->where('role_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function($deptQuery) use ($search) {
                      $deptQuery->where('department_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Contar total de registros antes da paginação
        $totalRecords = Worker::whereNull('deleted_at')->count();
        
        // Clonar query para contar filtrados
        $countQuery = clone $query;
        $filteredCount = $countQuery->count();
        
        // Aplicar ordenação baseada na coluna selecionada
        $orderByColumn = $columns[$orderColumn] ?? 'worker_id';
        
        // Ordenação simples para colunas diretas
        if (in_array($orderByColumn, ['worker_id', 'worker_name', 'worker_email', 'worker_start_date', 'worker_status'])) {
            $query->orderBy($orderByColumn, $orderDir);
        } else {
            // Default para ordenação por ID
            $query->orderBy('worker_id', $orderDir);
        }
        
        // Aplicar paginação
        $workers = $query->skip($start)
            ->take($length)
            ->get();
        
        // Formatar dados para o DataTables
        $data = $workers->map(function($worker) {
            // Obter o primeiro role (ou concatenar todos se houver múltiplos)
            $roles = $worker->roles->pluck('role_name')->implode(', ');
            $position = $roles ?: '-';
            
            return [
                'id' => $worker->worker_id,
                'name' => $worker->worker_name,
                'email' => $worker->worker_email,
                'position' => $position,
                'department' => $worker->department?->department_name ?? '-',
                'hire_date' => $worker->worker_start_date?->format('Y-m-d') ?? null,
                'status' => $worker->worker_status,
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
