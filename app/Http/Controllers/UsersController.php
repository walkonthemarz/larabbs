<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth', ['except' => ['show']]);
        $this->middleware('auth');
    }

    public function show(User $user)
    {
        $this->authorize('update', $user);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
        $this->authorize('update', $user);
        
        $data = $request->all();

        if (isset($request->avatar)) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 416);

            if(is_array($result) && isset($result['path'])) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
