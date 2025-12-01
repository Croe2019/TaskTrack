<?php

namespace App\Repositories;

use App\Models\Task;

class TaskAttachmentRepository
{
    public function create(Task $task, string $path, string $originalName)
    {
        return $task->attachments()->create([
            'file_path' => $path,
            'original_name' => $originalName,
        ]);
    }
}
