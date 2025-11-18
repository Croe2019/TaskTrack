@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">

    {{-- 🔍 検索エリア --}}
    <div class="bg-white p-4 rounded-xl shadow mb-6">
        <form class="flex flex-col md:flex-row items-center gap-3">

            <input
                type="text"
                name="keyword"
                placeholder="タイトル or タグ or 状態"
                class="w-full md:w-2/3 p-2 border rounded-lg"
            >

            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                🔍 検索
            </button>
        </form>

        {{-- 📅 フィルタ --}}
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="#" class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300">全て</a>
            <a href="#" class="px-3 py-1 bg-blue-200 rounded-lg hover:bg-blue-300">進行中</a>
            <a href="#" class="px-3 py-1 bg-green-200 rounded-lg hover:bg-green-300">完了済み</a>
            <a href="#" class="px-3 py-1 bg-red-200 rounded-lg hover:bg-red-300">期限切れ</a>
        </div>
    </div>

    {{-- ＋ 新規追加ボタン --}}
    <div class="flex justify-end mb-6">
        <button
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
            onclick="openTaskModal()"
        >
            ＋ 新規タスク追加
        </button>
    </div>

    {{-- タスク一覧カード --}}
    <div class="space-y-4">

        {{-- タスクカード 1 --}}
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-400">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <input type="checkbox" class="w-4 h-4">
                    <h2 class="text-lg font-bold">API連携設計</h2>
                </div>
                <span class="text-sm bg-yellow-200 px-2 py-1 rounded">優先度：高</span>
            </div>

            <p class="text-gray-600 mt-1">期限：10/15　状態：進行中</p>

            <div class="flex items-center gap-4 mt-2 text-sm text-gray-700">
                <span>💬 コメント(3)</span>
                <span>📎 添付1件</span>
            </div>

            <div class="flex gap-2 mt-3">
                <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">編集</button>
                <button class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">完了</button>
                <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">削除</button>
            </div>
        </div>

        {{-- タスクカード 2 --}}
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-400">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <input type="checkbox" checked class="w-4 h-4">
                    <h2 class="text-lg font-bold line-through text-gray-500">UIデザイン整理</h2>
                </div>
                <span class="text-sm bg-green-200 px-2 py-1 rounded">優先度：中</span>
            </div>

            <p class="text-gray-600 mt-1">完了済み</p>

            <div class="flex items-center gap-4 mt-2 text-sm text-gray-700">
                <span>💬 コメント(1)</span>
            </div>
        </div>

    </div>

</div>

{{-- モーダルは後で追加予定 --}}

@endsection
