<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (FacadesAuth::guard('web')->attempt($request->only('email', 'password'))) {
            return response()->json(['success' => 'Login realizado com sucesso', 'redirect' => route('dashboard')], 200);
        }

        return response()->json(['error' => 'Credenciais inválidas', 'redirect' => route('login')], 401);
    }
}