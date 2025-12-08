<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\TaskService;
use App\Repositories\TaskRepository;
use App\Repositories\TagRepository;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class TaskServiceTest extends TestCase
{
    protected $taskRepo;
    protected $tagRepo;
    protected $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->taskRepo = Mockery::mock(TaskRepository::class);
        $this->tagRepo  = Mockery::mock(TagRepository::class);

        $this->service = new TaskService(
            $this->taskRepo,
            $this->tagRepo
        );

        Auth::shouldReceive('id')->andReturn(1); // 全テストで user_id = 1 にする
    }

    /** @test */
    public function get_all_tasks_calls_repository()
    {
        $this->taskRepo
            ->shouldReceive('getAll')
            ->once()
            ->andReturn(collect(['task1']));

        $result = $this->service->getAllTasks();

        $this->assertEquals(['task1'], $result->toArray());
    }

    /** @test */
    public function get_find_task_calls_repository()
    {
        $this->taskRepo
            ->shouldReceive('findById')
            ->with(10)
            ->once()
            ->andReturn('task');

        $result = $this->service->getFindTask(10);

        $this->assertEquals('task', $result);
    }

    /** @test */
    public function create_task_calls_repository_with_user_id()
    {
        $data = ['title' => 'テストタスク'];

        $this->taskRepo
            ->shouldReceive('create')
            ->once()
            ->with([
                'title' => 'テストタスク',
                'user_id' => 1,
            ])
            ->andReturn(new Task($data));

        $task = $this->service->createTask($data);

        $this->assertEquals('テストタスク', $task->title);
    }

    /** @test */
    public function update_task_with_tags_and_attachments_works()
    {
       // タスクをDBに作成
        $task = Task::factory()->create(['id' => 1]);

        // タグもDBに作成（外部キー対策）
        Tag::factory()->create(['id' => 3]);
        Tag::factory()->create(['id' => 5]);

        // タスクのモック（tags()->sync をモックする）
        $taskMock = Mockery::mock(Task::class)->makePartial();
        $taskMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $taskMock->title = '新しいタイトル';

        $relationMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
        $relationMock->shouldReceive('sync')->once()->with([3, 5]);

        $taskMock->shouldReceive('tags')->andReturn($relationMock);

        // update のモック
        $this->taskRepo
            ->shouldReceive('update')
            ->with(1, Mockery::any())
            ->andReturn($taskMock);

        // 新規タグのモック
        $this->tagRepo
            ->shouldReceive('getOrCreateTags')
            ->with(['新タグ'])
            ->andReturn([5]);

        // 実行
        $result = $this->service->updateTaskWithTagsAndAttachments(
            $taskMock,
            ['title' => '新しいタイトル', 'user_id' => 1],
            [3],        // 既存タグ
            ['新タグ'], // 新規タグ
            []          // 添付ファイルなし
        );

        // アサート
        $this->assertEquals('新しいタイトル', $result->title);

    }


    /** @test */
    public function complete_task_updates_status_and_completed_at()
    {
        $this->taskRepo
            ->shouldReceive('update')
            ->once()
            ->with(2, Mockery::type('array'))
            ->andReturn(new Task(['status' => 'completed']));

        $task = $this->service->completeTask(2);

        $this->assertEquals('completed', $task->status);
    }

    /** @test */
    public function delete_task_calls_repository_delete()
    {
        $this->taskRepo
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $result = $this->service->deleteTask(1);

        $this->assertTrue($result);
    }

    /** @test */
    public function search_tasks_delegates_to_repository()
    {
        $this->taskRepo
            ->shouldReceive('search')
            ->with(['keyword' => '重要'])
            ->once()
            ->andReturn(collect(['task']));

        $result = $this->service->searchTasks(['keyword' => '重要']);

        $this->assertEquals(['task'], $result->toArray());
    }
}
