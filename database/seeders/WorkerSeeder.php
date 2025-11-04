<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Worker;
use App\Models\Department;
use App\Models\Role;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar departamentos e roles
        $tiDepartment = Department::where('department_name', 'Tecnologia da Informação')->first();
        $rhDepartment = Department::where('department_name', 'Recursos Humanos')->first();
        $vendasDepartment = Department::where('department_name', 'Vendas')->first();
        $financeiroDepartment = Department::where('department_name', 'Financeiro')->first();
        $marketingDepartment = Department::where('department_name', 'Marketing')->first();

        $devSeniorRole = Role::where('role_name', 'Desenvolvedor Senior')->first();
        $analistaRHRole = Role::where('role_name', 'Analista de RH')->first();
        $gerenteVendasRole = Role::where('role_name', 'Gerente de Vendas')->first();
        $contadorRole = Role::where('role_name', 'Contador')->first();
        $designerRole = Role::where('role_name', 'Designer')->first();

        $workers = [
            [
                'worker_name' => 'João Silva',
                'worker_email' => 'joao.silva@empresa.com',
                'worker_document' => '12345678901',
                'worker_rg' => '123456789',
                'worker_birth_date' => '1990-05-15',
                'worker_start_date' => '2020-01-15',
                'worker_status' => 1,
                'worker_salary' => 8500.00,
                'department_id' => $tiDepartment?->department_id,
                'created_by' => 'system',
                'updated_by' => 'system',
                'roles' => [$devSeniorRole?->role_id],
            ],
            [
                'worker_name' => 'Maria Santos',
                'worker_email' => 'maria.santos@empresa.com',
                'worker_document' => '23456789012',
                'worker_rg' => '234567890',
                'worker_birth_date' => '1988-03-20',
                'worker_start_date' => '2019-03-20',
                'worker_status' => 1,
                'worker_salary' => 6500.00,
                'department_id' => $rhDepartment?->department_id,
                'created_by' => 'system',
                'updated_by' => 'system',
                'roles' => [$analistaRHRole?->role_id],
            ],
            [
                'worker_name' => 'Pedro Costa',
                'worker_email' => 'pedro.costa@empresa.com',
                'worker_document' => '34567890123',
                'worker_rg' => '345678901',
                'worker_birth_date' => '1985-07-10',
                'worker_start_date' => '2018-07-10',
                'worker_status' => 1,
                'worker_salary' => 9500.00,
                'department_id' => $vendasDepartment?->department_id,
                'created_by' => 'system',
                'updated_by' => 'system',
                'roles' => [$gerenteVendasRole?->role_id],
            ],
            [
                'worker_name' => 'Ana Oliveira',
                'worker_email' => 'ana.oliveira@empresa.com',
                'worker_document' => '45678901234',
                'worker_rg' => '456789012',
                'worker_birth_date' => '1992-06-05',
                'worker_start_date' => '2021-06-05',
                'worker_status' => 1,
                'worker_salary' => 7200.00,
                'department_id' => $financeiroDepartment?->department_id,
                'created_by' => 'system',
                'updated_by' => 'system',
                'roles' => [$contadorRole?->role_id],
            ],
            [
                'worker_name' => 'Carlos Ferreira',
                'worker_email' => 'carlos.ferreira@empresa.com',
                'worker_document' => '56789012345',
                'worker_rg' => '567890123',
                'worker_birth_date' => '1995-02-14',
                'worker_start_date' => '2022-02-14',
                'worker_status' => 0,
                'worker_salary' => 5500.00,
                'department_id' => $marketingDepartment?->department_id,
                'created_by' => 'system',
                'updated_by' => 'system',
                'roles' => [$designerRole?->role_id],
            ],
            [
                'worker_name' => 'Juliana Almeida',
                'worker_email' => 'juliana.almeida@empresa.com',
                'worker_document' => '67890123456',
                'worker_rg' => '678901234',
                'worker_birth_date' => '1991-09-22',
                'worker_start_date' => '2020-09-22',
                'worker_status' => 1,
                'worker_salary' => 7800.00,
                'department_id' => $tiDepartment?->department_id,
                'created_by' => 'system',
                'updated_by' => 'system',
                'roles' => [$devSeniorRole?->role_id],
            ],
            [
                'worker_name' => 'Roberto Lima',
                'worker_email' => 'roberto.lima@empresa.com',
                'worker_document' => '78901234567',
                'worker_rg' => '789012345',
                'worker_birth_date' => '1987-11-30',
                'worker_start_date' => '2019-11-30',
                'worker_status' => 1,
                'worker_salary' => 6800.00,
                'department_id' => $vendasDepartment?->department_id,
                'created_by' => 'system',
                'updated_by' => 'system',
                'roles' => [$gerenteVendasRole?->role_id],
            ],
        ];

        foreach ($workers as $workerData) {
            $roles = $workerData['roles'] ?? [];
            unset($workerData['roles']);
            
            $worker = Worker::create($workerData);
            
            // Associar roles ao worker
            if (!empty($roles) && $worker) {
                $rolesIds = array_filter($roles);
                if (!empty($rolesIds)) {
                    foreach ($rolesIds as $roleId) {
                        $worker->roles()->attach($roleId, [
                            'worker_role_status' => 1,
                            'created_by' => 'system',
                            'created_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
