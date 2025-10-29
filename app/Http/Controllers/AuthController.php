<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Permission;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuário pelo email usando o novo campo
        $user = User::where('user_email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->user_password)) {
            // Login bem-sucedido
            Auth::login($user, $request->has('remember'));
            
            // Carregar permissões do usuário
            $permissions = $user->permissions()->active()->pluck('permission_name')->toArray();
            // cria a sessão do usuario
            session(['user' => [
                'id' => $user->users_id,
                'name' => $user->user_name,
                'email' => $user->user_email,
                'permissions' => $permissions
            ]]);
            return response()->json([
                'success' => 'Login realizado com sucesso',
                'redirect' => route('dashboard'),
            ], 200);
        }

        return response()->json([
            'error' => 'Credenciais inválidas'
        ], 401);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    /**
     * Check if user has permission
     */
    public function hasAnyPermission(Request $request, $permissions)
    {
        $user = Auth::user();
        $hasPermission = $user->hasAnyPermission($permissions);
        return response()->json(['has_permission' => $hasPermission]);
    }

    /**
     * Check if user has all permissions
     */
    public function hasAllPermissions(Request $request, $permissions)
    {
        $user = Auth::user();
        $hasPermission = $user->hasAllPermissions($permissions);
        return response()->json(['has_permission' => $hasPermission]);
    }
}