<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'role_name' => 'Desenvolvedor Senior',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Desenvolvedor Pleno',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Desenvolvedor Junior',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Analista de RH',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Gerente de Vendas',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Vendedor',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Contador',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Analista Financeiro',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Designer',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'role_name' => 'Coordenador de Marketing',
                'role_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
