<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;




class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToLogin()
    {
        // return redirect()->route('admin.session.create');
    }

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

    public function validate($request, $rule)
    {
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            throw new HttpResponseException(Controller::ReturnJsonFailMsg(config('app.error_code.field_error')));
        }

    }
}
