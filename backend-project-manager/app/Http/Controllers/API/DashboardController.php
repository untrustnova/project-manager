<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get overview data for dashboard
     */
    public function getOverview()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $overview = [
            'total_users' => User::count(),
            'active_projects' => Project::where('status', 'ongoing')->count(),
            'completed_tasks' => Task::where('status', 'completed')
                ->whereMonth('created_at', $today->month)
                ->count(),
            'present_today' => Activity::where('activity_date', $today)
                ->where('status', 'present')
                ->count()
        ];

        return response()->json($overview);
    }

    /**
     * Get project statistics for charts
     */
    public function getProjectStats()
    {
        // Project status distribution
        $projectByStatus = Project::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Project completion trend (last 6 months)
        $completionTrend = Project::where('status', 'completed')
            ->where('updated_at', '>=', Carbon::now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Projects by level
        $projectByLevel = Project::select('level', DB::raw('count(*) as total'))
            ->groupBy('level')
            ->get()
            ->pluck('total', 'level')
            ->toArray();

        return response()->json([
            'status_distribution' => [
                'labels' => array_keys($projectByStatus),
                'datasets' => [[
                    'data' => array_values($projectByStatus),
                    'backgroundColor' => ['#4CAF50', '#FFC107', '#F44336']
                ]]
            ],
            'completion_trend' => [
                'labels' => $completionTrend->pluck('month'),
                'datasets' => [[
                    'label' => 'Completed Projects',
                    'data' => $completionTrend->pluck('total'),
                    'borderColor' => '#2196F3'
                ]]
            ],
            'level_distribution' => [
                'labels' => array_keys($projectByLevel),
                'datasets' => [[
                    'data' => array_values($projectByLevel),
                    'backgroundColor' => ['#8BC34A', '#FF9800', '#E91E63']
                ]]
            ]
        ]);
    }

    /**
     * Get task statistics for charts
     */
    public function getTaskStats()
    {
        // Task status distribution
        $taskByStatus = Task::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Daily task completion (last 7 days)
        $taskCompletion = Task::where('status', 'completed')
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Task priority distribution
        $taskByPriority = Task::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->get()
            ->pluck('total', 'priority')
            ->toArray();

        return response()->json([
            'status_distribution' => [
                'labels' => array_keys($taskByStatus),
                'datasets' => [[
                    'data' => array_values($taskByStatus),
                    'backgroundColor' => ['#4CAF50', '#2196F3', '#FFC107']
                ]]
            ],
            'daily_completion' => [
                'labels' => $taskCompletion->pluck('date'),
                'datasets' => [[
                    'label' => 'Completed Tasks',
                    'data' => $taskCompletion->pluck('total'),
                    'borderColor' => '#4CAF50'
                ]]
            ],
            'priority_distribution' => [
                'labels' => array_keys($taskByPriority),
                'datasets' => [[
                    'data' => array_values($taskByPriority),
                    'backgroundColor' => ['#8BC34A', '#FFC107', '#F44336']
                ]]
            ]
        ]);
    }

    /**
     * Get attendance statistics for charts
     */
    public function getAttendanceStats()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Daily attendance status
        $dailyAttendance = Activity::whereBetween('activity_date', [$startOfMonth, $endOfMonth])
            ->select('activity_date', 'status', DB::raw('count(*) as total'))
            ->groupBy('activity_date', 'status')
            ->orderBy('activity_date')
            ->get()
            ->groupBy('activity_date');

        // Prepare data for stacked bar chart
        $dates = [];
        $present = [];
        $late = [];
        $absent = [];

        foreach ($dailyAttendance as $date => $records) {
            $dates[] = $date;
            $present[] = $records->firstWhere('status', 'present')->total ?? 0;
            $late[] = $records->firstWhere('status', 'late')->total ?? 0;
            $absent[] = $records->firstWhere('status', 'absent')->total ?? 0;
        }

        // Monthly attendance summary
        $monthlyStats = Activity::whereMonth('activity_date', $today->month)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        return response()->json([
            'daily_attendance' => [
                'labels' => $dates,
                'datasets' => [
                    [
                        'label' => 'Present',
                        'data' => $present,
                        'backgroundColor' => '#4CAF50'
                    ],
                    [
                        'label' => 'Late',
                        'data' => $late,
                        'backgroundColor' => '#FFC107'
                    ],
                    [
                        'label' => 'Absent',
                        'data' => $absent,
                        'backgroundColor' => '#F44336'
                    ]
                ]
            ],
            'monthly_summary' => [
                'labels' => array_keys($monthlyStats),
                'datasets' => [[
                    'data' => array_values($monthlyStats),
                    'backgroundColor' => ['#4CAF50', '#FFC107', '#F44336']
                ]]
            ]
        ]);
    }

    /**
     * Get user performance data for charts
     */
    public function getUserPerformance(Request $request)
    {
        $users = User::withCount(['tasks' => function($query) {
                $query->where('status', 'completed');
            }])
            ->withCount('tasks as total_tasks')
            ->having('total_tasks', '>', 0)
            ->orderByDesc('tasks_count')
            ->paginate(10);

        $userData = $users->map(function($user) {
            return [
                'name' => $user->name,
                'completion_rate' => $user->total_tasks > 0 
                    ? round(($user->tasks_count / $user->total_tasks) * 100, 2)
                    : 0,
                'total_tasks' => $user->total_tasks,
                'completed_tasks' => $user->tasks_count
            ];
        });

        return response()->json([
            'performance_chart' => [
                'labels' => $userData->pluck('name'),
                'datasets' => [
                    [
                        'label' => 'Task Completion Rate (%)',
                        'data' => $userData->pluck('completion_rate'),
                        'backgroundColor' => '#2196F3'
                    ]
                ]
            ],
            'pagination' => [
                'current_page' => $users->currentPage(),
                'total_pages' => $users->lastPage(),
                'total_users' => $users->total(),
                'per_page' => $users->perPage()
            ]
        ]);
    }
}
