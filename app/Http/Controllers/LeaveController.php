<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('submittedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $users = User::all();
        return view('leaves.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_category' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'bring_laptop' => 'boolean',
            'still_be_contacted' => 'boolean',
            'submitted_by_user_id' => 'required|exists:users,user_id',
        ]);

        $validated['bring_laptop'] = $request->has('bring_laptop');
        $validated['still_be_contacted'] = $request->has('still_be_contacted');

        Leave::create($validated);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave request created successfully.');
    }

    public function show(Leave $leave)
    {
        $leave->load('submittedBy');
        return view('leaves.show', compact('leave'));
    }

    public function edit(Leave $leave)
    {
        $users = User::all();
        return view('leaves.edit', compact('leave', 'users'));
    }

    public function update(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'leave_category' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'bring_laptop' => 'boolean',
            'still_be_contacted' => 'boolean',
            'submitted_by_user_id' => 'required|exists:users,user_id',
        ]);

        $validated['bring_laptop'] = $request->has('bring_laptop');
        $validated['still_be_contacted'] = $request->has('still_be_contacted');

        $leave->update($validated);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave request updated successfully.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();

        return redirect()->route('leaves.index')
            ->with('success', 'Leave request deleted successfully.');
    }

    public function myLeaves()
    {
        $user = Auth::user();
        $leaves = Leave::where('submitted_by_user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('leaves.my-leaves', compact('leaves'));
    }

    public function apply()
    {
        return view('leaves.apply');
    }

    public function submitApplication(Request $request)
    {
        $validated = $request->validate([
            'leave_category' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'bring_laptop' => 'boolean',
            'still_be_contacted' => 'boolean',
        ]);

        $validated['bring_laptop'] = $request->has('bring_laptop');
        $validated['still_be_contacted'] = $request->has('still_be_contacted');
        $validated['submitted_by_user_id'] = Auth::user()->user_id;

        Leave::create($validated);

        return redirect()->route('leaves.my-leaves')
            ->with('success', 'Leave application submitted successfully.');
    }
}
