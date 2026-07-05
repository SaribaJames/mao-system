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

    Route::get('/farmers/{farmer}/print', [FarmerController::class, 'print'])->name('farmers.print');
});