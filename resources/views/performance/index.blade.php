@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h1 class="mb-4">月次実績（個人）</h1>

    {{-- 月選択 --}}
    <form method="GET" class="flex gap-4 items-end mb-6">
        <div>
            <label>月</label>
            <input type="month" name="month" value="{{ $month }}" class="border px-2 py-1 rounded">
        </div>

        <div>
            <label>タグ</label>
            <select name="tag_id" class="border px-2 py-1 rounded">
                <option value="">すべて</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}"
                        @if($selectedTag == $tag->id) selected @endif>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>優先度</label>
            <select name="priority" class="border px-2 py-1 rounded">
                <option value="">すべて</option>
                <option value="high"   @selected($selectedPriority=='high')>高</option>
                <option value="medium" @selected($selectedPriority=='medium')>中</option>
                <option value="low"    @selected($selectedPriority=='low')>低</option>
            </select>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">フィルタ</button>
    </form>


    {{-- 集計カード --}}
    <table class="table">
        <thead>
            <tr>
                <th>日付</th>
                <th>完了タスク数</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dailyCounts as $date => $count)
                <tr>
                    <td>{{ $date }}</td>
                    <td>{{ $count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- グラフ --}}
    <div class="card p-4 mb-4 h-64 md:h-80">
        <canvas id="taskChart"></canvas>
    </div>

    {{-- 一覧 --}}
    <h4 class="mb-3">完了タスク一覧</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>タイトル</th>
                <th>完了日</th>
                <th>作業時間(分)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allTasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ optional($task->completed_at)->format('Y-m-d') }}</td>
                    <td>{{ $task->worked_minutes }}分</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex gap-4 mb-4">
        <a href="{{ route('performance.export.csv') }}" class="text-blue-600">CSVエクスポート</a>
        <a href="{{ route('performance.export.excel') }}" class="text-green-600">Excelエクスポート</a>
    </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
     const taskChart = new Chart(document.getElementById('taskChart'), {
        type: 'line',
        data: {
            labels: @json($dailyCounts->keys()),
            datasets: [{
                label: '完了タスク数',
                 data: @json($dailyCounts->values()),
                borderWidth: 2
            }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
        }
    });
    window.addEventListener('resize', () => taskChart.resize());
</script>

@endsection
