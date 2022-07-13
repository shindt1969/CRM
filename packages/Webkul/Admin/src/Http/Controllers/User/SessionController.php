<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use Webkul\Admin\Http\Controllers\Controller;

class SessionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * 檢查user 
     * 檢查過來的URL值是否包含admin，如果沒有就$intendedUrl等於跳轉前的URL
     * 將URL利用session做紀錄
     * check() 回到失敗後登入畫面
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
     *
     * @return \Illuminate\Http\Response
     */
    //post login
    public function store()
    {
        $this->validate(request(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (! auth()->guard('user')->attempt(request(['email', 'password']), request('remember'))) {
            session()->flash('error', trans('admin::app.sessions.login.login-error'));
            //  1.帳號或密碼錯誤
            return redirect()->back();
        }
            //  2.判斷登入者的status，status資料從資料庫抓取。
            //  找看看有沒有auth()->guard('user')->user()->status寫在哪裡MODEL。
        if (auth()->guard('user')->user()->status == 0) {
            session()->flash('warning', trans('admin::app.sessions.login.activate-warning'));
            // 將已經logout狀態作登出
            auth()->guard('user')->logout();
            return redirect()->route('admin.session.create');
            //  2.1 轉跳到'admin.session.create'，在routes.php
        }
        return redirect()->intended(route('admin.dashboard.index'));
            // 1.1判斷帳號密碼正確，尋找有沒有session()->put()的內容，
            // 如果有就導向session()->put()的內容  如果沒有就導向admin.dashboard.index
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        auth()->guard('user')->logout();

        return redirect()->route('admin.session.create');
    }
}