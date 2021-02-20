<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
//        if (Auth::guard($guard)->check()) {
//            if (auth()->user()->isAdmin() || auth()->user()->roles->groupPermission()->where('name', 'admin_dashboard')->first())
//                return redirect()->route('admin_dashboard.view');
//
//            return redirect()->route('home');
//        }

        return $next($request);
    }
}
