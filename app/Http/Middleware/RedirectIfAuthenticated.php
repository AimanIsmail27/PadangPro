<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('user_id')) {
            // Redirect based on user type
            $userType = $request->session()->get('user_type');
            switch ($userType) {
                case 'staff':
                    return redirect()->route('staff.dashboard');
                case 'administrator':
                    return redirect()->route('administrator.dashboard');
                case 'customer':
                    return redirect()->route('customer.dashboard');
            }
        }

        return $next($request);
    }
}
