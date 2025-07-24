<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request)
    {
        $query = Project::with(['director', 'tasks']);

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by level
        if ($request->input('level')) {
            $query->where('level', $request->input('level'));
        }

        // Search by name
        if ($request->input('search')) {
            $query->where('project_name', 'like', '%' . $request->input('search') . '%');
        }

        // Sort by created date descending by default
        $query->orderBy('created_at', 'desc');

        $projects = $query->paginate(10);

        // Transform the data
        $transformedProjects = $projects->map(function ($project) {
            return [
                'id' => $project->getAttribute('id'),
                'project_name' => $project->getAttribute('project_name'),
                'start_date' => Carbon::parse($project->getAttribute('start_date'))->format('M d, Y'),
                'deadline' => Carbon::parse($project->getAttribute('deadline'))->format('M d, Y'),
                'project_director' => optional($project->director)->name ?? 'Unassigned',
                'level' => ucfirst($project->getAttribute('level')),
                'status' => ucfirst($project->getAttribute('status'))
            ];
        });

        return response()->json([
            'projects' => $transformedProjects,
            'pagination' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total()
            ]
        ]);
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after:start_date',
            'project_director' => 'required|exists:users,id',
            'level' => 'required|in:high,medium,low',
            'engineer_web' => 'nullable|exists:users,id',
            'engineer_android' => 'nullable|exists:users,id',
            'engineer_ios' => 'nullable|exists:users,id',
            'ui_ux' => 'nullable|exists:users,id',
            'content_creator' => 'nullable|exists:users,id',
            'copywriter' => 'nullable|exists:users,id',
            'tester' => 'nullable|exists:users,id',
        ]);

        $project = new Project();
        $project->project_name = $validated['project_name'];
        $project->start_date = $validated['start_date'];
        $project->deadline = $validated['deadline'];
        $project->project_director = $validated['project_director'];
        $project->level = $validated['level'];
        $project->status = 'running'; // Default status for new projects
        $project->save();

        // Assign team members if provided
        $teamRoles = [
            'engineer_web', 'engineer_android', 'engineer_ios',
            'ui_ux', 'content_creator', 'copywriter', 'tester'
        ];

        foreach ($teamRoles as $role) {
            if (isset($validated[$role])) {
                $project->teamMembers()->create([
                    'user_id' => $validated[$role],
                    'role' => $role
                ]);
            }
        }

        $project->load(['director', 'teamMembers.user']);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => [
                'id' => $project->getAttribute('id'),
                'project_name' => $project->getAttribute('project_name'),
                'start_date' => Carbon::parse($project->getAttribute('start_date'))->format('M d, Y'),
                'deadline' => Carbon::parse($project->getAttribute('deadline'))->format('M d, Y'),
                'project_director' => optional($project->director)->name ?? 'Unassigned',
                'level' => ucfirst($project->getAttribute('level')),
                'status' => ucfirst($project->getAttribute('status')),
                'team' => $project->teamMembers->map(function ($member) {
                    return [
                        'role' => $member->role,
                        'name' => optional($member->user)->name
                    ];
                })
            ]
        ], 201);
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $project->load(['director', 'tasks.assignedUser']);
        
        return response()->json($project);
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'project_name' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'deadline' => 'sometimes|date|after:start_date',
            'project_director' => 'nullable|exists:users,user_id',
            'level' => 'sometimes|in:easy,medium,hard',
            'status' => 'sometimes|in:ongoing,pending,completed',
        ]);

        $project->update($request->all());
        $project->load(['director', 'tasks']);

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project
        ]);
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ]);
    }

    /**
     * Get project statistics
     */
    public function statistics()
    {
        $stats = [
            'total_projects' => Project::count(),
            'ongoing_projects' => Project::where('status', 'ongoing')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'overdue_projects' => Project::overdue()->count(),
            'projects_by_level' => [
                'easy' => Project::where('level', 'easy')->count(),
                'medium' => Project::where('level', 'medium')->count(),
                'hard' => Project::where('level', 'hard')->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Assign multiple users to project
     */
    public function assignUsers(Request $request, Project $project)
    {
        Log::info('Assigning users to project', [
            'project_id' => $project->id,
            'admin_id' => $request->user()->id
        ]);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        DB::transaction(function () use ($project, $request) {
            // Clear existing assignments (optional)
            $project->assignedUsers()->sync($request->input('user_ids'));
            
            // Create initial tasks for new users if needed
            foreach ($request->input('user_ids') as $userId) {
                if (!$project->tasks()->where('assigned_user_id', $userId)->exists()) {
                    $project->tasks()->create([
                        'task_name' => 'Initial Task',
                        'assigned_user_id' => $userId,
                        'status' => 'pending'
                    ]);
                }
            }
        });

        $project->load(['assignedUsers', 'tasks']);
        
        return response()->json([
            'message' => 'Users assigned successfully',
            'project' => $project
        ]);
    }

    /**
     * Get project timeline/milestones
     */
    public function getProjectTimeline(Project $project)
    {
        Log::info('Accessing project timeline', [
            'project_id' => $project->id,
            'user_id' => request()->user()->id
        ]);

        $project->load(['tasks' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }, 'tasks.assignedUser', 'activities']);

        $timeline = [
            'project_info' => [
                'name' => $project->project_name,
                'start_date' => $project->start_date,
                'deadline' => $project->deadline,
                'progress' => $project->calculateProgress()
            ],
            'milestones' => $project->tasks
                ->map(function ($task) {
                    return [
                        'date' => $task->created_at,
                        'type' => 'task',
                        'title' => $task->task_name,
                        'status' => $task->status,
                        'assigned_to' => $task->assignedUser->name
                    ];
                })
                ->concat($project->activities->map(function ($activity) {
                    return [
                        'date' => $activity->created_at,
                        'type' => 'activity',
                        'description' => $activity->description
                    ];
                }))
                ->sortBy('date')
                ->values()
        ];

        return response()->json($timeline);
    }

    /**
     * Duplicate existing project
     */
    public function duplicateProject(Project $project)
    {
        Log::info('Duplicating project', [
            'original_project_id' => $project->id,
            'user_id' => request()->user()->id
        ]);

        DB::transaction(function () use ($project, &$newProject) {
            // Clone main project
            $newProject = $project->replicate();
            $newProject->project_name = $project->project_name . ' (Copy)';
            $newProject->status = 'pending';
            $newProject->start_date = Carbon::today();
            $newProject->deadline = Carbon::today()->addDays(
                Carbon::parse($project->deadline)->diffInDays($project->start_date)
            );
            $newProject->save();

            // Clone tasks
            foreach ($project->tasks as $task) {
                $newTask = $task->replicate();
                $newTask->project_id = $newProject->id;
                $newTask->status = 'pending';
                $newTask->save();
            }
        });

        $newProject->load(['director', 'tasks']);

        return response()->json([
            'message' => 'Project duplicated successfully',
            'project' => $newProject
        ]);
    }

    /**
     * Archive completed project
     */
    public function archiveProject(Project $project)
    {
        Log::info('Archiving project', [
            'project_id' => $project->id,
            'user_id' => request()->user()->id
        ]);

        if ($project->status !== 'completed') {
            return response()->json([
                'message' => 'Only completed projects can be archived'
            ], 422);
        }

        $project->update(['is_archived' => true]);

        return response()->json([
            'message' => 'Project archived successfully',
            'project' => $project
        ]);
    }

    /**
     * Generate project reports
     */
    public function getProjectReports(Request $request)
    {
        Log::info('Generating project reports', [
            'user_id' => $request->user()->id,
            'filters' => $request->all()
        ]);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:ongoing,pending,completed',
            'level' => 'nullable|in:easy,medium,hard'
        ]);

        $projects = Project::with(['director', 'tasks'])
            ->whereBetween('created_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ])
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->input('status'));
            })
            ->when($request->filled('level'), function ($query) use ($request) {
                return $query->where('level', $request->input('level'));
            })
            ->get();

        $report = [
            'period' => [
                'start' => $request->input('start_date'),
                'end' => $request->input('end_date')
            ],
            'summary' => [
                'total_projects' => $projects->count(),
                'completed_projects' => $projects->where('status', 'completed')->count(),
                'ongoing_projects' => $projects->where('status', 'ongoing')->count(),
                'average_completion_time' => $projects->where('status', 'completed')
                    ->avg(function ($project) {
                        return Carbon::parse($project->created_at)
                            ->diffInDays($project->updated_at);
                    })
            ],
            'by_level' => $projects->groupBy('level')
                ->map(fn($items) => $items->count()),
            'by_director' => $projects->groupBy('director.name')
                ->map(fn($items) => $items->count()),
            'projects' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->project_name,
                    'director' => $project->director->name ?? 'Unassigned',
                    'status' => $project->status,
                    'progress' => $project->calculateProgress(),
                    'tasks_completed' => $project->tasks->where('status', 'completed')->count(),
                    'total_tasks' => $project->tasks->count()
                ];
            })
        ];

        return response()->json($report);
    }
}