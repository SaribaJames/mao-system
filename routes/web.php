<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\EndorsementController;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Farmers
    Route::resource('farmers', FarmerController::class);
    Route::get('/farmers/{farmer}/print', [FarmerController::class, 'print'])->name('farmers.print');

    // Stocks
    Route::resource('stocks', StockController::class)->only(['index', 'store', 'destroy']);
    Route::post('/stocks/{stock}/release', [StockController::class, 'release'])->name('stocks.release');

    // Requests
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{farmerRequest}', [RequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{farmerRequest}/status', [RequestController::class, 'updateStatus'])->name('requests.status');

    // Users
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/approve', [UserController::class, 'approvePending'])->name('users.approve');

    // Activities
    Route::resource('activities', ActivityController::class)->only(['index', 'store', 'destroy']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');

    // Service Records
    Route::resource('service-records', ServiceRecordController::class)->only(['index', 'create', 'store', 'show', 'update']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generateForm'])->name('reports.generate');
    Route::post('/reports/generate', [ReportController::class, 'generatePDF'])->name('reports.generate.pdf');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('reports.export.pdf');
    Route::get('/reports/export/farmers', [ReportController::class, 'exportFarmersPDF'])->name('reports.export.farmers');
    Route::get('/reports/export/requests', [ReportController::class, 'exportRequestsPDF'])->name('reports.export.requests');
    Route::get('/reports/export/services', [ReportController::class, 'exportServicesPDF'])->name('reports.export.services');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/chat', [MessageController::class, 'chat'])->name('messages.chat');
    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/unread', [MessageController::class, 'unreadCount'])->name('messages.unread');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])->name('messages.conversation');

    // Forms & Documents
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/pcic-adss', [FormController::class, 'pcicAdss'])->name('forms.pcic-adss');
    Route::post('/forms/pcic-adss', [FormController::class, 'pcicAdssPDF'])->name('forms.pcic-adss.pdf');

    // Programs
    Route::resource('programs', ProgramController::class)->only(['index', 'show']);
    Route::post('/programs/{program}/unlock', [ProgramController::class, 'unlock'])->name('programs.unlock');
    Route::post('/programs/{program}/enroll', [ProgramController::class, 'enroll'])->name('programs.enroll');
    Route::post('/program-enrollments/{enrollment}/status', [ProgramController::class, 'updateEnrollment'])->name('program-enrollments.status');

    Route::get('/farmers/create/db', [FarmerController::class, 'createDb'])->name('farmers.create.db');

    Route::post('/programs/{program}/activities', [ProgramController::class, 'storeActivity'])->name('programs.activities.store');
    Route::put('/programs/activities/{activity}', [ProgramController::class, 'updateActivity'])->name('programs.activities.update');
    Route::delete('/programs/activities/{activity}', [ProgramController::class, 'destroyActivity'])->name('programs.activities.destroy');

    Route::get('/programs/{program}/report', [ProgramController::class, 'report'])->name('programs.report');


    Route::get('/endorsements', [EndorsementController::class, 'index'])->name('endorsements.index');
    Route::post('/endorsements', [EndorsementController::class, 'store'])->name('endorsements.store');
    Route::post('/endorsements/{endorsement}/approve', [EndorsementController::class, 'approve'])->name('endorsements.approve');
    Route::post('/endorsements/{endorsement}/reject', [EndorsementController::class, 'reject'])->name('endorsements.reject');

    Route::post('/farmers/{farmer}/approve', [FarmerController::class, 'approveRegistration'])->name('farmers.approve');
    Route::post('/farmers/{farmer}/reject', [FarmerController::class, 'rejectRegistration'])->name('farmers.reject');

});