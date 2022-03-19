<?php

namespace App\Http\Middleware\browser;

use Closure;

class BrowserMiddleware
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
         if($request->isMethod('get')){
        $browser=$_SERVER["HTTP_USER_AGENT"];
        $url="/static/upgrade_browser/upgrade.html";
        if (strpos($browser,"MSIE 8.0")){
            return redirect($url);
        }else if(strpos($browser,"MSIE 7.0")){
            return redirect($url);
        }else if(strpos($browser,"MSIE 6.0")){
            return redirect($url);
        }
        }
        return $next($request);
    }
}
