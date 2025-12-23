@extends('layouts.app')

@section('content')
    {{-- コメント一覧 --}}
<div class="container">
     <p>{{ $task->title }}</p>
    <p>{{ $task->description }}</p>
    {{-- コメント投稿 --}}
    @can('create', [App\Models\TeamTaskComment::class, $task])
        <form method="POST" action="{{ route('teams.tasks.comments.store', [$team, $task]) }}" class="mt-4">
            @csrf
            <textarea name="comment" class="w-full border p-2" rows="3"></textarea>
            <button class="mt-2 px-3 py-1 bg-blue-600 text-white">投稿</button>
        </form>
    @endcan
</div>

@forelse ($task->comments as $comment)
    <div class="border p-2 mb-2">

        <p class="text-sm text-gray-600">
            {{ $comment->user->name }}
        </p>

        <p>{{ $comment->comment }}</p>

        <div class="flex gap-3 mt-1">
            @can('update', $comment)
                <button onclick="toggleEdit({{ $comment->id }})"
                        class="text-blue-600 text-sm">
                    編集
                </button>
            @endcan

            @can('delete', $comment)
                <button type="button" class="text-red-600 text-sm js-delete" data-action="{{ route('teams.tasks.comments.destroy', [$team, $task, $comment]) }}">
                    削除
                </button>
            @endcan
        </div>

        {{-- 編集フォーム --}}
        @can('update', $comment)
        <form method="POST"
              action="{{ route('teams.tasks.comments.update', [$team->id, $task->id, $comment->id]) }}"
              id="edit-form-{{ $comment->id }}"
              class="hidden mt-2">
            @csrf
            @method('PATCH')

            <textarea name="comment"
                      class="w-full border p-2"
                      rows="2">{{ $comment->comment }}</textarea>

            <button type="submit" class="mt-1 px-3 py-1 bg-blue-600 text-white text-sm rounded">
                更新
            </button>
        </form>
        @endcan
    </div>

@empty
    <p class="text-sm text-gray-500">コメントはまだありません</p>
@endforelse

<h2 class="text-lg font-semibold">作業時間</h2>

    <div class="mt-2 p-3 bg-gray-50 rounded">
        <div class="text-sm text-gray-700">
            合計作業時間：
            <span class="font-semibold">{{ $totalMinutes }}</span> 分
            （{{ floor($totalMinutes / 60) }}h {{ $totalMinutes % 60 }}m）
        </div>

        @can('update', $task)
            <form method="POST" action="{{ route('teams.tasks.workLogs.store', [$team, $task]) }}" class="mt-3 flex gap-2 items-end">
                @csrf
                <div>
                    <label class="text-xs text-gray-600">作業日</label>
                    <input type="date" name="worked_at" value="{{ old('worked_at', now()->toDateString()) }}"
                        class="border rounded px-2 py-1">
                </div>
                <div>
                    <label class="text-xs text-gray-600">分</label>
                    <input type="number" name="worked_minutes" value="{{ old('worked_minutes') }}"
                        min="1" max="1440" class="border rounded px-2 py-1 w-28" placeholder="例: 60">
                </div>
                <div class="flex-1">
                    <label class="text-xs text-gray-600">メモ</label>
                    <input type="text" name="note" value="{{ old('note') }}"
                        class="border rounded px-2 py-1 w-full" placeholder="任意">
                </div>
                <button class="px-3 py-2 bg-blue-600 text-white rounded">追加</button>
            </form>
        @endcan
    </div>

    <div class="mt-4">
        <h3 class="font-semibold">作業ログ一覧</h3>

        <div class="mt-2 space-y-2">
            @forelse($task->workLogs->sortByDesc('worked_at') as $log)
                <div class="p-3 border rounded flex items-center justify-between">
                    <div class="text-sm">
                        <div>
                            <span class="font-semibold">{{ $log->worked_at->format('Y/m/d') }}</span>
                            ・{{ $log->worked_minutes }} 分
                            ・{{ $log->user?->name ?? '不明' }}
                        </div>
                        @if($log->note)
                            <div class="text-gray-600">{{ $log->note }}</div>
                        @endif
                    </div>

                    @can('update', $task)
                        <form method="POST" action="{{ route('teams.tasks.workLogs.destroy', [$team, $task, $log]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 text-sm">削除</button>
                        </form>
                    @endcan
                </div>
            @empty
                <div class="text-sm text-gray-500">作業ログはまだありません。</div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
    <script>
        function toggleEdit(id) {
            const form = document.getElementById('edit-form-' + id);
            form.classList.toggle('hidden');
        }
    </script>
@endpush
