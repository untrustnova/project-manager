<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Activity;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Helper untuk eager loading umum pada User
    private function eagerLoadUserRelations($query)
    {
        return $query->with([
            'activities' => function($q) {
                $q->latest('activity_date')->latest('check_in')->take(1);
            },
            'activities.relatedTask',
            'activities.relatedProject'
        ]);
    }

    public function adminDashboard()
    {
        // Ambil semua user dengan eager loading yang dibutuhkan
        $allUsers = $this->eagerLoadUserRelations(User::query())->get();

        // Filter user berdasarkan status untuk masing-masing tab
        $readyUsers = $allUsers->where('status', 'Ready');
        $standbyUsers = $allUsers->where('status', 'Stand by');
        $notReadyUsers = $allUsers->where('status', 'Not ready');
        // 'Complete' dan 'Absent' di gambar adalah status user, bukan tasks.
        $completeUsers = $allUsers->where('status', 'Complete');
        $absentUsers = $allUsers->where('status', 'Absent');

        // Untuk Tasks Card (menggunakan $tasks dari admin dashboard, bukan user-specific)
        $tasks = Task::all(); // Atau bisa difilter jika hanya ingin yang belum complete
        // Di Blade, Anda menggunakan $userTasks, jadi kita compact $tasks sebagai $userTasks
        $userTasks = $tasks;

        // Untuk Project Card (menggunakan $projects dari admin dashboard, bukan user-specific)
        $projects = Project::all();
        // Di Blade, Anda menggunakan $userProjects, jadi kita compact $projects sebagai $userProjects
        $userProjects = $projects;


        $leaves = Leave::all();
        $activities = Activity::all();

        // Data statistik
        $totalUsers = $allUsers->count(); // Menggunakan allUsers untuk total
        $totalProjects = $projects->count();
        $totalTasks = $tasks->count();
        $activeLeaves = $leaves->where('start_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())->count();

        // Data chart
        $activityChartData = Activity::selectRaw('MONTH(activity_date) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
        $activityChartData = array_pad($activityChartData, 12, 0);

        return view('dashboard', compact(
            'readyUsers', 'standbyUsers', 'notReadyUsers', 'completeUsers', 'absentUsers',
            'userTasks', 'userProjects', // Data untuk kartu Tasks dan Projects di kanan
            'totalUsers', 'totalProjects', 'totalTasks', 'activeLeaves',
            'activityChartData'
        ));
    }

    public function hrDashboard()
    {
        // Ambil user employee dan hr dengan eager loading
        $allUsers = $this->eagerLoadUserRelations(User::whereIn('role', ['employee', 'hr']))->get();

        // Filter user berdasarkan status untuk masing-masing tab
        $readyUsers = $allUsers->where('status', 'Ready');
        $standbyUsers = $allUsers->where('status', 'Stand by');
        $notReadyUsers = $allUsers->where('status', 'Not ready');
        $completeUsers = $allUsers->where('status', 'Complete');
        $absentUsers = $allUsers->where('status', 'Absent');

        // Untuk Tasks Card (di HR dashboard, bisa tasks umum atau tasks yang HR pantau)
        $tasks = Task::with(['project', 'assignedUser'])->orderBy('created_at', 'desc')->take(5)->get();
        $userTasks = $tasks; // Menggunakan nama variabel Blade

        // Untuk Project Card (di HR dashboard, projects umum)
        $projects = Project::orderBy('created_at', 'desc')->take(5)->get();
        $userProjects = $projects; // Menggunakan nama variabel Blade


        // Data statistik
        $totalEmployees = User::where('role', 'employee')->count();
        $totalProjects = Project::count();
        $todayActivities = Activity::whereDate('activity_date', today())->count();
        $activeLeaves = Leave::where('start_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())->count();

        $activityChartData = Activity::selectRaw('MONTH(activity_date) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
        $activityChartData = array_pad($activityChartData, 12, 0);

        return view('dashboard', compact(
            'readyUsers', 'standbyUsers', 'notReadyUsers', 'completeUsers', 'absentUsers',
            'userTasks', 'userProjects', // Data untuk kartu Tasks dan Projects di kanan
            'totalEmployees', 'totalProjects', 'todayActivities', 'activeLeaves',
            'activityChartData'
        ));
    }

    public function employeeDashboard()
    {
        $user = Auth::user();

        // User yang sedang login untuk ditampilkan di kartu (hanya dirinya sendiri)
        $loggedInUser = $this->eagerLoadUserRelations(User::where('user_id', $user->user_id))->first(); // Ambil hanya user ini
        $readyUsers = collect([$loggedInUser])->where('status', 'Ready'); // Hanya 1 user ini
        $standbyUsers = collect([$loggedInUser])->where('status', 'Stand by');
        $notReadyUsers = collect([$loggedInUser])->where('status', 'Not ready');
        $completeUsers = collect([$loggedInUser])->where('status', 'Complete');
        $absentUsers = collect([$loggedInUser])->where('status', 'Absent');

        // Data statistik
        $myTasks = $user->assignedTasks()->count();
        $completedTasks = $user->assignedTasks()->where('status', 'completed')->count();
        $myLeaves = $user->leaves()->count();
        $activeLeaves = $user->leaves()->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->count();
        $todayActivity = $user->activities()->whereDate('activity_date', today())->first();

        // Data untuk Tasks Card (tugas yang dia assign)
        $userTasks = $user->assignedTasks()->with('project')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Data untuk Project Card (project yang dia directornya atau ada tugasnya)
        $userProjects = Project::whereHas('tasks', function($query) use ($user) {
            $query->where('assigned_user_id', $user->user_id);
        })->orWhere('project_director', $user->user_id)
        ->orderBy('created_at', 'desc')->take(5)->get();

        $activityChartData = $user->activities()
            ->selectRaw('MONTH(activity_date) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
        $activityChartData = array_pad($activityChartData, 12, 0);

        return view('dashboard', compact(
            'readyUsers', 'standbyUsers', 'notReadyUsers', 'completeUsers', 'absentUsers', // Data untuk tabs
            'myTasks', 'completedTasks', 'myLeaves', 'activeLeaves',
            'todayActivity', 'userTasks', 'userProjects', // Data untuk kartu Tasks dan Projects di kanan
            'activityChartData'
        ));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Ready,Stand by', // HANYA Ready dan Stand by yang bisa diupdate
            // 'status' => 'required|in:Ready,Stand by,Not ready,Complete,Absent' // Jika ingin semua status bisa diupdate via tombol
        ]);

        $user = Auth::user();
        $user->update([
            'status' => $request->status
        ]);

        return redirect()->route('employee.dashboard')->with('success', 'Status updated successfully!');
    }
}
