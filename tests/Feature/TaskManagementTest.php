<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Tag;
use App\Models\Comment;

class TaskManagementTest extends TestCase
{
   use RefreshDatabase;

   // タスク一覧が表示される
   public function test_tasks_list_displayed()
   {
        $user = User::factory()->create();
        $task1 = Task::factory()->create(['user_id' => $user->id, 'title' => 'タスクA']);
        $task2 = Task::factory()->create(['user_id' => $user->id, 'title' => 'タスクB']);

        $this->actingAs($user)
             ->get(route('tasks.index'))
             ->assertStatus(200)
             ->assertSee('タスクA')
             ->assertSee('タスクB');
   }

   // 状態フィルターが正しく動作する
   public function test_state_filters_work_correctly()
   {
        $user = User::factory()->create();
        Task::factory()->create(['user_id' => $user->id, 'status' => 'completed', 'title' => '完了タスク']);
        Task::factory()->create(['user_id' => $user->id, 'status' => 'in_progress', 'title' => '進行中タスク']);

        $this->actingAs($user)
             ->get(route('tasks.index', ['status' => 'completed']))
             ->assertSee('完了タスク')
             ->assertDontSee('進行中タスク');
   }

   // タスク検索が正しく動作する
   public function test_task_search_works_correctly()
   {
        $user = User::factory()->create();
        Task::factory()->create(['user_id' => $user->id, 'title' => '検索対象タスク']);
        Task::factory()->create(['user_id' => $user->id, 'title' => 'その他のタスク']);

        $this->actingAs($user)
             ->get(route('tasks.index', ['keyword' => '検索対象']))
             ->assertSee('検索対象タスク')
             ->assertDontSee('その他のタスク');
   }

   // コメントの追加編集削除
   public function test_add_edit_delete_comment()
   {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        // コメント追加
        $response = $this->post(route('comments.store', $task->id), [
            'content' => '新しいコメント'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'task_id' => $task->id,
            'content' => '新しいコメント'
        ]);

        $comment = Comment::first();

        // コメント編集
        $response = $this->put(route('comments.update', $comment->id), [
            'content' => '編集済みコメント'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => '編集済みコメント'
        ]);

        // コメント削除
        $response = $this->delete(route('comments.destroy', $comment->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id
        ]);
   }

   // タスクにタグが正しく表示される
   public function test_tags_are_displayed_correctly_in_tasks()
   {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'title' => 'タグ付きタスク']);
        $tag = Tag::factory()->create(['name' => '重要']);
        $task->tags()->attach($tag->id);

        $this->actingAs($user)
             ->get(route('tasks.index'))
             ->assertSee('重要')
             ->assertSee('タグ付きタスク');
   }
}
