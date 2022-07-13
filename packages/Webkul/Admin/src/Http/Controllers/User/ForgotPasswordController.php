<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Notifications\User\UserResetPassword;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Show the form for creating a new resource.
     * 簡單的 check user token 之後傳回 views/session/forgot-password
     ******************************* 不用 ************************************
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (auth()->guard('user')->check()) {
            return redirect()->route('admin.dashboard.index');
        } else {
            if (strpos(url()->previous(), 'user') !== false) {
                $intendedUrl = url()->previous();
            } else {
                $intendedUrl = route('admin.dashboard.index');
            }

            session()->put('url.intended', $intendedUrl);

            return view('admin::sessions.forgot-password');
        }
    }

    /**
     * Store a newly created resource in storage.
     * 驗證 email
     * 送出包含有 reset password link 的 email，然後回到上一頁
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        try {
            $this->validate(request(), [
                'email' => 'required|email',
            ]);

            $response = $this->broker()->sendResetLink(request(['email']), function ($user, $token) {
                $user->notify(new UserResetPassword($token));
            });

            if ($response == Password::RESET_LINK_SENT) {
                session()->flash('success', trans('admin::app.sessions.forgot-password.reset-link-sent'));

                // return back();
                return response()->json([
                    'status' => "OK",
                ]);
            }

            // return back()
            //     ->withInput(request(['email']))
            //     ->withErrors([
            //         'email' => trans('admin::app.sessions.forgot-password.email-not-exist'),
            //     ]);
            return response()->json([
                'status' => "Failed",
                'error' => 'email not exist'
            ]);

        } catch (\Exception $exception) {
            session()->flash('error', trans($exception->getMessage()));

            // return redirect()->back();
            return response()->json([
                'status' => 'Failed',
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Get the broker to be used during password reset.
     * broker 參數會去尋找 config/auth.php 的 password.users
     * 原始碼在 framework/src/Illuminate/Auth/Passwords/PasswordBrokerManager.php 
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('users');
    }
}
