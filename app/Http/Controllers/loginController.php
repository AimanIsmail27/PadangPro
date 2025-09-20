<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use App\Models\User;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Administrator;

class loginController extends Controller
{
    // Handle login
    public function login(Request $request)
    {
        // 1. Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // 2. Retrieve user
        $user = User::where('user_Email', $request->email)->first();

        // 3. Check if user exists
        if (!$user) {
            return back()->with('error', 'Email not found')->withInput();
        }

        // 4. Verify password
        if (!Hash::check($request->password, $user->user_Password)) {
            return back()->with('error', 'Incorrect password')->withInput();
        }

       

        // 5. Store user data in session
        $request->session()->put('user_id', $user->userID);
        $request->session()->put('user_email', $user->user_Email);
        $request->session()->put('user_type', $user->user_Type);
        

        

        // 6. Redirect based on user type
    switch ($user->user_Type) {
        case 'administrator':
            $admin = Administrator::where('userID', $user->userID)->first();
            
            return redirect()->route('administrator.dashboard')->with('success', 'Login successful');

        case 'staff':
            $staff = Staff::where('userID', $user->userID)->first();
            
            return redirect()->route('staff.dashboard')->with('success', 'Login successful');

        case 'customer':
            $customer = Customer::where('userID', $user->userID)->first();
           
            return redirect()->route('customer.dashboard')->with('success', 'Login successful');

        default:
            $request->session()->flush();
            return redirect()->route('login')->with('error', 'Unknown user type.');
    }
    }

    // Logout user
    public function logout(Request $request)
{
    // If using Auth facade (optional, but good practice)
    // Auth::logout();

    // Invalidate the session completely
    $request->session()->invalidate();

    // Regenerate CSRF token to prevent reuse
    $request->session()->regenerateToken();

    // Redirect to login page
    return redirect()->route('login')->with('success', 'Logged out successfully');
}

}
