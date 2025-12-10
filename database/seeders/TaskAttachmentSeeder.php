<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();

        if ($tasks->isEmpty()) return;

        for ($i = 0; $i < 20; $i++) {

            // ダミーファイル作成
            $fakeFileName = 'dummy_' . uniqid() . '.txt';
            Storage::disk('public')->put('attachments/' . $fakeFileName, 'Dummy content');

            TaskAttachment::create([
                'task_id'   => $tasks->random()->id,
                'file_path' => 'attachments/' . $fakeFileName,
                'original_name' => $fakeFileName,
            ]);
        }
    }
}
