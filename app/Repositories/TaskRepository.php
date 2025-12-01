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
        $query = $this->task->with('tags', 'attachments', 'comments.user');
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

    public function attachTags(Task $task, array $tagIds)
    {
        $task->tags()->sync($tagIds); // 多対多リレーション
    }

    public function tagFindById($id)
    {
        return Task::with(['tags'])->findOrFail($id);
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
     * 検索機能
     */
    public function search(array $filters)
    {
        $query = $this->task->query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                ->orWhereHas('tags', fn($q2) => $q2->where('name', 'like', "%{$keyword}%"));
            });
        }

        return $query->get();
    }

}
