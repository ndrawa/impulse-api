<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Authorization;
use App\Models\Session;
use App\Models\User;
use App\Transformers\AuthorizationTransformer;

class AuthController extends BaseController
{
    function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string|min:5',
            // 'user_agent' => 'required'
        ]);

        $credentials = $request->only('username', 'password');

        if(!$token = \Auth::attempt($credentials)) {
            $this->response->errorUnauthorized("Wrong username or password");
        }

        $authorization = new Authorization($token);

        $user = $authorization->user();

        if(!$this->authenticated($user)) {
            return $this->response->errorUnauthorized("This user has logged in somewhere else");
        }

        $session = Session::create([
            'user_id' => $user->id,
            'token' => $token,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'login_at' => date("Y-m-d H:i:s")
        ]);

        return $this->response->item($authorization, new AuthorizationTransformer);
    }

    public function authenticated($user) {
        $login = Session::where('user_id', $user->id)->count();
        if($login > 0) {
            $this->logout($user->id);
            return FALSE;
        }
        return TRUE;
    }

    public function logout($user_id) {
        $user_session = Session::where('user_id', $user_id)->first();
        $user_session->delete();
    }
}
