@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-[#FAFAFA] flex">
    <aside class="w-[90px] bg-white border-r border-[#E0E0E0] flex flex-col items-center py-4 fixed h-full z-20">
        <div class="flex flex-col items-center gap-4 mt-6">
            <a href="{{ route('dashboard') }}" class="group relative flex items-center justify-center w-[40px] h-[40px]">
                <div class="absolute w-[40px] h-[40px] rounded-full bg-[#6FAEC9] group-hover:bg-[#5A9BB8] transition-all duration-300"></div>
                <i class="fa-solid fa-gauge-high text-[20px] text-white relative z-10"></i>
            </a>

            <a href="{{ route('projects.index') }}" class="group relative flex items-center justify-center w-[40px] h-[40px]">
                <div class="absolute w-[40px] h-[40px] rounded-full bg-[#F5F5F5] group-hover:bg-[#FFB42E] transition-all duration-300"></div>
                <i class="fa-solid fa-diagram-project text-[20px] text-[#7D7D7D] group-hover:text-white relative z-10 transition-all duration-300"></i>
            </a>

            @php
                $tasksIndexRoute = match(Auth::user()->role) {
                    'admin' => 'admin.tasks.index',
                    'hr' => 'hr.tasks.index',
                    'employee' => 'employee.tasks.index',
                    default => '#'
                };
            @endphp
            <a href="{{ route($tasksIndexRoute) }}" class="group relative flex items-center justify-center w-[40px] h-[40px]">
                <div class="absolute w-[40px] h-[40px] rounded-full bg-[#F5F5F5] group-hover:bg-[#7DB546] transition-all duration-300"></div>
                <i class="fa-solid fa-list-check text-[20px] text-[#7D7D7D] group-hover:text-white relative z-10 transition-all duration-300"></i>
            </a>

            @php
                $activityIndexRoute = match(Auth::user()->role) {
                    'admin' => 'admin.activities.index',
                    'hr' => 'hr.activities.index',
                    'employee' => 'employee.activities.index',
                    default => '#'
                };
            @endphp
            <a href="{{ route($activityIndexRoute) }}" class="group relative flex items-center justify-center w-[40px] h-[40px]">
                <div class="absolute w-[40px] h-[40px] rounded-full bg-[#F5F5F5] group-hover:bg-[#4397BB] transition-all duration-300"></div>
                <i class="fa-solid fa-chart-line text-[20px] text-[#7D7D7D] group-hover:text-white relative z-10 transition-all duration-300"></i>
            </a>

            @php
                $leavesIndexRoute = match(Auth::user()->role) {
                    'admin' => 'admin.leaves.index',
                    'hr' => 'hr.leaves.index',
                    'employee' => 'employee.leaves.index',
                    default => '#'
                };
            @endphp
            <a href="{{ route($leavesIndexRoute) }}" class="group relative flex items-center justify-center w-[40px] h-[40px]">
                <div class="absolute w-[40px] h-[40px] rounded-full bg-[#F5F5F5] group-hover:bg-[#6FAEC9] transition-all duration-300"></div>
                <i class="fa-solid fa-book text-[20px] text-[#7D7D7D] group-hover:text-white relative z-10 transition-all duration-300"></i>
            </a>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}" class="group relative flex items-center justify-center w-[40px] h-[40px]">
                <div class="absolute w-[40px] h-[40px] rounded-full bg-[#F5F5F5] group-hover:bg-[#7D7D7D] transition-all duration-300"></div>
                <i class="fa-solid fa-user-shield text-[20px] text-[#7D7D7D] group-hover:text-white relative z-10 transition-all duration-300"></i>
            </a>
            @endif
            @if(Auth::user()->role === 'hr')
                <a href="{{ route('hr.employees.index') }}" class="group relative flex items-center justify-center w-[40px] h-[40px]">
                    <div class="absolute w-[40px] h-[40px] rounded-full bg-[#F5F5F5] group-hover:bg-[#7D7D7D] transition-all duration-300"></div>
                    <i class="fa-solid fa-users text-[20px] text-[#7D7D7D] group-hover:text-white relative z-10 transition-all duration-300"></i>
                </a>
            @endif
        </div>
    </aside>

    <div class="flex-1 ml-[90px]">
        <header class="h-[70px] bg-white border-b border-[#E0E0E0] flex items-center px-4 sticky top-0 z-10">
            <div class="flex items-center justify-between w-full">
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/images/crocodic.png') }}" alt="Logo" class="w-[120px] h-[30px] object-contain"
                         onerror="this.onerror=null;this.src='{{ asset('images/default_logo.png') }}';" />
                </div>

                <div class="flex-1 max-w-[350px] mx-4">
                    <div class="flex items-center bg-white shadow-[0_0_4px_rgba(0,0,0,0.15)] rounded-[20px] px-[14px] py-[7px] h-[36px]">
                        <input type="text" placeholder="Search project" class="flex-1 text-[#7D7D7D] font-medium text-[13px] bg-transparent border-none outline-none">
                        <i class="fa-solid fa-magnifying-glass text-[#7D7D7D] text-[16px] cursor-pointer hover:text-[#6FAEC9] transition-colors"></i>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex flex-col items-end">
                        <span class="font-semibold text-[15px] text-[#111111]">{{ Auth::user()->name ?? 'User' }}</span>
                        <span class="font-medium text-[11px] text-[#7D7D7D]">{{ ucfirst(Auth::user()->role ?? 'Employee') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('employee.profile') }}" class="relative w-[36px] h-[36px] flex items-center justify-center group">
                            <div class="absolute w-[36px] h-[36px] rounded-full bg-[#F5F5F5] shadow-[0_0_4px_rgba(0,0,0,0.15)] group-hover:bg-[#E0E0E0] transition-all duration-300"></div>
                            <i class="fa-solid fa-user text-[22px] text-[#7D7D7D] group-hover:text-[#6FAEC9] transition-all duration-300"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="relative w-[36px] h-[36px] flex items-center justify-center group">
                                <div class="absolute w-[36px] h-[36px] rounded-full bg-[#F5F5F5] shadow-[0_0_4px_rgba(0,0,0,0.15)] group-hover:bg-[#E0E0E0] transition-all duration-300"></div>
                                <i class="fa-solid fa-right-from-bracket text-[22px] text-[#7D7D7D] group-hover:text-red-500 transition-all duration-300"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-4 space-y-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <div class="bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-users text-[15px] text-[#6FAEC9]"></i>
                        <span class="font-semibold text-[11px] text-[#7D7D7D]">Total Users</span>
                    </div>
                    <span class="font-bold text-[15px] text-[#111111]">{{ $totalUsers ?? ($totalEmployees ?? 0) }}</span>
                </div>

                <div class="bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-diagram-project text-[15px] text-[#FFB42E]"></i>
                        <span class="font-semibold text-[11px] text-[#7D7D7D]">Total Projects</span>
                    </div>
                    <span class="font-bold text-[15px] text-[#111111]">{{ $totalProjects ?? 0 }}</span>
                </div>

                <div class="bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-list-check text-[15px] text-[#7DB546]"></i>
                        <span class="font-semibold text-[11px] text-[#7D7D7D]">Total Tasks</span>
                    </div>
                    <span class="font-bold text-[15px] text-[#111111]">{{ $totalTasks ?? ($myTasks ?? 0) }}</span>
                </div>

                <div class="bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-book text-[15px] text-[#4397BB]"></i>
                        <span class="font-semibold text-[11px] text-[#7D7D7D]">Active Leaves</span>
                    </div>
                    <span class="font-bold text-[15px] text-[#111111]">{{ $activeLeaves ?? 0 }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                <div class="xl:col-span-2 space-y-4">
                    <div class="bg-white shadow-[0_0_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3">
                        <div class="flex flex-wrap gap-2 p-1">
                            {{-- Gunakan request()->input('tab', 'ready') untuk mengatur tab aktif awal --}}
                            <button type="button" class="tab-button px-5 py-1 rounded-[6px] text-[13px] font-medium transition-all duration-200
                                {{ request()->input('tab', 'ready') == 'ready' ? 'bg-[#111111] text-white shadow-[0_0_4px_rgba(0,0,0,0.10)]' : 'bg-white text-[#111111] border border-[#D9D9D9] hover:bg-gray-50' }}" data-tab="ready-users">
                                Ready
                            </button>
                            <button type="button" class="tab-button px-4 py-1 rounded-[6px] text-[13px] font-medium transition-all duration-200
                                {{ request()->input('tab') == 'standby' ? 'bg-[#111111] text-white shadow-[0_0_4px_rgba(0,0,0,0.10)]' : 'bg-white text-[#111111] border border-[#D9D9D9] hover:bg-gray-50' }}" data-tab="standby-users">
                                Stand by
                            </button>
                            <button type="button" class="tab-button px-3 py-1 rounded-[6px] text-[13px] font-medium transition-all duration-200
                                {{ request()->input('tab') == 'not-ready' ? 'bg-[#111111] text-white shadow-[0_0_4px_rgba(0,0,0,0.10)]' : 'bg-white text-[#111111] border border-[#D9D9D9] hover:bg-gray-50' }}" data-tab="not-ready-users">
                                Not ready
                            </button>
                            <button type="button" class="tab-button px-5 py-1 rounded-[6px] text-[13px] font-medium transition-all duration-200
                                {{ request()->input('tab') == 'complete' ? 'bg-[#111111] text-white shadow-[0_0_4px_rgba(0,0,0,0.10)]' : 'bg-white text-[#111111] border border-[#D9D9D9] hover:bg-gray-50' }}" data-tab="complete-users">
                                Complete
                            </button>
                            <button type="button" class="tab-button px-5 py-1 rounded-[6px] text-[13px] font-medium transition-all duration-200
                                {{ request()->input('tab') == 'absent' ? 'bg-[#111111] text-white shadow-[0_0_4px_rgba(0,0,0,0.10)]' : 'bg-white text-[#111111] border border-[#D9D9D9] hover:bg-gray-50' }}" data-tab="absent-users">
                                Absent
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div id="ready-users" class="tab-content col-span-full {{ request()->input('tab', 'ready') == 'ready' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($readyUsers as $userItem)
                                    @include('partials.user_card', ['userItem' => $userItem])
                                @empty
                                    <div class="col-span-full text-center py-4 text-[#7D7D7D]">No Ready users found.</div>
                                @endforelse
                            </div>
                        </div>

                        <div id="standby-users" class="tab-content col-span-full {{ request()->input('tab') == 'standby' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($standbyUsers as $userItem)
                                    @include('partials.user_card', ['userItem' => $userItem])
                                @empty
                                    <div class="col-span-full text-center py-4 text-[#7D7D7D]">No Stand by users found.</div>
                                @endforelse
                            </div>
                        </div>

                        <div id="not-ready-users" class="tab-content col-span-full {{ request()->input('tab') == 'not-ready' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($notReadyUsers as $userItem)
                                    @include('partials.user_card', ['userItem' => $userItem])
                                @empty
                                    <div class="col-span-full text-center py-4 text-[#7D7D7D]">No Not ready users found.</div>
                                @endforelse
                            </div>
                        </div>

                        <div id="complete-users" class="tab-content col-span-full {{ request()->input('tab') == 'complete' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($completeUsers as $userItem)
                                    @include('partials.user_card', ['userItem' => $userItem])
                                @empty
                                    <div class="col-span-full text-center py-4 text-[#7D7D7D]">No Complete users found.</div>
                                @endforelse
                            </div>
                        </div>

                        <div id="absent-users" class="tab-content col-span-full {{ request()->input('tab') == 'absent' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($absentUsers as $userItem)
                                    @include('partials.user_card', ['userItem' => $userItem])
                                @empty
                                    <div class="col-span-full text-center py-4 text-[#7D7D7D]">No Absent users found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-1 space-y-4">
                    <div class="bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3 border-l-4 border-[#7DB546]">
                        <div class="flex items-center gap-1 mb-3">
                            <i class="fa-solid fa-list-check text-[18px] text-[#7D7D7D]"></i>
                            <span class="font-semibold text-[13px] text-[#7D7D7D]">Tasks</span>
                        </div>
                        @if(isset($userTasks) && $userTasks instanceof \Illuminate\Database\Eloquent\Collection && $userTasks->count() > 0)
                            <ul class="mt-2 space-y-2">
                                @foreach($userTasks->take(2) as $task) {{-- Only two tasks like in the image --}}
                                    @if(is_object($task) && property_exists($task, 'task_id'))
                                        <li class="bg-[#F5F5F5] rounded px-3 py-2 text-[12px] flex flex-col">
                                            <span class="font-bold text-[#111111]">{{ $task->task_name ?? 'No Task Name' }}</span>
                                            <span class="text-xs text-[#7D7D7D] mt-1">{{ $task->description ?? 'No description.' }}</span>
                                            <div class="flex mt-2 items-center justify-between">
                                                <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-semibold
                                                    @php
                                                        $priorityStatus = $task->priority ?? $task->status ?? 'unknown';
                                                        $priorityClass = '';
                                                        if ($priorityStatus === 'Low') $priorityClass = 'bg-blue-500 text-white';
                                                        elseif ($priorityStatus === 'Medium') $priorityClass = 'bg-orange-500 text-white';
                                                        elseif ($priorityStatus === 'High') $priorityClass = 'bg-red-500 text-white';
                                                        elseif ($priorityStatus === 'completed') $priorityClass = 'bg-[#7DB546] text-white';
                                                        else $priorityClass = 'bg-gray-400 text-white'; // Default
                                                    @endphp
                                                    {{ $priorityClass }}
                                                    ">
                                                    {{ ucfirst($priorityStatus) }}
                                                </span>
                                                @php
                                                    $taskDetailRoute = match(Auth::user()->role) {
                                                        'admin' => 'admin.tasks.show',
                                                        'hr' => 'hr.tasks.show',
                                                        'employee' => 'employee.tasks.show',
                                                        default => null
                                                    };
                                                @endphp
                                                @if($taskDetailRoute)
                                                    <a href="{{ route($taskDetailRoute, $task->task_id) }}" class="text-xs text-[#6FAEC9] underline">Detail</a>
                                                @endif
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <div class="text-xs mt-2">No tasks found.</div>
                        @endif
                    </div>

                    <div class="bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3 border-l-4 border-[#FFB42E]">
                        <div class="flex items-center gap-1 mb-3">
                            <i class="fa-solid fa-diagram-project text-[18px] text-[#7D7D7D]"></i>
                            <span class="font-semibold text-[13px] text-[#7D7D7D]">Project</span>
                        </div>
                        @if(isset($userProjects) && $userProjects instanceof \Illuminate\Database\Eloquent\Collection && $userProjects->count() > 0)
                            <ul class="mt-2 space-y-2">
                                @foreach($userProjects->take(1) as $project) {{-- Only one project like in the image --}}
                                    @if(is_object($project) && property_exists($project, 'project_id'))
                                        <li class="bg-[#F5F5F5] rounded px-3 py-2 text-[12px] flex flex-col">
                                            <span class="font-bold text-[#111111]">{{ $project->project_name ?? 'No Project Name' }}</span>
                                            <span class="text-xs text-[#7D7D7D] mt-1">{{ $project->level ? 'Level: ' . ucfirst($project->level) : 'No description.' }}</span>
                                            <div class="flex mt-2 items-center justify-between">
                                                <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-semibold
                                                    @php
                                                        $projectStatus = $project->status ?? 'unknown';
                                                        $projectStatusClass = '';
                                                        if ($projectStatus === 'On create') $projectStatusClass = 'bg-red-500 text-white';
                                                        elseif ($projectStatus === 'In Progress') $projectStatusClass = 'bg-orange-500 text-white';
                                                        elseif ($projectStatus === 'Completed') $projectStatusClass = 'bg-[#7DB546] text-white';
                                                        else $projectStatusClass = 'bg-gray-400 text-white'; // Default
                                                    @endphp
                                                    {{ $projectStatusClass }}
                                                    ">
                                                    {{ ucfirst($projectStatus) }}
                                                </span>
                                                <a href="{{ route('projects.show', $project->project_id) }}" class="text-xs text-[#6FAEC9] underline">Detail</a>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <div class="text-xs mt-2">No projects found.</div>
                        @endif
                    </div>

                    <div class="bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3">
                        <div class="flex items-center gap-1 mb-3">
                            <i class="fa-solid fa-chart-line text-[18px] text-[#7D7D7D]"></i>
                            <h2 class="text-[#7D7D7D] font-semibold text-[16px]">Activity</h2>
                        </div>
                        <div class="h-40">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartElement = document.getElementById('activityChart');
    if (chartElement) {
        const ctx = chartElement.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Activity Hours',
                    // Perbaikan untuk memastikan data selalu array dan valid
                    data: {!! json_encode(isset($activityChartData) && is_array($activityChartData) ? $activityChartData : [0,0,0,0,0,0,0,0,0,0,0,0]) !!},
                    borderColor: '#6FAEC9',
                    backgroundColor: 'rgba(111, 174, 201, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6FAEC9',
                    pointBorderColor: '#6FAEC9',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#7D7D7D',
                            font: {
                                size: 10,
                                family: 'Arial',
                                weight: 600
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E0E0E0' },
                        ticks: {
                            color: '#7D7D7D',
                            font: {
                                size: 10,
                                family: 'Arial',
                                weight: 600
                            }
                        }
                    }
                }
            }
        });
    }

    // Tab functionality for user cards
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');

            // Update button styles
            tabButtons.forEach(btn => {
                btn.classList.remove('bg-[#111111]', 'text-white', 'shadow-[0_0_4px_rgba(0,0,0,0.10)]');
                btn.classList.add('bg-white', 'text-[#111111]', 'border', 'border-[#D9D9D9]', 'hover:bg-gray-50');
            });

            button.classList.add('bg-[#111111]', 'text-white', 'shadow-[0_0_4px_rgba(0,0,0,0.10)]');
            button.classList.remove('bg-white', 'text-[#111111]', 'border', 'border-[#D9D9D9]', 'hover:bg-gray-50');

            // Update content visibility
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            document.getElementById(tabId).classList.remove('hidden');
        });
    });

    // Initialize the active tab based on URL parameter on page load
    const urlParams = new URLSearchParams(window.location.search);
    const activeTabParam = urlParams.get('tab');
    if (activeTabParam) {
        const initialButton = document.querySelector(`.tab-button[data-tab="${activeTabParam}-users"]`);
        if (initialButton) {
            initialButton.click(); // Simulate click to activate tab
        }
    } else {
        // Default to 'ready' tab if no parameter
        const defaultButton = document.querySelector(`.tab-button[data-tab="ready-users"]`);
        if (defaultButton) {
            defaultButton.click();
        }
    }
});
</script>

{{-- Partial for User Card to avoid repetition --}}
@if(!function_exists('renderUserCardPartial'))
    @php
    function renderUserCardPartial($userItem) {
        $activityDescription = 'Working on nothing special';
        $lastActivity = $userItem->activities->first(); // Mengambil yang sudah di-eager load (take(1) di controller)

        if ($lastActivity) {
            $activityDescText = $lastActivity->note ?? 'an activity';
            $relatedTask = $lastActivity->relatedTask; // Sudah di-eager load
            $relatedProject = $lastActivity->relatedProject; // Sudah di-eager load

            if ($relatedTask) {
                $activityDescription = 'Working on ' . ($relatedTask->task_name ?? 'a task');
            } elseif ($relatedProject) {
                $activityDescription = 'Working on Project ' . ($relatedProject->project_name ?? 'a project');
            } else {
                $activityDescription = $activityDescText;
            }
        }

        $userShowRoute = '#';
        if (Auth::user()->role === 'admin' && Route::has('admin.users.show')) {
            $userShowRoute = route('admin.users.show', $userItem->user_id);
        } elseif (Auth::user()->role === 'hr' && Route::has('hr.employees.show')) {
            $userShowRoute = route('hr.employees.show', $userItem->user_id);
        } elseif (Auth::user()->role === 'employee' && Auth::user()->user_id === $userItem->user_id && Route::has('employee.profile')) {
            $userShowRoute = route('employee.profile');
        }

        $statusClass = '';
        $userStatus = $userItem->status ?? 'Ready';
        if ($userStatus === 'Ready') $statusClass = 'bg-[#6FAEC9] text-white';
        elseif ($userStatus === 'Stand by') $statusClass = 'bg-[#FFB42E] text-white';
        elseif ($userStatus === 'Not ready') $statusClass = 'bg-red-500 text-white';
        elseif ($userStatus === 'Complete') $statusClass = 'bg-[#7DB546] text-white';
        elseif ($userStatus === 'Absent') $statusClass = 'bg-gray-500 text-white';
        else $statusClass = 'bg-gray-300 text-[#111111]';

        echo "
        <div class='bg-white shadow-[0_4px_4px_rgba(0,0,0,0.10)] rounded-[10px] p-3 flex flex-col items-center text-center'>
            <img src='" . asset('storage/profile_pictures/' . ($userItem->image ?? 'default_avatar.png')) . "' alt='Avatar' class='w-[60px] h-[60px] rounded-full object-cover mb-2'>
            <h4 class='font-bold text-[15px] text-[#111111]'>" . ($userItem->name ?? 'User Name') . "</h4>
            <p class='font-medium text-[11px] text-[#7D7D7D]'>" . (ucfirst($userItem->role ?? 'Role')) . "</p>
            <p class='font-medium text-[11px] text-[#7D7D7D] mt-1'>
                " . $activityDescription . "
            </p>
            <div class='flex mt-2 gap-2'>
                <span class='px-3 py-1 rounded-[6px] text-[10px] font-semibold " . $statusClass . "'>
                    " . ($userItem->status ?? 'N/A') . "
                </span>
                <a href='" . $userShowRoute . "' class='px-3 py-1 rounded-[6px] text-[10px] font-semibold bg-[#F5F5F5] text-[#7D7D7D] hover:bg-[#E0E0E0] transition-colors'>
                    Review
                </a>
            </div>
        </div>
        ";
    }
    @endphp
@endif
@section('content')
