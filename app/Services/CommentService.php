<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentService
{
    private $repository;

    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list($taskId)
    {
        return $this->repository->deleteByIds($taskId);
    }

    public function add($taskId, $content)
    {
        return $this->repository->create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'content' => $content
        ]);
    }

     /** コメント更新 */
    public function update(int|array $ids, array $data): array
    {
        // 更新
        $this->repository->updateByIds($ids, $data);

        // 更新したIDを返す（仕様）
        return is_array($ids) ? $ids : [$ids];
    }

    /** コメント削除 */
    public function delete(int|array $ids): array
    {
        // 削除
        $this->repository->deleteByIds($ids);

        // 削除したIDを返す（仕様）
        return is_array($ids) ? $ids : [$ids];
    }
}
