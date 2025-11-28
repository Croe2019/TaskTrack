<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

     /**
     * 全タスク取得（ユーザーの絞り込みも可能）
     */
    public function getAll($userId = null)
    {
        $query = $this->task->with('comments.user', 'tags');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->get();
    }

    /**
     * タスクIDで取得
     */
    public function findById($id)
    {
        return $this->task->with('comments.user', 'tags')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->task->create($data);
    }

    public function update($id, array $data)
    {
        $task = $this->task->findOrFail($id);
        $task->update($data);
        return $task;
    }

    public function delete($id)
    {
        $task = $this->task->findOrFail($id);
        return $task->delete();
    }

    /**
     * タスクにタグを紐付け
     */
    public function attachTags($task, array $tagIds)
    {
        $task->tags()->sync($tagIds);
    }

    /**
     * 検索機能
     */
    public function search(array $filters)
    {
        $q = $this->task->query();

        if (!empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $q->where(fn($q2) => $q2->where('title', 'like', "%{$kw}%")
                                   ->orWhere('description', 'like', "%{$kw}%"));
        }

        if (!empty($filters['status'])) $q->where('status', $filters['status']);
        if (!empty($filters['priority'])) $q->where('priority', $filters['priority']);
        if (!empty($filters['tag'])) {
            $tagId = (int)$filters['tag'];
            $q->whereHas('tags', fn($q2) => $q2->where('tags.id', $tagId));
        }

        return $q->with(['comments.user','tags'])->get();
    }

}
