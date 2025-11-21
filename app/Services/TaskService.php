<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class TaskService
{
    protected $tasks;

    public function __construct(TaskRepository $tasks)
    {
        $this->tasks = $tasks;
    }

    public function getAllTasks()
    {
        return $this->tasks->getAll();
    }

    public function getFindTask($id)
    {
        return $this->tasks->findById($id);
    }

    public function createTask(array $data)
    {
        return DB::transaction(function () use ($data){
            $data['user_id'] = Auth::id();
            $task = $this->tasks->create($data);

            return $task;
        });
    }

    public function updateTask($id, array $data)
    {
        return DB::transaction(function () use ($id, $data){
            $task = $this->tasks->findById($id);

            // 完了タスクは更新禁止
            if($task->status === 'completed')
            {
                throw new Exception('完了したタスクは編集できません');
            }

            return $this->tasks->update($id, $data);
        });
    }

    public function completeTask($id)
    {
        return $this->tasks->update($id, [
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function deleteTask($id)
    {
        return $this->tasks->delete($id);
    }

    // 検索機能の処理はあらかじめ用意しておく
    public function searchTasks(array $filters)
    {
        return $this->tasks->search($filters);
    }
}
