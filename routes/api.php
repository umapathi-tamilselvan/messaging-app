<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify', [AuthController::class, 'verifyOtp']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User Profile Management
    Route::get('user', [UserController::class, 'show']);
    Route::put('user/profile', [UserController::class, 'updateProfile']);
    Route::post('user/avatar', [UserController::class, 'uploadAvatar']);
    Route::put('user/status', [UserController::class, 'updateStatus']);
    
    // Users
    Route::get('users/search', [UserController::class, 'search']);
    
    // Conversations
    Route::apiResource('conversations', ConversationController::class);
    Route::get('conversations/{conversation}/messages', [MessageController::class, 'index']);
    
    // Messages
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store']);
    Route::put('messages/{message}/seen', [MessageController::class, 'markAsSeen']);
    Route::delete('messages/{message}', [MessageController::class, 'destroy']);
    
    // Upload
    Route::post('upload/sign', [UploadController::class, 'sign']);
});

