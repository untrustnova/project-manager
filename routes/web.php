<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    UserController,
    ProjectController,
    TaskController,
    ActivityController,
    LeaveController
};


Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // Register routes
    Route::get('register', [AuthController::class, 'showRegisterForm'])
        ->name('register');
    Route::post('register', [AuthController::class, 'register']);

    // Forgot password routes
    Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('password.email');

    // Reset password routes
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.store');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Logout route
    Route::post('logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Change password routes (for authenticated users)
    Route::get('change-password', [AuthController::class, 'showChangePasswordForm'])
        ->name('password.change');
    Route::post('change-password', [AuthController::class, 'changePassword'])
        ->name('password.update');

    // Dashboard routes based on role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'hr' => redirect()->route('hr.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default => redirect()->route('home')
        };
    })->name('dashboard');

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('checkrole:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // User management
        Route::resource('users', UserController::class);

        // Project management
        Route::resource('projects', ProjectController::class);

        // Task management
        Route::resource('tasks', TaskController::class);
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

        // Activity management
        Route::resource('activities', ActivityController::class);

        // Leave management
        Route::resource('leaves', LeaveController::class);
    });

    // HR routes
    Route::prefix('hr')->name('hr.')->middleware('checkrole:hr')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'hrDashboard'])->name('dashboard');

        // Employee management
        Route::resource('employees', UserController::class)->except(['create', 'store']);

        // Project management
        Route::resource('projects', ProjectController::class);

        // Task management
        Route::resource('tasks', TaskController::class);
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

        // Activity management
        Route::resource('activities', ActivityController::class);

        // Leave management
        Route::resource('leaves', LeaveController::class);
    });

    // Employee routes
    Route::prefix('employee')->name('employee.')->middleware('checkrole:employee')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'employeeDashboard'])->name('dashboard');

        // Profile management
        Route::get('/profile', function() {
            $user = Auth::user();
            return view('users.show', compact('user'));
        })->name('profile');
        Route::get('/profile/edit', function() {
            $user = Auth::user();
            return view('users.edit', compact('user'));
        })->name('profile.edit');
        Route::patch('/profile', function(Request $request) {
            $user = Auth::user();
            $userController = new UserController();
            return $userController->update($request, $user);
        })->name('profile.update');

        // Task management
        Route::get('/tasks', [TaskController::class, 'myTasks'])->name('tasks.index');
        Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

        // Attendance management
        Route::get('/activities', function() {
            $user = Auth::user();
            $activities = $user->activities()->orderBy('activity_date', 'desc')->paginate(10);
            return view('activities.index', compact('activities'));
        })->name('activities.index');
        Route::post('/check-in', [ActivityController::class, 'checkIn'])->name('activities.check-in');
        Route::post('/check-out', [ActivityController::class, 'checkOut'])->name('activities.check-out');

        // Leave management
        Route::get('/leaves', [LeaveController::class, 'myLeaves'])->name('leaves.index');
        Route::get('/leaves/apply', [LeaveController::class, 'apply'])->name('leaves.apply');
        Route::post('/leaves/apply', [LeaveController::class, 'submitApplication'])->name('leaves.submit');
        Route::get('/leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    });

    // Common routes for all authenticated users
    Route::middleware('checkrole:admin,hr,employee')->group(function () {
        // View projects (read-only for employees)
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    });
});
