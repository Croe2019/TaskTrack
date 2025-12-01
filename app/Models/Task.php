<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'status', 'priority', 'deadline', 'completed_at', 'user_id'];
    // 日付カラムをCarbonに変換
    protected $casts = [
        'deadline'     => 'datetime',
        'completed_at' => 'datetime',
    ];

    // 完全自動化するために記述
    public function getStatusLabelAttribute()
    {
        return [
            'not_started' => '未着手',
            'in_progress' => '進行中',
            'completed'   => '完了',
        ][$this->status] ?? '';
    }

    public function getPriorityLabelAttribute()
    {
        return [
            'high'   => '高',
            'medium' => '中',
            'low'    => '低',
        ][$this->priority] ?? '';
    }

    // リレーション
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_task');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}
