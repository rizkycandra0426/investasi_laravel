<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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

class AuthenticationController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->responseError($validator->errors(), 'Data tidak valid.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!Auth::attempt($request->all())) {
            return $this->responseError(null, 'Email dan Password tidak sesuai!', Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request->email)->first();

        $data = [
            'token_type' => 'bearer',
            'accessToken' => $user->createToken('token')->plainTextToken,
            'userData' => $user,
        ];

        return $this->successResponse($data, 'Login berhasil!');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect('/');
    }

    /**
     * Create User
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $checkuser = User::where('email', $request['email'])->first();
        if ($checkuser) {
            return response()->json([
                'error' => 1, 
                'message' => 'user already exists', 
                'code' => 409
            ]);
        }

        $user = User::create([
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'name' => $request['name'],
        ]);

        return response()->json([
            'error' => 1, 
            'message' => 'Registration Successfully', 
            'code' => 200,
            "data" => $user
        ]);

        // return $this->successResponse($user, 'Registration Successfully');
    }
}