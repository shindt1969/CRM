<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Webkul\Admin\Http\Controllers\Controller;

class SessionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     ****************************** 不用 *********************************
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (auth()->guard('user')->check()) {
            return redirect()->route('admin.dashboard.index');
        } else {
            if (strpos(url()->previous(), 'admin') !== false) {
                $intendedUrl = url()->previous();
            } else {
                $intendedUrl = route('admin.dashboard.index');
            }
            session()->put('url.intended', $intendedUrl);
            return view('admin::sessions.login');
        }
    }

    /**
     * Store a newly created resource in storage.
     * 驗證 email 格式，password required
     * 
     *
     * @return \Illuminate\Http\Response
     */
    //post login
    public function store(Request $request)
    {
        $this->validate(request(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // 1.帳號或密碼錯誤
        if (!auth()->guard('user')->attempt(request(['email', 'password']), request('remember'))) {
            session()->flash('error', trans('admin::app.sessions.login.login-error'));
            return $this->ReturnJsonFailMsg(trans('admin::app.sessions.login.login-error'));
            // return redirect()->back();
        }

        if (auth()->guard('user')->user()->status == 0) {
            session()->flash('warning', trans('admin::app.sessions.login.activate-warning'));
            auth()->guard('user')->logout();
            return $this->ReturnJsonFailMsg(trans('admin::app.sessions.login.activate-warning'));
            // return redirect()->route('admin.session.create');
        }
        // return $this->ReturnJsonSuccessMsg('OK');
        return redirect()->intended(route('admin.dashboard.index'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        auth()->guard('user')->logout();

        return $this->ReturnJsonSuccessMsg('OK');
        // return redirect()->route('admin.session.create');
    }
}
