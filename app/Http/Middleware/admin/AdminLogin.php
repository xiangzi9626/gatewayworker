<?php

namespace App\Http\Middleware\admin;

use Closure;

class AdminLogin
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
            return redirect("/");
        }else if(session("user")->level>=3){
            return redirect("/");
        }
        return $next($request);
    }
}
