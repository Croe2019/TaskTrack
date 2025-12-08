<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Repositories\TaskRepository;
use App\Models\Task;
use App\Models\User;
use App\Models\Tag;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repo;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = new TaskRepository(new Task());
    }

    /** @test */
    public function get_all_returns_all_tasks_or_filtered_by_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Task::factory()->create(['user_id' => $user1->id, 'title' => 'ユーザー1のタスク']);
        Task::factory()->create(['user_id' => $user2->id, 'title' => 'ユーザー2のタスク']);

        // 全件
        $tasks = $this->repo->getAll();
        $this->assertCount(2, $tasks);

        // user1 だけ
        $tasks_user1 = $this->repo->getAll($user1->id);
        $this->assertCount(1, $tasks_user1);
        $this->assertEquals('ユーザー1のタスク', $tasks_user1->first()->title);
    }

    /** @test */
    public function find_by_id_returns_correct_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'サンプルタスク',
        ]);

        $found = $this->repo->findById($task->id);

        $this->assertEquals('サンプルタスク', $found->title);
    }

    /** @test */
    public function create_creates_a_new_task()
    {
        $user = User::factory()->create();

        $data = [
            'title' => '新規タスク',
            'status' => 'not_started',
            'description' => '詳細',
            'user_id' => $user->id
        ];

        $task = $this->repo->create($data);

        $this->assertDatabaseHas('tasks', ['title' => '新規タスク']);
        $this->assertEquals('not_started', $task->status);
    }

    /** @test */
    public function update_updates_task()
    {
        $task = Task::factory()->create(['title' => '旧タイトル']);

        $updated = $this->repo->update($task->id, [
            'title' => '新タイトル'
        ]);

        $this->assertEquals('新タイトル', $updated->title);
        $this->assertDatabaseHas('tasks', ['title' => '新タイトル']);
    }

    /** @test */
    public function delete_deletes_task()
    {
        $task = Task::factory()->create();

        $this->repo->delete($task->id);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function attach_tags_correctly_updates_relations()
    {
        $task = Task::factory()->create();
        $tag1 = Tag::factory()->create(['name' => '重要']);
        $tag2 = Tag::factory()->create(['name' => '緊急']);

        $this->repo->attachTags($task, [$tag1->id, $tag2->id]);

        $this->assertCount(2, $task->tags()->get());
    }

    /** @test */
    public function search_filters_by_status_or_keyword()
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => '完了タスク',
            'status' => 'completed'
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => '進行中タスク',
            'status' => 'in_progress'
        ]);

        // 状態検索
        $completed = $this->repo->search(['status' => 'completed']);
        $this->assertCount(1, $completed);
        $this->assertEquals('完了タスク', $completed->first()->title);

        // キーワード検索
        $keyword = $this->repo->search(['keyword' => '進行中']);
        $this->assertCount(1, $keyword);
        $this->assertEquals('進行中タスク', $keyword->first()->title);
    }
}
