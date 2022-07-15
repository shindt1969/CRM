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
    public function ReturnJsonSuccessMsg($data)
    {
        if (is_array($data)) {
            $ok = array("status" => true);
            return  json_encode(array_merge($ok, $data));
        } else {
            return  json_encode(array("status" => true, "Message" => $data));
        }
    }

    public function ReturnJsonFailMsg($data)
    {
        return  json_encode(array("status" => false, 'error' => $data));
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
