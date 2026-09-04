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
use App\Http\Controllers\LivestockInsuranceController;
use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Farmers
    Route::get('/farmers/pending', [FarmerController::class, 'pendingRegistrations'])->name('farmers.pending');
    Route::resource('farmers', FarmerController::class);
    Route::get('/farmers/{farmer}/print', [FarmerController::class, 'print'])->name('farmers.print');

    // Stocks
    Route::resource('stocks', StockController::class)->only(['index', 'store', 'destroy']);
    Route::post('/stocks/{stock}/release', [StockController::class, 'release'])->name('stocks.release');
    Route::get('/stocks/receipts', [StockController::class, 'receipts'])->name('stocks.receipts.index');
    Route::get('/stocks/receipts/{transaction}/print', [StockController::class, 'printReceipt'])->name('stocks.receipts.print');

    // Requests
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{farmerRequest}', [RequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{farmerRequest}/status', [RequestController::class, 'updateStatus'])->name('requests.status');
    Route::delete('/requests/{farmerRequest}', [RequestController::class, 'destroy'])->name('requests.destroy');

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
    Route::get('/messages/chat', [MessageController::class, 'repInbox'])->name('messages.chat');
    Route::get('/messages/rep-conversation/{user}', [MessageController::class, 'repConversation'])->name('messages.rep-conversation');
    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/unread', [MessageController::class, 'unreadCount'])->name('messages.unread');
    Route::get('/messages/mentions', [MessageController::class, 'mentions'])->name('messages.mentions');
    Route::get('/messages/monitor', [MessageController::class, 'monitorIndex'])->name('messages.monitor');
    Route::get('/messages/monitor/{rep}/{coordinator}', [MessageController::class, 'monitorShow'])->name('messages.monitor.show');
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
    Route::post('/programs/{program}/achievements', [ProgramController::class, 'storeAchievement'])->name('programs.achievements.store');
    Route::delete('/programs/achievements/{achievement}', [ProgramController::class, 'destroyAchievement'])->name('programs.achievements.destroy');

    Route::get('/farmers/create/db', [FarmerController::class, 'createDb'])->name('farmers.create.db');

    Route::post('/programs/{program}/activities', [ProgramController::class, 'storeActivity'])->name('programs.activities.store');
    Route::put('/programs/activities/{activity}', [ProgramController::class, 'updateActivity'])->name('programs.activities.update');
    Route::delete('/programs/activities/{activity}', [ProgramController::class, 'destroyActivity'])->name('programs.activities.destroy');

    Route::get('/programs/{program}/report', [ProgramController::class, 'report'])->name('programs.report');

    // Activity Recipients — the list of farmers who actually received resources
    // during a program activity. Replaces the old Stocks-level "Group Release":
    // releasing to a group now happens inside the coordinator's own program,
    // so the accomplishment record and the released stock stay together.
    Route::post('/programs/activities/{activity}/items', [ProgramController::class, 'updateActivityItems'])->name('programs.activities.items');
    Route::post('/programs/activities/{activity}/recipients', [ProgramController::class, 'storeRecipient'])->name('programs.activities.recipients.store');
    Route::post('/programs/activities/{activity}/recipients/bulk', [ProgramController::class, 'storeRecipientsFromFarmers'])->name('programs.activities.recipients.bulk');
    Route::delete('/programs/recipients/{recipient}', [ProgramController::class, 'destroyRecipient'])->name('programs.recipients.destroy');
    Route::get('/programs/activities/{activity}/recipients/print', [ProgramController::class, 'printRecipients'])->name('programs.activities.recipients.print');

    Route::get('/endorsements', [EndorsementController::class, 'index'])->name('endorsements.index');
    Route::post('/endorsements', [EndorsementController::class, 'store'])->name('endorsements.store');
    Route::post('/endorsements/{endorsement}/approve', [EndorsementController::class, 'approve'])->name('endorsements.approve');
    Route::post('/endorsements/{endorsement}/reject', [EndorsementController::class, 'reject'])->name('endorsements.reject');
    Route::delete('/endorsements/{endorsement}', [EndorsementController::class, 'destroy'])->name('endorsements.destroy');

    Route::post('/farmers/{farmer}/approve', [FarmerController::class, 'approveRegistration'])->name('farmers.approve');
    Route::post('/farmers/{farmer}/reject', [FarmerController::class, 'rejectRegistration'])->name('farmers.reject');

    Route::post('/programs/{program}/dispersal', [ProgramController::class, 'storeDispersalRecord'])->name('programs.dispersal.store');
    Route::put('/programs/dispersal/{record}', [ProgramController::class, 'updateDispersalRecord'])->name('programs.dispersal.update');
    Route::delete('/programs/dispersal/{record}', [ProgramController::class, 'destroyDispersalRecord'])->name('programs.dispersal.destroy');

    Route::get('/forms/livestock-insurance', [LivestockInsuranceController::class, 'index'])->name('livestock-insurance.index');
    Route::get('/forms/livestock-insurance/create', [LivestockInsuranceController::class, 'create'])->name('livestock-insurance.create');
    Route::post('/forms/livestock-insurance', [LivestockInsuranceController::class, 'store'])->name('livestock-insurance.store');
    Route::get('/forms/livestock-insurance/{application}/print', [LivestockInsuranceController::class, 'print'])->name('livestock-insurance.print');

});