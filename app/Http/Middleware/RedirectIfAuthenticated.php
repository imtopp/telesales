<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
          if(Auth::User()->userRole->name=="Administrator"){
            return redirect(\URL::route('administrator_home'));
          }else if(Auth::User()->userRole->name=="Agent"){
            return redirect(\URL::route('agent_home'));
          }else if(Auth::User()->userRole->name=="Order Fulfillment"){
            return redirect(\URL::route('order_fulfillment_home'));
          }else{
            return redirect('/');
          }
        }

        return $next($request);
    }
}
