<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Lang;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware extends BaseMiddleware
{
    use ResponseTrait;

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
            if (JWTAuth::getToken()) {
                $user = JWTAuth::parseToken()->authenticate();
                return $next($request);
            } else {
                return $this->sendFailedResponse(Lang::get('messages.user.auth_token_not_found'), 401);
            }
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return $this->sendFailedResponse(Lang::get('messages.user.token_invalid'), 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return $this->sendFailedResponse(Lang::get('messages.user.token_expired'), 401);
            } else {
                return $this->sendFailedResponse(Lang::get('messages.user.auth_token_not_found'), 401);
            }
        }
    }
}
