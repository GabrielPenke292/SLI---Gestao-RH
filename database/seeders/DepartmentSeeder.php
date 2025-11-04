<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'department_name' => 'Tecnologia da Informação',
                'department_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'department_name' => 'Recursos Humanos',
                'department_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'department_name' => 'Vendas',
                'department_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'department_name' => 'Financeiro',
                'department_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'department_name' => 'Marketing',
                'department_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'department_name' => 'Operações',
                'department_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
