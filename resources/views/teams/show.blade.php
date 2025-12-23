@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">

    {{-- ヘッダー --}}
    <div class="flex gap-3 mt-4">
        <a href="{{ route('teams.tasks.dashboard', $team) }}"
        class="inline-block mt-4 text-blue-600 hover:underline">
            ダッシュボード
        </a>

        @can('updateTeam', $team)
            <a href="{{ route('teams.edit', $team) }}"
            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                編集
            </a>
        @endcan

        @can('deleteTeam', $team)
            <form action="{{ route('teams.destroy', $team) }}" method="POST"
                onsubmit="return confirm('本当に削除しますか？')">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    削除
                </button>
            </form>
        @endcan

    </div>


    {{-- サマリー --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white p-4 rounded-xl shadow">
            <small class="text-gray-500">メンバー数</small>
            <h3 class="text-2xl font-bold">{{ $team->members->count() }} 人</h3>
        </div>

        <div class="bg-white p-4 rounded-xl shadow">
            <small class="text-gray-500">総タスク数</small>
            @if(!$team->tasks->isEmpty())
                タスクはありません
            @else
                <h3 class="text-2xl font-bold">{{ $team->teamTasks->count() }} 件</h3>
            @endif
        </div>

        <div class="bg-white p-4 rounded-xl shadow">
            <small class="text-gray-500">完了率</small>
            <h3 class="text-2xl font-bold">
                {{ $completionRate ?? 0 }} %
            </h3>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- メンバー一覧 --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">👤 メンバー</h2>

            <ul class="space-y-2">
                @foreach ($team->members as $member)
                    <li class="flex justify-between items-center border-b pb-2">
                        <span>{{ $member->name }}</span>

                        <span class="text-sm text-gray-500">
                            {{ ucfirst($member->pivot->role) }}
                        </span>
                    </li>
                @endforeach
            </ul>

            @can('invite', $team)
                <a href="{{ route('teams.invite', $team) }}"
                   class="inline-block mt-4 text-blue-600 hover:underline">
                    ＋ メンバーを招待
                </a>
            @endcan

            @can('transferOwnership', $team)
                <a href="{{ route('teams.owner.transfer.create', $team) }}"
                class="text-red-600 underline">
                    Owner を移譲
                </a>
            @endcan

        </div>

        {{-- 最近のチームタスク --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">📋 最近のタスク</h2>

            @forelse ($team->teamTasks->take(5) as $task)
                <div class="border-b pb-2 mb-2">
                    <p class="font-medium">{{ $task->title }}</p>
                    <small class="text-gray-500">
                        担当：{{ optional($task->assignee)->name ?? '未設定' }}
                    </small>
                </div>
            @empty
                <p class="text-gray-500">まだタスクがありません。</p>
            @endforelse

            <a href="{{ route('teams.tasks.index', $team) }}"
               class="inline-block mt-4 text-blue-600 hover:underline">
                すべてのタスクを見る →
            </a>
        </div>

    </div>

</div>
@endsection
