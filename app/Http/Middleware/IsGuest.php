<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd(Auth::check());
        // if ($request->is('login') || $request->is('login/*')) {
        //     return $next($request);
        // }

        // Check if the user is authenticated
        if (!Auth::check()) {
            return $next($request);
        } else {
            return redirect()->back()->with('cantAccess', 'Anda sudah login!');
        }
    }
    }
