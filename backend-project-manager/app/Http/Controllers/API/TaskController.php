<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class TaskController extends Controller
{
    /**
     * Display a listing of tasks
     */
    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignedUser']);

        // Filter by view type (board or list)
        $viewType = $request->input('view_type', 'board');

        // Apply filters
        if ($request->filled('project')) {
            $query->whereHas('project', function($q) use ($request) {
                $q->where('project_name', 'like', '%' . $request->project . '%');
            });
        }

        if ($request->filled('assigned_to')) {
            $query->whereHas('assignedUser', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->assigned_to . '%');
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Sort by created date
        $query->orderBy('created_at', 'desc');

        $tasks = $query->get();

        if ($viewType === 'board') {
            // Group tasks by status for board view
            $groupedTasks = [
                'to_do' => $tasks->where('status', 'to_do')->values(),
                'in_progress' => $tasks->where('status', 'in_progress')->values(),
                'review' => $tasks->where('status', 'review')->values(),
                'completed' => $tasks->where('status', 'completed')->values()
            ];

            // Transform tasks for board view
            foreach ($groupedTasks as $status => $statusTasks) {
                $groupedTasks[$status] = $statusTasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'task_name' => $task->task_name,
                        'project' => [
                            'name' => $task->project->project_name,
                            'description' => $task->project->description
                        ],
                        'level' => $task->level,
                        'assigned_to' => [
                            'name' => $task->assignedUser->name,
                            'avatar' => $task->assignedUser->avatar
                        ],
                        'created_at' => Carbon::parse($task->created_at)->format('M d, Y')
                    ];
                });
            }

            return response()->json(['board_view' => $groupedTasks]);
        } else {
            // Transform tasks for list view
            $transformedTasks = $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'task_name' => $task->task_name,
                    'project' => $task->project->project_name,
                    'assigned_employee' => $task->assignedUser->name,
                    'task_level' => $task->level,
                    'task_status' => $task->status,
                    'created' => Carbon::parse($task->created_at)->format('M d, Y')
                ];
            });

            return response()->json([
                'list_view' => $transformedTasks,
                'total' => $transformedTasks->count()
            ]);
        }
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'level' => 'required|in:low,medium,high',
        ]);

        $task = new Task();
        $task->task_name = $request->task_name;
        $task->project_id = $request->project_id;
        $task->level = $request->level;
        $task->status = 'to_do'; // Default status
        $task->save();

        return response()->json([
            'message' => 'Task created successfully',
            'task' => [
                'id' => $task->id,
                'task_name' => $task->task_name,
                'project' => $task->project->project_name,
                'level' => $task->level,
                'status' => $task->status,
                'created_at' => Carbon::parse($task->created_at)->format('M d, Y')
            ]
        ], 201);
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        $task->load(['project', 'assignedUser']);
        
        return response()->json($task);
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'task_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'priority' => 'sometimes|in:low,medium,high',
            'assigned_user_id' => 'nullable|exists:users,user_id',
        ]);

        $task->update($request->all());
        $task->load(['project', 'assignedUser']);

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ]);
    }

    /**
     * Remove the specified task
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Get tasks by authenticated user
     */
    public function myTasks(Request $request)
    {
        $tasks = Task::with(['project'])
            ->where('assigned_user_id', $request->user()->user_id)
            ->paginate(15);

        return response()->json($tasks);
    }

    /**
     * Quick status update for task
     */
    public function updateTaskStatus(Request $request, Task $task)
    {
        Log::info('Updating task status', [
            'task_id' => $task->id,
            'user_id' => $request->user()->id
        ]);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $oldStatus = $task->status;
        $task->update([
            'status' => $request->input('status'),
            'completed_at' => $request->input('status') === 'completed' ? now() : null
        ]);

        // Record status change in activity log
        $task->activities()->create([
            'user_id' => $request->user()->id,
            'description' => "Status changed from {$oldStatus} to {$task->status}"
        ]);

        return response()->json([
            'message' => 'Task status updated successfully',
            'task' => $task->load(['project', 'assignedUser'])
        ]);
    }

    /**
     * Assign task to user
     */
    public function assignTask(Request $request, Task $task)
    {
        Log::info('Assigning task', [
            'task_id' => $task->id,
            'admin_id' => $request->user()->id
        ]);

        $request->validate([
            'assigned_user_id' => 'required|exists:users,id'
        ]);

        $oldAssignee = $task->assignedUser ? $task->assignedUser->name : 'Unassigned';
        $task->update([
            'assigned_user_id' => $request->input('assigned_user_id')
        ]);

        // Record assignment in activity log
        $newAssignee = $task->assignedUser->name;
        $task->activities()->create([
            'user_id' => $request->user()->id,
            'description' => "Task reassigned from {$oldAssignee} to {$newAssignee}"
        ]);

        return response()->json([
            'message' => 'Task assigned successfully',
            'task' => $task->load(['project', 'assignedUser'])
        ]);
    }

    /**
     * Get task comments/notes
     */
    public function getTaskComments(Task $task)
    {
        Log::info('Accessing task comments', [
            'task_id' => $task->id,
            'user_id' => request()->user()->id
        ]);

        $comments = $task->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name
                    ],
                    'created_at' => $comment->created_at
                ];
            });

        return response()->json($comments);
    }

    /**
     * Add comment to task
     */
    public function addTaskComment(Request $request, Task $task)
    {
        Log::info('Adding task comment', [
            'task_id' => $task->id,
            'user_id' => $request->user()->id
        ]);

        $request->validate([
            'content' => 'required|string'
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content')
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user')
        ]);
    }

    /**
     * Set individual task deadline
     */
    public function setTaskDeadline(Request $request, Task $task)
    {
        Log::info('Setting task deadline', [
            'task_id' => $task->id,
            'user_id' => $request->user()->id
        ]);

        $request->validate([
            'deadline' => 'required|date|after:now'
        ]);

        $task->update([
            'deadline' => $request->input('deadline')
        ]);

        // Record deadline change in activity log
        $task->activities()->create([
            'user_id' => $request->user()->id,
            'description' => "Deadline set to " . Carbon::parse($request->input('deadline'))->format('Y-m-d H:i:s')
        ]);

        return response()->json([
            'message' => 'Task deadline set successfully',
            'task' => $task->load(['project', 'assignedUser'])
        ]);
    }

    /**
     * Update multiple tasks at once
     */
    public function bulkUpdateTasks(Request $request)
    {
        Log::info('Bulk updating tasks', [
            'user_id' => $request->user()->id
        ]);

        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.status' => 'sometimes|in:pending,in_progress,completed',
            'tasks.*.priority' => 'sometimes|in:low,medium,high',
            'tasks.*.assigned_user_id' => 'sometimes|exists:users,id'
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->input('tasks') as $taskData) {
                $task = Task::find($taskData['id']);
                $updateData = array_filter($taskData, function ($key) {
                    return in_array($key, ['status', 'priority', 'assigned_user_id']);
                }, ARRAY_FILTER_USE_KEY);
                
                if (!empty($updateData)) {
                    $task->update($updateData);
                    
                    // Record changes in activity log
                    $task->activities()->create([
                        'user_id' => $request->user()->id,
                        'description' => "Bulk update performed"
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Tasks updated successfully'
        ]);
    }
}