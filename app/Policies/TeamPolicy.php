<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Team;
use App\Models\TeamTask;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\Response;


class TeamPolicy
{
    use AuthorizesRequests;

    public function view(User $user, Team $team)
    {
        return $team->hasUser($user);
    }


    public function updateTeam(User $user, Team $team): bool
    {
        return $team->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'owner')
                ->exists();
    }

    public function deleteTeam(User $user, Team $team): bool
    {
        return $team->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'owner')
                ->exists();
    }

    public function transferOwner(Team $team, User $newOwner)
    {
        $this->authorize('update', $team);

        DB::transaction(function () use ($team, $newOwner) {
            // 現在の owner を admin に
            $team->members()
                ->wherePivot('role', 'owner')
                ->update(['role' => 'admin']);

            // 新 owner 設定
            $team->members()
                ->updateExistingPivot($newOwner->id, ['role' => 'owner']);

            $team->update(['owner_id' => $newOwner->id]);

        });

        return back()->with('success', 'オーナーを移譲しました');
    }

    public function changeRole(User $user, Team $team, User $member)
    {
        $this->authorize('changeRole', [$team, $member]);

        // 操作ユーザーが Owner か
        if (! $team->members()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists()
        ) {
            return false;
        }

        // 対象メンバーが最後の Owner か
        if (
            $member->pivot->role === 'owner' &&
            $team->members()->wherePivot('role', 'owner')->count() <= 1
        ) {
            return Response::deny('Owner は最低1人必要です');
        }

        return Response::allow();
    }

    public function createInvite(User $user, Team $team, string $role = null)
    {
        $member = $team->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $member) {
            return false;
        }

        // member は招待不可
        if ($member->pivot->role === 'member') {
            return false;
        }

        // admin は admin を招待不可
        if (
            $member->pivot->role === 'admin' &&
            $role === 'admin'
        ) {
            return false;
        }

        // owner はすべて OK
        return true;
    }

    public function transferOwnership(User $user, Team $team)
    {
        return $team->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

}

