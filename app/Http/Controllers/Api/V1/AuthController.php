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
        ]);

        $credentials = $request->only('username', 'password');

        if(!$token = \Auth::attempt($credentials)) {
            $this->response->errorUnauthorized("Wrong username or password");
        }

        $authorization = new Authorization($token);

        $user = $authorization->user();

        if($this->authenticated($user)) {
            $user_id = $user->id;
            $user_session = Session::where('user_id', $user_id)->first();
            if($user_session){
                $now = \Carbon\Carbon::now()->toDateTimeString();
                if($user_session['expired_at'] < $now){
                    $user_session->delete();
                }
                else{
                    return $this->response->errorUnauthorized("User ini telah login di perangkat lain. Coba login kembali.");
                }
            }
        }

        $expired_time = $authorization->toArray()['expired_at'];

        $session = Session::create([
            'user_id' => $user->id,
            'token' => $token,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'login_at' => date("Y-m-d H:i:s"),
            'expired_at' => $expired_time,
        ]);

        return $this->response->item($authorization, new AuthorizationTransformer);
    }

    public function authenticated($user) {
        $login = Session::where('user_id', $user->id)->first();

        if($login) {
            if(strtotime($login->expired_at) < strtotime(date('Y-m-d H:i:s'))) {
                $this->logout($user->id);
                return TRUE;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function logout($user_id) {
        $user_session = Session::where('user_id', $user_id)->first()->delete();
    }
}
