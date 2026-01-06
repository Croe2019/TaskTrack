<?php

namespace App\Notifications;

use App\Models\TeamTask;
use App\Models\TeamTaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCommented extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TeamTask $task, public TeamTaskComment $comment)
    {


    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): MailMessage
    {
        return (new MailMessage)
                ->subject("【コメント】{$this->task->title}")
                ->line('タスクに新しいコメントが投稿されました。')
                ->line("タスク: {$this->task->title}")
                ->line('コメント: ' . ($this->comment->body ?? ''))
                ->action('タスクを開く', route('teams.tasks.show', [$this->task->team_id, $this->task->id]))
                ->line('このメールは自動送信です。');
    }
}
