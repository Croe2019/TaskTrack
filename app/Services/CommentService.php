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

    public function getFindComment($id)
    {
        return $this->repository->getByTaskId($id);
    }

    public function list($taskId)
    {
        return $this->repository->getByTaskId($taskId);
    }

    public function add($taskId, $content)
    {
        return $this->repository->create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'content' => $content
        ]);
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data){
            $this->repository->getByTaskId($id);

            return $this->repository->update($id, $data);
        });
    }

    public function remove($id)
    {
        return $this->repository->delete($id);
    }
}
