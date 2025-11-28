<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use Exception;


class TaskService
{
    protected $taskRepo;
    protected $tagRepo;

    public function __construct(TaskRepository $taskRepo, TagRepository $tagRepo)
    {
        $this->taskRepo = $taskRepo;
        $this->tagRepo = $tagRepo;
    }


    public function getAllTasks()
    {
        return $this->taskRepo->getAll();
    }

    public function getFindTask($id)
    {
        return $this->taskRepo->findById($id);
    }

    /**
     * シンプル作成（タグなし）
     */
    public function createTask(array $data): Task
    {
        $data['user_id'] = Auth::id();
        return $this->taskRepo->create($data);
    }

    public function createTaskWithTags(array $taskData, array $newTagNames, array $existingTagIds = []): Task
    {
        return DB::transaction(function () use ($taskData, $newTagNames, $existingTagIds) {

            // ユーザー紐付け
            $taskData['user_id'] = Auth::id();

            // タスク作成
            $task = $this->taskRepo->create($taskData);

            // 新規タグ作成 or 取得
            $newTagIds = $this->tagRepo->getOrCreateTags($newTagNames);

            // 既存タグ + 新規タグ をマージ
            $allTagIds = array_merge($existingTagIds, $newTagIds);

            // 紐付け
            $this->taskRepo->attachTags($task, $allTagIds);

            return $task;
        });
    }

    public function updateTask($id, array $data, array $newTagNames, array $existingTagIds = [])
    {
        return DB::transaction(function () use ($id, $data, $newTagNames, $existingTagIds){
            $task = $this->taskRepo->findById($id);
            // ユーザー紐付け
            $taskData['user_id'] = Auth::id();

            // 完了タスクは更新禁止
            if($task->status === 'completed')
            {
                throw new Exception('完了したタスクは編集できません');
            }

             // 新規タグ作成 or 取得
            $newTagIds = $this->tagRepo->getOrCreateTags($newTagNames);

            // 既存タグ + 新規タグ をマージ
            $allTagIds = array_merge($existingTagIds, $newTagIds);

            // 紐付け
            $this->taskRepo->attachTags($task, $allTagIds);

            // 更新
            $this->taskRepo->update($id, $data);

            return $this->taskRepo;

        });
    }

    public function completeTask($id)
    {
        return $this->taskRepo->update($id, [
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function deleteTask($id)
    {
        return $this->taskRepo->delete($id);
    }

    public function searchTasks(array $filters)
    {
        return $this->taskRepo->search($filters);
    }
}
