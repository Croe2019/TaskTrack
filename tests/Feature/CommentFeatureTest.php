<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;
use App\Models\Comment;
use Tests\TestCase;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_edit_delete_comment()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // 追加
        $this->post(route('comments.store', $task->id), [
            'content' => '新しいコメント'
        ]);
        $this->assertDatabaseHas('comments', ['content' => '新しいコメント']);

        $comment = Comment::first();

        // 編集
        $this->put(route('comments.update', $comment->id), [
            'content' => '修正後のコメント'
        ]);
        $this->assertDatabaseHas('comments', ['content' => '修正後のコメント']);

        // 削除
        $this->delete(route('comments.destroy', $comment->id));
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
