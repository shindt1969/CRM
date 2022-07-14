<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResponseJsonController extends Controller
{
    public function ReturnSuccessMsg($data)
    {
        if (is_array($data))
        {
            $ok = array("status" => true);
            return  json_encode(array_merge($ok, $data));
        }
        else
        {
            return  json_encode(array("status" => true, "Message" => $data));
        }

    }

    public function ReturnFailMsg($data)
    {
        return  json_encode(array("status" => false, 'error' => $data));
    }
}
