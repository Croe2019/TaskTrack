<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function getAll()
    {
        return Task::orderBy('created_at', 'desc')->get();
    }

    public function findById($id)
    {
        return Task::findOrFail($id);
    }

    public function create(array $data)
    {
        return Task::create($data);
    }

    public function update($id, array $data)
    {
        $task = Task::findOrFail($id);
        $task->update($data);
        return $task;
    }

    public function delete($id)
    {
        return Task::destroy($id);
    }

    // 検索機能実装時に呼び出すためにあらかじめ用意しておく
    public function search(array $cond)
    {
        return Task::query()
            ->when($cond['keyword'] ?? null, function ($q, $keyword) {
                $q->where('title', 'like', "%{$keyword}%");
            })
            ->when($cond['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->get();
    }

}
