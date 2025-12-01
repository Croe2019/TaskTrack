@extends('layouts.app')

@section('content')
<div class="container edit-wrapper">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="title">タスク編集</h2>

    <form method="POST" action="{{ route('tasks.update', $task->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- タイトル -->
        <div class="form-group">
            <label>タイトル</label>
            <input type="text" name="title" value="{{ old('title', $task->title) }}" required>
        </div>

        <!-- 詳細 -->
        <div class="form-group">
            <label>詳細内容</label>
            <textarea name="description" required>{{ old('description', $task->description) }}</textarea>
        </div>

        <!-- ステータス -->
        <div class="form-group">
            <label>ステータス</label>
            <select name="status">
                <option value="not_started"  @selected($task->status === 'not_started')>未着手</option>
                <option value="in_progress"  @selected($task->status === 'in_progress')>進行中</option>
                <option value="completed"    @selected($task->status === 'completed')>完了</option>
            </select>
        </div>

        <!-- 優先度 -->
        <div class="form-group">
            <label>優先度</label>
            <select name="priority">
                <option value="high"   @selected($task->priority === 'high')>高</option>
                <option value="medium" @selected($task->priority === 'medium')>中</option>
                <option value="low"    @selected($task->priority === 'low')>低</option>
            </select>
        </div>

        <!-- 期限 -->
        <div class="form-group">
            <label>期限</label>
            <input type="date" name="deadline" value="{{ old('deadline', $task->deadline?->format('Y-m-d')) }}">
        </div>

        <!-- タグ -->
        <h3 class="subtitle">タグ</h3>
        <div class="tag-list">
            @foreach ($tags as $tag)
                <label class="tag-item">
                    <input
                        type="checkbox"
                        name="tag_ids[]"
                        value="{{ $tag->id }}"
                        @checked($task->tags->contains($tag->id))
                    >
                    {{ $tag->name }}
                </label>
            @endforeach
        </div>

        <!-- 新規タグ -->
        <div class="form-group">
            <label>新しいタグ（カンマ区切り）</label>
            <input type="text" name="tags" value="{{ old('tags') }}">
        </div>

        @if ($task->attachments->isNotEmpty())
            <div class="form-group">
                <label>現在の添付ファイル</label>
                <ul>
                    @foreach ($task->attachments as $file)
                        <li>
                            <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank">
                                {{ $file->original_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif


        <label>添付ファイル</label>
        <input type="file" name="attachments[]" multiple style="margin-bottom:10px;">

        <div class="button-area">
            <button type="submit" class="btn-submit">更新</button>
            <a href="{{ route('tasks.index') }}" class="btn-cancel">キャンセル</a>
        </div>

    </form>
</div>

<style>
.edit-wrapper {
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
.subtitle {
    margin-top: 20px;
}
.form-group {
    margin-bottom: 16px;
}
.form-group label {
    display:block;
    font-weight: bold;
    margin-bottom: 6px;
}
.form-group input,
.form-group textarea,
.form-group select {
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
}
.tag-list {
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-bottom:20px;
}
.tag-item {
    padding:6px 10px;
    background:#eee;
    border-radius:4px;
}
.button-area {
    display:flex;
    justify-content:flex-end;
    gap:12px;
    margin-top:20px;
}
.btn-submit {
    padding:10px 20px;
    background:#3b82f6;
    color:white;
    border:none;
    border-radius:6px;
}
.btn-cancel {
    padding:10px 20px;
    background:#aaa;
    color:white;
    border-radius:6px;
}
</style>
@endsection
