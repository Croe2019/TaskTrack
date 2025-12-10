<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskAttachment>
 */
class TaskAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'task_id' => Task::factory(), // Task が自動生成される
            'file_path' => 'attachments/' . $this->faker->uuid . '.txt',
            'original_name' => $this->faker->word . '.txt',
        ];
    }
}
