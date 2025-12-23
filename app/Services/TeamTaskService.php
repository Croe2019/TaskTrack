<?php
namespace App\Services;

use App\Models\Team;
use App\Models\TeamTask;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeamTaskService
{
     public function getTeamTasksForUser(Team $team, User $user): Collection
    {
        $query = TeamTask::where('team_id', $team->id);

        if (! $team->isOwner($user) && ! $team->isAdmin($user)) {
            $query->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                    ->orWhere('creator_id', $user->id);
            });
        }

        return $query
            ->with(['assignee', 'creator'])
            ->latest()
            ->get();
    }

    public function storeTeamTask(array $data, Team $team)
    {
        return DB::transaction(function () use ($data, $team){
            $data['team_id'] = $team->id;
            $data['creator_id'] = Auth::id();
            $teamTask = TeamTask::create($data);
            return $teamTask;
        });
    }

    public function updateTaskForUser(TeamTask $task, User $user, array $payload): TeamTask
    {
        $oldStatus = $task->status;
        $newStatus = $payload['status'] ?? $oldStatus;

        // まず通常更新
        $task->fill($payload);

        // completed_at の制御（statusが送られてきた場合だけ）
        if (array_key_exists('status', $payload)) {
            if ($oldStatus !== 'done' && $newStatus === 'done') {
                $task->completed_at = $task->completed_at ?? now();
            } elseif ($oldStatus === 'done' && $newStatus !== 'done') {
                $task->completed_at = null;
            }
        }

        $task->save();

        return $task;
    }


    public function validateUpdateData(TeamTask $task, User $user, array $input): array
    {
        if ($task->team->isOwner($user) || $task->team->isAdmin($user)) {
            return Validator::make($input, [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'assignee_id' => ['nullable', 'exists:users,id'],
                'status' => ['required', 'in:open,doing,done'],
            ])->validate();
        }

        return Validator::make($input, [
            'status' => ['required', 'in:open,doing,done'],
        ])->validate();
    }
}
