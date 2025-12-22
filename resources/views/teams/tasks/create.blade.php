@extends('layouts.app')


@section('content')
<div class="container max-w-xl">
    <h1 class="text-xl font-bold mb-4">
        タスク作成（{{ $team->name }}）
    </h1>


    <form method="POST" action="{{ route('teams.tasks.store', $team) }}">
        @csrf

        {{-- タイトル --}}
        <div class="mb-4">
            <label class="block mb-1">タイトル</label>
        <input type="text" name="title" class="w-full border p-2" required>
        </div>


        {{-- 説明 --}}
        <div class="mb-4">
            <label class="block mb-1">説明</label>
            <textarea name="description" class="w-full border p-2" rows="4"></textarea>
        </div>


        {{-- 担当者 --}}
        <div class="mb-4">
            <label class="block mb-1">担当者</label>
            <select name="assignee_id" class="w-full border p-2">
                <option value="">未割当</option>
                @foreach ($team->users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                作成
            </button>

            <a href="{{ route('teams.tasks.index', $team) }}" class="px-4 py-2 bg-gray-300 rounded">
                戻る
            </a>
        </div>
    </form>
</div>
@endsection
