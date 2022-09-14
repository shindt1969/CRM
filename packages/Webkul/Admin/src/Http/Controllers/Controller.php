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
        $error_message = [
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute field must be unique.',
            'exists' => 'The :attribute doesn\'t exist.' 
        ];

        $validator = Validator::make($request->all(), $rule, $error_message);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $requests = $request->all();
            Log::error("validate error, request: ");
            Log::error($requests);
            Log::error("error: ");
            Log::error($errors);
            throw new HttpResponseException(Controller::ReturnJsonFailMsg(config('app.error_code.field_error')));
        }

    }
}
