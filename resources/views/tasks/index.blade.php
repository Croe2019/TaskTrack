@extends('layouts.app')

@section('content')

<style>
.task-card {
    background: #fff;
    border-left: 4px solid #facc15; /* 黄色 */
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 12px;
}

.task-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.priority {
    background: #fde68a; /* 黄色 */
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}

.task-info {
    margin-top: 4px;
    color: #555;
}

.task-meta {
    margin-top: 6px;
    font-size: 12px;
    color: #444;
    display: flex;
    gap: 8px;
}

.task-buttons {
    margin-top: 8px;
    display: flex;
    gap: 6px;
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    color: #fff;
}

.btn-edit {
    background-color: #3b82f6; /* 青 */
}

.btn-complete {
    background-color: #22c55e; /* 緑 */
}

.btn-delete {
    background-color: #ef4444; /* 赤 */
}

.btn-edit:hover {
    background-color: #2563eb;
}
.btn-complete:hover {
    background-color: #16a34a;
}
.btn-delete:hover {
    background-color: #dc2626;
}

</style>

<div class="max-w-6xl mx-auto p-6">

    {{-- 🔍 検索・フィルタ --}}
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

        {{-- フィルタ --}}
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="#" class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300">全て</a>
            <a href="#" class="px-3 py-1 bg-blue-200 rounded-lg hover:bg-blue-300">進行中</a>
            <a href="#" class="px-3 py-1 bg-green-200 rounded-lg hover:bg-green-300">完了済み</a>
            <a href="#" class="px-3 py-1 bg-red-200 rounded-lg hover:bg-red-300">期限切れ</a>
        </div>
    </div>

    {{-- ＋ 新規タスク追加ボタン --}}
    <div style="text-align: right; margin-bottom: 1.5rem;">
        <button
            style="
                background-color: #16a34a;  /* 緑色 */
                color: white;               /* 文字色 */
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                border: none;
                cursor: pointer;
            "
            onmouseover="this.style.backgroundColor='#15803d';"
            onmouseout="this.style.backgroundColor='#16a34a';"
            type="button"
        >
            <a href="{{route('tasks.create')}}"> ＋ 新規タスク追加 </a>
        </button>
    </div>


    {{-- タスク一覧 --}}
    <div class="space-y-4">
        {{-- タスクカード 1 --}}
        @foreach ($tasks as $task)
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-400">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" class="w-4 h-4">
                        <h2 class="text-lg font-bold">{{ $task->title }}</h2>
                    </div>
                    <span class="text-sm bg-yellow-200 px-2 py-1 rounded">優先度：{{ $task->priority_label  }}</span>
                </div>
                <p class="text-gray-600 mt-1">期限：{{ $task->deadline }}　状態：{{ $task->status_label }}</p>
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-700">
                    <span>💬 コメント(3)</span>
                    <span>📎 添付1件</span>
                </div>
                 <div class="task-buttons">
                    <button class="btn btn-edit"><a href="{{ route('tasks.show', ['id' => $task->id]) }}"> 編集 </a></button>
                    <form action="{{ route('tasks.complete', $task->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-complete">
                            完了
                        </button>
                    </form>

                    <form method="POST" action="{{ route('tasks.destroy', $task->id) }}">
                        @csrf
                        @method('DELETE') <!-- ここでDELETEメソッドを指定 -->

                        <button type="submit" class="btn btn-delete" onclick="return confirm('本当に削除しますか？')">削除</button>
                    </form>

                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
