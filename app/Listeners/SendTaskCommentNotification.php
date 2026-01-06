<?php

namespace App\Listeners;

use App\Events\TaskCommentCreated;
use App\Notifications\TaskCommented;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;


class SendTaskCommentNotification
{
    // DBコミット後に処理
    public bool $afterCommit = true;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCommentCreated $event): void
    {
        $task = $event->task;
        $actor = $event->actor;

        $recipients = $task->team->members()
            ->whereNotNull('email')
            ->get()
            ->unique('id')
            ->reject(fn ($u) => $u->id === $actor->id);

        Notification::send($recipients, new TaskCommented($task, $event->comment));
    }
}
