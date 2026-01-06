<?php

namespace App\Listeners;

use App\Events\TaskMarkedCompleted;
use App\Notifications\TaskCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;


class SendTaskCompletedNotification
{
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
    public function handle(TaskMarkedCompleted $event): void
    {
        $task = $event->task;
        $actor = $event->actor;

        $recipients = $task->team->members()
            ->whereNotNull('email')
            ->get()
            ->unique('id')
            ->reject(fn ($u) => $u->id === $actor->id);

        foreach ($recipients as $user) {
            $user->notify(new TaskCompleted($task));
        }
        Notification::send($recipients, new TaskCompleted($task));
    }
}
