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

        $recentActivities = Activity::with('user')
            ->orderBy('activity_date', 'desc')
            ->take(7)
            ->get();

        $activityChartData = Activity::selectRaw('MONTH(activity_date) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
        $activityChartData = array_pad($activityChartData, 12, 0);

        return view('dashboard', compact(
            'totalUsers', 'totalProjects', 'totalTasks', 'activeLeaves',
            'recentProjects', 'recentTasks', 'pendingLeaves', 'recentActivities', 'activityChartData'
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

        $recentProjects = Project::orderBy('created_at', 'desc')->take(5)->get();
        $recentTasks = Task::with(['project', 'assignedUser'])->orderBy('created_at', 'desc')->take(5)->get();
        $activityChartData = Activity::selectRaw('MONTH(activity_date) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
        $activityChartData = array_pad($activityChartData, 12, 0);

        return view('dashboard', compact(
            'totalEmployees', 'totalProjects', 'todayActivities', 'activeLeaves',
            'recentActivities', 'recentLeaves', 'recentProjects', 'recentTasks', 'activityChartData'
        ));
    }

    public function employeeDashboard()
    {
        $user = Auth::user();

        $myTasks = $user->assignedTasks()->count();
        $completedTasks = $user->assignedTasks()->where('status', 'completed')->count();
        $myLeaves = $user->leaves()->count();
        $activeLeaves = $user->leaves()->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        $todayActivity = $user->activities()->whereDate('activity_date', today())->first();

        $recentTasks = $user->assignedTasks()->with('project')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $recentActivities = $user->activities()
            ->orderBy('activity_date', 'desc')
            ->take(7)
            ->get();
        $recentProjects = Project::where('project_director', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $activityChartData = $user->activities()
            ->selectRaw('MONTH(activity_date) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
        $activityChartData = array_pad($activityChartData, 12, 0);

        return view('dashboard', compact(
            'myTasks', 'completedTasks', 'myLeaves', 'activeLeaves',
            'todayActivity', 'recentTasks', 'recentActivities', 'recentProjects', 'activityChartData'
        ));
    }
}
