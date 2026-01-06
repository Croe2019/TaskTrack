<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamTask extends Model
{
    protected $fillable = [
        'team_id',
        'creator_id',
        'assignee_id',
        'title',
        'description',
        'status',
        'completed_at',
    ];

    const STATUS_OPEN      = 'open';
    const STATUS_DOING     = 'doing';
    const STATUS_DONE      = 'done';

    protected $casts = [
        'completed_at' => 'datetime',
    ];


    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_DOING,
            self::STATUS_DONE,
        ];
    }

    public function team(): BelongsTo // ここに戻り値を設定している理由を聞く
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TeamTaskComment::class, 'team_task_id');
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(TeamTaskWorkLog::class, 'team_task_id');
    }

}
