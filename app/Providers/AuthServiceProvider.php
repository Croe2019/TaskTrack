<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\TeamTask;
use App\Models\TeamTaskComment;
use App\Policies\TeamPolicy;
use App\Policies\TeamTaskPolicy;
use App\Policies\TeamTaskCommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Team::class => TeamPolicy::class,
        TeamTask::class => TeamTaskPolicy::class,
        TeamTaskComment::class => TeamTaskCommentPolicy::class,
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
