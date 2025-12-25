@extends('layouts.app')
@section('content')
    <form method="POST"
        action="{{ route('teams.invites.store', $team) }}">
        @csrf
        <div class="flex flex-col items-stretch space-y-3 md:flex-row md:items-center md:space-y-0 md:space-x-3">
            {{-- 招待ユーザー --}}
            <select name="invitee_id" required
                class="w-full px-3 py-2 border rounded md:w-auto">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            {{-- role 指定 --}}
            <select name="role" required
                class="w-full px-3 py-2 border rounded md:w-auto">
                <option value="member">メンバー</option>

             @can('createInvite', [$team, 'admin'])
                    <option value="admin">管理者</option>
                @endcan
            </select>

            <button type="submit"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 md:w-auto">
                招待を送信
            </button>

            <a href="{{ route('teams.index', $team) }}"
               class="w-full px-4 py-2 text-center bg-red-600 text-white rounded hover:bg-red-700 md:w-auto">
                キャンセル
            </a>
        </div>
    </form>
@endsection
