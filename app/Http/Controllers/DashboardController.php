<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Activity;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $totalUsers = User::count();
        $totalProjects = Project::count();
        $totalTasks = Task::count();
        $activeLeaves = Leave::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        $recentProjects = Project::with('director')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentTasks = Task::with(['project', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $pendingLeaves = Leave::with('submittedBy')
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalUsers', 'totalProjects', 'totalTasks', 'activeLeaves',
            'recentProjects', 'recentTasks', 'pendingLeaves'
        ));
    }

    public function hrDashboard()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $totalProjects = Project::count();
        $todayActivities = Activity::whereDate('activity_date', today())->count();
        $activeLeaves = Leave::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        $recentActivities = Activity::with('user')
            ->orderBy('activity_date', 'desc')
            ->take(10)
            ->get();

        $recentLeaves = Leave::with('submittedBy')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.hr', compact(
            'totalEmployees', 'totalProjects', 'todayActivities', 'activeLeaves',
            'recentActivities', 'recentLeaves'
        ));
    }

    public function employeeDashboard()
    {
        $user = Auth::user();

        $myTasks = Task::where('assigned_user_id', $user->user_id)->count();
        $completedTasks = Task::where('assigned_user_id', $user->user_id)
            ->where('status', 'completed')
            ->count();

        $myLeaves = Leave::where('submitted_by_user_id', $user->user_id)->count();
        $activeLeaves = Leave::where('submitted_by_user_id', $user->user_id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        $todayActivity = Activity::where('user_id', $user->user_id)
            ->whereDate('activity_date', today())
            ->first();

        $recentTasks = Task::with('project')
            ->where('assigned_user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentActivities = Activity::where('user_id', $user->user_id)
            ->orderBy('activity_date', 'desc')
            ->take(7)
            ->get();

        return view('dashboard.employee', compact(
            'myTasks', 'completedTasks', 'myLeaves', 'activeLeaves',
            'todayActivity', 'recentTasks', 'recentActivities'
        ));
    }
}
