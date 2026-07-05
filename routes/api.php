<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\EmergencyRequestController;
use App\Http\Controllers\Api\ResponderController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\FeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API endpoints for the ResQ Emergency Response System
| Mobile app and external integrations use these routes
|
*/

// Public Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
    Route::post('/password/email', [AuthController::class, 'sendResetLink']);
});

Route::get('/emergency-types', [EmergencyRequestController::class, 'getTypes']);
Route::get('/emergency-agencies', [EmergencyRequestController::class, 'getAgencies']);
Route::get('/announcements/public', [AnnouncementController::class, 'publicIndex']);
Route::get('/facilities/nearby', [FacilityController::class, 'nearby']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/password/update', [AuthController::class, 'updatePassword']);
    });

    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/phone/verify', [ProfileController::class, 'verifyPhone']);
    });

    // Emergency Contacts
    Route::prefix('emergency-contacts')->group(function () {
        Route::get('/', [ProfileController::class, 'getContacts']);
        Route::post('/', [ProfileController::class, 'addContact']);
        Route::put('/{id}', [ProfileController::class, 'updateContact']);
        Route::delete('/{id}', [ProfileController::class, 'deleteContact']);
    });

    // Medical Info
    Route::prefix('medical-info')->group(function () {
        Route::get('/', [ProfileController::class, 'getMedicalInfo']);
        Route::put('/', [ProfileController::class, 'updateMedicalInfo']);
    });

    // Emergency Requests
    Route::prefix('emergency')->group(function () {
        Route::post('/request', [EmergencyRequestController::class, 'createRequest']);
        Route::post('/sos', [EmergencyRequestController::class, 'sosAlert']);
        Route::get('/requests', [EmergencyRequestController::class, 'index']);
        Route::get('/requests/{id}', [EmergencyRequestController::class, 'show']);
        Route::put('/requests/{id}/status', [EmergencyRequestController::class, 'updateStatus']);
        Route::put('/requests/{id}/location', [EmergencyRequestController::class, 'updateLocation']);
        Route::get('/requests/{id}/track', [EmergencyRequestController::class, 'trackRequest']);
        Route::post('/requests/{id}/cancel', [EmergencyRequestController::class, 'cancelRequest']);
    });

    // Responders
    Route::prefix('responder')->group(function () {
        Route::get('/status', [ResponderController::class, 'getStatus']);
        Route::put('/status', [ResponderController::class, 'updateStatus']);
        Route::get('/assignments', [ResponderController::class, 'getAssignments']);
        Route::post('/assignments/{id}/accept', [ResponderController::class, 'acceptAssignment']);
        Route::post('/assignments/{id}/reject', [ResponderController::class, 'rejectAssignment']);
        Route::put('/assignments/{id}/status', [ResponderController::class, 'updateAssignmentStatus']);
        Route::post('/assignments/{id}/location', [ResponderController::class, 'updateLocation']);
        Route::get('/facilities', [ResponderController::class, 'getFacilities']);
    });

    // Messaging
    Route::prefix('messages')->group(function () {
        Route::get('/{request_id}', [MessageController::class, 'getMessages']);
        Route::post('/', [MessageController::class, 'sendMessage']);
        Route::get('/conversations', [MessageController::class, 'getConversations']);
    });

    // Media Upload
    Route::prefix('media')->group(function () {
        Route::post('/upload', [MediaController::class, 'upload']);
        Route::get('/{id}', [MediaController::class, 'show']);
        Route::delete('/{id}', [MediaController::class, 'delete']);
    });

    // Facilities
    Route::prefix('facilities')->group(function () {
        Route::get('/', [FacilityController::class, 'index']);
        Route::get('/{id}', [FacilityController::class, 'show']);
    });

    // Feedback
    Route::prefix('feedback')->group(function () {
        Route::post('/{request_id}', [FeedbackController::class, 'submitFeedback']);
        Route::get('/{request_id}', [FeedbackController::class, 'getFeedback']);
    });

    // User Announcements
    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index']);
        Route::put('/{id}/read', [AnnouncementController::class, 'markAsRead']);
    });

    // Admin Routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/requests', [AdminController::class, 'getRequests']);
        Route::put('/requests/{id}', [AdminController::class, 'updateRequest']);
        Route::put('/requests/{id}/assign', [AdminController::class, 'assignResponder']);
        Route::put('/requests/{id}/verify', [AdminController::class, 'verifyRequest']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/responders', [AdminController::class, 'getResponders']);
        Route::put('/responders/{id}', [AdminController::class, 'updateResponder']);
        Route::post('/responders', [AdminController::class, 'createResponder']);
        Route::get('/reports', [AdminController::class, 'getReports']);
        Route::get('/reports/export', [AdminController::class, 'exportReports']);
        Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
        Route::get('/settings', [AdminController::class, 'getSettings']);
        Route::put('/settings', [AdminController::class, 'updateSettings']);
        Route::post('/emergency-types', [AdminController::class, 'createEmergencyType']);
        Route::put('/emergency-types/{id}', [AdminController::class, 'updateEmergencyType']);
        Route::delete('/emergency-types/{id}', [AdminController::class, 'deleteEmergencyType']);
    });

    // Announcement Management (Admin)
    Route::prefix('admin/announcements')->middleware('admin')->group(function () {
        Route::get('/', [AnnouncementController::class, 'adminIndex']);
        Route::post('/', [AnnouncementController::class, 'store']);
        Route::put('/{id}', [AnnouncementController::class, 'update']);
        Route::delete('/{id}', [AnnouncementController::class, 'destroy']);
    });
});