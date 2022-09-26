<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
        auth()->guard('api')->logout();

        return $this->ReturnJsonSuccessMsg('OK');
        // return redirect()->route('admin.session.create');
    }

    public function TokenVerify(){
        
    }

}
