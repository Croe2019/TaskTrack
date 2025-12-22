@extends('layouts.app')
@section('content')
<div class="container max-w-xl">
    <h1 class="text-xl font-bold mb-4">
        タスク編集（{{ $team->name }}）
    </h1>

    <form method="POST" action="{{ route('teams.tasks.update', [$team, $task]) }}">
        @csrf
        @method('PATCH')

        {{-- タイトル・説明・担当者は Admin 以上のみ --}}
        @can('create', [App\Models\TeamTask::class, $team])
        {{-- 入力欄表示 --}}
        <div class="mb-4">
            <label class="block mb-1">タイトル</label>
            <input type="text" name="title" value="{{ old('title', $task->title) }}" class="w-full border p-2" required>
        </div>
        @endcan

        {{-- 説明 --}}
        <div class="mb-4">
            <label class="block mb-1">説明</label>
            <textarea name="description" class="w-full border p-2" rows="4">{{ old('description', $task->description) }}</textarea>
        </div>


        {{-- ステータス --}}
        <div class="mb-4">
            <label class="block mb-1">ステータス</label>
            <select name="status" class="w-full border p-2">
            @foreach (['open' => '未着手', 'doing' => '進行中', 'done' => '完了'] as $value => $label)
                <option value="{{ $value }}"
                @selected(old('status', $task->status) === $value)>
                {{ $label }}
                </option>
            @endforeach
            </select>
        </div>


        {{-- 担当者 --}}
        <div class="mb-4">
        <label class="block mb-1">担当者</label>
        <select name="assignee_id" class="w-full border p-2">
        <option value="">未割当</option>
            @foreach ($team->users as $user)
                <option value="{{ $user->id }}"
            @selected(old('assignee_id', $task->assignee_id) == $user->id)>
                {{ $user->name }}
            </option>
            @endforeach
        </select>
        </div>


        <div class="flex gap-3">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                更新
            </button>

            <a href="{{ route('teams.tasks.index', $team) }}" class="px-4 py-2 bg-gray-300 rounded">
                戻る
            </a>
        </div>
    </form>
</div>
@endsection
