<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamOwnerController extends Controller
{
    use AuthorizesRequests;
    public function create(Team $team)
    {
        $this->authorize('transferOwnership', $team);

        // 自分以外のメンバー
        $members = $team->members()
            ->where('user_id', '!=', Auth::id())
            ->get();

        return view(
            'teams.owner.transfer',
            compact('team', 'members')
        );
    }

    public function store(Request $request, Team $team)
    {
        $this->authorize('transferOwnership', $team);

        $request->validate([
            'new_owner_id' => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($team, $request) {

            // 現 owner
            $currentOwnerId = Auth::id();

            // 念のため owner 数チェック
            $ownerCount = $team->members()
                ->wherePivot('role', 'owner')
                ->count();

            if ($ownerCount <= 1) {
                // OK（移譲前に 1 人は存在）
            }

            // 新 owner
            $team->members()->updateExistingPivot(
                $request->new_owner_id,
                ['role' => 'owner']
            );

            // 自分を admin に降格
            $team->members()->updateExistingPivot(
                Auth::id(),
                ['role' => 'admin']
            );
        });

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Owner を移譲しました');
    }
}
