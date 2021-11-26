<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Authorization;
use App\Models\Session;
use App\Transformers\AuthorizationTransformer;

class AuthController extends BaseController
{
    function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string|min:5',
            'user_agent' => 'required'
        ]);

        $credentials = $request->only('username', 'password');

        if(!$token = \Auth::attempt($credentials)) {
            $this->response->errorUnauthorized("Wrong username or password");
        }

        $authorization = new Authorization($token);

        $user = $authorization->user();
        
        $session = Session::create([
            'user_id' => $user->id,
            'token' => $token,
            'user_agent' => $request->user_agent,
            'login_at' => date("Y-m-d H:i:s")
        ]);

        return $this->response->item($authorization, new AuthorizationTransformer);
    }
}