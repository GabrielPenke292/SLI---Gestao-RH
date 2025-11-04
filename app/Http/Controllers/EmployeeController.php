<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Worker;

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
