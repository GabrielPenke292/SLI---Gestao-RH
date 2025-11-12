<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatesController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeHistoryController;
use App\Http\Controllers\EmployeeUploadController;
use App\Http\Controllers\EmployeeCalendarController;
use App\Http\Controllers\ExamsController;
use App\Http\Controllers\LayoffsController;
use App\Http\Controllers\MovementsController;
use App\Http\Controllers\NegotiationsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacanciesController;
use App\Http\Controllers\SelectionsController;

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

    // Selection routes
    Route::get('/selections', [SelectionsController::class, 'index'])->name('selections.index');
    Route::get('/selections/awaiting', [SelectionsController::class, 'awaiting'])->name('selections.awaiting');
    Route::get('/selections/in-progress', [SelectionsController::class, 'inProgress'])->name('selections.in-progress');
    Route::get('/selections/finished', [SelectionsController::class, 'finished'])->name('selections.finished');
    Route::get('/selections/create', [SelectionsController::class, 'create'])->name('selections.create');
    Route::post('/selections', [SelectionsController::class, 'store'])->name('selections.store');
    Route::get('/selections/{id}/edit', [SelectionsController::class, 'edit'])->name('selections.edit');
    Route::put('/selections/{id}', [SelectionsController::class, 'update'])->name('selections.update');
    Route::post('/selections/{id}/approve', [SelectionsController::class, 'approve'])->name('selections.approve');
    Route::post('/selections/{id}/reject', [SelectionsController::class, 'reject'])->name('selections.reject');
    Route::delete('/selections/{id}', [SelectionsController::class, 'destroy'])->name('selections.destroy');
    Route::get('/selections/awaiting/data', [SelectionsController::class, 'getAwaitingApprovalData'])->name('selections.awaiting.data');
    Route::get('/selections/in-progress/data', [SelectionsController::class, 'getInProgressData'])->name('selections.in-progress.data');
    Route::get('/selections/finished/data', [SelectionsController::class, 'getFinishedData'])->name('selections.finished.data');
    Route::get('/selections/vacancy/{id}/dates', [SelectionsController::class, 'getVacancyDates'])->name('selections.vacancy.dates');
    Route::get('/selections/candidates/search', [SelectionsController::class, 'searchCandidates'])->name('selections.candidates.search');
    Route::post('/selections/{id}/attach-candidate', [SelectionsController::class, 'attachCandidate'])->name('selections.attach.candidate');
    Route::post('/selections/{id}/move-candidate', [SelectionsController::class, 'moveCandidate'])->name('selections.move.candidate');
    Route::post('/selections/{id}/detach-candidate', [SelectionsController::class, 'detachCandidate'])->name('selections.detach.candidate');
    Route::post('/selections/{id}/add-note', [SelectionsController::class, 'addCandidateNote'])->name('selections.add.note');
    Route::get('/selections/{id}/candidates', [SelectionsController::class, 'getProcessCandidates'])->name('selections.candidates');
    Route::get('/selections/{id}/check-step-candidates', [SelectionsController::class, 'checkStepHasCandidates'])->name('selections.check.step.candidates');
    Route::get('/selections/{id}/step-interactions', [SelectionsController::class, 'getStepInteractions'])->name('selections.step.interactions');
    Route::post('/selections/{id}/step-interactions', [SelectionsController::class, 'storeStepInteraction'])->name('selections.step.interactions.store');
    Route::put('/selections/{id}/step-interactions/{interactionId}', [SelectionsController::class, 'updateStepInteraction'])->name('selections.step.interactions.update');
    Route::delete('/selections/{id}/step-interactions/{interactionId}', [SelectionsController::class, 'deleteStepInteraction'])->name('selections.step.interactions.delete');

    // Candidate routes
    Route::get('/candidates', [CandidatesController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/data', [CandidatesController::class, 'getData'])->name('candidates.data');
    Route::get('/candidates/create', [CandidatesController::class, 'create'])->name('candidates.create');
    Route::post('/candidates', [CandidatesController::class, 'store'])->name('candidates.store');
    Route::get('/candidates/{id}', [CandidatesController::class, 'show'])->name('candidates.show');
    Route::get('/candidates/{id}/timeline', [CandidatesController::class, 'getProcessTimeline'])->name('candidates.timeline');
    Route::get('/candidates/{id}/edit', [CandidatesController::class, 'edit'])->name('candidates.edit');
    Route::put('/candidates/{id}', [CandidatesController::class, 'update'])->name('candidates.update');
    Route::delete('/candidates/{id}', [CandidatesController::class, 'destroy'])->name('candidates.destroy');

    // Negotiation routes
    Route::get('/negotiations', [NegotiationsController::class, 'index'])->name('negotiations.index');

    // Exam routes
    Route::get('/exams', [ExamsController::class, 'index'])->name('exams.index');

    // Layoffs routes
    Route::get('/layoffs', [LayoffsController::class, 'index'])->name('layoffs.index');

    // Movements routes
    Route::get('/movements', [MovementsController::class, 'index'])->name('movements.index');

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