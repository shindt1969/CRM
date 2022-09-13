<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Webkul\Admin\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($token = null)
    {
        return response()->json([
            'status' => "OK",
            'token' => $token
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * reset()的原型在 Illuminate\Auth\Passwords\PasswordBroker;
     * $this->broker()->reset() 檢查 password 跟 token 之後 call resetPassword
     * broker 的原型在 vendor\laravel\framework\src\Illuminate\Auth\Passwords\PasswordBroker.php
     *
     * @return \Illuminate\Http\Response
     */
    
    public function store()
    {
        // try {
            $this->validate(request(), [
                // 'token'    => 'required',
                'email'    => 'required|email',
                'password' => 'required|confirmed|min:6',
                'password_confirmation' => 'required_with:password|same:password'
            ]);

            $token = auth()->user()->token;
            Log::info('token'.$token);

            $data = array_merge(
                request(['email', 'password', 'password_confirmation']),
                ['token' => $token]
            );
            Log::info($data);

            $response = $this->broker()->reset(
                $data, function ($admin, $password) {
                    $this->resetPassword($admin, $password);
                }
            );

            if ($response == Password::PASSWORD_RESET) {
                return $this->ReturnJsonSuccessMsg("ok");
            }

            return $this->ReturnJsonFailMsg(trans($response));

        // } catch(\Exception $exception) {
        //     // session()->flash('error', trans($exception->getMessage()));
        //     return $this->ReturnJsonSuccessMsg($exception->getMessage());
        // }
    }

    /**
     * Reset the given admin's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $admin
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($admin, $password)
    {
        $admin->password = Hash::make($password);

        $admin->setRememberToken(Str::random(60));

        $admin->save();

        event(new PasswordReset($admin));

        auth()->login($admin);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('users');
    }
}