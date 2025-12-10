<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Tag;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $tags  = Tag::all();

        Task::factory(50)->create()->each(function ($task) use ($users, $tags) {

            // ランダムユーザー付与
            $task->update([
                'user_id' => $users->random()->id
            ]);

            // タグを0〜3個ランダム付与（タグが存在する時のみ）
            if ($tags->isNotEmpty()) {
                $task->tags()->sync(
                    $tags->random(rand(0, min(3, $tags->count())))->pluck('id')->toArray()
                );
            }
        });
    }
}
