<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
        // TODO: Substituir por dados reais do model Employee quando criado
        // Por enquanto, retornamos dados mockados para demonstração
        
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        
        // Dados mockados - substituir quando tiver o model Employee
        $employees = [
            [
                'id' => 1,
                'name' => 'João Silva',
                'email' => 'joao.silva@empresa.com',
                'position' => 'Desenvolvedor Senior',
                'department' => 'TI',
                'hire_date' => '2020-01-15',
                'status' => 'active'
            ],
            [
                'id' => 2,
                'name' => 'Maria Santos',
                'email' => 'maria.santos@empresa.com',
                'position' => 'Analista de RH',
                'department' => 'Recursos Humanos',
                'hire_date' => '2019-03-20',
                'status' => 'active'
            ],
            [
                'id' => 3,
                'name' => 'Pedro Costa',
                'email' => 'pedro.costa@empresa.com',
                'position' => 'Gerente de Vendas',
                'department' => 'Vendas',
                'hire_date' => '2018-07-10',
                'status' => 'active'
            ],
            [
                'id' => 4,
                'name' => 'Ana Oliveira',
                'email' => 'ana.oliveira@empresa.com',
                'position' => 'Contadora',
                'department' => 'Financeiro',
                'hire_date' => '2021-06-05',
                'status' => 'active'
            ],
            [
                'id' => 5,
                'name' => 'Carlos Ferreira',
                'email' => 'carlos.ferreira@empresa.com',
                'position' => 'Designer',
                'department' => 'Marketing',
                'hire_date' => '2022-02-14',
                'status' => 'inactive'
            ],
        ];

        // Filtrar por busca se houver
        if (!empty($search)) {
            $employees = array_filter($employees, function($employee) use ($search) {
                return stripos($employee['name'], $search) !== false ||
                       stripos($employee['email'], $search) !== false ||
                       stripos($employee['position'], $search) !== false ||
                       stripos($employee['department'], $search) !== false;
            });
        }

        $totalRecords = count($employees);
        $employees = array_values($employees); // Reindexar array
        
        // Paginação
        $employees = array_slice($employees, $start, $length);

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $employees
        ]);
    }
}
