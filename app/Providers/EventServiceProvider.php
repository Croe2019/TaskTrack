<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
    \App\Events\TaskCommentCreated::class => [
        \App\Listeners\SendTaskCommentNotification::class,
    ],
    \App\Events\TaskMarkedCompleted::class => [
        \App\Listeners\SendTaskCompletedNotification::class,
    ],
];

}
