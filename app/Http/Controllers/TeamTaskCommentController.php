<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamTask;
use App\Models\User;
use App\Models\TeamTaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamTaskCommentController extends Controller
{
    use AuthorizesRequests;
    /**
     * コメント追加
     */
    public function store(Request $request, Team $team, TeamTask $task)
    {
        abort_unless($task->team_id === $team->id, 404);

        $this->authorize('create', [TeamTaskComment::class, $task]);

        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        TeamTaskComment::create([
            'team_task_id' => $task->id,
            'user_id'      => Auth::id(),
            'comment'      => $request->comment,
        ]);

        return back();
    }

     /**
     * コメント削除
     */
    public function destroy(Team $team, TeamTask $task, TeamTaskComment $comment)
    {
        // スコープ厳密チェック
        abort_unless(
            $task->team_id === $team->id &&
            $comment->team_task_id === $task->id,
            404
        );

        $this->authorize('delete', $comment);

        $comment->delete();

        return back();
    }


    public function update(Request $request, Team $team, TeamTask $task, TeamTaskComment $comment)
    {
        abort_unless(
            $task->team_id === $team->id &&
            $comment->team_task_id === $task->id,
            404
        );

        $this->authorize('update', $comment);

        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return back();
    }


}
