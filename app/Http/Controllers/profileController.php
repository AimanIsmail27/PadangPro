<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Staff;
USE App\Models\Administrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ProfileController extends Controller
{
    /**
     * Show the customer profile page.
     */
    public function showCustomerProfile(Request $request)
    {
        // Get the logged-in user ID from session
        $userId = session('user_id');

        // Retrieve user information
        $user = User::where('userID', $userId)->first();

        // Retrieve customer information linked to this user
        $customer = Customer::where('userID', $userId)->first();

        if (!$user || !$customer) {
            return redirect()->route('customer.dashboard')->with('error', 'Profile not found.');
        }

        return view('Profile.customer.MainProfilePage', [
            'fullName' => $customer->customer_FullName,
            'email' => $user->user_Email,
            'phoneNumber' => $customer->customer_PhoneNumber,
            'age' => $customer->customer_Age,
            'address' => $customer->customer_Address,
            'position' => $customer->customer_Position,
        ]);
    }

    /**
     * Show edit profile page.
     */
    public function edit()
    {
        $userId = session('user_id');

        $user = User::where('userID', $userId)->first();
        $customer = Customer::where('userID', $userId)->first();

        if (!$user || !$customer) {
            return redirect()->route('customer.profile')->with('error', 'Profile not found.');
        }

        return view('Profile.customer.editPage', compact('user', 'customer'));
    }

    /**
     * Update customer profile.
     */
public function update(Request $request)
{
    $userId = session('user_id');
    $user = User::where('userID', $userId)->first();
    $customer = Customer::where('userID', $userId)->first();

    if (!$user || !$customer) {
        return redirect()->route('customer.profile')->with('error', 'Profile not found.');
    }

    // Validate input (all fields now required)
    $request->validate([
        'customer_FullName' => 'required|string|max:255',
        'user_Email' => 'required|email',
        'customer_PhoneNumber' => 'required|string|max:20',
        'customer_Age' => 'required|integer|min:1|max:120',
        'customer_Address' => 'required|string|max:255',
        'customer_Position' => 'required|string|max:50',
    ]);

    // Update user email
    $user->update([
        'user_Email' => $request->user_Email,
    ]);

    // Update customer details
    $customer->update([
        'customer_FullName' => $request->customer_FullName,
        'customer_PhoneNumber' => $request->customer_PhoneNumber,
        'customer_Age' => $request->customer_Age,
        'customer_Address' => $request->customer_Address,
        'customer_Position' => $request->customer_Position,
    ]);

    return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
}



public function destroy(Request $request)
{
    $userId = session('user_id'); // Now contains something like "UJKGQQCGM"

    if (!$userId) {
        return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
    }

    Customer::where('userID', $userId)->delete();
    User::where('userID', $userId)->delete();

    $request->session()->flush();

    return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
}

//ADMIN SECTION

/**
 * Show the administrator profile page.
 */
public function showAdminProfile(Request $request)
{
    // Get logged-in user ID
    $userId = session('user_id');

    // Retrieve user information
    $user = User::where('userID', $userId)->first();

    // Retrieve administrator information linked to this user
    $admin = Administrator::where('userID', $userId)->first();

    if (!$user || !$admin) {
        return redirect()->route('admin.dashboard')->with('error', 'Profile not found.');
    }

    return view('Profile.admin.MainProfilePage', [
        'fullName' => $admin->admin_FullName,
        'email' => $user->user_Email,
        'phoneNumber' => $admin->admin_PhoneNumber,
        'age' => $admin->admin_Age,
        'address' => $admin->admin_Address,
    ]);
}

/**
 * Show edit admin profile page.
 */
public function editAdmin()
{
    $userId = session('user_id');

    $user = User::where('userID', $userId)->first();
    $admin = Administrator::where('userID', $userId)->first();

    if (!$user || !$admin) {
        return redirect()->route('admin.profile')->with('error', 'Profile not found.');
    }

    $fullName = $admin->admin_FullName;
    return view('Profile.admin.editPage', compact('user', 'admin', 'fullName'));
}

/**
 * Update admin profile.
 */
public function updateAdmin(Request $request)
{
    $userId = session('user_id');
    $user = User::where('userID', $userId)->first();
    $admin = Administrator::where('userID', $userId)->first();

    if (!$user || !$admin) {
        return redirect()->route('admin.profile')->with('error', 'Profile not found.');
    }

    // Validate input
    $request->validate([
        'admin_FullName' => 'required|string|max:255',
        'user_Email' => 'required|email',
        'admin_PhoneNumber' => 'required|string|max:20',
        'admin_Age' => 'required|integer|min:1|max:120',
        'admin_Address' => 'required|string|max:255',
    ]);

    // Update user email
    $user->update([
        'user_Email' => $request->user_Email,
    ]);

    // Update admin details
    $admin->update([
        'admin_FullName' => $request->admin_FullName,
        'admin_PhoneNumber' => $request->admin_PhoneNumber,
        'admin_Age' => $request->admin_Age,
        'admin_Address' => $request->admin_Address,
    ]);

    return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
}

public function destroyAdmin(Request $request)
{
    $userId = session('user_id');

    if (!$userId) {
        return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
    }

    // Delete admin and user record
    Administrator::where('userID', $userId)->delete();
    User::where('userID', $userId)->delete();

    // Clear session
    $request->session()->flush();

    return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
}




}
