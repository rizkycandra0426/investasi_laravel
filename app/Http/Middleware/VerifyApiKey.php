<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->hasHeader('x-api-key') && $request->header('x-api-key') == env('API_KEY')) {
            return $next($request);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Akses ditolak. API key tidak sesuai.',
        ], Response::HTTP_FORBIDDEN);
    }
}
