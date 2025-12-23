@extends('layouts.app')

@section('content')
<div class="container">


    <h1 class="text-xl font-bold mb-4">
    {{ $team->name }} のタスク一覧
    </h1>


    {{-- タスク作成（Owner / Admin） --}}
    @can('create', [App\Models\TeamTask::class, $team])
        <a href="{{ route('teams.tasks.create', $team) }}"
        class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded">
        タスク作成
        </a>
    @endcan


    {{-- タスク一覧 --}}
    <table class="w-full border">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">タイトル</th>
            <th class="p-2 border">担当者</th>
            <th class="p-2 border">ステータス</th>
            <th class="p-2 border">操作</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($tasks as $task)
        <tr>
            {{-- タイトル --}}
            <td class="p-2 border">
                <a href="{{ route('teams.tasks.show', [$team, $task]) }}" class="text-blue-600 text-sm"> {{ $task->title }} </a>
            </td>

            {{-- 担当者 --}}
            <td class="p-2 border">
                {{ optional($task->assignee)->name ?? '未割当' }}
            </td>

            {{-- ステータス --}}
            <td class="p-2 border">
                @can('updateStatus', $task)
                    <form method="POST" action="{{ route('teams.tasks.updateStatus', [$team, $task]) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()">
                            <option value="open"  @selected($task->status==='open')>未着手</option>
                            <option value="doing" @selected($task->status==='doing')>進行中</option>
                            <option value="done"  @selected($task->status==='done')>完了</option>
                        </select>
                    </form>
                @else
                    {{ $task->status }}
                @endcan
            </td>

            {{-- 操作 --}}
            <td class="p-2 border">
                <div class="flex items-center gap-3">
                    @can('update', $task)
                        <a href="{{ route('teams.tasks.edit', [$team, $task]) }}"
                        class="text-blue-600 text-sm">
                            編集
                        </a>
                    @endcan

                    @can('delete', $task)
                        <form method="POST"
                            action="{{ route('teams.tasks.destroy', [$team, $task]) }}"
                            class="inline-flex">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 text-sm">
                                削除
                            </button>
                        </form>
                    @endcan
                </div>
            </td>
        </tr>
    @empty
    <tr>
        <td colspan="4" class="p-4 text-center text-gray-500">
            タスクはまだありません
        </td>
    </tr>
    @endforelse
    </tbody>
    </table>
</div>
@endsection
