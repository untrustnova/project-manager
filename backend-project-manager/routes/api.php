<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LeaveController;
use App\Http\Controllers\API\ActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('resend-otp', [AuthController::class, 'resendOTP']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('role:admin,hr');
        Route::post('/', [UserController::class, 'store'])->middleware('role:admin,hr');
        Route::get('export', [UserController::class, 'exportUsers'])->middleware('role:admin,hr');
        Route::post('bulk-update', [UserController::class, 'bulkUpdateUsers'])->middleware('role:admin,hr');
        Route::get('dashboard', [UserController::class, 'getUserDashboard']);
        Route::get('{user}', [UserController::class, 'show']);
        Route::put('{user}', [UserController::class, 'update'])->middleware('role:admin,hr');
        Route::delete('{user}', [UserController::class, 'destroy'])->middleware('role:admin');
        Route::post('{user}/image', [UserController::class, 'updateImage']);
        Route::patch('{user}/status', [UserController::class, 'updateStatus'])->middleware('role:admin,hr');
        Route::get('{user}/statistics', [UserController::class, 'getUserStatistics'])->middleware('role:admin,hr');
    });

    // Project routes
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store'])->middleware('role:admin,hr');
        Route::get('statistics', [ProjectController::class, 'statistics'])->middleware('role:admin,hr');
        Route::get('{project}', [ProjectController::class, 'show']);
        Route::put('{project}', [ProjectController::class, 'update'])->middleware('role:admin,hr');
        Route::delete('{project}', [ProjectController::class, 'destroy'])->middleware('role:admin');
    });

    // Task routes
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store'])->middleware('role:admin,hr');
        Route::get('my-tasks', [TaskController::class, 'myTasks']);
        Route::get('{task}', [TaskController::class, 'show']);
        Route::put('{task}', [TaskController::class, 'update']);
        Route::delete('{task}', [TaskController::class, 'destroy'])->middleware('role:admin,hr');
    });

    // Leave routes  
    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->middleware('role:admin,hr');
        Route::post('/', [LeaveController::class, 'store']);
        Route::get('my-leaves', [LeaveController::class, 'myLeaves']);
        Route::get('{leave}', [LeaveController::class, 'show']);
        Route::patch('{leave}/status', [LeaveController::class, 'updateStatus'])->middleware('role:admin,hr');
        Route::delete('{leave}', [LeaveController::class, 'destroy']);
    });

    // Activity/Attendance routes
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->middleware('role:admin,hr');
        Route::post('/', [ActivityController::class, 'store']);
        Route::post('check-in', [ActivityController::class, 'checkIn']);
        Route::post('check-out', [ActivityController::class, 'checkOut']);
        Route::get('today', [ActivityController::class, 'todayActivity']);
        Route::get('my-activities', [ActivityController::class, 'myActivities']);
        Route::get('work-hours', [ActivityController::class, 'getWorkingHoursReport'])->middleware('role:admin,hr');
        Route::get('{activity}', [ActivityController::class, 'show']);
        Route::put('{activity}', [ActivityController::class, 'update']);
        Route::delete('{activity}', [ActivityController::class, 'destroy'])->middleware('role:admin,hr');
    });
});