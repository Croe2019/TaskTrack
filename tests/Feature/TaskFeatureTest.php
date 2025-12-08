<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_task_list()
    {
        $user = User::factory()->create();
        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'タスクA'
        ]);

        $this->actingAs($user)
            ->get(route('tasks.index'))
            ->assertStatus(200)
            ->assertSee('タスクA');
    }
}
