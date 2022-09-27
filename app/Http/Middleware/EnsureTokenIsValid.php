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
        // Log::info("path: ".$request->path());

        if (! $request->hasHeader('Authorization')) {
            Log::info('no_token and request headers: ');
            Log::info($request->headers);
            return Controller::ReturnJsonFailMsg(config("app.error_code.no_token"));
        }
        if (is_null(auth()->user())){
            return Controller::ReturnJsonFailMsg(config("app.error_code.invalid_token"));
        }
        
        return $next($request);
    }
}
