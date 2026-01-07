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
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeMail;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        DB::beginTransaction();

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('user_Email', $googleUser->getEmail())->first();

            if (!$user) {
                $newUserID = strtoupper(uniqid('USR'));

                $user = User::create([
                    'userID'        => $newUserID,
                    'user_Email'    => $googleUser->getEmail(),
                    // store NULL means "no password set yet"
                    'user_Password' => '',
                    'user_Type'     => 'customer',
                ]);

                $customerID = 'CM' . strtoupper(Str::random(8));

                Customer::create([
                    'customerID'            => $customerID,
                    'userID'                => $newUserID,
                    'customer_FullName'     => $googleUser->getName() ?? 'Google User',
                    'customer_Age'          => null,
                    'customer_PhoneNumber'  => null,
                    'customer_Address'      => null,
                    'customer_Position'     => null,
                ]);

                Mail::to($googleUser->getEmail())
                    ->queue(new WelcomeMail($googleUser->getName() ?? 'Google User'));
            }

            DB::commit();

            session([
                'user_id'    => $user->userID,
                'user_email' => $user->user_Email,
                'user_type'  => $user->user_Type,
            ]);

            // ✅ If customer has no password yet, force set password page first
            if ($user->user_Type === 'customer' && empty($user->user_Password)) {
                return redirect()->route('google.password.form')
                    ->with('info', 'Please set a password to enable email/password login.');
            }

            return match ($user->user_Type) {
                'administrator' => redirect()->route('administrator.dashboard')->with('success', 'Login successful'),
                'staff'         => redirect()->route('staff.dashboard')->with('success', 'Login successful'),
                default         => redirect()->route('customer.dashboard')->with('success', 'Login successful'),
            };

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Google login failed: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Failed to login with Google');
        }
    }

    // =========================
    // Set Password (GET)
    // =========================
    public function showSetPasswordForm()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = User::where('userID', session('user_id'))->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // If already has password, go dashboard
        if (!empty($user->user_Password)) {
            return redirect()->route('customer.dashboard')->with('info', 'Password already set.');
        }

        return view('auth.set-password'); // you create this blade
    }

    // =========================
    // Set Password (POST)
    // =========================
    public function saveSetPassword(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::where('userID', session('user_id'))->firstOrFail();

        // Only allow if first-time / no password yet
        if (!empty($user->user_Password)) {
            return redirect()->route('customer.dashboard')->with('info', 'Password already set.');
        }

        $user->update([
            'user_Password' => Hash::make($request->password),
        ]);

        return redirect()->route('customer.dashboard')->with('success', 'Password set successfully.');
    }
}
