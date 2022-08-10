<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Webkul\Admin\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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
        catch(TokenInvalidException $te){
            // invalid token
            return Controller::ReturnJsonFailMsg('0');
        }
        catch(JWTException $je){
            // No token
            return Controller::ReturnJsonFailMsg('1');
        }
        return $next($request);
    }
}
