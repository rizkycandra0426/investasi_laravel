<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        if($token != null){   
            $user = \App\Models\User::where('api_token','=',$token)->first();
            if($user){
                $request->merge(['user' => $user]);
                if(isset($request->admin)){
                    unset($request->admin);
                }
                return $next($request);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Akses ditolak.',
        ], Response::HTTP_FORBIDDEN);
    }
}
