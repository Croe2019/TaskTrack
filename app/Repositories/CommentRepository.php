<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository
{
    public function getByTaskId($taskId)
    {
        return Comment::where('task_id', $taskId)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
    }

    public function create(array $data)
    {
        return Comment::create($data);
    }

    public function update($id, array $data)
    {
        $comment = Comment::findOrFail($id);
        $comment->update($data);
        return $comment;
    }

    public function delete($id)
    {
        return Comment::destroy($id);
    }
}
