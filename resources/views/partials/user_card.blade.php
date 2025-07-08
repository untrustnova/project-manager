{{-- resources/views/partials/user_card.blade.php --}}

@php
    $activityDescription = 'Offline'; // Default jika tidak ada aktivitas
    $lastActivity = $userItem->activities->first(); // Mengambil aktivitas terakhir yang sudah di-eager load

    if ($lastActivity) {
        $activityDescText = $lastActivity->note ?? 'Working';
        $relatedTask = $lastActivity->relatedTask;
        $relatedProject = $lastActivity->relatedProject;

        if ($relatedTask) {
            $activityDescription = 'Working on task: ' . \Illuminate\Support\Str::limit($relatedTask->task_name, 25);
        } elseif ($relatedProject) {
            $activityDescription = 'In project: ' . \Illuminate\Support\Str::limit($relatedProject->project_name, 25);
        } else {
            $activityDescription = $activityDescText;
        }
    }

    // Menentukan route untuk tombol "Review"
    $userShowRoute = match(Auth::user()->role) {
        'admin' => route('admin.users.show', $userItem->user_id),
        'hr' => route('hr.employees.show', $userItem->user_id),
        'employee' => ($userItem->user_id === Auth::id()) ? route('employee.profile') : '#',
        default => '#'
    };

    // Menentukan warna status
    $statusClass = match($userItem->status ?? 'Ready') {
        'Ready' => 'bg-sky-500 text-white',
        'Stand by' => 'bg-amber-500 text-white',
        'Not ready' => 'bg-red-500 text-white',
        'Complete' => 'bg-green-500 text-white',
        'Absent' => 'bg-slate-500 text-white',
        default => 'bg-gray-300 text-gray-800'
    };
@endphp

<div class="bg-white shadow-md rounded-lg p-4 flex flex-col items-center text-center transition-all duration-300 hover:shadow-xl hover:scale-105" data-aos="zoom-in">
    <a href="{{ $userShowRoute }}">
        <img src="{{ $userItem->image ? asset('storage/' . $userItem->image) : asset('images/default-avatar.png') }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover mb-3 shadow-sm">
    </a>
    <h4 class="font-bold text-base text-slate-800 truncate w-full" title="{{ $userItem->name ?? 'User Name' }}">{{ $userItem->name ?? 'User Name' }}</h4>
    <p class="font-medium text-xs text-slate-500 capitalize">{{ $userItem->role ?? 'Role' }}</p>

    <p class="font-medium text-xs text-slate-400 mt-2 h-8" title="{{ $activityDescription }}">
        {{ $activityDescription }}
    </p>

    <div class="flex mt-3 gap-2">
        <span class="px-3 py-1 rounded-full text-[10px] font-semibold {{ $statusClass }}">
            {{ $userItem->status ?? 'N/A' }}
        </span>
        <a href="{{ $userShowRoute }}" class="px-3 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
            Review
        </a>
    </div>
</div>
