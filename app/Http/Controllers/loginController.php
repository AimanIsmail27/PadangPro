<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use App\Models\User;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Administrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

// Show the form to request a reset link (Already handled by closure in web.php, but good to have here)
public function showForgotPasswordForm()
{
    return view('auth.forgot-password');
}

// Handle the email submission
public function sendResetLinkEmail(Request $request)
{
    // 1. Validate email
    $request->validate(['email' => 'required|email']);

    // 2. Check if user exists
    $user = User::where('user_Email', $request->email)->first();

    // For security, even if user doesn't exist, we tell them the email was sent
    if (!$user) {
        return back()->with('status', 'If an account exists for this email, a reset link has been sent.');
    }

    // 3. Create a unique token
    $token = Str::random(64);

    // 4. Store token in password_reset_tokens table (default Laravel table)
    // If you don't have this table, run: php artisan make:notifications-table
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        [
            'email' => $request->email,
            'token' => Hash::make($token), // We hash it for security
            'created_at' => Carbon::now()
        ]
    );

    // 5. Send the Email
    // Note: You will need to create a simple 'emails.forgetPassword' view later
    Mail::send('emails.forgetPassword', ['token' => $token, 'email' => $request->email], function($message) use($request){
        $message->to($request->email);
        $message->subject('Reset Password - PadangPro');
    });

    return back()->with('status', 'We have e-mailed your password reset link!');
}

// Show the form to enter a NEW password
public function showResetPasswordForm($token, Request $request)
{
    // We pass the token and email (from the URL) to the view
    return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
}

// Logic to update the password in the database
public function updatePassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed', // 'confirmed' looks for password_confirmation field
        'token' => 'required'
    ]);

    // 1. Verify the token exists and is valid
    $resetData = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$resetData || !Hash::check($request->token, $resetData->token)) {
        return back()->with('error', 'Invalid or expired reset link.');
    }

    // 2. Update the user's password
    $user = User::where('user_Email', $request->email)->first();
    if (!$user) {
        return back()->with('error', 'User not found.');
    }

    $user->update([
        'user_Password' => Hash::make($request->password)
    ]);

    // 3. Delete the token so it can't be used again
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('success', 'Password reset successfully! You can now login.');
}
}
