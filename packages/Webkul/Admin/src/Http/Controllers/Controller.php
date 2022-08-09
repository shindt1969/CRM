<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use App\Http\Controllers\ResponseJsonController;
use Webkul\Admin\Functions\ResponseJson;




class Controller extends BaseController
{
    public static function ReturnJsonSuccessMsg($data)
    {
        if (is_array($data)) {
            $ok = array("status" => true);
            return response()->json(array_merge($ok, $data));
        } else {
            return response()->json(array("status" => true, "message" => $data));
        }
    }

    public static function ReturnJsonFailMsg($data)
    {
        return response()->json(array("status" => false, 'error' => $data));
    }

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToLogin()
    {
        return redirect()->route('admin.session.create');
    }
}
