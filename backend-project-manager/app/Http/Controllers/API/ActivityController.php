<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities
     */
    public function index(Request $request)
    {
        $query = Activity::with(['user', 'user.projectsAsDirector', 'user.tasks', 'user.leaves']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by division
        if ($request->filled('division')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('division', $request->input('division'));
            });
        }

        // Get activities for the current month by default
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
        
        $query->whereBetween('activity_date', [$startDate, $endDate]);

        $activities = $query->get()->groupBy('user_id');

        // Transform and calculate statistics
        $userActivities = $activities->map(function ($userActivities) use ($startDate, $endDate) {
            $user = $userActivities->first()->user;
            $totalWorkHours = $userActivities->sum(function ($activity) {
                if ($activity->check_in && $activity->check_out) {
                    return Carbon::parse($activity->check_out)->diffInHours(Carbon::parse($activity->check_in));
                }
                return 0;
            });

            // Calculate expected work hours for the period
            $workdays = Carbon::parse($startDate)->diffInWeekdays(Carbon::parse($endDate));
            $expectedHours = $workdays * 8; // 8 hours per workday
            $workPercentage = $expectedHours > 0 ? ($totalWorkHours / $expectedHours) * 100 : 0;

            return [
                'user' => [
                    'id' => $user->getAttribute('user_id'),
                    'name' => $user->getAttribute('name'),
                    'image' => $user->getAttribute('image'),
                    'division' => $user->getAttribute('division')
                ],
                'statistics' => [
                    'projects' => $user->projectsAsDirector->count(),
                    'tasks_done' => $user->tasks->where('status', 'completed')->count(),
                    'leave_entitlement' => $user->leaves->where('status', 'approved')
                        ->whereBetween('start_date', [$startDate, $endDate])
                        ->count(),
                    'work_hours' => [
                        'total' => $totalWorkHours,
                        'expected' => $expectedHours,
                        'percentage' => round($workPercentage, 1),
                        'status' => $workPercentage >= 100 ? 'over_work' : 'normal'
                    ]
                ],
                'activities' => $userActivities->map(function ($activity) {
                    return [
                        'date' => $activity->activity_date,
                        'check_in' => $activity->check_in,
                        'check_out' => $activity->check_out,
                        'status' => $activity->status
                    ];
                })
            ];
        })->values();

        return response()->json([
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'users' => $userActivities
        ]);
    }

    /**
     * Check in user
     */
    public function checkIn(Request $request)
    {
        $today = Carbon::today();
        $user = $request->user();

        // Check if already checked in today
        $existingActivity = Activity::where('user_id', $user->user_id)
            ->where('activity_date', $today)
            ->first();

        if ($existingActivity && $existingActivity->check_in) {
            return response()->json([
                'message' => 'You have already checked in today'
            ], 422);
        }

        // Create or update activity
        $activity = Activity::updateOrCreate(
            [
                'user_id' => $user->user_id,
                'activity_date' => $today
            ],
            [
                'check_in' => Carbon::now(),
                'status' => Carbon::now()->format('H:i') > '09:00' ? 'late' : 'present'
            ]
        );

        return response()->json([
            'message' => 'Checked in successfully',
            'activity' => $activity
        ]);
    }

    /**
     * Check out user
     */
    public function checkOut(Request $request)
    {
        $today = Carbon::today();
        $user = $request->user();

        $activity = Activity::where('user_id', $user->user_id)
            ->where('activity_date', $today)
            ->first();

        if (!$activity || !$activity->check_in) {
            return response()->json([
                'message' => 'You must check in first'
            ], 422);
        }

        if ($activity->check_out) {
            return response()->json([
                'message' => 'You have already checked out today'
            ], 422);
        }

        $activity->update([
            'check_out' => Carbon::now()
        ]);

        return response()->json([
            'message' => 'Checked out successfully',
            'activity' => $activity
        ]);
    }

    /**
     * Get user's today activity
     */
    public function todayActivity(Request $request)
    {
        $activity = Activity::where('user_id', $request->user()->user_id)
            ->where('activity_date', Carbon::today())
            ->first();

        return response()->json($activity);
    }

    /**
     * Get user's activity history
     */
    public function myActivities(Request $request)
    {
        $activities = Activity::where('user_id', $request->user()->user_id)
            ->latest('activity_date')
            ->paginate(15);

        return response()->json($activities);
    }

    /**
     * Generate attendance report for a specific period
     */
    public function getAttendanceReport(Request $request)
    {
        Log::info('Generating attendance report', [
            'user_id' => $request->user()->user_id ?? null,
            'params' => $request->all()
        ]);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,user_id'
        ]);

        $query = Activity::with('user')
            ->whereBetween('activity_date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $report = $query->get()
            ->groupBy('user_id')
            ->map(function ($activities) {
                return [
                    'user' => $activities->first()->user,
                    'total_days' => $activities->count(),
                    'present' => $activities->where('status', 'present')->count(),
                    'late' => $activities->where('status', 'late')->count(),
                    'absent' => $activities->where('status', 'absent')->count(),
                    'activities' => $activities
                ];
            });

        return response()->json($report);
    }

    /**
     * Mark a user as absent
     */
    public function markAbsent(Request $request)
    {
        Log::info('Marking user as absent', [
            'admin_id' => $request->user()->user_id ?? null,
            'target_user' => $request->user_id
        ]);

        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'date' => 'required|date'
        ]);

        $activity = Activity::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'activity_date' => Carbon::parse($request->date)
            ],
            [
                'status' => 'absent',
                'check_in' => null,
                'check_out' => null
            ]
        );

        return response()->json([
            'message' => 'User marked as absent successfully',
            'activity' => $activity
        ]);
    }

    /**
     * Get working hours statistics
     */
    public function getWorkingHoursReport(Request $request)
    {
        $userId = $request->input('user_id', $request->user()->getAttribute('id'));
        
        Log::info('Accessing working hours report', [
            'user_id' => $userId
        ]);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $user = User::with(['projectsAsDirector', 'tasks', 'leaves'])->findOrFail($userId);
        
        $activities = Activity::where('user_id', $userId)
            ->whereBetween('activity_date', [$startDate, $endDate])
            ->whereNotNull(['check_in', 'check_out'])
            ->get();

        // Calculate working hours
        $totalWorkHours = $activities->sum(function ($activity) {
            return Carbon::parse($activity->getAttribute('check_out'))
                ->diffInHours(Carbon::parse($activity->getAttribute('check_in')));
        });

        // Calculate expected work hours
        $workdays = $startDate->diffInWeekdays($endDate);
        $expectedHours = $workdays * 8; // 8 hours per workday
        $workPercentage = $expectedHours > 0 ? ($totalWorkHours / $expectedHours) * 100 : 0;

        $stats = [
            'user' => [
                'name' => $user->getAttribute('name'),
                'division' => $user->getAttribute('division'),
                'image' => $user->getAttribute('image')
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_days' => $workdays
            ],
            'work_statistics' => [
                'projects' => $user->projectsAsDirector->count(),
                'tasks_completed' => $user->tasks->where('status', 'completed')->count(),
                'total_tasks' => $user->tasks->count(),
                'leaves_taken' => $user->leaves
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->where('status', 'approved')
                    ->count()
            ],
            'working_hours' => [
                'total' => $totalWorkHours,
                'expected' => $expectedHours,
                'percentage' => round($workPercentage, 1),
                'status' => $workPercentage >= 100 ? 'over_work' : 'normal',
                'daily_average' => $activities->count() > 0 
                    ? round($totalWorkHours / $activities->count(), 1) 
                    : 0
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Get team attendance overview
     */
    public function getTeamAttendance(Request $request)
    {
        Log::info('Accessing team attendance', [
            'user_id' => $request->user()->user_id ?? null
        ]);

        $today = Carbon::today();
        
        $teamActivities = Activity::with('user')
            ->where('activity_date', $today)
            ->get()
            ->groupBy('status')
            ->map(function ($activities, $status) {
                return [
                    'status' => $status,
                    'count' => $activities->count(),
                    'users' => $activities->map(function ($activity) {
                        return [
                            'user_id' => $activity->user->user_id,
                            'name' => $activity->user->name,
                            'check_in' => $activity->check_in,
                            'check_out' => $activity->check_out
                        ];
                    })
                ];
            });

        $summary = [
            'date' => $today->toDateString(),
            'total_employees' => Activity::where('activity_date', $today)->count(),
            'present' => $teamActivities['present'] ?? ['count' => 0],
            'late' => $teamActivities['late'] ?? ['count' => 0],
            'absent' => $teamActivities['absent'] ?? ['count' => 0],
            'details' => $teamActivities
        ];

        return response()->json($summary);
    }
}