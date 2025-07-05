@php
    $activityDescription = 'Working on nothing special';
    // Pastikan relasi 'activities' dimuat (eager loaded di controller)
    // Ambil aktivitas terakhir dari koleksi yang sudah di-eager load (take(1) di controller)
    $lastActivity = $userItem->activities->first();

    if ($lastActivity) {
        $activityDescText = $lastActivity->note ?? 'an activity';
        $relatedTask = $lastActivity->relatedTask; // Sudah di-eager load
        $relatedProject = $lastActivity->relatedProject; // Sudah di-eager load

        if ($relatedTask) {
            $activityDescription = 'Working on ' . ($relatedTask->task_name ?? 'a task');
        } elseif ($relatedProject) {
            $activityDescription = 'Working on Project ' . ($relatedProject->project_name ?? 'a project');
        } else {
            $activityDescription = $activityDescText; // Jika tidak ada task/project terkait
        }
    }

    $userShowRoute = '#'; // Default fallback
    if (Auth::user()->role === 'admin' && Route::has('admin.users.show')) {
        $userShowRoute = route('admin.users.show', $userItem->user_id);
    } elseif (Auth::user()->role === 'hr' && Route::has('hr.employees.show')) {
        $userShowRoute = route('hr.employees.show', $userItem->user_id);
    } elseif (Auth::user()->role === 'employee' && Auth::user()->user_id === $userItem->user_id && Route::has('employee.profile')) {
        $userShowRoute = route('employee.profile'); // Employee melihat profilnya sendiri
    }

    $statusClass = '';
    $userStatus = $userItem->status ?? 'Ready';
    if ($userStatus === 'Ready') $statusClass = 'bg-[#6FAEC9] text-white';
    elseif ($userStatus === 'Stand by') $statusClass = 'bg-[#FFB42E] text-white';
    elseif ($userStatus === 'Not ready') $statusClass = 'bg-red-500 text-white';
    elseif ($userStatus === 'Complete') $statusClass = 'bg-[#7DB546] text-white';
    elseif ($userStatus === 'Absent') $statusClass = 'bg-gray-500 text-white';
    else $statusClass = 'bg-gray-300 text-[#111111]';
@endphp

<div class="bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3 flex flex-col items-center text-center">
    <img src="{{ asset('storage/profile_pictures/' . ($userItem->image ?? 'default_avatar.png')) }}" alt="Avatar" class="w-[60px] h-[60px] rounded-full object-cover mb-2">
    <h4 class="font-bold text-[15px] text-[#111111]">{{ $userItem->name ?? 'User Name' }}</h4>
    <p class="font-medium text-[11px] text-[#7D7D7D]">{{ $userItem->role ?? 'Role' }}</p>
    <p class="font-medium text-[11px] text-[#7D7D7D] mt-1">
        {{ $activityDescription }}
    </p>
    <div class="flex mt-2 gap-2">
        <span class="px-3 py-1 rounded-[6px] text-[10px] font-semibold {{ $statusClass }}">
            {{ $userItem->status ?? 'N/A' }}
        </span>
        <a href="{{ $userShowRoute }}" class="px-3 py-1 rounded-[6px] text-[10px] font-semibold bg-[#F5F5F5] text-[#7D7D7D] hover:bg-[#E0E0E0] transition-colors">
            Review
        </a>
    </div>
</div>
