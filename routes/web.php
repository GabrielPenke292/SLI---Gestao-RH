<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeHistoryController;
use App\Http\Controllers\EmployeeUploadController;
use App\Http\Controllers\EmployeeCalendarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacanciesController;

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


    // User routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/data', [UserController::class, 'getData'])->name('users.data');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    // Vacancy routes
    Route::get('/vacancies', [VacanciesController::class, 'index'])->name('vacancies.index');
    Route::get('/vacancies/open', [VacanciesController::class, 'open'])->name('vacancies.open');
    Route::get('/vacancies/closed', [VacanciesController::class, 'closed'])->name('vacancies.closed');
    Route::get('/vacancies/data', [VacanciesController::class, 'getData'])->name('vacancies.data');
    Route::get('/vacancies/closed/data', [VacanciesController::class, 'getClosedData'])->name('vacancies.closed.data');
    Route::get('/vacancies/create', [VacanciesController::class, 'create'])->name('vacancies.create');
    Route::post('/vacancies', [VacanciesController::class, 'store'])->name('vacancies.store');
    Route::get('/vacancies/{id}/edit', [VacanciesController::class, 'edit'])->name('vacancies.edit');
    Route::put('/vacancies/{id}', [VacanciesController::class, 'update'])->name('vacancies.update');
    Route::delete('/vacancies/{id}', [VacanciesController::class, 'destroy'])->name('vacancies.destroy');
    
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
    Route::get('/employees/calendar', [EmployeeCalendarController::class, 'index'])->name('employees.calendar');
    Route::get('/employees/calendar/events', [EmployeeCalendarController::class, 'getEvents'])->name('employees.calendar.events');
    Route::post('/employees/calendar/events', [EmployeeCalendarController::class, 'store'])->name('employees.calendar.events.store');
    Route::put('/employees/calendar/events/{id}', [EmployeeCalendarController::class, 'update'])->name('employees.calendar.events.update');
    Route::delete('/employees/calendar/events/{id}', [EmployeeCalendarController::class, 'destroy'])->name('employees.calendar.events.destroy');
    Route::get('/employees/{id}', [EmployeeController::class, 'view'])->name('employees.view');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});