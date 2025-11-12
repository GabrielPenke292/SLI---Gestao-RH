<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Worker;
use App\Models\Layoff;

class LayoffsController extends Controller
{
    public function index()
    {
        return view('layoffs.index');
    }

    /**
     * Buscar todos os funcionários ativos
     */
    public function getActiveWorkers(): JsonResponse
    {
        $workers = Worker::whereNull('deleted_at')
            ->where('worker_status', 1)
            ->with(['department', 'roles'])
            ->orderBy('worker_name', 'asc')
            ->get()
            ->map(function($worker) {
                $roles = $worker->roles->pluck('role_name')->implode(', ');
                
                return [
                    'worker_id' => $worker->worker_id,
                    'worker_name' => $worker->worker_name,
                    'worker_email' => $worker->worker_email,
                    'worker_document' => $worker->worker_document ?? '-',
                    'department' => $worker->department?->department_name ?? '-',
                    'position' => $roles ?: '-',
                    'worker_start_date' => $worker->worker_start_date?->format('d/m/Y') ?? '-',
                    'worker_salary' => $worker->worker_salary ? number_format($worker->worker_salary, 2, ',', '.') : '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $workers
        ]);
    }

    /**
     * Buscar todos os desligamentos
     */
    public function getLayoffsData(): JsonResponse
    {
        $layoffs = Layoff::whereNull('deleted_at')
            ->with(['worker.department', 'worker.roles'])
            ->orderBy('layoff_date', 'desc')
            ->get()
            ->map(function($layoff) {
                $roles = $layoff->worker->roles->pluck('role_name')->implode(', ');
                
                $typeLabels = [
                    'pedido_demissao' => 'Pedido de Demissão',
                    'demitido' => 'Demitido',
                    'rescisao_indireta' => 'Rescisão Indireta',
                    'justa_causa' => 'Justa Causa',
                    'outro' => 'Outro'
                ];
                
                return [
                    'layoff_id' => $layoff->layoff_id,
                    'worker_name' => $layoff->worker->worker_name ?? '-',
                    'worker_email' => $layoff->worker->worker_email ?? '-',
                    'department' => $layoff->worker->department?->department_name ?? '-',
                    'position' => $roles ?: '-',
                    'layoff_date' => $layoff->layoff_date?->format('d/m/Y') ?? '-',
                    'layoff_type' => $typeLabels[$layoff->layoff_type] ?? $layoff->layoff_type,
                    'reason' => $layoff->reason ?? '-',
                    'created_at' => $layoff->created_at?->format('d/m/Y H:i') ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $layoffs
        ]);
    }

    /**
     * Criar novo desligamento
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'worker_id' => 'required|integer|exists:workers,worker_id',
            'layoff_date' => 'required|date',
            'layoff_type' => 'required|in:pedido_demissao,demitido,rescisao_indireta,justa_causa,outro',
            'reason' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'has_notice_period' => 'nullable|in:0,1',
            'notice_period_days' => 'nullable|integer|min:0',
            'severance_pay' => 'nullable|numeric|min:0',
            'severance_details' => 'nullable|string',
            'returned_equipment' => 'nullable|in:0,1',
            'equipment_details' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();
            
            // Verificar se funcionário está ativo
            $worker = Worker::whereNull('deleted_at')
                ->where('worker_status', 1)
                ->findOrFail($request->input('worker_id'));

            // Verificar se já existe desligamento para este funcionário
            $existingLayoff = Layoff::where('worker_id', $request->input('worker_id'))
                ->whereNull('deleted_at')
                ->first();

            if ($existingLayoff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este funcionário já possui um registro de desligamento.'
                ], 400);
            }

            $layoff = Layoff::create([
                'worker_id' => $request->input('worker_id'),
                'layoff_date' => $request->input('layoff_date'),
                'layoff_type' => $request->input('layoff_type'),
                'reason' => $request->input('reason'),
                'observations' => $request->input('observations'),
                'has_notice_period' => $request->input('has_notice_period', 0) == 1,
                'notice_period_days' => $request->input('notice_period_days'),
                'severance_pay' => $request->input('severance_pay'),
                'severance_details' => $request->input('severance_details'),
                'returned_equipment' => $request->input('returned_equipment', 0) == 1,
                'equipment_details' => $request->input('equipment_details'),
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            // Atualizar status do funcionário para inativo
            $worker->update([
                'worker_status' => 0,
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Desligamento registrado com sucesso!',
                'data' => $layoff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar desligamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar desligamento por ID
     */
    public function getLayoff($id): JsonResponse
    {
        $layoff = Layoff::whereNull('deleted_at')
            ->with(['worker'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'layoff_id' => $layoff->layoff_id,
                'worker_id' => $layoff->worker_id,
                'layoff_date' => $layoff->layoff_date?->format('Y-m-d'),
                'layoff_type' => $layoff->layoff_type,
                'reason' => $layoff->reason,
                'observations' => $layoff->observations,
                'has_notice_period' => $layoff->has_notice_period,
                'notice_period_days' => $layoff->notice_period_days,
                'severance_pay' => $layoff->severance_pay,
                'severance_details' => $layoff->severance_details,
                'returned_equipment' => $layoff->returned_equipment,
                'equipment_details' => $layoff->equipment_details,
            ]
        ]);
    }
}
