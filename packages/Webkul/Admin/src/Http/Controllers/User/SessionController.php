<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Controllers\Controller;

class SessionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * 驗證 email 格式，password required
     * 
     *
     * @return \Illuminate\Http\Response
     */
    //post login
    // public function store(Request $request)
    public function store()
    {

        Log::info("=============login user:=============");
        Log::info(request()->headers);
        $this->validate(request(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->ReturnJsonFailMsg(config("app.error_code.invalid_password_or_email"));
        }
        $user = auth()->user();

        return $this->ReturnJsonSuccessMsg([
            "_token" => $token,
            "id" => $user->id,
            "account" => $user->name
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        // auth()->guard()->logout();

        return $this->ReturnJsonSuccessMsg('OK');
    }

    public function TokenVerify()
    {
        Log::info("=============TokenVerify user:=============");
        $user = auth()->user();
        Log::info($user);
        Log::info("=============TokenVerify header:=============");
        Log::info(request()->headers);
        // return $this->ReturnJsonSuccessMsg($user);
        // return "hello";

        if (!is_null($user))
            return $this->ReturnJsonSuccessMsg("");
        else
            return $this->ReturnJsonFailMsg("");

    }



}
