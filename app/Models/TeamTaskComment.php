<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamTaskComment extends Model
{
    protected $fillable = [
        'team_task_id',
        'user_id',
        'comment',
    ];

    public function task()
    {
        return $this->belongsTo(TeamTask::class, 'team_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
