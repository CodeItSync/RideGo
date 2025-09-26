<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class OnlyRidersAndActiveDriversCanPassMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if (auth()->user()->user_type != 'driver' || auth()->user()->status == 'active') {
            return $next($request);
        }else {
            auth()->logout();
            abort(401, __('message.access_denied'));
        }
    }
}
