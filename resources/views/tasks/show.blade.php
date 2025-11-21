@extends('layouts.app')

@section('content')
<div class="container" style="max-width:600px; margin:auto; background:white; padding:24px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <h2 style="margin-bottom:20px; font-size:22px; border-bottom:2px solid #ddd; padding-bottom:10px;">
        タスク編集
    </h2>

    {{-- 成功・エラーメッセージ --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:12px; color:green;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:12px; color:red;">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('tasks.update', $task->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- タイトル --}}
        <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">タイトル</label>
            <input type="text" name="title" value="{{ old('title', $task->title) }}" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>

        {{-- 詳細内容 --}}
        <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">詳細内容</label>
            <textarea name="description" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; height:80px; resize:vertical;">{{ old('description', $task->description) }}</textarea>
        </div>

        {{-- ステータス --}}
        <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">ステータス</label>
            <select name="status" {{ $task->status === 'completed' ? 'disabled' : '' }} style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                <option value="not_started" {{ old('status', $task->status) === 'not_started' ? 'selected' : '' }}>未着手</option>
                <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>進行中</option>
                <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>完了</option>
            </select>
        </div>

        {{-- 優先度 --}}
        <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">優先度</label>
            <select name="priority" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>高</option>
                <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>中</option>
                <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>低</option>
            </select>
        </div>

        {{-- 期限 --}}
        <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">期限</label>
            <input type="date" name="deadline" value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d') : '') }}" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>

        {{-- 完了日時 --}}
        <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">完了日時</label>
            <input type="datetime-local" name="completed_at" value="{{ old('completed_at', $task->completed_at ? $task->completed_at->format('Y-m-d\TH:i') : '') }}" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>

        {{-- タグ --}}
        <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">タグ</label>
            <div class="tags" style="display:flex; gap:8px; flex-wrap:wrap;">
                @foreach($task->tags ?? [] as $tag)
                    <div class="tag" style="background:#eee; padding:6px 10px; border-radius:6px;">{{ $tag->name }}</div>
                @endforeach
                <input type="text" name="new_tag" class="tag add-tag" placeholder="＋ 追加タグ" style="background:#d4f0ff; border:1px solid #a0d8ff; cursor:pointer;">
            </div>
        </div>

        {{-- ファイル添付 --}}
        <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">ファイル添付</label>
            <input type="file" name="attachment">
        </div>

        {{-- ボタン --}}
        <div class="buttons" style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px;">
            <button class="btn btn-submit" type="submit" style="padding:10px 20px; border:none; border-radius:6px; background:#4CAF50; color:white; cursor:pointer;">更新</button>
            <button class="btn btn-cancel" type="button" onclick="history.back()" style="padding:10px 20px; border:none; border-radius:6px; background:#ccc; cursor:pointer;">キャンセル</button>
        </div>

    </form>
</div>
@endsection
