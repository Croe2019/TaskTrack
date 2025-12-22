<form method="POST"
      action="{{ route('teams.invites.store', $team) }}">
    @csrf

    {{-- 招待ユーザー --}}
    <select name="invitee_id" required>
        @foreach ($users as $user)
            <option value="{{ $user->id }}">
                {{ $user->name }}
            </option>
        @endforeach
    </select>

    {{-- role 指定 --}}
    <select name="role" required>
        <option value="member">メンバー</option>

        @can('createInvite', [$team, 'admin'])
            <option value="admin">管理者</option>
        @endcan
    </select>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded">
        招待を送信
    </button>
</form>
