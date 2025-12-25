@extends('layouts.app')
@section('content')

    <form method="POST"
        action="{{ route('teams.invites.store', $team) }}">
        @csrf
        <div class="flex items-center gap-3">
        {{-- 招待ユーザー --}}
        <select name="invitee_id" required
                class="px-3 py-2 border rounded">
            @foreach ($users as $user)
                <option value="{{ $user->id }}">
                    {{ $user->name }}
                </option>
            @endforeach
        </select>

            {{-- role 指定 --}}
        <select name="role" required
                class="px-3 py-2 border rounded">
            <option value="member">メンバー</option>

             @can('createInvite', [$team, 'admin'])
                <option value="admin">管理者</option>
            @endcan
        </select>

         <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            招待を送信
        </button>

        <a href="{{ route('teams.show', $team) }}"
           class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            キャンセル
        </a>
    </form>
@endsection
