<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\{User, Project, Task, Activity, Leave};

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define custom route model binding for models with custom primary keys
        Route::bind('user', function ($value) {
            return User::where('user_id', $value)->firstOrFail();
        });

        Route::bind('project', function ($value) {
            return Project::where('project_id', $value)->firstOrFail();
        });

        Route::bind('task', function ($value) {
            return Task::where('task_id', $value)->firstOrFail();
        });

        Route::bind('activity', function ($value) {
            return Activity::where('activity_id', $value)->firstOrFail();
        });

        Route::bind('leave', function ($value) {
            return Leave::where('leave_id', $value)->firstOrFail();
        });
    }
}
