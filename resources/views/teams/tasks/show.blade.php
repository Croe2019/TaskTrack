@extends('layouts.app')

@section('content')
    {{-- コメント一覧 --}}
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

</div>

@endsection

<script>
    function toggleEdit(id) {
        const form = document.getElementById('edit-form-' + id);
        form.classList.toggle('hidden');
    }
</script>
