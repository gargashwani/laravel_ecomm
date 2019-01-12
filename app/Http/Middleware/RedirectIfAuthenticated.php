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
            // dd(Auth::user()->role->name);
            // This middleware works for the users, who registered through the register page like customers.
            // so, if any loggedin user is admin, and then he tries to goto customer register page, he will
            // be redirected to the admin dashboard page with this check.

            if(Auth::user()->role->name == 'admin')
            {
                return redirect('/admin');
            }
            else{
                return redirect('/home');
            }

        }

        return $next($request);
    }
}
