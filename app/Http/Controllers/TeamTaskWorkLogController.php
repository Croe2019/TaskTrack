<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamTask;
use App\Models\TeamTaskWorkLog;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamTaskWorkLogController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request, Team $team, TeamTask $task)
    {
        // チーム閲覧/タスク更新権限など、あなたの方針に合わせて
        $this->authorize('view', $team);
        $this->authorize('update', $task);

        $validated = $request->validate([
            'worked_at' => ['required', 'date'],
            'worked_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        // チーム不一致ガード（重要）
        abort_unless($task->team_id === $team->id, 404);

        $task->workLogs()->create([
            'user_id' => $request->user()->id,
            'worked_at' => $validated['worked_at'],
            'worked_minutes' => $validated['worked_minutes'],
            'note' => $validated['note'] ?? null,
        ]);

        return back()->with('status', '作業ログを追加しました');
    }

    public function update(Request $request, Team $team, TeamTask $task, TeamTaskWorkLog $workLog)
    {
        $this->authorize('view', $team);
        $this->authorize('update', $task);

        abort_unless($task->team_id === $team->id, 404);
        abort_unless($workLog->team_task_id === $task->id, 404);

        $validated = $request->validate([
            'worked_at' => ['required', 'date'],
            'worked_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $workLog->update($validated);

        return back()->with('status', '作業ログを更新しました');
    }

    public function destroy(Team $team, TeamTask $task, TeamTaskWorkLog $workLog)
    {
        $this->authorize('view', $team);
        $this->authorize('update', $task);

        abort_unless($task->team_id === $team->id, 404);
        abort_unless($workLog->team_task_id === $task->id, 404);

        $workLog->delete();

        return back()->with('status', '作業ログを削除しました');
    }
}
