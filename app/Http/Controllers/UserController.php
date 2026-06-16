<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request)
    {
        $user = User::updateUser($request->all());

        return $this->success(new UserResource($user), 'Profile updated successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return $this->success(new UserResource(auth()->user()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->success([], 'User deleted successfully');
    }
}
