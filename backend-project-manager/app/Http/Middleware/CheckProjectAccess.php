<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;

class CheckProjectAccess
{
    /**
     * Handle an incoming request.
     * Check if user has access to project (either as director or assigned to tasks)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Admin and HR have access to all projects
        if (in_array($user->getRole(), ['admin', 'hr'])) {
            return $next($request);
        }

        $projectId = $request->route('project')?->project_id ?? $request->input('project_id');
        
        if ($projectId) {
            $project = Project::where('project_id', $projectId)->first();
            
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }

            // Check if user is project director
            if ($project->project_director === $user->getKey()) {
                return $next($request);
            }

            // Check if user has tasks in this project
            $hasTasksInProject = $user->tasks()->where('project_id', $projectId)->exists();
            
            if (!$hasTasksInProject) {
                return response()->json(['message' => 'Access denied to this project'], 403);
            }
        }

        return $next($request);
    }
}
