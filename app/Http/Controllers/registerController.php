<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Administrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.Register'); // adjust this if your view file name is different
    }

    public function register(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'user_Email' => 'required|email|unique:user,user_Email',
            'user_Password' => 'required|min:6|confirmed',
            'customer_FullName' => 'required|string|max:50',
            'customer_Age' => 'required|integer|min:1',
            'customer_PhoneNumber' => 'required|string|max:20',
            'customer_Address' => 'required|string|max:50',
            'customer_Position' => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            // Generate unique IDs
            $userID = 'U' . Str::upper(Str::random(8));
            $customerID = 'C' . Str::upper(Str::random(8));

            // Insert into User table
            $user = User::create([
                'userID' => $userID,
                'user_Email' => $validated['user_Email'],
                'user_Password' => Hash::make($validated['user_Password']),
                'user_Type' => 'customer', // default as customer
            ]);

            // Insert into Customer table
            Customer::create([
                'customerID' => $customerID,
                'customer_FullName' => $validated['customer_FullName'],
                'customer_Age' => $validated['customer_Age'],
                'customer_PhoneNumber' => $validated['customer_PhoneNumber'],
                'customer_Address' => $validated['customer_Address'],
                'customer_Position' => $validated['customer_Position'],
                'userID' => $userID,
            ]);

            DB::commit();

            return redirect()->route('login')->with('success', 'Registration successful! Please login.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }


    // Show Staff Registration Form
    public function showStaffRegistrationForm()
    {
        $generatedStaffID = $this->getNextStaffID();
        $generatedAdminID = $this->getNextAdminID();
            return response()
        ->view('LoginAndRegister.RegisterStaff', compact('generatedStaffID', 'generatedAdminID'))
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }

    // Register Staff
    public function registerStaff(Request $request)
{
    $validated = $request->validate([
        'user_Email' => 'required|email|unique:user,user_Email',
        'confirm_email' => 'required|same:user_Email',
        'staff_FullName' => 'required|string|max:100',
        'staff_Age' => 'required|integer|min:18',
        'staff_PhoneNumber' => 'required|string|max:20',
        'staff_Address' => 'required|string|max:255',
        'user_Type' => 'required|in:staff,administrator',
        'staff_Job' => 'nullable|required_if:user_Type,staff|string|max:255', // only required for staff
    ]);

    DB::beginTransaction();

    try {
        $userID = 'U' . Str::upper(Str::random(8));
        User::create([
            'userID' => $userID,
            'user_Email' => $validated['user_Email'],
            'user_Password' => Hash::make('default123'),
            'user_Type' => $validated['user_Type'],
        ]);

        if ($validated['user_Type'] === 'staff') {
            // Generate staffID
            $staffID = $this->getNextStaffID();

            Staff::create([
                'staffID' => $staffID,
                'staff_Job' => $validated['staff_Job'],
                'staff_FullName' => $validated['staff_FullName'],
                'staff_Age' => $validated['staff_Age'],
                'staff_PhoneNumber' => $validated['staff_PhoneNumber'],
                'staff_Address' => $validated['staff_Address'],
                'userID' => $userID,
            ]);

            $message = "Staff registered successfully! Staff ID: $staffID";
        } else {
            // Generate adminID (similar to staff ID but with ADM prefix)
            $adminID = $this->getNextAdminID();

            Administrator::create([
                'adminID' => $adminID,
                'admin_FullName' => $validated['staff_FullName'],
                'admin_Age' => $validated['staff_Age'],
                'admin_PhoneNumber' => $validated['staff_PhoneNumber'],
                'admin_Address' => $validated['staff_Address'],
                'userID' => $userID,
            ]);

            $message = "Administrator registered successfully! Admin ID: $adminID";
        }

        DB::commit();

        return redirect()->route('staff.register')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        $generatedStaffID = $this->getNextStaffID(); // recalc staff ID for next attempt
        return back()
            ->withInput()
            ->with([
                'error' => 'Registration failed: ' . $e->getMessage(),
                'generatedStaffID' => $generatedStaffID
            ]);
    }
}

    // Private helper to calculate next Staff ID
    private function getNextStaffID()
    {
        $latestStaff = Staff::selectRaw("CAST(SUBSTRING(staffID, 4) AS UNSIGNED) as id_number")
                            ->orderByDesc('id_number')
                            ->first();

        $nextIdNumber = $latestStaff ? $latestStaff->id_number + 1 : 1;
        return 'STF' . str_pad($nextIdNumber, 4, '0', STR_PAD_LEFT);
    }

    // Helper to calculate next Admin ID
private function getNextAdminID()
{
    $latestAdmin = DB::table('administrator')
                    ->selectRaw("CAST(SUBSTRING(adminID, 4) AS UNSIGNED) as id_number")
                    ->orderByDesc('id_number')
                    ->first();

    $nextIdNumber = $latestAdmin ? $latestAdmin->id_number + 1 : 1;
    return 'ADM' . str_pad($nextIdNumber, 4, '0', STR_PAD_LEFT);
}
}
    

    




