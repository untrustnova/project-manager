<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Activity;
use App\Models\Task;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by division
        if ($request->has('division')) {
            $query->where('division', $request->input('division'));
        }

        // Search by name or email
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('email', 'like', '%' . $request->input('search') . '%');
            });
        }

        $users = $query->paginate(15);

        return response()->json($users);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,employee,hr',
            'telegram_link' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'telegram_link' => $request->input('telegram_link'),
            'birthdate' => $request->input('birthdate'),
            'address' => $request->input('address'),
            'phone_number' => $request->input('phone_number'),
            'is_verified' => true, // Admin created users are auto-verified
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['projectsAsDirector', 'tasks', 'activities' => function($query) {
            $query->latest()->limit(10);
        }]);

        return response()->json($user);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id . ',id',
            'division' => 'sometimes|string',
            'status' => 'sometimes|in:ready,stand_by,not_ready,absent,complete',
            'telegram_link' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'tanggal_masuk' => 'sometimes|date',
            'pendidikan_terakhir' => 'sometimes|string',
            'image' => 'nullable|image|max:2048',
            'role' => 'sometimes|in:admin,employee,hr',
        ]);

        $user->update($request->all());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Delete user image if exists
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Update user profile image
     */
    public function updateImage(Request $request, User $user)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Delete old image
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        // Store new image
        $imagePath = $request->file('image')->store('user-images', 'public');
        $user->update(['image' => $imagePath]);

        return response()->json([
            'message' => 'Profile image updated successfully',
            'image_url' => Storage::url($imagePath)
        ]);
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:ready,stand_by,not_ready,absent,complete'
        ]);

        $user->update(['status' => $request->input('status')]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Get user dashboard data
     */
    public function getUserDashboard(Request $request)
    {
        $user = $request->user();
        Log::info('Accessing user dashboard', ['user_id' => $user->id]);

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Get current tasks
        $tasks = Task::where('assigned_user_id', $user->id)
            ->with('project')
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->take(5)
            ->get();

        // Get recent activities
        $activities = Activity::where('user_id', $user->id)
            ->with('task', 'project')
            ->latest()
            ->take(10)
            ->get();

        // Calculate statistics
        $taskStats = Task::where('assigned_user_id', $user->id)
            ->whereMonth('created_at', $today->month)
            ->get();

        $dashboard = [
            'tasks' => [
                'pending' => $taskStats->where('status', 'pending')->count(),
                'in_progress' => $taskStats->where('status', 'in_progress')->count(),
                'completed' => $taskStats->where('status', 'completed')->count(),
            ],
            'current_tasks' => $tasks,
            'recent_activities' => $activities,
            'monthly_completion_rate' => $taskStats->count() > 0
                ? round(($taskStats->where('status', 'completed')->count() / $taskStats->count()) * 100, 2)
                : 0
        ];

        return response()->json($dashboard);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        Log::info('Password change attempt', ['user_id' => $request->user()->id]);

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password'))
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    /**
     * Get user work statistics
     */
    public function getUserStatistics(Request $request, User $user)
    {
        Log::info('Accessing user statistics', [
            'target_user_id' => $user->id,
            'requested_by' => $request->user()->id
        ]);

        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()));
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()));

        // Task statistics
        $tasks = Task::where('assigned_user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Activity statistics
        $activities = Activity::where('user_id', $user->id)
            ->whereBetween('activity_date', [$startDate, $endDate])
            ->get();

        $stats = [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString()
            ],
            'tasks' => [
                'total' => $tasks->count(),
                'completed' => $tasks->where('status', 'completed')->count(),
                'completion_rate' => $tasks->count() > 0
                    ? round(($tasks->where('status', 'completed')->count() / $tasks->count()) * 100, 2)
                    : 0,
                'by_priority' => $tasks->groupBy('priority')
                    ->map(fn($items) => $items->count())
            ],
            'attendance' => [
                'total_days' => $activities->count(),
                'present' => $activities->where('status', 'present')->count(),
                'late' => $activities->where('status', 'late')->count(),
                'absent' => $activities->where('status', 'absent')->count(),
            ],
            'average_completion_time' => $tasks->where('status', 'completed')
                ->avg(function($task) {
                    return Carbon::parse($task->created_at)
                        ->diffInHours($task->completed_at);
                })
        ];

        return response()->json($stats);
    }

    /**
     * Export users to CSV/Excel
     */
    public function exportUsers(Request $request)
    {
        Log::info('Exporting users data', ['admin_id' => $request->user()->id]);

        $users = User::when($request->filled('role'), function($query) use ($request) {
            return $query->where('role', $request->input('role'));
        })->get();

        $csv = "Name,Email,Role,Status,Phone Number,Address\n";
        foreach ($users as $user) {
            $csv .= sprintf(
                '"%s","%s",%s,%s,"%s","%s"\n',
                $user->name,
                $user->email,
                $user->role,
                $user->status,
                $user->phone_number ?? '',
                str_replace('"', '""', $user->address ?? '')
            );
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="users.csv"');
    }

    /**
     * Update multiple users
     */
    public function bulkUpdateUsers(Request $request)
    {
        Log::info('Bulk updating users', ['admin_id' => $request->user()->id]);

        $request->validate([
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
            'users.*.status' => 'sometimes|in:ready,stand_by,not_ready,absent,complete',
            'users.*.role' => 'sometimes|in:admin,employee,hr'
        ]);

        $updated = 0;
        foreach ($request->input('users') as $userData) {
            $user = User::find($userData['id']);
            $updateData = array_filter($userData, function ($key) {
                return in_array($key, ['status', 'role']);
            }, ARRAY_FILTER_USE_KEY);

            if (!empty($updateData)) {
                $user->update($updateData);
                $updated++;
            }
        }

        return response()->json([
            'message' => "{$updated} users updated successfully"
        ]);
    }
}
