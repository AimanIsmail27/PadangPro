<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user_id') || !session()->has('user_type')) {
            return redirect()->route('login')->with('fail', 'Please login first.');
        }

        return $next($request);
    }
}
