@extends('layouts.app')

@section('content')
<div class="container" style="max-width:900px; margin:auto; padding:20px;">
    <h2>{{ $team->name }} — メンバー管理</h2>

    <div style="margin-top:16px; padding:12px; background:#f8fafc; border-radius:8px;">
        <form method="POST" action="{{ route('teams.members.add', $team) }}" style="display:flex; gap:8px; align-items:center;">
            @csrf
            <select name="user_id" required>
                <option value="">ユーザーを選択</option>
                @foreach(\App\Models\User::limit(50)->get() as $u) {{-- 実運用は検索UIを作る --}}
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>

            <select name="role">
                <option value="member">Member</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" style="padding:8px 12px; background:#10b981; color:white; border:none; border-radius:6px;">追加</button>

            <form method="POST" action="{{ route('teams.invite.create', $team) }}" style="margin-left:auto;">
                @csrf
                <input type="email" name="email" placeholder="メールで招待(任意)">
                <button type="submit" style="padding:6px 10px; background:#3b82f6; color:#fff; border:none; border-radius:6px;">招待リンク作成</button>
            </form>
        </form>
    </div>

    <table style="width:100%; margin-top:12px; border-collapse:collapse;">
        <thead>
            <tr style="background:#f1f5f9;">
                <th style="padding:8px; text-align:left;">ユーザー</th>
                <th style="padding:8px;">ロール</th>
                <th style="padding:8px;">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
                <tr>
                    <td style="padding:8px;">{{ $member->name }} ({{ $member->email }})</td>
                    <td style="padding:8px;">{{ $member->pivot->role }}</td>
                    <td style="padding:8px;">
                        <form method="POST" action="{{ route('teams.members.updateRole', [$team, $member]) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <select name="role" onchange="this.form.submit()">
                                <option value="owner" @selected($member->pivot->role==='owner')>owner</option>
                                <option value="admin"  @selected($member->pivot->role==='admin')>admin</option>
                                <option value="member" @selected($member->pivot->role==='member')>member</option>
                            </select>
                        </form>

                        <form method="POST" action="{{ route('teams.members.remove', [$team, $member]) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('削除しますか？')">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px;">{{ $members->links() }}</div>
</div>
@endsection
