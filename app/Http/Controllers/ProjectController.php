<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('director')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $directors = User::whereIn('role', ['admin', 'hr'])->get();
        return view('projects.create', compact('directors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_director' => 'nullable|exists:users,user_id',
            'project_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after:start_date',
            'level' => 'nullable|integer|min:1|max:10',
            'status' => 'nullable|string|max:50',
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['director', 'tasks.assignedUser']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $directors = User::whereIn('role', ['admin', 'hr'])->get();
        return view('projects.edit', compact('project', 'directors'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_director' => 'nullable|exists:users,user_id',
            'project_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after:start_date',
            'level' => 'nullable|integer|min:1|max:10',
            'status' => 'nullable|string|max:50',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
