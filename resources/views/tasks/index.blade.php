@extends('layouts.app')

@section('content')
<style>
/* 全体コンテナ */
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}

/* 検索・フィルタ */
.search-filter {
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.search-filter form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.search-filter input[type="text"] {
    flex: 1;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.search-filter button {
    padding: 8px 15px;
    border-radius: 6px;
    border: none;
    background: #1d4ed8;
    color: #fff;
    cursor: pointer;
}

.search-filter button:hover {
    background: #2563eb;
}

.filters a {
    display: inline-block;
    margin: 5px 5px 0 0;
    padding: 5px 10px;
    border-radius: 6px;
    background: #e5e7eb;
    text-decoration: none;
    color: #000;
}

.filters a:hover {
    background: #d1d5db;
}

/* 新規タスクボタン */
.add-task {
    text-align: right;
    margin-bottom: 20px;
}

.add-task a {
    display: inline-block;
    background: #16a34a;
    color: #fff;
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
}

.add-task a:hover {
    background: #15803d;
}

/* タスクカード */
.task-card {
    background: #fff;
    padding: 15px;
    border-left: 5px solid #facc15;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 15px;
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.task-header h2 {
    margin: 0;
}

.priority-label {
    background: #fde68a;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}

.task-meta {
    margin-top: 5px;
    color: #555;
}

.task-buttons {
    margin-top: 10px;
    display: flex;
    gap: 5px;
}

.task-buttons a,
.task-buttons form button {
    padding: 5px 10px;
    border-radius: 6px;
    border: none;
    color: #fff;
    text-decoration: none;
    cursor: pointer;
    font-size: 12px;
}

.task-buttons a.edit { background: #3b82f6; }
.task-buttons a.edit:hover { background: #2563eb; }

.task-buttons form button.complete { background: #22c55e; }
.task-buttons form button.complete:hover { background: #16a34a; }

.task-buttons form button.delete { background: #ef4444; }
.task-buttons form button.delete:hover { background: #dc2626; }

/* コメント */
.comment {
    background: #f3f4f6;
    padding: 5px 10px;
    border-radius: 6px;
    margin-top: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comment-author {
    font-weight: bold;
}

.comment-actions a,
.comment-actions form button {
    font-size: 12px;
    color: #1d4ed8;
    background: none;
    border: none;
    padding: 0;
    margin-left: 5px;
    cursor: pointer;
    text-decoration: underline;
}

.comment-actions form button:hover,
.comment-actions a:hover {
    color: #2563eb;
}

.add-comment {
    margin-top: 5px;
    display: flex;
    gap: 5px;
}

.add-comment input[type="text"] {
    flex: 1;
    padding: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.add-comment button {
    padding: 5px 10px;
    border-radius: 6px;
    border: none;
    background: #1d4ed8;
    color: #fff;
    cursor: pointer;
}

.add-comment button:hover {
    background: #2563eb;
}
</style>

<div class="container">

    <!-- 検索・フィルタ -->
    <div class="search-filter">
        <form>
            <input type="text" placeholder="タイトル or タグ or 状態">
            <button type="submit">検索</button>
        </form>
        <div class="filters">
            <a href="#">全て</a>
            <a href="#">進行中</a>
            <a href="#">完了済み</a>
            <a href="#">期限切れ</a>
        </div>
    </div>

    <!-- 新規タスク -->
    <div class="add-task">
        <a href="{{ route('tasks.create') }}">＋ 新規タスク追加</a>
    </div>

    <!-- タスク一覧 -->
    @foreach($tasks as $task)
    <div class="task-card">
        <div class="task-header">
            <h2>{{ $task->title }}</h2>
            <span class="priority-label">優先度: {{ $task->priority_label }}</span>
        </div>
        <div class="task-meta">
            期限: {{ $task->deadline ? $task->deadline->format('Y-m-d') : '未設定' }} | 状態: {{ $task->status_label }}
        </div>


        <!-- コメント一覧 -->
        @foreach($task->comments as $comment)
        <div class="comment">
            <div>
                <span class="comment-author">{{ $comment->user->name }}:</span> {{ $comment->content }}
            </div>
            @if($comment->user_id === auth()->id())
            <div class="comment-actions">
                <a href="{{ route('comments.update', $comment->id) }}">編集</a>
                <form method="POST" action="{{ route('comments.destroy', $comment->id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('コメントを削除しますか？')">削除</button>
                </form>
            </div>
            @endif
        </div>
        @endforeach

        <!-- コメント追加 -->
        @auth
        <form method="POST" action="{{ route('comments.store', $task->id) }}" class="add-comment">
            @csrf
            <input type="text" name="content" placeholder="コメントを追加" required>
            <button type="submit">追加</button>
        </form>
        @endauth

        <!-- タスク操作 -->
        <div class="task-buttons">
            @if($task->user_id === auth()->id())
                <a href="{{ route('tasks.update', $task->id) }}" class="edit">編集</a>

                <form method="POST" action="{{ route('tasks.destroy', $task->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete" onclick="return confirm('タスクを削除しますか？')">削除</button>
                </form>
            @endif

            @if($task->user_id === auth()->id())
                @if($task->status !== 'completed')
                <form method="POST" action="{{ route('tasks.complete', $task->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="complete">完了</button>
                </form>
                @endif
            @endif
        </div>
    </div>
    @endforeach

</div>
@endsection
