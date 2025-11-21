<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getProfile($userId)
    {
        return $this->userRepo->find($userId);
    }

    public function updateProfile($userId, array $data)
    {
        // パスワードをハッシュ化
        if(!empty($data['password']))
        {
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']); // 空の場合は削除
        }

        // 画像アップロード
        if(!empty($data['avatar']))
        {
            $user = $this->userRepo->find($userId);
            if($user->avatar)
            {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $data['avatar']->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        return $this->userRepo->update($userId, $data);
    }
}
