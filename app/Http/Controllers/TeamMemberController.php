<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class TeamMemberController extends Controller
{
    use AuthorizesRequests;

    public function index(Team $team)
    {
        $this->authorize('view', $team);
        $members = $team->members()->paginate(20);
        return view('teams.members.index', compact('teams', 'members'));
    }

    public function add(Team $team)
    {
        $this->authorize('getTeams', $team);
        $teams = $team->members()->paginate(20);
        return view('teams.members.add', compact('team'));
    }

    public function addMember(Request $request, Team $team)
    {
        $this->authorize('manageMembers', $team);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:owner,admin,member',
        ]);

        $team->members()->syncWithoutDetaching([$request->user_id => ['role' => $request->role]]);
        return back()->with('success', 'メンバーを追加しました');
    }

    public function updateRole(Request $request, Team $team, User $user)
    {
        $this->authorize('manageMembers', $team);

        $request->validate(['role' => 'required|in:owner,admin,member']);
        $team->members()->updateExistingPivot($user->id, ['role' => $request->role]);

        return back()->with('success', 'ロールを更新しました');
    }

    public function removeMember(Team $team, User $user)
    {
        $this->authorize('manageMembers', $team);
        $team->members()->detach($user->id);
        return back()->with('success', 'メンバーを削除しました');
    }
}

