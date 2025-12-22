<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamTask extends Model
{
    protected $fillable = [
        'team_id',
        'creator_id',
        'assignee_id',
        'title',
        'description',
        'status',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments()
    {
        return $this->hasMany(TeamTaskComment::class);
    }

}
