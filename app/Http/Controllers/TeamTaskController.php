<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamTask;
use App\Services\TeamTaskService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\TeamTaskRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\TeamTaskWorkLog;
use Illuminate\Support\Facades\DB;

class TeamTaskController extends Controller
{
    use AuthorizesRequests;
    private TeamTaskService $service;

    public function __construct(TeamTaskService $service)
    {
        $this->service = $service;
    }

    public function index(Team $team)
    {
        $this->authorize('view', $team);
        $tasks = $this->service->getTeamTasksForUser($team, Auth::user());

        return view('teams.tasks.index', compact('team', 'tasks'));
    }

    public function dashboard(Team $team)
    {
        $this->authorize('view', $team);

        $start = now()->subMonths(5)->startOfMonth();

        // DBドライバごとに年月抽出関数を切り替え
        $driver = DB::connection()->getDriverName();

        $ymCompletedExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m', completed_at)",
            'pgsql'  => "to_char(completed_at, 'YYYY-MM')",
            default  => "DATE_FORMAT(completed_at, '%Y-%m')",
        };

        $ymWorkedExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m', worked_at)",
            'pgsql'  => "to_char(worked_at, 'YYYY-MM')",
            default  => "DATE_FORMAT(worked_at, '%Y-%m')",
        };

        // 月別：完了タスク数（completed_at）
        $doneRows = TeamTask::query()
            ->where('team_id', $team->id)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $start)
            ->selectRaw("$ymCompletedExpr as ym, COUNT(*) as done_cnt")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        // 月別：合計作業時間（work logs）
        $workRows = TeamTaskWorkLog::query()
            ->whereHas('task', fn ($q) => $q->where('team_id', $team->id))
            ->where('worked_at', '>=', $start->toDateString())
            ->selectRaw("$ymWorkedExpr as ym, COALESCE(SUM(worked_minutes), 0) as mins")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        // 直近6ヶ月（抜け月0にする）
        $months = collect(range(0, 5))
            ->map(fn ($i) => $start->copy()->addMonths($i)->format('Y-m'));

        $labels = $months->map(function ($ym) {
            [$y, $m] = explode('-', $ym);
            return sprintf('%s/%02d', $y, (int) $m);
        })->toArray();


        $doneCounts = $months->map(fn ($ym) => (int) ($doneRows->get($ym)->done_cnt ?? 0))->toArray();
        $minutes    = $months->map(fn ($ym) => (int) ($workRows->get($ym)->mins ?? 0))->toArray();

        $summary = [
            'completed_count' => array_sum($doneCounts),
            'total_minutes'   => array_sum($minutes),
        ];

        return view('teams.tasks.dashboard', [
            'team'    => $team,
            'chart'   => ['labels' => $labels, 'done_counts' => $doneCounts, 'minutes' => $minutes],
            'summary' => $summary,
        ]);
    }

    public function create(Team $team)
    {
        $this->authorize('create', [TeamTask::class, $team]);
        return view('teams.tasks.create', compact('team'));
    }


    public function store(TeamTaskRequest $request, Team $team)
    {
        $this->authorize('create', [TeamTask::class, $team]);
        $this->service->storeTeamTask($request->validated(), $team);
        return redirect()
            ->route('teams.tasks.index', $team)
            ->with('success', 'タスクを作成しました');
    }

    public function edit(Team $team, TeamTask $task)
    {
        abort_unless($task->team_id === $team->id, 404);
        $this->authorize('update', $task);
        return view('teams.tasks.edit', compact('team', 'task'));
    }

    public function update(TeamTaskRequest $request, Team $team, TeamTask $task)
    {
        abort_unless($task->team_id === $team->id, 404);
        $this->authorize('update', $task);
        $this->service->updateTaskForUser($task, Auth::user(), $request->all());
        return back();
    }

    public function destroy(Team $team, TeamTask $task)
    {
        abort_unless($task->team_id === $team->id, 404);
        $this->authorize('delete', $task);
        $task->delete();
        return back()->with('success', 'タスクを削除しました');
    }

    public function show(Team $team, TeamTask $task)
    {
        $this->authorize('view', $team);
        $this->authorize('view', $task);

        abort_unless($task->team_id === $team->id, 404);

        $task->load([
            'workLogs.user' => fn($q) => $q->select('id', 'name'),
        ]);

        $totalMinutes = (int) $task->workLogs()->sum('worked_minutes');

        return view('teams.tasks.show', compact('team', 'task', 'totalMinutes'));
    }



    public function updateStatus(Request $request, Team $team, TeamTask $task)
    {
        $this->authorize('update', $task);
        abort_unless($task->team_id === $team->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:open,doing,done'],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $task->status;

        $task->status = $newStatus;

        // 完了時刻の制御
        if ($oldStatus !== 'done' && $newStatus === 'done') {
            // 完了へ遷移した瞬間だけ埋める（既に埋まっていれば触らない）
            $task->completed_at = $task->completed_at ?? now();
        } elseif ($oldStatus === 'done' && $newStatus !== 'done') {
            // 完了解除したら消す（必要なら運用で固定も可）
            $task->completed_at = null;
        }

        $task->save();

        return back()->with('status', 'ステータスを更新しました');
    }
}
