<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
/**
     * Create a new AuthController instance
     *
     * @return void
     */public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
/**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */public function login()
    {
        $credentials = request(['email', 'password']); // request 會抓傳進來的參數變成一個 array，也可以帶入字串。
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorize'], 401);
        }
        return $this->respondWithToken($token);
    }
/**
     * Get a authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */public function me()
    {
        return response()->json(Auth::guard()->user());
    }
/**
     * Log the user out(Invalidate the Token).
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout()
    {
        Auth::guard()->logout();
        return response()->json(['message'=>'Successfully logged out']);
    }
/**
     * Refresh a Token.
     *
     * @return \Illuminate\Http\JsonResponse
     */public function refresh()
    {
        return $this->respondWithToken(Auth::guard()->refresh());
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>Auth::guard()->factory()->getTTL()*60
        ]);
    }
}