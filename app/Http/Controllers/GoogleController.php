<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;


class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

   public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        DB::beginTransaction();

        // Check if user already exists
        $user = User::where('user_Email', $googleUser->getEmail())->first();

        if (!$user) {
            // Generate a unique userID
            $newUserID = strtoupper(uniqid('USR'));

            // Create the new user
            $user = User::create([
                'userID' => $newUserID,
                'user_Email' => $googleUser->getEmail(),
                'user_Password' => '',
                'user_Type' => 'customer',
            ]);

            Log::info('User created with ID: ' . $newUserID);

            // Create customer record
            $customerID = 'CM' . strtoupper(Str::random(8));
            Customer::create([
                'customerID' => $customerID,
                'userID' => $newUserID,
                'customer_FullName' => $googleUser->getName() ?? 'Google User',
                'customer_Age' => null,
                'customer_PhoneNumber' => null,
                'customer_Address' => null,
                'customer_Position' => null,
            ]);

            Log::info('Customer created with ID: ' . $customerID);

            // ✅ Queue email instead of sending immediately
            Mail::to($googleUser->getEmail())
                ->queue(new WelcomeMail($googleUser->getName() ?? 'Google User'));
        }

        DB::commit();

        // Log user in
        session([
            'user_id' => $user->userID,
            'user_email' => $user->user_Email,
            'user_type' => $user->user_Type,
        ]);

        return match ($user->user_Type) {
            'administrator' => redirect()->route('administrator.dashboard')->with('success', 'Login successful'),
            'staff' => redirect()->route('staff.dashboard')->with('success', 'Login successful'),
            default => redirect()->route('customer.dashboard')->with('success', 'Login successful'),
        };

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Google login failed: ' . $e->getMessage());
        return redirect()->route('login')->with('error', 'Failed to login with Google');
    }
}

}
