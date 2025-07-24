<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveStatusMail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of leaves
     */
    public function index(Request $request)
    {
        $query = Leave::with('submittedBy');

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by category
        if ($request->input('leave_category')) {
            $query->where('leave_category', $request->input('leave_category'));
        }

        // Filter by date range
        if ($request->input('start_date') && $request->input('end_date')) {
            $query->whereBetween('start_date', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }

        // Filter by user (for HR/Admin)
        if ($request->input('user_id')) {
            $query->where('submitted_by_user_id', $request->input('user_id'));
        }

        $leaves = $query->latest()->paginate(10);

        return response()->json([
            'leaves' => $leaves->map(function ($leave) {
                return [
                    'id' => $leave->getAttribute('id'),
                    'category' => $leave->getAttribute('leave_category'),
                    'date_range' => [
                        'start' => Carbon::parse($leave->getAttribute('start_date'))->format('M d, Y'),
                        'end' => Carbon::parse($leave->getAttribute('end_date'))->format('M d, Y'),
                    ],
                    'description' => $leave->getAttribute('description'),
                    'status' => $leave->getAttribute('status'),
                    'options' => [
                        'bring_laptop' => $leave->getAttribute('bring_laptop'),
                        'contactable' => $leave->getAttribute('still_be_contacted')
                    ],
                    'submitted_by' => [
                        'name' => optional($leave->submittedBy)->name ?? 'Unknown'
                    ]
                ];
            }),
            'pagination' => [
                'current_page' => $leaves->currentPage(),
                'last_page' => $leaves->lastPage(),
                'per_page' => $leaves->perPage(),
                'total' => $leaves->total()
            ]
        ]);
    }

    /**
     * Store a newly created leave request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_category' => 'required|string|in:annual,sick,unpaid,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string',
            'bring_laptop' => 'required|boolean',
            'still_be_contacted' => 'required|boolean',
        ]);

        // Calculate duration in days
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $duration = $startDate->diffInDays($endDate) + 1;

        // Check remaining leave balance for annual leaves
        if ($validated['leave_category'] === 'annual') {
            $yearlyLeaves = $this->getYearlyLeaveCount($request->user()->getAttribute('id'));
            if ($yearlyLeaves + $duration > 12) {
                return response()->json([
                    'message' => 'Insufficient annual leave balance',
                    'remaining_days' => 12 - $yearlyLeaves
                ], 422);
            }
        }

        // Create new leave request
        $leave = new Leave();
        $leave->leave_category = $validated['leave_category'];
        $leave->start_date = $validated['start_date'];
        $leave->end_date = $validated['end_date'];
        $leave->description = $validated['description'];
        $leave->bring_laptop = $validated['bring_laptop'];
        $leave->still_be_contacted = $validated['still_be_contacted'];
        $leave->submitted_by_user_id = $request->user()->getAttribute('id');
        $leave->status = 'pending';
        $leave->save();

        $leave->load('submittedBy');

        // Format response
        return response()->json([
            'message' => 'Leave request submitted successfully',
            'leave' => [
                'id' => $leave->getAttribute('id'),
                'category' => $leave->getAttribute('leave_category'),
                'duration' => $duration . ' ' . ($duration === 1 ? 'day' : 'days'),
                'date_range' => [
                    'start' => $startDate->format('M d, Y'),
                    'end' => $endDate->format('M d, Y')
                ],
                'description' => $leave->getAttribute('description'),
                'options' => [
                    'bring_laptop' => $leave->getAttribute('bring_laptop'),
                    'contactable' => $leave->getAttribute('still_be_contacted')
                ],
                'status' => $leave->getAttribute('status'),
                'submitted_by' => [
                    'name' => optional($leave->submittedBy)->name ?? 'Unknown',
                ]
            ]
        ], 201);
    }

    /**
     * Get yearly leave count for a user
     */
    private function getYearlyLeaveCount($userId)
    {
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        return Leave::where('submitted_by_user_id', $userId)
            ->where('leave_category', 'annual')
            ->where('status', 'approved')
            ->whereBetween('start_date', [$startOfYear, $endOfYear])
            ->sum(function ($leave) {
                return Carbon::parse($leave->start_date)
                    ->diffInDays(Carbon::parse($leave->end_date)) + 1;
            });
    }

    /**
     * Display the specified leave
     */
    public function show(Leave $leave)
    {
        $leave->load('submittedBy');
        
        return response()->json($leave);
    }

    /**
     * Update leave status (for HR/Admin)
     */
    public function updateStatus(Request $request, Leave $leave)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string'
        ]);

        $leave->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note
        ]);

        // Send email notification
        Mail::to($leave->submittedBy->email)->send(
            new LeaveStatusMail($leave, $request->status)
        );

        return response()->json([
            'message' => 'Leave status updated successfully',
            'leave' => $leave
        ]);
    }

    /**
     * Get user's own leave requests
     */
    public function myLeaves(Request $request)
    {
        $leaves = Leave::where('submitted_by_user_id', $request->user()->user_id)
            ->latest()
            ->paginate(15);

        return response()->json($leaves);
    }

    /**
     * Remove the specified leave
     */
    public function destroy(Leave $leave)
    {
        // Only allow deletion if pending
        if ($leave->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot delete approved/rejected leave requests'
            ], 422);
        }

        $leave->delete();

        return response()->json([
            'message' => 'Leave request deleted successfully'
        ]);
    }

    /**
     * Get calendar view of leaves
     */
    public function getLeaveCalendar(Request $request)
    {
        Log::info('Accessing leave calendar', [
            'user_id' => $request->user()->id
        ]);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $leaves = Leave::with('submittedBy')
            ->whereBetween('start_date', [
                $request->input('start_date'),
                $request->input('end_date')
            ])
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'title' => $leave->submittedBy->name . ' - ' . $leave->leave_category,
                    'start' => $leave->start_date,
                    'end' => $leave->end_date,
                    'status' => $leave->status,
                    'category' => $leave->leave_category,
                    'user' => $leave->submittedBy->name
                ];
            });

        return response()->json($leaves);
    }

    /**
     * Get user leave balance
     */
    public function getLeaveBalance(Request $request)
    {
        Log::info('Checking leave balance', [
            'user_id' => $request->user()->id
        ]);

        $year = $request->input('year', date('Y'));
        $userId = $request->input('user_id', $request->user()->id);

        $leaves = Leave::where('submitted_by_user_id', $userId)
            ->whereYear('start_date', $year)
            ->where('status', 'approved')
            ->get();

        $balance = [
            'year' => $year,
            'total_annual_leave' => 12, // Default annual leave days
            'used_leaves' => $leaves->sum(function ($leave) {
                return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
            }),
            'leaves_by_category' => $leaves->groupBy('leave_category')
                ->map(function ($categoryLeaves) {
                    return $categoryLeaves->count();
                })
        ];

        $balance['remaining_leaves'] = $balance['total_annual_leave'] - $balance['used_leaves'];

        return response()->json($balance);
    }

    /**
     * Export leave data
     */
    public function exportLeaves(Request $request)
    {
        Log::info('Exporting leave data', [
            'user_id' => $request->user()->id,
            'filters' => $request->all()
        ]);

        $leaves = Leave::with('submittedBy')
            ->when($request->has('start_date'), function ($query) use ($request) {
                return $query->where('start_date', '>=', $request->input('start_date'));
            })
            ->when($request->has('end_date'), function ($query) use ($request) {
                return $query->where('end_date', '<=', $request->input('end_date'));
            })
            ->get();

        $csv = "Employee,Category,Start Date,End Date,Status,Description\n";
        foreach ($leaves as $leave) {
            $csv .= sprintf(
                '"%s",%s,%s,%s,%s,"%s"\n',
                $leave->submittedBy->name,
                $leave->leave_category,
                $leave->start_date,
                $leave->end_date,
                $leave->status,
                str_replace('"', '""', $leave->description ?? '')
            );
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="leaves.csv"');
    }

    /**
     * Get leave statistics
     */
    public function getLeaveStatistics(Request $request)
    {
        Log::info('Accessing leave statistics', [
            'user_id' => $request->user()->id
        ]);

        $year = $request->input('year', date('Y'));

        $leaves = Leave::with('submittedBy')
            ->whereYear('start_date', $year)
            ->get();

        $statistics = [
            'total_leaves' => $leaves->count(),
            'by_status' => $leaves->groupBy('status')
                ->map(fn($items) => $items->count()),
            'by_category' => $leaves->groupBy('leave_category')
                ->map(fn($items) => $items->count()),
            'monthly_distribution' => $leaves
                ->groupBy(fn($leave) => Carbon::parse($leave->start_date)->format('F'))
                ->map(fn($items) => $items->count()),
            'average_duration' => $leaves->avg(function ($leave) {
                return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
            })
        ];

        return response()->json($statistics);
    }

    /**
     * Bulk approve leave requests
     */
    public function bulkApproveLeaves(Request $request)
    {
        Log::info('Bulk approving leaves', [
            'admin_id' => $request->user()->id,
            'leave_ids' => $request->input('leave_ids')
        ]);

        $request->validate([
            'leave_ids' => 'required|array',
            'leave_ids.*' => 'exists:leaves,id',
            'admin_note' => 'nullable|string'
        ]);

        $leaves = Leave::whereIn('id', $request->input('leave_ids'))
            ->where('status', 'pending')
            ->get();

        foreach ($leaves as $leave) {
            $leave->update([
                'status' => 'approved',
                'admin_note' => $request->input('admin_note')
            ]);

            // Send email notification
            Mail::to($leave->submittedBy->email)->send(
                new LeaveStatusMail($leave, 'approved')
            );
        }

        return response()->json([
            'message' => count($leaves) . ' leave requests approved successfully',
            'processed_leaves' => $leaves
        ]);
    }
}
