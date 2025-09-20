<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

            // Start a database transaction to ensure both inserts succeed
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
                    'user_Password' => '', // No password for Google login
                    'user_Type' => 'customer',
                ]);

                Log::info('User created with ID: ' . $newUserID);

                // Always create a customer entry for new Google users
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
            }

            DB::commit();

            // Log user in
            session([
                'user_id' => $user->userID,
                'user_email' => $user->user_Email,
                'user_type' => $user->user_Type,
            ]);

            // Redirect based on user type
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
