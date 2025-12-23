<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Team;
use App\Models\TeamTask;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\TeamService;
use App\Http\Requests\TeamRequest;
use App\Models\TeamInvitation;

class TeamController extends Controller
{
    use AuthorizesRequests;
    private TeamService $service;

    public function __construct(TeamService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $teams = Auth::user()->teams;

        $hasInvites = TeamInvitation::where('invitee_id', Auth::id())
            ->where('status', TeamInvitation::STATUS_PENDING)
            ->exists();

        return view('teams.index', compact('teams', 'hasInvites'));

    }


    public function create()
    {
        return view('teams.create');
    }

    public function store(TeamRequest $request)
    {
        $team = $this->service->store($request->validated(), Auth::user());

        return redirect()->route('teams.show', $team)->with('success', 'チームを作成しました');
    }

    public function show(Team $team)
    {
        $team->load(['members', 'tasks']);

        $completed = $team->teamTasks()
            ->where('status', 'done')
            ->count();

        $total = $team->teamTasks()->count();

        $completionRate = $total > 0
            ? round(($completed / $total) * 100)
            : 0;
        return view('teams.show', compact('team', 'completionRate'));
    }

    public function edit(Team $team)
    {
        $this->authorize('updateTeam', $team);
        return view('teams.edit', compact('team'));
    }

    public function update(TeamRequest $request, Team $team)
    {
        $this->authorize('updateTeam', $team);
        $team = $this->service->update($team, $request->validated());
        return back()->with('success', '更新しました');
    }

    public function destroy(Team $team)
    {
        $this->authorize('deleteTeam', $team);
        $this->service->delete($team);
        return redirect()->route('teams.index')->with('success', 'チームを削除しました');
    }
}

