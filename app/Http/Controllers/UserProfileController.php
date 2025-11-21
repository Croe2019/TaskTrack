<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserProfileService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    protected $service;

    public function __construct(UserProfileService $service)
    {
        $this->service = $service;
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $this->service->getProfile($user->id);
        return view('userProfile.userProfile', compact('profile'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        if($request->hasFile('avatar'))
        {
            $data['avatar'] = $request->file('avatar');
        }

        $this->service->updateProfile($user->id, $data);

        return redirect()->route('userProfile.userProfile')->with('success', 'プロフィールを更新しました');
    }
}
