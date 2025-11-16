<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles (e.g., 'administrator', 'staff', 'customer')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userType = $request->session()->get('user_type');

        // If the user's type is in the list of allowed roles, let them pass
        if (in_array($userType, $roles)) {
            return $next($request);
        }

        // If they are not allowed, redirect them to their own dashboard.
        $dashboardRoute = match($userType) {
            'administrator' => 'administrator.dashboard',
            'staff' => 'staff.dashboard',
            'customer' => 'customer.dashboard',
            default => 'login',
        };

        return redirect()->route($dashboardRoute)
                         ->with('error', 'You are not authorized to access that page.');
    }
}