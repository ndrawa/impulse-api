<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    public function me(Request $request)
    {
        return $this->response->item($this->user(), new UserTransformer);
    }

    public function updateUsername(Request $request)
    {
        $this->validate($request, [
            'username' => [
                'required',
                'min:5',
                Rule::unique('users')->ignore($this->user()->username, 'username')
            ]
        ]);
        $user = $this->user();
        $user->username = $request->input('username');
        $user->save();

        return $this->response->item($user, new UserTransformer);
    }

    public function updatePassword(Request $request)
    {
        $user = $this->user();
        $this->validate($request, [
            'old_password' => [
                'required',
                'min:5',
                function($attribute, $value, $fail) use ($user) {
                    if(!Hash::check($value, $user->password)) {
                        $fail("Wrong old password");
                    }
                }
            ],
            'new_password' => 'required|min:5',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return $this->response->noContent();
    }
}