<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use App\Repositories\TagRepository;
use App\Repositories\TaskAttachmentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use App\Models\TaskAttachment;
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

    /**
     * タスク作成（タグ・添付ファイルを含む）
     */
    public function createTaskWithTagsAndAttachments($data, $existingTagIds, $newTagNames, $files)
    {
        DB::transaction(function () use ($data, $existingTagIds, $newTagNames, $files) {

            $data['user_id'] = Auth::id();

            // 1. タスク作成
            $task = Task::create($data);

            // 2. 既存タグを紐づけ
            if (!empty($existingTagIds)) {
                $task->tags()->attach($existingTagIds);
            }

            // 3. 新規タグを作成して紐づけ
            foreach ($newTagNames as $name) {
                if ($name === '') continue;

                $tag = Tag::firstOrCreate(['name' => $name]);
                $task->tags()->attach($tag->id);
            }

            // 4. 添付ファイル処理
            if (!empty($files)) {
                foreach ($files as $file) {
                    $path = $file->store('attachments', 'public');

                    TaskAttachment::create([
                        'task_id' => $task->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        });
    }


    public function updateTaskWithTagsAndAttachments(Task $task, array $data, array $existingTagIds = [], array $newTagNames = [], array $files = [])
    {
        return DB::transaction(function () use ($task, $data, $existingTagIds, $newTagNames, $files) {

            // ① タスク更新
            $data['user_id'] = Auth::id();
            $task = $this->taskRepo->update($task->id, $data);

            // ② 新規タグ作成
            $newTagIds = $this->tagRepo->getOrCreateTags($newTagNames);

            // ③ タグ紐付け
            $allTagIds = array_merge($existingTagIds, $newTagIds);
            $task->tags()->sync($allTagIds);

            // ④ 添付ファイル
            foreach ($files as $file) {
                $path = $file->store('attachments', 'public');
                TaskAttachment::create([
                    'task_id' => $task->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }

            return $task;
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
