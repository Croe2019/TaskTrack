<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'color'];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function isOwner(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->where('team_user.role', 'owner')
            ->exists();
    }

    public function isAdmin(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', ['owner', 'admin'])
            ->exists();
    }

    public function ownersAndAdmins(): BelongsToMany
    {
        return $this->members()
            ->wherePivotIn('role', ['owner', 'admin']);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function teamTasks()
    {
        return $this->hasMany(TeamTask::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
                ->withPivot('role')
                ->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function hasUser(User $user): bool
    {
        return $this->users()
            ->where('users.id', $user->id)
            ->exists();
    }

}
