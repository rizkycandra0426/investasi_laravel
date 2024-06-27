<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = request()->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);
        if ($accessToken != null) {
            $current_user = $accessToken->tokenable;
            $request->current_user = $current_user;
            $request->user_id = $current_user->user_id;
        }
        else {
            $request->current_user = null;
            $request->user_id = null;   
        }
        return $next($request);
    }
}
