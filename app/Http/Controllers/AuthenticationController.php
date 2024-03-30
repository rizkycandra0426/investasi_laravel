<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Admin;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

class AuthenticationController extends Controller
{
    public function registerUser(Request $request) {
        try {
            $checkUser = User::where('email', $request->email)->first();
            if ($checkUser) {
                return response()->json([
                    "status" => 409,
                    "message" => 'User already exists.',
                ], Response::HTTP_CONFLICT);
            }    
            DB::beginTransaction();
            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "name" => $request->name,
            ]);
            DB::commit();    
            return response()->json([
                "status" => 200,
                "message" => 'Berhasil Membuat User.',
                "data" => $user->only(['user_id', 'email', 'name', 'created_at', 'updated_at']),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => 500,
                "message" => 'Gagal Membuat User.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }

    public function auth(Request $request): JsonResponse {
        if(!empty($request->admin)){
            return response()->json([
                'status' => 200,
                'message' => 'Admin terautentikasi.',
                'data' => [
                    'admin' => $request->admin
                ],
            ], Response::HTTP_OK);

            // return response()->json([
            //     'status' => 200,
            //     'message' => 'Login Berhasil',
            //     'token' => $token,
            //     'data' => $user,
            // ], Response::HTTP_OK); 
        }

        if(!empty($request->user)){
            return response()->json([
                'status' => 'success',
                'message' => 'User terautentikasi.',
                'data' => [
                    'user' => $request->user
                ],
            ], Response::HTTP_OK);
        }
    }
    
    public function login(Request $request):JsonResponse{
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($validated)) {
                throw new AuthenticationException('Email dan Password tidak sesuai.');
        }

        $credentials = $request->only('email', 'password');
        $user = Auth::guard('user')->getProvider()->retrieveByCredentials($credentials);
        if ($user && Auth::guard('user')->getProvider()->validateCredentials($user, $credentials)) {
            $token = Str::random(60);
            $user->api_token = $token;
            $user->update();    
            return response()->json([
                'status' => 200,
                'message' => 'Login Berhasil',
                'token' => $token,
                "data" => [
                                'user_id' => $user['user_id'],
                                'email' => $user['email'],
                                'name' => $user['name'],
                                'created_at' => $user['created_at'],
                                'updated_at' => $user['updated_at'],
                            ],
            ], Response::HTTP_OK); 
        } else {
            return response()->json(['message' => 'Autentikasi gagal'], 401);
        }
    }

    public function logout(Request $request){
        if ($data = $request->user){
            $user = User::find($data->user_id);
            $user->api_token = null;
            $user->update();
            return response()->json([
                'response_code' => 200,
                'Message' =>'Logout Berhasil',
            ], Response::HTTP_OK); 
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Anda Belum Login Data Tidak Tersedia.',
            ], Response::HTTP_NOT_FOUND);
        }
    }  
}