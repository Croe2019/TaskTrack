<div style="display:flex; gap:8px; align-items:center;">
    <form method="POST" action="{{ route('teams.switch') }}">
        @csrf
        <select name="team_id" onchange="this.form.submit()">
            <option value="">個人タスク</option>
            @foreach(auth()->user()->teams as $team)
                <option value="{{ $team->id }}" @selected(session('current_team_id') == $team->id)>{{ $team->name }}</option>
            @endforeach
        </select>
    </form>
</div>
