@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-[#FAFAFA] flex">
    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col items-center py-8 bg-white border-r border-[#E0E0E0] w-20 min-h-screen z-20">
        <div class="flex flex-col items-center gap-10 mt-8">
            <a href="{{ route('dashboard') }}" class="text-[#6FAEC9] text-2xl transition-colors duration-300 hover:text-[#111111] hover:bg-[#E0E0E0] rounded-lg p-3 mb-2 animate__animated animate__fadeInLeft" title="Dashboard">
                <i class="fa-solid fa-gauge-high"></i>
            </a>
            <a href="{{ route('projects.index') }}" class="text-[#7D7D7D] text-2xl transition-colors duration-300 hover:text-[#FFB42E] hover:bg-[#FFF7E0] rounded-lg p-3 mb-2 animate__animated animate__fadeInLeft" title="Projects">
                <i class="fa-solid fa-diagram-project"></i>
            </a>
            <a href="{{ route('employee.tasks.index') }}" class="text-[#7D7D7D] text-2xl transition-colors duration-300 hover:text-[#7DB546] hover:bg-[#F0F8EC] rounded-lg p-3 mb-2 animate__animated animate__fadeInLeft" title="Tasks">
                <i class="fa-solid fa-list-check"></i>
            </a>
            <a href="{{ route('employee.activities.index') }}" class="text-[#7D7D7D] text-2xl transition-colors duration-300 hover:text-[#4397BB] hover:bg-[#E6F4FA] rounded-lg p-3 mb-2 animate__animated animate__fadeInLeft" title="Activity">
                <i class="fa-solid fa-chart-line"></i>
            </a>
            <a href="{{ route('employee.leaves.index') }}" class="text-[#7D7D7D] text-2xl transition-colors duration-300 hover:text-[#6FAEC9] hover:bg-[#E6F4FA] rounded-lg p-3 mb-2 animate__animated animate__fadeInLeft" title="Leaves">
                <i class="fa-solid fa-book"></i>
            </a>
            <a href="{{ route('admin.users.index') }}" class="text-[#7D7D7D] text-2xl transition-colors duration-300 hover:text-[#7D7D7D] hover:bg-[#F5F5F5] rounded-lg p-3 mb-2 animate__animated animate__fadeInLeft" title="Users">
                <i class="fa-solid fa-user-shield"></i>
            </a>
        </div>
    </aside>
    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Header -->
        <header class="w-full h-20 bg-white border-b border-[#E0E0E0] flex items-center px-8 justify-between sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <img src="{{ asset('storage/image/crocodic.png') }}" alt="Logo" class="h-10 w-auto" />
            </div>
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end">
                    <span class="font-semibold text-lg text-[#111111] animate__animated animate__fadeInDown">{{ Auth::user()->name }}</span>
                    <span class="text-sm text-[#7D7D7D]">{{ ucfirst(Auth::user()->role) }}</span>
                </div>
                <a href="{{ route('employee.profile') }}" class="w-12 h-12 rounded-full bg-[#F5F5F5] flex items-center justify-center shadow hover:bg-[#E0E0E0] transition-colors duration-300">
                    <i class="fa-solid fa-user text-2xl text-[#7D7D7D]"></i>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-12 h-12 rounded-full bg-[#F5F5F5] flex items-center justify-center shadow hover:bg-[#E0E0E0] transition-colors duration-300">
                        <i class="fa-solid fa-right-from-bracket text-2xl text-[#7D7D7D]"></i>
                    </button>
                </form>
            </div>
        </header>
        <!-- Content -->
        @yield('dashboard_content')
    </div>
    <!-- Chart.js & AOS Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartElement = document.getElementById('activityChart');
            if (chartElement) {
                const ctx = chartElement.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Jam Kerja',
                            data: {!! json_encode($activityChartData ?? [40, 28, 34, 52, 27, 17, 38, 36, 11, 54, 42, 2]) !!},
                            borderColor: '#6FAEC9',
                            backgroundColor: 'rgba(111, 174, 201, 0.2)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#6FAEC9',
                            pointBorderColor: '#6FAEC9',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#7D7D7D', font: { size: 14, family: 'Montserrat', weight: 600 } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#E0E0E0' },
                                ticks: { color: '#7D7D7D', font: { size: 14, family: 'Montserrat', weight: 600 } }
                            }
                        }
                    }
                });
            }
            
            // Inisialisasi AOS jika tersedia
            if (typeof AOS !== 'undefined') {
                AOS.init();
            }
        });
    </script>
</div>
@endsection

@section('dashboard_content')
<div class="p-8">
    <!-- Chart Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Grafik Aktivitas</h2>
        <canvas id="activityChart" width="400" height="200"></canvas>
    </div>

    <h2 class="text-xl font-bold mb-4">Tugas Terbaru</h2>
    @if(isset($recentTasks) && count($recentTasks) > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 font-semibold">Nama Tugas</th>
                            <th class="text-left py-3 px-4 font-semibold">Proyek</th>
                            <th class="text-left py-3 px-4 font-semibold">Status</th>
                            <th class="text-left py-3 px-4 font-semibold">Prioritas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTasks as $task)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $task->task_name }}</td>
                            <td class="py-3 px-4">{{ $task->project->project_name ?? 'N/A' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    @if($task->status == 'completed') bg-green-100 text-green-800
                                    @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    @if($task->priority == 'high') bg-red-100 text-red-800
                                    @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center text-gray-500 my-8">Tidak ada data tugas terbaru.</div>
    @endif

    <h2 class="text-xl font-bold mb-4 mt-8">Proyek Terbaru</h2>
    @if(isset($recentProjects) && count($recentProjects) > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 font-semibold">Nama Proyek</th>
                            <th class="text-left py-3 px-4 font-semibold">Level</th>
                            <th class="text-left py-3 px-4 font-semibold">Status</th>
                            <th class="text-left py-3 px-4 font-semibold">Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentProjects as $project)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $project->project_name }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    @if($project->level == 'easy') bg-green-100 text-green-800
                                    @elseif($project->level == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($project->level) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    @if($project->status == 'completed') bg-green-100 text-green-800
                                    @elseif($project->status == 'ongoing') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">{{ $project->deadline ? $project->deadline->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center text-gray-500 my-8">Tidak ada data proyek terbaru.</div>
    @endif
</div>
@endsection
