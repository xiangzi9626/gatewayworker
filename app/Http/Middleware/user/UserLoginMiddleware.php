<?php

namespace App\Http\Middleware\user;

use Closure;

class UserLoginMiddleware
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
        if (session("user")==false){
            return redirect("/user/login");
        }
        return $next($request);
    }
}
