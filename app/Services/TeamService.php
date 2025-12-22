<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\DB;


class TeamService
{
    public function getMember()
    {
        $teams = Auth::user()
        ->teams()
        ->withCount('members')
        ->get();

        return $teams;
    }

    public function store(array $data, User $owner)
    {
        return DB::transaction(function () use ($data, $owner){

            $team = Team::create($data);

            // owner を team_user に登録
            $team->members()->attach($owner->id, ['role' => 'owner']);
            return $team;
        });
    }

    public function update(Team $team, array $data)
    {
        return DB::transaction(function () use ($team, $data){
            $team->update($data);
            return $team;
        });
    }

    public function delete(Team $team)
    {
        return $team->delete();
    }
}
