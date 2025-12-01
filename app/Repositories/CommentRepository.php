<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentRepository
{
    public function deleteByIds(int|array $commentIds): int
    {
        // 配列でない場合配列に変換
        if(!is_array($commentIds))
        {
            $commentIds = [$commentIds];
        }

        // トランザクションで削除(必要に応じて)
        return DB::transaction(function () use ($commentIds) {
            return Comment::whereIn('id', $commentIds)->delete();
        });
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
