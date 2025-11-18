@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">Dashboard - 概要</h1>

    <div class="bg-white p-5 rounded-xl shadow mb-6">
        <p class="text-lg font-semibold">
            ✔ 今月のタスク： <span class="font-bold text-blue-600">24件</span>　
            完了： <span class="font-bold text-green-600">18件</span>　
            完了率： <span class="font-bold text-purple-600">75%</span>
        </p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-3">📊 月別完了数の棒グラフ</h2>
        <div class="w-full h-64">
            <canvas id="taskChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-4">🕓 最近完了したタスク（5件）</h2>

        <ul class="space-y-3">
            <li class="border-b pb-2">
                ・「APIエンドポイント整備」 ✅ <span class="text-gray-600">10/10 完了</span>
            </li>
            <li class="border-b pb-2">
                ・「UI改善」 ✅ <span class="text-gray-600">10/09 完了</span>
            </li>
            <li class="border-b pb-2">
                ・「DBマイグレーション整理」 ✅ <span class="text-gray-600">10/08 完了</span>
            </li>
            <li class="border-b pb-2">
                ・「メール通知実装」 ✅ <span class="text-gray-600">10/07 完了</span>
            </li>
            <li>
                ・「デザイン調整」 ✅ <span class="text-gray-600">10/06 完了</span>
            </li>
        </ul>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('taskChart').getContext('2d');

    const taskChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['4月', '5月', '6月', '7月', '8月', '9月'],
            datasets: [{
                label: '完了タスク数',
                data: [12, 19, 5, 17, 23, 18],
                borderWidth: 1,
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

@endsection
