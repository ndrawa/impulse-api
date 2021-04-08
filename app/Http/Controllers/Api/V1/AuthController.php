<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Authorization;
use App\Transformers\AuthorizationTransformer;

class AuthController extends BaseController
{
    function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string|min:5'
        ]);

        $credentials = $request->only('username', 'password');

        if(!$token = \Auth::attempt($credentials)) {
            $this->response->errorUnauthorized("Wrong username or password");
        }

        $authorization = new Authorization($token);

        $user = $authorization->user();

        return $this->response->item($authorization, new AuthorizationTransformer);
    }
}