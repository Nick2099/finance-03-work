<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('appoptions.maintenance_lock_up')) {
            Auth::logout(); // Ensure the user is logged out if in maintenance mode
            $whitelistedRoutes = ['home', 'about', 'contact'];
            if (!in_array($request->route()?->getName(), $whitelistedRoutes)) {
                return response()->view('errors.maintenance');
            }
        }

        if (session()->has('locale')) {
            App::setLocale(session('locale'));
        } elseif (Auth::check()) {
            App::setLocale(Auth::user()->language);
        }

        return $next($request);
    }
}
