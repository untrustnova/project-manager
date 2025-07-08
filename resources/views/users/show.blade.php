@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="bg-slate-50 min-h-screen">

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 pt-24">
        {{-- Profile Card --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    {{-- Kolom Kiri: Avatar & Statistik --}}
                    <div class="col-span-1 lg:col-span-4 flex flex-col items-center text-center lg:items-start lg:text-left">
                        <div class="flex flex-col items-center lg:items-start gap-5">
                            <img class="w-40 h-40 rounded-full object-cover shadow-lg"
                                 src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-avatar.png') }}"
                                 alt="Profile picture">

                            <div>
                                <h2 class="text-3xl font-extrabold text-slate-800">{{ $user->name }}</h2>
                                <p class="text-lg text-slate-500 font-medium mt-1 capitalize">{{ $user->role }}</p>
                            </div>
                        </div>

                        <div class="w-full mt-6">
                            @php
                                $editRoute = match(Auth::user()->role) {
                                    'admin' => route('admin.users.edit', $user->user_id),
                                    'hr' => route('hr.employees.edit', $user->user_id),
                                    'employee' => route('employee.profile.edit'),
                                    default => '#'
                                };
                            @endphp
                            <a href="{{ $editRoute }}" class="block w-full text-center bg-blue-600 text-white py-3 px-4 rounded-xl hover:bg-blue-700 transition-all duration-300 font-semibold shadow-lg shadow-blue-500/20">
                                Edit Profile
                            </a>
                        </div>

                        <div class="w-full space-y-3 pt-8 mt-auto">
                            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-lg">
                                <span class="text-base font-medium text-slate-600">Project Total</span>
                                <span id="stats-projects" class="bg-white shadow-sm border border-slate-200/80 text-lg font-bold px-3 py-1 rounded-md text-slate-800">{{ $user->directedProjects->count() + $user->assignedTasks->groupBy('project_id')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-lg">
                                <span class="text-base font-medium text-slate-600">Tasks Done</span>
                                <span id="stats-tasks-done" class="bg-white shadow-sm border border-slate-200/80 text-lg font-bold px-3 py-1 rounded-md text-slate-800">{{ $user->assignedTasks->where('status', 'completed')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-lg">
                                <span class="text-base font-medium text-slate-600">Total Leave</span>
                                <span id="stats-leaves" class="bg-white shadow-sm border border-slate-200/80 text-lg font-bold px-3 py-1 rounded-md text-slate-800">{{ $user->leaves->count() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Detail Informasi Pengguna --}}
                    <div class="col-span-1 lg:col-span-8 lg:border-l lg:pl-8 border-slate-200">
                        <h3 class="text-2xl font-bold text-slate-800 mb-6">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            @php
                            function renderInfoItem($icon, $label, $value, $isLink = false, $href = '#') {
                                $displayValue = $value ?? '<span class="italic text-slate-400">Not set</span>';
                                $item = $isLink
                                    ? "<a href='{$href}' class='text-blue-600 hover:underline'>{$displayValue}</a>"
                                    : $displayValue;
                                echo "
                                <div class='flex items-start gap-4'>
                                    <div class='mt-1 text-blue-600'>{$icon}</div>
                                    <div>
                                        <label class='text-sm font-semibold text-slate-500'>{$label}</label>
                                        <div class='mt-1 text-lg font-medium text-slate-800'>{$item}</div>
                                    </div>
                                </div>
                                ";
                            }
                            @endphp

                            {{-- Menggunakan helper --}}
                            {!! renderInfoItem('<i class="fa-solid fa-envelope"></i>', 'Email', $user->email) !!}
                            {!! renderInfoItem('<i class="fa-solid fa-phone"></i>', 'Phone Number', $user->phone_number) !!}
                            {!! renderInfoItem('<i class="fa-solid fa-location-dot"></i>', 'Address', $user->address) !!}
                            {!! renderInfoItem('<i class="fa-solid fa-paper-plane"></i>', 'Telegram Link', $user->telegram_link) !!}
                            {!! renderInfoItem('<i class="fa-solid fa-cake-candles"></i>', 'Birth Date', $user->birth ? $user->birth->format('d F Y') : null) !!}
                            {!! renderInfoItem('<i class="fa-solid fa-lock"></i>', 'Password', 'Change Password', true, route('password.change')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hanya jalankan jika kita di halaman profil user yang sedang login
        if ({{ Auth::check() && Auth::id() === $user->user_id ? 'true' : 'false' }}) {

            window.Echo.private('user.{{ $user->user_id }}')
                .listen('UserStatsUpdated', (e) => {
                    console.log('Stats received:', e.stats);

                    // Update elemen UI dengan data baru
                    const tasksDoneEl = document.getElementById('stats-tasks-done');
                    const projectsTotalEl = document.getElementById('stats-projects');
                    const leavesTotalEl = document.getElementById('stats-leaves');

                    if (tasksDoneEl) {
                        tasksDoneEl.innerText = e.stats.tasks_done;
                        // Tambahkan efek visual singkat
                        tasksDoneEl.classList.add('transition-all', 'duration-300', 'transform', 'scale-125', 'bg-green-200');
                        setTimeout(() => {
                            tasksDoneEl.classList.remove('transform', 'scale-125', 'bg-green-200');
                        }, 300);
                    }
                    if (projectsTotalEl) {
                        projectsTotalEl.innerText = e.stats.projects_total;
                    }
                    if (leavesTotalEl) {
                        leavesTotalEl.innerText = e.stats.leaves_total;
                    }
                });
        }
    });
</script>
@endpush
@endsection
