<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Team;
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

    public function dashboard(Team $team, Request $request)
    {
        $this->authorize('view', $team);

        // 月別集計（過去6ヶ月）
        $start = now()->subMonths(5)->startOfMonth();
        $data = \App\Models\Task::query()
            ->where('team_id', $team->id)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $start)
            ->selectRaw("DATE_FORMAT(completed_at, '%Y-%m') as ym, count(*) as cnt, sum(coalesce(worked_minutes,0)) as mins")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $labels = $data->pluck('ym')->map(fn($v)=>\Carbon\Carbon::createFromFormat('Y-m',$v)->format('Y/m'))->toArray();
        $counts = $data->pluck('cnt')->toArray();

        $summary = [
            'completed_count' => $data->sum('cnt'),
            'total_minutes' => $data->sum('mins'),
        ];

        return view('teams.dashboard', [
            'team' => $team,
            'chart' => ['labels' => $labels, 'data' => $counts],
            'summary' => $summary,
        ]);
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

        $completed = $team->tasks()
            ->whereNotNull('completed_at')
            ->count();

        $total = $team->tasks()->count();

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

