<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Exception;

class JWTMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            dd($e);
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response("Token is invalid", 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response("Token is expired", 401);
            } else {
                return response("Token is not found", 401);
            }
        }
        return $next($request);
    }
}
