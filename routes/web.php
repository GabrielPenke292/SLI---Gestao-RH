<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeHistoryController;
use App\Http\Controllers\EmployeeUploadController;

Route::get('/', [HomeController::class, 'index']);

// Login routes (públicas)
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Permission routes
    Route::get('/permissions/check/{permissions}', [AuthController::class, 'hasAnyPermission'])->name('permissions.check');
    Route::get('/permissions/check-all/{permissions}', [AuthController::class, 'hasAllPermissions'])->name('permissions.check.all');

    // Employee routes
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/board', [EmployeeController::class, 'board'])->name('employees.board');
    Route::get('/employees/data', [EmployeeController::class, 'getData'])->name('employees.data');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/upload', [EmployeeUploadController::class, 'index'])->name('employees.upload');
    Route::post('/employees/upload/process', [EmployeeUploadController::class, 'processUpload'])->name('employees.upload.process');
    Route::post('/employees/upload/confirm', [EmployeeUploadController::class, 'confirmStore'])->name('employees.upload.confirm');
    Route::get('/employees/history', [EmployeeHistoryController::class, 'index'])->name('employees.history');
});