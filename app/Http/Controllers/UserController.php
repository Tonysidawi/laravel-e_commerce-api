<?php

namespace App\Http\Controllers;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function update(UserUpdateRequest $request)
    {
        $user = User::updateMe($request->all());

        return $this->success(new UserResource($user), 'Profile updated successfully');
    }

    public function show()
    {
        return $this->success(new UserResource(auth()->user()));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return $this->success([], 'User deleted successfully');
    }


}
