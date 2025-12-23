<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamTaskWorkLog extends Model
{
    protected $fillable = ['team_task_id', 'user_id', 'worked_minutes', 'worked_at', 'note'];

    protected $casts = [
        'worked_at' => 'date',
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
