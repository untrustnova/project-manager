<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('user')
            ->orderBy('activity_date', 'desc')
            ->paginate(10);

        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $users = User::all();
        return view('activities.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'activity_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:hadir,telat,izin,sakit,cuti,alfa',
            'note' => 'nullable|string',
        ]);

        Activity::create($validated);

        return redirect()->route('employee.activities.index')
            ->with('success', 'Activity created successfully.');
    }

    public function show(Activity $activity)
    {
        $activity->load('user');
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $users = User::all();
        return view('activities.edit', compact('activity', 'users'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'activity_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:hadir,telat,izin,sakit,cuti,alfa',
            'note' => 'nullable|string',
        ]);

        $activity->update($validated);

        return redirect()->route('employee.activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('employee.activities.index')
            ->with('success', 'Activity deleted successfully.');
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        $activity = Activity::where('user_id', $user->user_id)
            ->where('activity_date', $today)
            ->first();

        if ($activity) {
            return redirect()->back()
                ->with('error', 'You have already checked in today.');
        }

        Activity::create([
            'user_id' => $user->user_id,
            'activity_date' => $today,
            'check_in' => now()->format('H:i'),
            'status' => 'hadir',
        ]);

        return redirect()->back()
            ->with('success', 'Check in successful.');
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        $activity = Activity::where('user_id', $user->user_id)
            ->where('activity_date', $today)
            ->first();

        if (!$activity) {
            return redirect()->back()
                ->with('error', 'You must check in first.');
        }

        if ($activity->check_out) {
            return redirect()->back()
                ->with('error', 'You have already checked out today.');
        }

        $activity->update([
            'check_out' => now()->format('H:i'),
        ]);

        return redirect()->back()
            ->with('success', 'Check out successful.');
    }
}
