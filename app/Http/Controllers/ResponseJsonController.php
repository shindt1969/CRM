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
            return array_merge($ok, $data);
        }
        else
        {
            return array("status" => true, "Message" => $data);
        }

    }

    public function ReturnFailMsg($data)
    {
        return array("status" => false, 'error' => $data);
    }
}
