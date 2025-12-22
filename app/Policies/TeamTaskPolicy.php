<?php

namespace App\Policies;

use App\Models\TeamTask;
use App\Models\Team;
use App\Models\User;


class TeamTaskPolicy
{
    /** 閲覧：チームメンバー全員 */
    public function view(User $user, TeamTask $task)
    {
        return $task->team->hasUser($user);
    }

    /** 作成：Owner / Admin */
    public function create(User $user, Team $team)
    {
        return $team->isOwner($user)
            || $team->isAdmin($user);
    }

    /** 編集 */
    public function update(User $user, TeamTask $task)
    {
        if ($task->team->isOwner($user)) return true;
        if ($task->team->isAdmin($user)) return true;

        // Member は自分の担当タスクのみ
        return $task->assignee_id === $user->id;
    }

    /** 削除：Owner のみ */
    public function delete(User $user, TeamTask $task)
    {
        return $task->team->isOwner($user);
    }

    public function updateStatus(User $user, TeamTask $task): bool
    {
        $team = $task->team;

        if ($team->isOwner($user) || $team->isAdmin($user)) {
            return true;
        }

        // Member は自分の担当タスクのみ
        return $task->assignee_id === $user->id;
    }
}
