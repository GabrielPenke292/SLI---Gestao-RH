<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Movement;
use App\Models\Worker;
use App\Models\Department;
use App\Models\Role;
use App\Helpers\ActivityLogger;

class MovementsController extends Controller
{
    public function index()
    {
        return view('movements.index');
    }

    /**
     * Buscar todas as movimentações
     */
    public function getMovementsData(): JsonResponse
    {
        $movements = Movement::whereNull('deleted_at')
            ->with([
                'worker',
                'oldDepartment',
                'newDepartment',
                'oldRole',
                'newRole',
                'requester',
                'approver',
                'rejecter'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($movement) {
                $oldRoles = $movement->oldRole ? $movement->oldRole->role_name : '-';
                $newRoles = $movement->newRole ? $movement->newRole->role_name : '-';
                
                $statusLabels = [
                    'pendente' => 'Pendente',
                    'aprovado' => 'Aprovado',
                    'rejeitado' => 'Rejeitado'
                ];
                
                $statusBadges = [
                    'pendente' => 'warning',
                    'aprovado' => 'success',
                    'rejeitado' => 'danger'
                ];
                
                return [
                    'movement_id' => $movement->movement_id,
                    'worker_name' => $movement->worker->worker_name ?? '-',
                    'worker_email' => $movement->worker->worker_email ?? '-',
                    'old_department' => $movement->oldDepartment?->department_name ?? '-',
                    'new_department' => $movement->newDepartment?->department_name ?? '-',
                    'old_role' => $oldRoles,
                    'new_role' => $newRoles,
                    'status' => $movement->status,
                    'status_label' => $statusLabels[$movement->status] ?? $movement->status,
                    'status_badge' => $statusBadges[$movement->status] ?? 'secondary',
                    'observation' => $movement->observation ?? '-',
                    'rejection_reason' => $movement->rejection_reason ?? null,
                    'requested_by' => $movement->requester?->user_name ?? '-',
                    'approved_by' => $movement->approver?->user_name ?? null,
                    'rejected_by' => $movement->rejecter?->user_name ?? null,
                    'created_at' => $movement->created_at?->format('d/m/Y H:i') ?? '-',
                    'approved_at' => $movement->approved_at?->format('d/m/Y H:i') ?? null,
                    'rejected_at' => $movement->rejected_at?->format('d/m/Y H:i') ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $movements
        ]);
    }

    /**
     * Buscar funcionários para Select2
     */
    public function getWorkers(Request $request): JsonResponse
    {
        $search = $request->input('q', '');
        
        $workers = Worker::whereNull('deleted_at')
            ->where('worker_status', 1)
            ->where('worker_name', 'like', "%{$search}%")
            ->with(['department', 'roles'])
            ->orderBy('worker_name', 'asc')
            ->limit(50)
            ->get()
            ->map(function($worker) {
                $roles = $worker->roles->pluck('role_name')->implode(', ');
                
                return [
                    'id' => $worker->worker_id,
                    'text' => $worker->worker_name . ' - ' . ($worker->department?->department_name ?? 'Sem departamento') . ' (' . ($roles ?: 'Sem cargo') . ')',
                    'worker_name' => $worker->worker_name,
                    'worker_email' => $worker->worker_email,
                    'department_id' => $worker->department_id,
                    'department_name' => $worker->department?->department_name ?? null,
                    'role_ids' => $worker->roles->pluck('role_id')->toArray(),
                    'role_names' => $worker->roles->pluck('role_name')->toArray(),
                ];
            });

        return response()->json([
            'results' => $workers
        ]);
    }

    /**
     * Buscar departamentos ativos
     */
    public function getDepartments(): JsonResponse
    {
        $departments = Department::whereNull('deleted_at')
            ->where('department_status', 1)
            ->orderBy('department_name', 'asc')
            ->get()
            ->map(function($dept) {
                return [
                    'id' => $dept->department_id,
                    'text' => $dept->department_name,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Buscar cargos ativos
     */
    public function getRoles(): JsonResponse
    {
        $roles = Role::whereNull('deleted_at')
            ->where('role_status', 1)
            ->orderBy('role_name', 'asc')
            ->get()
            ->map(function($role) {
                return [
                    'id' => $role->role_id,
                    'text' => $role->role_name,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Buscar dados do funcionário
     */
    public function getWorkerData($id): JsonResponse
    {
        $worker = Worker::whereNull('deleted_at')
            ->where('worker_status', 1)
            ->with(['department', 'roles'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'worker_id' => $worker->worker_id,
                'worker_name' => $worker->worker_name,
                'worker_email' => $worker->worker_email,
                'department_id' => $worker->department_id,
                'department_name' => $worker->department?->department_name ?? null,
                'role_ids' => $worker->roles->pluck('role_id')->toArray(),
                'role_names' => $worker->roles->pluck('role_name')->toArray(),
            ]
        ]);
    }

    /**
     * Criar nova movimentação
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'worker_id' => 'required|integer|exists:workers,worker_id',
            'new_department_id' => 'nullable|integer|exists:departments,department_id',
            'new_role_id' => 'nullable|integer|exists:roles,role_id',
            'observation' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();
            
            $worker = Worker::whereNull('deleted_at')
                ->where('worker_status', 1)
                ->with(['department', 'roles'])
                ->findOrFail($request->input('worker_id'));

            // Verificar se há mudança
            $hasDepartmentChange = $request->input('new_department_id') && 
                $request->input('new_department_id') != $worker->department_id;
            
            $hasRoleChange = $request->input('new_role_id') && 
                !$worker->roles->contains('role_id', $request->input('new_role_id'));

            if (!$hasDepartmentChange && !$hasRoleChange) {
                return response()->json([
                    'success' => false,
                    'message' => 'É necessário selecionar um novo departamento ou cargo diferente do atual.'
                ], 400);
            }

            // Obter IDs antigos
            $oldDepartmentId = $worker->department_id;
            $oldRoleId = $worker->roles->first()?->role_id;

            $movement = Movement::create([
                'worker_id' => $request->input('worker_id'),
                'old_department_id' => $oldDepartmentId,
                'new_department_id' => $request->input('new_department_id') ?: $oldDepartmentId,
                'old_role_id' => $oldRoleId,
                'new_role_id' => $request->input('new_role_id') ?: $oldRoleId,
                'status' => 'pendente',
                'observation' => $request->input('observation'),
                'requested_by' => $user->users_id ?? null,
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            // Registrar no log de atividades
            ActivityLogger::logWorkerMovement(
                $movement->movement_id,
                $worker->worker_id,
                $worker->department?->department_name,
                Department::find($request->input('new_department_id'))?->department_name,
                $worker->roles->first()?->role_name,
                Role::find($request->input('new_role_id'))?->role_name,
                $request->input('observation')
            );

            return response()->json([
                'success' => true,
                'message' => 'Movimentação solicitada com sucesso! Aguardando aprovação.',
                'data' => $movement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao solicitar movimentação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar se usuário pode aprovar movimentações
     */
    private function canApprove(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Verificar permissões do usuário
        $permissions = session('user.permissions', []);
        if (empty($permissions) && $user) {
            // Se não estiver na sessão, buscar do banco
            $permissions = $user->permissions()->where('permission_status', 1)->pluck('permission_name')->toArray();
        }
        
        return in_array('admin', $permissions) || 
               in_array('diretoria', $permissions) || 
               in_array('gerente rh', $permissions);
    }

    /**
     * Aprovar movimentação
     */
    public function approve(Request $request, $id): JsonResponse
    {
        if (!$this->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para aprovar movimentações.'
            ], 403);
        }

        $movement = Movement::whereNull('deleted_at')
            ->where('status', 'pendente')
            ->with(['worker', 'oldDepartment', 'newDepartment', 'oldRole', 'newRole'])
            ->findOrFail($id);

        try {
            $user = Auth::user();
            
            DB::beginTransaction();

            // Atualizar funcionário
            $worker = $movement->worker;
            
            if ($movement->new_department_id) {
                $worker->update([
                    'department_id' => $movement->new_department_id,
                    'updated_at' => now(),
                    'updated_by' => $user->user_name ?? 'system',
                ]);
            }

            if ($movement->new_role_id) {
                // Remover cargo antigo se houver
                if ($movement->old_role_id) {
                    $worker->roles()->detach($movement->old_role_id);
                }
                
                // Adicionar novo cargo
                $worker->roles()->syncWithoutDetaching([$movement->new_role_id => [
                    'worker_role_status' => 1,
                    'created_at' => now(),
                    'created_by' => $user->user_name ?? 'system',
                ]]);
            }

            // Atualizar movimentação
            $movement->update([
                'status' => 'aprovado',
                'approved_by' => $user->users_id ?? null,
                'approved_at' => now(),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            // Registrar no log de atividades
            ActivityLogger::logWorkerMovementApproved(
                $movement->movement_id,
                $worker->worker_id,
                $movement->oldDepartment?->department_name,
                $movement->newDepartment?->department_name,
                $movement->oldRole?->role_name,
                $movement->newRole?->role_name
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Movimentação aprovada e aplicada com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar movimentação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeitar movimentação
     */
    public function reject(Request $request, $id): JsonResponse
    {
        if (!$this->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para rejeitar movimentações.'
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string',
        ]);

        $movement = Movement::whereNull('deleted_at')
            ->where('status', 'pendente')
            ->with(['worker'])
            ->findOrFail($id);

        try {
            $user = Auth::user();

            $movement->update([
                'status' => 'rejeitado',
                'rejection_reason' => $request->input('rejection_reason'),
                'rejected_by' => $user->users_id ?? null,
                'rejected_at' => now(),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            // Registrar no log de atividades
            ActivityLogger::logWorkerMovementRejected(
                $movement->movement_id,
                $movement->worker->worker_id,
                $request->input('rejection_reason')
            );

            return response()->json([
                'success' => true,
                'message' => 'Movimentação rejeitada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao rejeitar movimentação: ' . $e->getMessage()
            ], 500);
        }
    }
}
