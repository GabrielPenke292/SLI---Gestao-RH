<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware já aplicado nas rotas
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $permissions = $user->permissions()->active()->pluck('permission_name')->toArray();
        
        return view('dashboard.index', compact('user', 'permissions'));
    }
}
