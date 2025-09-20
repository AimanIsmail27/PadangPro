<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Administrator;
use App\Models\Staff;
use App\Models\Customer;

class ShareFullName
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('user_id') && session()->has('user_type')) {
            $userId = session('user_id');
            $userType = session('user_type');
            $fullName = 'User';

            switch ($userType) {
                case 'administrator':
                    $fullName = Administrator::where('userID', $userId)->value('admin_FullName') ?? 'Admin';
                    break;
                case 'staff':
                    $fullName = Staff::where('userID', $userId)->value('staff_FullName') ?? 'Staff';
                    break;
                case 'customer':
                    $fullName = Customer::where('userID', $userId)->value('customer_FullName') ?? 'Customer';
                    break;
            }

            view()->share('fullName', $fullName);
        }

        return $next($request);
    }
}
