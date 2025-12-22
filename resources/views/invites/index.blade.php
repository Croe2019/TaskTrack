@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">チーム招待</h1>

    @forelse ($invites as $invite)
        <div class="border rounded p-4 mb-4">

            <p class="font-semibold">
                {{ $invite->team->name }}
            </p>

            <p class="text-sm text-gray-600">
                 招待者：
                {{ optional($invite->inviter)->name ?? '不明' }}
            </p>

            <div class="mt-3 flex gap-3">

                <form method="POST"
                      action="{{ route('invites.accept', $invite) }}">
                    @csrf
                    @method('PATCH')
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">
                        承認
                    </button>
                </form>

                <form method="POST"
                      action="{{ route('invites.reject', $invite) }}">
                    @csrf
                    @method('PATCH')
                    <button class="px-4 py-2 bg-gray-300 rounded">
                        拒否
                    </button>
                </form>

            </div>
        </div>
    @empty
        <p class="text-gray-500 text-center">
            現在、招待はありません
        </p>
    @endforelse

</div>
@endsection
