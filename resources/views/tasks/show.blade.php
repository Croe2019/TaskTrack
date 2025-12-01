@extends('layouts.app')

@section('content')
<div class="container show-wrapper">

    <h2 class="title">{{ $task->title }}</h2>

    <div class="task-detail">

        <p><strong>詳細内容:</strong></p>
        <p class="box">{{ $task->description }}</p>

        <p><strong>ステータス:</strong> {{ $task->status_label }}</p>
        <p><strong>優先度:</strong> {{ $task->priority_label }}</p>
        <p><strong>期限:</strong> {{ $task->deadline?->format('Y-m-d') ?? '未設定' }}</p>

        <p><strong>タグ:</strong></p>
        <div class="tag-list">
            @foreach ($task->tags as $tag)
                <span class="tag-item">{{ $tag->name }}</span>
            @endforeach
        </div>

        <h3>添付ファイル</h3>
        <ul>
            @foreach($task->attachments as $attachment)
                <li>
                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                        {{ $attachment->original_name }}
                    </a>
                </li>
            @endforeach
        </ul>


        <div class="button-area">
            <a href="{{ route('tasks.edit', $task->id) }}" class="btn-edit">編集</a>
            <a href="{{ route('tasks.index') }}" class="btn-back">戻る</a>
        </div>

    </div>

</div>

<style>
.show-wrapper {
    max-width: 650px;
    margin: auto;
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.title {
    font-size: 24px;
    margin-bottom: 20px;
    border-bottom: 2px solid #ddd;
    padding-bottom: 10px;
}
.box {
    background:#f3f4f6;
    padding:10px;
    border-radius:6px;
}
.tag-list {
    display:flex;
    flex-wrap:wrap;
    gap:10px;
}
.tag-item {
    background:#e1e1e1;
    padding:6px 10px;
    border-radius:4px;
}
.button-area {
    margin-top:20px;
    display:flex;
    gap:12px;
}
.btn-edit {
    padding:10px 20px;
    background:#3b82f6;
    color:white;
    border-radius:6px;
}
.btn-back {
    padding:10px 20px;
    background:#aaa;
    color:white;
    border-radius:6px;
}
</style>
@endsection
