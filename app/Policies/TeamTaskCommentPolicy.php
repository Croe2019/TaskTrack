<?php

namespace App\Policies;

use App\Models\TeamTaskComment;
use App\Models\User;

class TeamTaskCommentPolicy
{
    /** 閲覧 */
    public function view(User $user, TeamTaskComment $comment)
    {
        return $comment->task->team->hasUser($user);
    }

    /** 作成 */
    public function create(User $user, \App\Models\TeamTask $task)
    {
        return $task->team->hasUser($user);
    }

    /** 編集（自分のみ） */
    public function update(User $user, TeamTaskComment $comment)
    {
        return $comment->user_id === $user->id;
    }

    /**
     * コメント削除
     * - 自分のコメント
     * - Owner / Admin
     */
    public function delete(User $user, TeamTaskComment $comment): bool
    {
        $team = $comment->task->team;

        return $comment->user_id === $user->id
            || $team->isOwner($user)
            || $team->isAdmin($user);
    }
}
