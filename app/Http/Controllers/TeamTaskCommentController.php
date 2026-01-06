<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamTask;
use App\Models\User;
use App\Models\TeamTaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Events\TaskCommentCreated;
use Illuminate\Support\Facades\DB;


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

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($task, $validated) {
            $commentModel = $task->comments()->create([
                'user_id' => Auth::id(),
                'comment' => $validated['comment'],
            ]);

            // 型が崩れていないかをここで確実にチェック（崩れてたら原因は別箇所）
            if (!($commentModel instanceof \App\Models\TeamTaskComment)) {
                throw new \RuntimeException('Task comments() relation is not returning TeamTaskComment model.');
            }

            event(new TaskCommentCreated($task, $commentModel, Auth::user()));
        });

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
