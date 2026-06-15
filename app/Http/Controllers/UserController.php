<?php

namespace App\Http\Controllers;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

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
