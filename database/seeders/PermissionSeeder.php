<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar permissões básicas do sistema
        $permissions = [
            [
                'permission_name' => 'admin',
                'permission_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'permission_name' => 'user_management',
                'permission_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'permission_name' => 'permission_management',
                'permission_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'permission_name' => 'employee_view',
                'permission_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'permission_name' => 'employee_create',
                'permission_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'permission_name' => 'employee_edit',
                'permission_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'permission_name' => 'employee_delete',
                'permission_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
            [
                'permission_name' => 'reports_view',
                'permission_status' => 1,
                'created_by' => 'system',
                'updated_by' => 'system',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Criar usuário administrador padrão
        $admin = User::create([
            'user_name' => 'Administrador',
            'user_email' => 'admin@sli.com',
            'user_password' => Hash::make('admin123'),
        ]);

        // Atribuir todas as permissões ao administrador
        $admin->permissions()->attach(Permission::pluck('permissio_id'));

        // Criar usuário de teste
        $user = User::create([
            'user_name' => 'Usuário Teste',
            'user_email' => 'user@sli.com',
            'user_password' => Hash::make('user123'),
        ]);

        // Atribuir permissões básicas ao usuário
        $basicPermissions = Permission::whereIn('permission_name', [
            'employee_view',
            'reports_view'
        ])->pluck('permissio_id');
        
        $user->permissions()->attach($basicPermissions);
    }
}
