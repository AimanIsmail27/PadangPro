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
        'fullName'         => $customer->customer_FullName,
        'email'            => $user->user_Email,
        'phoneNumber'      => $customer->customer_PhoneNumber,
        'age'              => $customer->customer_Age,
        'address'          => $customer->customer_Address,
        'position'         => $customer->customer_Position ?? 'Not set',
        'skillLevel'       => $customer->customer_SkillLevel ?? 'Not set',
        'availabilityDays' => $availabilityDays,
        'availabilityTimes'=> $availabilityTimes,
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

    // Validate input
    $request->validate([
        'customer_FullName'            => 'required|string|max:255',
        'user_Email'                   => 'required|email',
        'customer_PhoneNumber'         => 'required|string|max:20',
        'customer_Age'                 => 'required|integer|min:1|max:120',
        'customer_Address'             => 'required|string|max:255',
        'customer_Position'            => 'required|string|max:50',
        'customer_SkillLevel'          => 'nullable|integer|min:1|max:5',
        'customer_Availability_days'   => 'nullable|array',
        'customer_Availability_days.*' => 'string',
        'customer_Availability_times'  => 'nullable|array',
        'customer_Availability_times.*'=> 'string',
    ]);

    // Update user email
    $user->update([
        'user_Email' => $request->user_Email,
    ]);

    // Build availability JSON
    $availability = null;
    if ($request->has('customer_Availability_days') || $request->has('customer_Availability_times')) {
        $availability = json_encode([
            'days' => $request->input('customer_Availability_days', []),
            'time' => $request->input('customer_Availability_times', []),
        ]);
    }

    // Update customer details
    $customer->update([
        'customer_FullName'     => $request->customer_FullName,
        'customer_PhoneNumber'  => $request->customer_PhoneNumber,
        'customer_Age'          => $request->customer_Age,
        'customer_Address'      => $request->customer_Address,
        'customer_Position'     => $request->customer_Position,
        'customer_SkillLevel'   => $request->customer_SkillLevel,
        'customer_Availability' => $availability,
    ]);

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

    // 2. Get Data for "Next Booking" panel
    $nextBooking = Booking::with('field', 'slot')
        ->where('booking.userID', $userId)
        ->where('booking.booking_Status', 'paid') // Only find 'paid' bookings
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->where('slot.slot_Date', '>=', $now->toDateString())
        ->where(function($query) use ($now) { // Ensure it hasn't already passed today
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

    // 3. Get Data for "Upcoming Bookings" table
    $upcomingBookingsTable = Booking::with('field', 'slot')
        ->where('booking.userID', $userId)
        ->where('booking.booking_Status', 'paid') // Only 'paid'
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->where('slot.slot_Date', '>=', $now->toDateString())
        ->orderBy('slot.slot_Date', 'asc')
        ->orderBy('slot.slot_Time', 'asc')
        ->select('booking.*')
        ->take(3) // Get the next 3
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

    // 5. Get Data for "Action Hub" (KPIs)
    $kpi_totalBookings = Booking::where('userID', $userId)
        ->where('booking_Status', 'paid')
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
        
        // Data for new Action Hub
        'kpi_totalBookings' => $kpi_totalBookings,
        'kpi_activeRentals' => $kpi_activeRentals,
        'kpi_newApplications' => $kpi_newApplications,

        // Data for other panels
        'nextBooking' => $nextBooking,
        'upcomingBookingsTable' => $upcomingBookingsTable,
        
        // --- THIS IS THE FIX ---
        // We must encode the PHP arrays into JSON strings here
        'chartLabels' => json_encode($chartLabels),
        'chartData' => json_encode($chartData),
        // --- END FIX ---
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
        return view('Profile.staff.editPage', compact('user', 'staff', 'fullName'));
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
        ]);

        $user->update(['user_Email' => $request->user_Email]);
        
        $staff->update($request->only([
            'staff_FullName', 
            'staff_PhoneNumber', 
            'staff_Age', 
            'staff_Address'
        ]));

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
    $startOfMonth = $now->copy()->startOfMonth();
    $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();

    // 1. Get Admin's name
    $admin = Administrator::where('userID', $userId)->first();
    $fullName = $admin ? $admin->admin_FullName : 'Administrator';

    // 2. Get Data for KPI Cards (This Month)
    $kpi_totalRevenue = Payment::whereIn('payment_Status', ['paid', 'paid_balance', 'paid_balance (cash)'])
        ->where('created_at', '>=', $startOfMonth)
        ->sum('payment_Amount');

    $kpi_totalBookings = Booking::whereIn('booking_Status', ['paid', 'completed'])
        ->where('booking_CreatedAt', '>=', $startOfMonth)
        ->count();

    $kpi_totalRentals = Rental::where('rental_Status', 'paid')
        ->where('rental_StartDate', '>=', $startOfMonth)
        ->count();
    
    // --- 'kpi_newCustomers' query is permanently removed ---

    // 3. Get Data for Revenue Chart (Last 6 Months)
    $bookingRevenue = Booking::where('booking.booking_Status', 'paid')
        ->where('booking.booking_CreatedAt', '>=', $sixMonthsAgo)
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->select(DB::raw('SUM(slot.slot_Price * 0.20) as total'), DB::raw('DATE_FORMAT(booking.booking_CreatedAt, "%Y-%m") as month'))
        ->groupBy('month')->get()->keyBy('month');
    
    $balanceRevenue = Payment::where('payment_Status', 'paid_balance')
        ->where('created_at', '>=', $sixMonthsAgo)
        ->select(DB::raw('SUM(payment_Amount) as total'), DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'))
        ->groupBy('month')->get()->keyBy('month');

    $rentalRevenue = Rental::where('rental.rental_Status', 'paid')
        ->where('rental.rental_StartDate', '>=', $sixMonthsAgo)
        ->join('item', 'rental.itemID', '=', 'item.itemID')
        ->select(
            DB::raw('SUM(rental.quantity * item.item_Price * (DATEDIFF(rental.rental_EndDate, rental.rental_StartDate) + 1)) as total'),
            DB::raw('DATE_FORMAT(rental.rental_StartDate, "%Y-%m") as month')
        )
        ->groupBy('month')
        ->get()->keyBy('month');

    $chartLabels = [];
    $chartBookingData = [];
    $chartRentalData = [];

    for ($i = 5; $i >= 0; $i--) {
        $month = $now->copy()->subMonths($i);
        $monthKey = $month->format('Y-m');
        $chartLabels[] = $month->format('M');
        
        $b_revenue = $bookingRevenue->get($monthKey) ? $bookingRevenue->get($monthKey)->total : 0;
        $bal_revenue = $balanceRevenue->get($monthKey) ? $balanceRevenue->get($monthKey)->total : 0;
        $chartBookingData[] = $b_revenue + $bal_revenue; 
        
        $chartRentalData[] = $rentalRevenue->get($monthKey) ? $rentalRevenue->get($monthKey)->total : 0;
    }

    // 4. Get Data for Field Popularity (Doughnut Chart)
    $fieldPopularity = Booking::whereIn('booking_Status', ['paid', 'completed'])
        ->join('field', 'booking.fieldID', '=', 'field.fieldID')
        ->select('field.field_Label', DB::raw('COUNT(booking.bookingID) as count'))
        ->groupBy('booking.fieldID', 'field.field_Label')
        ->get();

    // 5. Get Data for Action Lists
    $pendingApprovals = Rental::with('customer', 'item')
        ->where('return_Status', 'requested')
        ->latest('rental_EndDate')
        ->take(5)
        ->get();
    
    // --- THIS IS THE FIX ---
    // Sort by the primary key 'customerID' in descending order
    $recentCustomers = Customer::with('user')
        ->orderBy('customerID', 'desc') 
        ->take(5)
        ->get();
    // --- END FIX ---

    // 6. Pass all data to the view
    return view('Profile.admin.dashboard', [
        'fullName' => $fullName,
        'kpi_totalRevenue' => $kpi_totalRevenue,
        'kpi_totalBookings' => $kpi_totalBookings,
        'kpi_totalRentals' => $kpi_totalRentals,
        // 'kpi_newCustomers' is removed
        
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
