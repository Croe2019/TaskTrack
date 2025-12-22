<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamTaskController extends Controller
{
    use AuthorizesRequests;
    public function index(Team $team)
    {
        $this->authorize('view', $team);

        $user = Auth::user();

        $query = TeamTask::where('team_id', $team->id);

        if ($team->isOwner($user) || $team->isAdmin($user)) {
            // 全件表示
        } else {
            // Member 制限
            $query->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                ->orWhere('creator_id', $user->id);
            });
        }

        $tasks = $query
            ->with(['assignee', 'creator'])
            ->latest()
            ->get();

        return view('teams.tasks.index', compact('team', 'tasks'));
    }

    public function create(Team $team)
    {
        $this->authorize('create', [TeamTask::class, $team]);
        return view('teams.tasks.create', compact('team'));
    }


    public function store(Request $request, Team $team)
    {
        $this->authorize('create', [TeamTask::class, $team]);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        TeamTask::create([
            'team_id'     => $team->id,
            'creator_id'  => Auth::id(),
            'assignee_id' => $request->assignee_id,
            'title'       => $request->title,
            'description' => $request->description,
        ]);

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

    public function update(Request $request, Team $team, TeamTask $task)
    {
        abort_unless($task->team_id === $team->id, 404);
        $this->authorize('update', $task);
        $user = Auth::user();


        if ($task->team->isOwner($user) || $task->team->isAdmin($user)) {
                $task->update(
                $request->only('title', 'description', 'assignee_id', 'status')
            );
        } else {
                // Member
                $task->update(
                $request->only('status')
            );
        }

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
        // チーム・タスクの不整合ガード
        abort_unless($task->team_id === $team->id, 404);

        // 閲覧権限
        $this->authorize('view', $task);

        // リレーションを明示的にロード
        $task->load([
            'assignee',
            'creator',
            'comments' => function ($query) {
                $query->with('user')
                    ->orderBy('created_at', 'asc');
            },
        ]);

        return view('teams.tasks.show', compact('team', 'task'));
    }


    public function updateStatus(Request $request, Team $team, TeamTask $task)
    {
        abort_unless($task->team_id === $team->id, 404);
        $this->authorize('updateStatus', $task);
        $task->update(['status' => $request->status]);
        return back();
    }
}
