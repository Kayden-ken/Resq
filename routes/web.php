<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ResponderController as AdminResponderController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\UserPageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ======================
// ADMIN AUTH
// ======================
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// ======================
// ADMIN PROTECTED
// ======================
Route::prefix('admin')->middleware('admin.auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/responders', [AdminResponderController::class, 'index'])->name('admin.responders');
    Route::get('/responders/create', [AdminResponderController::class, 'create'])->name('admin.responders.create');
    Route::post('/responders', [AdminResponderController::class, 'store'])->name('admin.responders.store');
    Route::get('/responders/{id}', [AdminResponderController::class, 'show'])->name('admin.responders.show');
    Route::get('/responders/{id}/edit', [AdminResponderController::class, 'edit'])->name('admin.responders.edit');
    Route::put('/responders/{id}', [AdminResponderController::class, 'update'])->name('admin.responders.update');
    Route::delete('/responders/{id}', [AdminResponderController::class, 'destroy'])->name('admin.responders.destroy');

    Route::get('/requests', [RequestController::class, 'index'])->name('admin.requests');
    Route::get('/requests/{id}', [RequestController::class, 'show'])->name('admin.requests.show');
    Route::put('/requests/{id}', [RequestController::class, 'update'])->name('admin.requests.update');
    Route::put('/requests/{id}/assign', [RequestController::class, 'assignResponder'])->name('admin.requests.assign');
    Route::put('/requests/{id}/status', [RequestController::class, 'updateStatus'])->name('admin.requests.status');
    Route::put('/requests/{id}/verify', [RequestController::class, 'verifyRequest'])->name('admin.requests.verify');

    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/response-time', [ReportController::class, 'responseTime'])->name('admin.reports.response-time');
    Route::get('/reports/incidents', [ReportController::class, 'incidents'])->name('admin.reports.incidents');
    Route::get('/reports/responders', [ReportController::class, 'responderPerformance'])->name('admin.reports.responders');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');

    Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('admin.announcements');
    Route::get('/announcements/create', [AdminAnnouncementController::class, 'create'])->name('admin.announcements.create');
    Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('admin.announcements.store');
    Route::get('/announcements/{id}/edit', [AdminAnnouncementController::class, 'edit'])->name('admin.announcements.edit');
    Route::put('/announcements/{id}', [AdminAnnouncementController::class, 'update'])->name('admin.announcements.update');
    Route::delete('/announcements/{id}', [AdminAnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    Route::get('/audit-logs', [DashboardController::class, 'auditLogs'])->name('admin.audit-logs');
});

// ======================
// AUTH (PUBLIC)
// ======================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ======================
// HOME
// ======================
Route::get('/', [UserDashboardController::class, 'index'])->name('home');

// ======================
// USER ROUTES
// ======================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [UserPageController::class, 'profile'])->name('user.profile');

    Route::get('/requests', [UserPageController::class, 'emergencyRequests'])->name('user.requests');
    Route::get('/requests/new', [UserPageController::class, 'emergencyForm'])->name('user.requests.new');
    Route::post('/requests/new', [UserPageController::class, 'storeEmergencyRequest'])->name('user.requests.store');

    Route::get('/contacts', [UserPageController::class, 'contacts'])->name('user.contacts');
    Route::post('/contacts', [UserPageController::class, 'addContact']);

    Route::get('/facilities', [UserPageController::class, 'facilities'])->name('user.facilities');
    Route::get('/announcements', [UserPageController::class, 'announcements'])->name('user.announcements');
});

// ======================
// DEBUG ROUTES (REMOVE AFTER FIX)
// ======================
use App\Models\User;

Route::get('/debug-users', function () {
    if (app()->environment('production')) {
        abort(403);
    }

    return \App\Models\User::select('id', 'email', 'user_type')->get();
});

Route::get('/debug-db', function () {
    if (app()->environment('production')) {
        abort(403);
    }

    return [
        'connection' => config('database.default'),
        'database' => config('database.connections.pgsql.database'),
        'host' => config('database.connections.pgsql.host'),
    ];
});