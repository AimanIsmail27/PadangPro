<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Staff;
USE App\Models\Administrator;
use App\Models\Booking;
use App\Models\Advertisement;
use App\Models\Payment;
use Carbon\Carbon;
use App\Models\Rental;
use App\Models\Applications;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    /**
 * Show the customer profile page.
 */
public function showCustomerProfile(Request $request)
    {
        $userId = session('user_id');

        $user = User::where('userID', $userId)->first();
        $customer = Customer::where('userID', $userId)->first();

        if (!$user || !$customer) {
            return redirect()->route('customer.dashboard')->with('error', 'Profile not found.');
        }

        // Decode availability JSON
        $availabilityDays = 'Not set';
        $availabilityTimes = 'Not set';

        if (!empty($customer->customer_Availability)) {
            $decoded = json_decode($customer->customer_Availability, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                if (!empty($decoded['days'])) {
                    $availabilityDays = implode(', ', $decoded['days']);
                }
                if (!empty($decoded['time'])) {
                    $availabilityTimes = implode(', ', $decoded['time']);
                }
            }
        }

        return view('Profile.customer.MainProfilePage', [
            // --- THIS IS THE FIX: Pass the full object ---
            'customer'          => $customer, 
            // --------------------------------------------
            'fullName'          => $customer->customer_FullName,
            'email'             => $user->user_Email,
            'phoneNumber'       => $customer->customer_PhoneNumber,
            'age'               => $customer->customer_Age,
            'address'           => $customer->customer_Address,
            'position'          => $customer->customer_Position ?? 'Not set',
            'skillLevel'        => $customer->customer_SkillLevel ?? 'Not set',
            'availabilityDays'  => $availabilityDays,
            'availabilityTimes' => $availabilityTimes,
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
// Update customer profile
    public function update(Request $request)
    {
        $userId = session('user_id');
        $customer = Customer::where('userID', $userId)->first();
        $user = User::where('userID', $userId)->first();

        // 1. Validate
        $request->validate([
            'customer_FullName' => 'required|string|max:50',
            'user_Email' => 'required|email|max:50|unique:user,user_Email,' . $userId . ',userID',
            'customer_PhoneNumber' => 'required|string|max:20',
            'customer_Age' => 'required|integer|min:18|max:100',
            'customer_Address' => 'required|string|max:255',
            'customer_Position' => 'required|string',
            'customer_SkillLevel' => 'required|integer|between:1,5',
            'customer_Availability_days' => 'array',
            'customer_Availability_times' => 'array',
            'customer_Image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // NEW: Image validation
        ]);

        // 2. Prepare JSON availability
        $availability = json_encode([
            'days' => $request->customer_Availability_days ?? [],
            'time' => $request->customer_Availability_times ?? [],
        ]);

        // 3. Update User Table (Email)
        $user->user_Email = $request->user_Email;
        $user->save();

        // 4. Update Customer Table
        $customer->customer_FullName = $request->customer_FullName;
        $customer->customer_PhoneNumber = $request->customer_PhoneNumber;
        $customer->customer_Age = $request->customer_Age;
        $customer->customer_Address = $request->customer_Address;
        $customer->customer_Position = $request->customer_Position;
        $customer->customer_SkillLevel = $request->customer_SkillLevel;
        $customer->customer_Availability = $availability;

        // --- NEW: IMAGE UPLOAD LOGIC ---
        if ($request->hasFile('customer_Image')) {
            // Delete old image if exists
            if ($customer->customer_Image && \Illuminate\Support\Facades\Storage::disk('public')->exists($customer->customer_Image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($customer->customer_Image);
            }
            // Store new image
            $path = $request->file('customer_Image')->store('profiles', 'public');
            $customer->customer_Image = $path;
        }
        // -------------------------------

        $customer->save();
        
        // Update session name
        session(['full_name' => $customer->customer_FullName]);

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }

public function updateCustomerPassword(Request $request)
    {
        $userId = session('user_id');
        $user = User::where('userID', $userId)->first();

        if (!$user) {
             return redirect()->route('customer.profile.edit')
                             ->with('error_password', 'User not found.');
        }

        // 1. Validate the form and put errors in the 'password' error bag
        $request->validateWithBag('password', [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed', // 'confirmed' checks for 'new_password_confirmation'
        ]);

        // 2. Check if the current password is correct
        if (!Hash::check($request->current_password, $user->user_Password)) {
            // --- FAILURE ---
            return redirect()->route('customer.profile.edit')
                             ->withErrors(['current_password' => 'Your current password does not match.'], 'password')
                             ->withInput();
        }

        // 3. Update the password
        $user->user_Password = Hash::make($request->new_password);
        $user->save();

        // --- SUCCESS ---
        // Redirect to the MAIN profile page with a success message
        return redirect()->route('customer.profile')
                         ->with('success', 'Password changed successfully!');
    }
/**
 * Delete customer account.
 */
public function destroy(Request $request)
{
    $userId = session('user_id');

    if (!$userId) {
        return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
    }

    Customer::where('userID', $userId)->delete();
    User::where('userID', $userId)->delete();

    $request->session()->flush();

    return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
}

// In app/Http/Controllers/ProfileController.php

public function dashboard()
    {
        $userId = session('user_id');
        $now = Carbon::now('Asia/Kuala_Lumpur');

        // 1. Get the customer
        $customer = Customer::where('userID', $userId)->first();
        if (!$customer) {
            return redirect()->route('login')->with('error', 'Customer profile not found.');
        }

        // 2. Get "Next Booking"
        $nextBooking = Booking::with('field', 'slot')
            ->where('booking.userID', $userId)
            ->whereIn('booking.booking_Status', ['paid', 'completed'])
            ->join('slot', 'booking.slotID', '=', 'slot.slotID')
            ->where('slot.slot_Date', '>=', $now->toDateString())
            ->where(function($query) use ($now) {
                $query->where('slot.slot_Date', '>', $now->toDateString())
                        ->orWhere(function($query) use ($now) {
                            $query->where('slot.slot_Date', '=', $now->toDateString())
                                    ->where('slot.slot_Time', '>', $now->toTimeString());
                        });
            })
            ->orderBy('slot.slot_Date', 'asc')
            ->orderBy('slot.slot_Time', 'asc')
            ->select('booking.*')
            ->first();

        // 3. Get "Upcoming Bookings" Table (Recent History)
        $upcomingBookingsTable = Booking::with('field', 'slot')
            ->where('booking.userID', $userId)
            ->whereIn('booking.booking_Status', ['paid', 'completed'])
            ->join('slot', 'booking.slotID', '=', 'slot.slotID')
            ->where('slot.slot_Date', '>=', $now->toDateString())
            ->orderBy('slot.slot_Date', 'asc')
            ->orderBy('slot.slot_Time', 'asc')
            ->select('booking.*')
            ->take(3)
            ->get();
        
        // 4. Get Data for "Monthly Bookings" Chart
        $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
        $monthlyData = Booking::where('userID', $userId)
            ->where('booking_Status', 'paid')
            ->where('booking_CreatedAt', '>=', $sixMonthsAgo)
            ->select(DB::raw('COUNT(*) as count'), DB::raw('DATE_FORMAT(booking_CreatedAt, "%Y-%m") as month'))
            ->groupBy('month')->orderBy('month', 'asc')->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = $now->copy()->subMonths($i)->format('Y-m');
            $label = $now->copy()->subMonths($i)->format('M');
            $chartLabels[] = $label;
            
            $data = $monthlyData->firstWhere('month', $monthKey);
            $chartData[] = $data ? $data->count : 0;
        }

        // 5. KPI Stats
        $kpi_totalBookings = Booking::where('userID', $userId)
            ->whereIn('booking_Status', ['paid', 'completed'])
            ->count();
        
        $kpi_activeRentals = Rental::where('userID', $userId)
            ->where('rental_Status', 'paid')
            ->where('rental_EndDate', '>=', $now->toDateString())
            ->count();

        $kpi_newApplications = Applications::where('status', 'pending')
            ->whereHas('advertisement', function ($query) use ($customer) {
                $query->where('customerID', $customer->customerID);
            })
            ->count();

        return view('Profile.customer.dashboard', [
            'fullName' => $customer->customer_FullName,
            
            // --- THIS WAS THE FIX ---
            'nextMatch' => $nextBooking, // Pass $nextBooking as 'nextMatch'
            // ------------------------

            'upcomingBookingsTable' => $upcomingBookingsTable,
            'kpi_totalBookings' => $kpi_totalBookings,
            'kpi_activeRentals' => $kpi_activeRentals,
            'kpi_newApplications' => $kpi_newApplications,
            'chartLabels' => json_encode($chartLabels),
            'chartData' => json_encode($chartData),
        ]);
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
        'adminPhoto' => $admin->admin_Photo,

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

    // ✅ Validate input including photo
    $request->validate([
        'admin_FullName' => 'required|string|max:255',
        'user_Email' => 'required|email',
        'admin_PhoneNumber' => 'required|string|max:20',
        'admin_Age' => 'required|integer|min:1|max:120',
        'admin_Address' => 'required|string|max:255',

        // ✅ NEW validation
        'admin_Photo' => 'nullable|image|max:2048',
    ]);

    // ✅ Update user email
    $user->update([
        'user_Email' => $request->user_Email,
    ]);

    // ✅ Update admin details
    $admin->update([
        'admin_FullName' => $request->admin_FullName,
        'admin_PhoneNumber' => $request->admin_PhoneNumber,
        'admin_Age' => $request->admin_Age,
        'admin_Address' => $request->admin_Address,
    ]);

    // ✅ Handle profile photo upload
    if ($request->hasFile('admin_Photo')) {

        // ✅ Delete old image if exists
        if ($admin->admin_Photo) {
            Storage::delete('public/' . $admin->admin_Photo);
        }

        // ✅ Store new photo under storage/app/public/admin_photos
        $path = $request->file('admin_Photo')->store('admin_photos', 'public');

        // ✅ Save new filename in DB
        $admin->admin_Photo = $path;
        $admin->save();
    }

    return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
}

public function updateAdminPassword(Request $request)
    {
        $userId = session('user_id');
        $user = User::where('userID', $userId)->first();

        // 1. Validate the form
        $request->validateWithBag('password', [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed', // 'confirmed' checks for 'new_password_confirmation'
        ]);

        // 2. Check if the current password is correct
        if (!Hash::check($request->current_password, $user->user_Password)) {
            // --- FAILURE ---
            return redirect()->route('admin.profile.edit')
                             ->withErrors(['current_password' => 'Your current password does not match.'], 'password')
                             ->withInput();
        }

        // 3. Update the password
        $user->user_Password = Hash::make($request->new_password);
        $user->save();

        // --- SUCCESS ---
        // Redirect to the MAIN profile page with a success message
        return redirect()->route('admin.profile')
                         ->with('success', 'Password changed successfully!');
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

//STAFF SECTION

public function showStaffProfile(Request $request)
    {
        $userId = session('user_id');
        $user = User::where('userID', $userId)->first();
        $staff = Staff::where('userID', $userId)->first();

        if (!$user || !$staff) {
            return redirect()->route('staff.dashboard')->with('error', 'Profile not found.');
        }

        return view('Profile.staff.MainProfilePage', [
            'staffID'     => $staff->staffID,
            'fullName'    => $staff->staff_FullName,
            'email'       => $user->user_Email,
            'phoneNumber' => $staff->staff_PhoneNumber,
            'age'         => $staff->staff_Age,
            'address'     => $staff->staff_Address,
            'job'         => $staff->staff_Job,
            'staff' => $staff,
        ]);
    }

    /**
    * Show edit staff profile page.
    */
    public function editStaff()
    {
        $userId = session('user_id');
        $user = User::where('userID', $userId)->first();
        $staff = Staff::where('userID', $userId)->first();

        if (!$user || !$staff) {
            // --- THIS IS THE FIX ---
            return redirect()->route('staff.profile')->with('error', 'Profile not found.');
        }

        $fullName = $staff->staff_FullName;
        $image = $staff->staff_image;

        return view('Profile.staff.editPage', compact('user', 'staff', 'fullName', 'image'));
    }

    /**
    * Update staff profile information.
    */
    public function updateStaff(Request $request)
    {
        $userId = session('user_id');
        $user = User::where('userID', $userId)->first();
        $staff = Staff::where('userID', $userId)->first();

        if (!$user || !$staff) {
            // --- THIS IS THE FIX ---
            return redirect()->route('staff.profile')->with('error', 'Profile not found.');
        }

        $request->validate([
            'staff_FullName' => 'required|string|max:255',
            'user_Email' => ['required', 'email', 'max:255', Rule::unique('user', 'user_Email')->ignore($userId, 'userID')],
            'staff_PhoneNumber' => 'required|string|max:20',
            'staff_Age' => 'required|integer|min:18|max:100',
            'staff_Address' => 'required|string|max:255',
            'staff_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        $user->update(['user_Email' => $request->user_Email]);
        
        $staff->update($request->only([
            'staff_FullName', 
            'staff_PhoneNumber', 
            'staff_Age', 
            'staff_Address'
        ]));

    if ($request->hasFile('staff_image')) {

    // Delete old image if exists
    if ($staff->staff_image && Storage::disk('public')->exists($staff->staff_image)) {
        Storage::disk('public')->delete($staff->staff_image);
    }

    // Upload new image to 'staff_photos' folder in storage/app/public
    $path = $request->file('staff_image')->store('staff_photos', 'public');

    // Save path in DB
    $staff->staff_image = $path;
    $staff->save();
}



        // --- THIS IS THE FIX ---
        return redirect()->route('staff.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * NEW: Update staff password.
     */
    // In app/Http/Controllers/ProfileController.php

public function updateStaffPassword(Request $request)
{
    $userId = session('user_id');
    $user = User::where('userID', $userId)->first();

    if (!$user) {
         return redirect()->route('staff.profile.edit')
                         ->with('error_password_alert', 'User not found.'); // Use a new session key for alerts
    }

    // 1. Validate the form and put errors in the 'password' error bag
    $request->validateWithBag('password', [
        'current_password' => 'required',
        'new_password' => 'required|string|min:6|confirmed',
    ]);

    // 2. Check if the current password is correct
    if (!Hash::check($request->current_password, $user->user_Password)) {
        // --- FAILURE 1 ---
        // Send a specific error message back to the 'password' error bag
        return redirect()->route('staff.profile.edit')
                         ->withErrors(['current_password' => 'Your current password does not match.'], 'password')
                         ->withInput();
    }

    // 3. Update the password
    $user->user_Password = Hash::make($request->new_password);
    $user->save();

    // --- SUCCESS ---
    // Redirect to the MAIN profile page with a success message
    return redirect()->route('staff.profile')
                     ->with('success', 'Password changed successfully!');
}

    /**
    * Delete staff account.
    */
    public function destroyStaff(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        // Add deletion for related records if necessary
        Staff::where('userID', $userId)->delete();
        User::where('userID', $userId)->delete();
        
        $request->session()->flush();

        return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
    }
    

    // In app/Http/Controllers/ProfileController.php

// In app/Http/Controllers/ProfileController.php

public function dashboardStaff()
{
    $now = Carbon::now('Asia/Kuala_Lumpur');
    $today = $now->toDateString();

    // 1. Get Staff's name for the header
    $fullName = 'Staff'; // Default
    $staff = Staff::where('userID', session('user_id'))->first();
    if ($staff) {
        $fullName = $staff->staff_FullName;
    }

    // 2. Get Data for KPI Cards
    $kpi_pendingApprovals = Rental::where('return_Status', 'requested')->count();

    $kpi_activeBookings = Booking::where('booking_Status', 'paid')
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->where('slot.slot_Date', '>=', $today)
        ->count();
        
    $kpi_currentRentals = Rental::where('rental_Status', 'paid')
        ->where('rental_StartDate', '<=', $today)
        ->where('rental_EndDate', '>=', $today)
        ->count();

    // 3. Get Data for "Pending Tasks" list
    $pendingTasks = Rental::with('customer', 'item')
        ->where('return_Status', 'requested') 
        ->latest('rental_EndDate') 
        ->take(3) 
        ->get();
        
    // 4. NEW: Get Data for "Upcoming Bookings" table
    $upcomingBookings = Booking::with('user', 'field', 'slot')
        ->where('booking_Status', 'paid')
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->where('slot.slot_Date', '>=', $today)
        ->orderBy('slot.slot_Date', 'asc')
        ->orderBy('slot.slot_Time', 'asc')
        ->select('booking.*')
        ->take(4) // Get the next 4
        ->get();
        
    // 5. NEW: Get Data for "Monthly Bookings" Chart
    $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
    $monthlyData = Booking::where('booking_Status', 'paid')
        ->where('booking_CreatedAt', '>=', $sixMonthsAgo)
        ->select(DB::raw('COUNT(*) as count'), DB::raw('DATE_FORMAT(booking_CreatedAt, "%Y-%m") as month'))
        ->groupBy('month')->orderBy('month', 'asc')->get();

    $chartLabels = [];
    $chartData = [];
    for ($i = 5; $i >= 0; $i--) {
        $monthKey = $now->copy()->subMonths($i)->format('Y-m');
        $label = $now->copy()->subMonths($i)->format('M');
        $chartLabels[] = $label;
        
        $data = $monthlyData->firstWhere('month', $monthKey);
        $chartData[] = $data ? $data->count : 0;
    }

    // 6. Pass all data to the view
    return view('Profile.staff.dashboard', [
        'fullName' => $fullName,
        'kpi_pendingApprovals' => $kpi_pendingApprovals,
        'kpi_activeBookings' => $kpi_activeBookings,
        'kpi_currentRentals' => $kpi_currentRentals,
        'pendingTasks' => $pendingTasks,
        'upcomingBookings' => $upcomingBookings,
        'chartLabels' => json_encode($chartLabels),
        'chartData' => json_encode($chartData),
    ]);
}

// In app/Http/Controllers/ProfileController.php

public function dashboardAdmin()
{
    $userId = session('user_id');
    $now = Carbon::now('Asia/Kuala_Lumpur');

    // Month boundaries (VERY IMPORTANT)
    $startOfMonth = $now->copy()->startOfMonth();
    $startOfNextMonth = $now->copy()->addMonth()->startOfMonth();
    $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();

    /*
    |--------------------------------------------------------------------------
    | 1. Admin Name
    |--------------------------------------------------------------------------
    */
    $admin = Administrator::where('userID', $userId)->first();
    $fullName = $admin ? $admin->admin_FullName : 'Administrator';

    /*
    |--------------------------------------------------------------------------
    | 2. KPI Cards (This Month – Service Date Based)
    |--------------------------------------------------------------------------
    */

    // Booking revenue (slot date falls within this month)
    $kpi_bookingRevenue = Payment::whereIn('payment.payment_Status', [
            'paid', 'paid_balance', 'paid_balance (cash)'
        ])
        ->whereNotNull('payment.bookingID')
        ->join('booking', 'payment.bookingID', '=', 'booking.bookingID')
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->whereBetween('slot.slot_Date', [$startOfMonth, $startOfNextMonth])
        ->sum('payment.payment_Amount');

    // Rental revenue (rental start date falls within this month)
    $kpi_rentalRevenue = Payment::whereIn('payment.payment_Status', [
            'paid', 'paid_balance', 'paid_balance (cash)'
        ])
        ->whereNotNull('payment.rentalID')
        ->join('rental', 'payment.rentalID', '=', 'rental.rentalID')
        ->whereBetween('rental.rental_StartDate', [$startOfMonth, $startOfNextMonth])
        ->sum('payment.payment_Amount');

    $kpi_totalRevenue = $kpi_bookingRevenue + $kpi_rentalRevenue;

    // Total bookings happening this month (by slot date)
    $kpi_totalBookings = Booking::whereIn('booking.booking_Status', ['paid', 'completed'])
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->whereBetween('slot.slot_Date', [$startOfMonth, $startOfNextMonth])
        ->count();

    // Total rentals starting this month
    $kpi_totalRentals = Rental::where('rental_Status', 'paid')
        ->whereBetween('rental_StartDate', [$startOfMonth, $startOfNextMonth])
        ->count();

    /*
    |--------------------------------------------------------------------------
    | 3. Revenue Overview Chart (Last 6 Months – Accrual Based)
    |--------------------------------------------------------------------------
    */

    // Booking revenue grouped by slot month
    $bookingRevenue = Payment::whereIn('payment.payment_Status', [
            'paid', 'paid_balance', 'paid_balance (cash)'
        ])
        ->whereNotNull('payment.bookingID')
        ->join('booking', 'payment.bookingID', '=', 'booking.bookingID')
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->where('slot.slot_Date', '>=', $sixMonthsAgo)
        ->select(
            DB::raw('SUM(payment.payment_Amount) as total'),
            DB::raw('DATE_FORMAT(slot.slot_Date, "%Y-%m") as month')
        )
        ->groupBy('month')
        ->get()
        ->keyBy('month');

    // Rental revenue grouped by rental start month
    $rentalRevenue = Payment::whereIn('payment.payment_Status', [
            'paid', 'paid_balance', 'paid_balance (cash)'
        ])
        ->whereNotNull('payment.rentalID')
        ->join('rental', 'payment.rentalID', '=', 'rental.rentalID')
        ->where('rental.rental_StartDate', '>=', $sixMonthsAgo)
        ->select(
            DB::raw('SUM(payment.payment_Amount) as total'),
            DB::raw('DATE_FORMAT(rental.rental_StartDate, "%Y-%m") as month')
        )
        ->groupBy('month')
        ->get()
        ->keyBy('month');

    $chartLabels = [];
    $chartBookingData = [];
    $chartRentalData = [];

    for ($i = 5; $i >= 0; $i--) {
        $month = $now->copy()->subMonths($i);
        $monthKey = $month->format('Y-m');

        $chartLabels[] = $month->format('M');
        $chartBookingData[] = $bookingRevenue[$monthKey]->total ?? 0;
        $chartRentalData[]  = $rentalRevenue[$monthKey]->total ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Field Popularity (All Time)
    |--------------------------------------------------------------------------
    */
    $fieldPopularity = Booking::whereIn('booking_Status', ['paid', 'completed'])
        ->join('field', 'booking.fieldID', '=', 'field.fieldID')
        ->select('field.field_Label', DB::raw('COUNT(booking.bookingID) as count'))
        ->groupBy('booking.fieldID', 'field.field_Label')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 5. Action Lists
    |--------------------------------------------------------------------------
    */
    $pendingApprovals = Rental::with('customer', 'item')
        ->where('return_Status', 'requested')
        ->latest('rental_EndDate')
        ->take(5)
        ->get();

    $recentCustomers = Customer::with('user')
        ->orderBy('customerID', 'desc')
        ->take(5)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 6. Return View
    |--------------------------------------------------------------------------
    */
    return view('Profile.admin.dashboard', [
        'fullName' => $fullName,

        'kpi_totalRevenue' => $kpi_totalRevenue,
        'kpi_totalBookings' => $kpi_totalBookings,
        'kpi_totalRentals' => $kpi_totalRentals,

        'chartLabels' => json_encode($chartLabels),
        'chartBookingData' => json_encode($chartBookingData),
        'chartRentalData' => json_encode($chartRentalData),

        'fieldPopularityLabels' => json_encode($fieldPopularity->pluck('field_Label')),
        'fieldPopularityData' => json_encode($fieldPopularity->pluck('count')),

        'pendingApprovals' => $pendingApprovals,
        'recentCustomers' => $recentCustomers,
    ]);
}

}
