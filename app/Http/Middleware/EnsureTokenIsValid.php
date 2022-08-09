<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Webkul\Admin\Http\Controllers\Controller;

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
            return Controller::ReturnJsonFailMsg('Invalid token');
        }
        return $next($request);
    }
}
