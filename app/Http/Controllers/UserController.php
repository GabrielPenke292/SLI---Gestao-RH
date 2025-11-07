<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:75',
            'user_email' => 'required|email|max:45|unique:users,user_email',
            'user_password' => 'required|string|min:6|confirmed',
        ], [
            'user_name.required' => 'O nome do usuário é obrigatório.',
            'user_email.required' => 'O email é obrigatório.',
            'user_email.email' => 'O email deve ser válido.',
            'user_email.unique' => 'Este email já está cadastrado.',
            'user_password.required' => 'A senha é obrigatória.',
            'user_password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'user_password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        try {
            // O cast 'hashed' no modelo já faz o hash automaticamente
            User::create([
                'user_name' => $validated['user_name'],
                'user_email' => $validated['user_email'],
                'user_password' => $validated['user_password'],
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Usuário cadastrado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Retorna os dados dos usuários para o DataTables
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
            'users_id',
            'user_name',
            'user_email',
            'created_at',
        ];
        
        // Query base
        $query = User::query();
        
        // Aplicar busca
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%");
            });
        }
        
        // Contar total de registros antes da paginação
        $totalRecords = User::count();
        
        // Clonar query para contar filtrados
        $countQuery = clone $query;
        $filteredCount = $countQuery->count();
        
        // Aplicar ordenação
        $orderByColumn = $columns[$orderColumn] ?? 'users_id';
        $query->orderBy($orderByColumn, $orderDir);
        
        // Aplicar paginação
        $users = $query->skip($start)
            ->take($length)
            ->get();
        
        // Formatar dados para o DataTables
        $data = $users->map(function($user) {
            return [
                'id' => $user->users_id,
                'name' => $user->user_name,
                'email' => $user->user_email,
                'created_at' => $user->created_at?->format('Y-m-d H:i:s') ?? null,
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
