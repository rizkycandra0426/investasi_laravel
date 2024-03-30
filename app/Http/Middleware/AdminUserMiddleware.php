<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {        
        $fullToken = $request->header('Authorization');
        $tokenArray = explode('Bearer ',$fullToken);
        if (count($tokenArray) == 2) {
            $tokenValue = trim($tokenArray[1]);
            if($admin = \App\Models\Admin::where('api_token','=',$tokenValue)->first()){
                $request->merge(['admin' => $admin]);
                if(isset($request->user)){
                    unset($request->user);
                    $this->myPrivateMethod('user',$request->user);
                }                
                return $next($request);
            }

            if($user = \App\Models\User::where('api_token','=',$tokenValue)->first()){
            // if($dosen = Auth::guard('dosen')->user()){
                $request->merge(['user' => $user]);
                if(isset($request->admin)){
                    unset($request->admin);
                    $this->myPrivateMethod('admin',$request->admin);
                }

                

                return $next($request);
            }

            
            
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Akses ditolak.',
            'data'=>$tokenArray, 
        ], Response::HTTP_FORBIDDEN);
    }

    private function myPrivateMethod($role,$val){
        if($role === 'admin'){
            $adm = \App\Models\Admin::find($val->id);
            $adm->api_token = null;
            $adm->update();
        }else{
            $user = \App\Models\User::find($val->id);
            $user->api_token = null;
            $user->update();
        }
    }
}
