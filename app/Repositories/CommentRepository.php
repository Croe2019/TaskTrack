<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentRepository
{
    public function create(array $data)
    {
        return Comment::create($data);
    }

    public function updateByIds(array|int $ids, array $data): int
    {
        if (!is_array($ids)) $ids = [$ids];

        return Comment::whereIn('id', $ids)->update($data);
    }

    public function deleteByIds(array|int $ids): int
    {
        if (!is_array($ids)) $ids = [$ids];

        return Comment::whereIn('id', $ids)->delete();
    }

    public function findById($id)
    {
        return Comment::find($id);
    }
}
