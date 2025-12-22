@extends('layouts.app')

@section('content')
<div class="container" style="max-width:960px; margin:auto; padding:20px;">
    <h2>{{ $team->name }} ダッシュボード</h2>

    <div style="display:flex; gap:12px; margin-top:12px;">
        <div style="flex:1; padding:12px; background:#fff; border-radius:8px;">
            <small>完了タスク数（期間）</small>
            <h3>{{ $summary['completed_count'] ?? 0 }}</h3>
        </div>
        <div style="flex:1; padding:12px; background:#fff; border-radius:8px;">
            <small>合計作業時間（分）</small>
            <h3>{{ $summary['total_minutes'] ?? 0 }}</h3>
        </div>
    </div>

    <div style="margin-top:20px; background:#fff; padding:12px; border-radius:8px;">
        <canvas id="teamChart" style="width:100%; height:300px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('teamChart').getContext('2d');
    const labels = @json($chart['labels'] ?? []);
    const data = @json($chart['data'] ?? []);
    new Chart(ctx, {
        type:'bar',
        data:{
            labels,
            datasets:[{ label: '完了タスク数', data }]
        }
    });
</script>
@endsection
