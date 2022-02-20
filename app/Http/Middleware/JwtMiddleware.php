<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Lang;
use Exception;

class JwtMiddleware
{
    protected $auth;
    protected $jwt;
    private $global_data = [];
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$this->jwt->setToken($this->jwt->getToken())->authenticate()) {
                $this->info = TOKEN_INVALID;
            } else {
                $token = $this->jwt->getToken();
                $user = $this->jwt->toUser($token);
                $this->global_data['logged_user_id'] = $user->id;
                $request->merge($this->global_data);
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => TOKEN_INVALID]);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => TOKEN_EXPIRED]);
            } else {
                return response()->json(['status' => NOT_FOUND]);
            }
        }
        return $next($request);
    }
}
