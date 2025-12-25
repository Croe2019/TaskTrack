{{-- resources/views/teams/tasks/dashboard.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-end justify-between">
            <h1 class="text-xl font-semibold">ダッシュボード</h1>
            <div class="text-sm text-gray-600">
                完了タスク合計：<span class="font-semibold">{{ $summary['completed_count'] }}</span>
                ／ 合計作業時間：
                <span class="font-semibold">{{ $summary['total_minutes'] }}</span> 分
                （{{ intdiv($summary['total_minutes'], 60) }}h {{ $summary['total_minutes'] % 60 }}m）
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <div class="p-4 bg-white border rounded min-w-full">
                <canvas id="teamTasksChart" class="w-full h-64"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const labels = @json($chart['labels']);
        const doneCounts = @json($chart['done_counts']);
        const minutes = @json($chart['minutes']);

        const ctx = document.getElementById('teamTasksChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: '完了タスク数',
                        data: doneCounts,
                        yAxisID: 'y',
                    },
                    {
                        label: '作業時間（分）',
                        data: minutes,
                        type: 'line',
                        yAxisID: 'y1',
                        tension: 0.2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: '完了タスク数' }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: '作業時間（分）' }
                    }
                }
            }
        });
    </script>
@endsection
