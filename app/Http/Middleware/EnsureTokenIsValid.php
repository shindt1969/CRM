<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Webkul\Admin\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            auth()->invalidate();
        }
        catch(TokenInvalidException $ie){
            // invalid token
            Log::info(request()->header());
            return Controller::ReturnJsonFailMsg(config("app.error_code.invalid_token"));
        }
        catch(TokenExpiredException $ee){
            // Token expired
            Log::info(request()->header());
            return Controller::ReturnJsonFailMsg(config("app.error_code.token_expired"));
        }
        catch(JWTException $je){
            // Token expired
            Log::info(request()->header());
            return Controller::ReturnJsonFailMsg(config("app.error_code.token_expired"));
        }
        return $next($request);
    }
}
