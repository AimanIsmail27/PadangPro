<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Staff;
use App\Models\Rental; // Make sure Rental model exists
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    // =========================
    // STAFF FUNCTIONS
    // =========================

    // Display all items for staff
    public function index()
    {
        $availableItems = Item::where('item_Status', 'Available')->get();
        $unavailableItems = Item::where('item_Status', 'Unavailable')->get();

        return view('Rental.staff.MainRentalPage', compact('availableItems', 'unavailableItems'));
    }

    // Store new item
    public function store(Request $request)
    {
        $request->validate([
            'item_Name' => 'required|string|max:50',
            'item_Quantity' => 'required|integer|min:1',
            'item_Price' => 'required|numeric|min:0',
            'item_Description' => 'required|string|max:255',
            'item_Status' => 'required|string|max:20',
        ]);

        $userID = $request->session()->get('user_id');
        $staff = Staff::where('userID', $userID)->first();

        if (!$staff) {
            return redirect()->back()->withErrors('Staff record not found for this user.');
        }

        $item = new Item();
        $item->itemID = 'ITEM' . strtoupper(substr(md5(uniqid()), 0, 6));
        $item->item_Name = $request->item_Name;
        $item->item_Quantity = $request->item_Quantity;
        $item->item_Price = $request->item_Price;
        $item->item_Description = $request->item_Description;
        $item->item_Status = $request->item_Status;
        $item->staffID = $staff->staffID;
        $item->save();

        return redirect()->route('staff.rental.main')->with('success', 'Item added successfully!');
    }

    // Show edit form for staff
    public function edit($itemID)
    {
        $item = Item::where('itemID', $itemID)->firstOrFail();
        return view('Rental.staff.editPage', compact('item'));
    }

    // Update item
    public function update(Request $request, $itemID)
    {
        $request->validate([
            'item_Name' => 'required|string|max:50',
            'item_Quantity' => 'required|integer|min:1',
            'item_Price' => 'required|numeric|min:0',
            'item_Description' => 'required|string|max:255',
            'item_Status' => 'required|string|max:20',
        ]);

        $item = Item::where('itemID', $itemID)->firstOrFail();

        $item->item_Name = $request->item_Name;
        $item->item_Quantity = $request->item_Quantity;
        $item->item_Price = $request->item_Price;
        $item->item_Description = $request->item_Description;
        $item->item_Status = $request->item_Status;

        $item->save();

        return redirect()->route('staff.rental.main')->with('success', 'Item updated successfully!');
    }

    // Delete item
    public function destroy($itemID)
    {
        $item = Item::where('itemID', $itemID)->firstOrFail();
        $item->delete();

        return response()->json(['success' => true]);
    }

public function viewReturnApprovals()
{
    $rentals = Rental::with('item')
        ->where('return_Status', 'requested')
        ->orderBy('rental_StartDate', 'desc')
        ->get();

    return view('Rental.staff.ApproveRent', compact('rentals'));
}

public function updateReturnApproval(Request $request, $rentalID)
{
    $rental = Rental::findOrFail($rentalID);
    $rental->return_Status = $request->status;
    $rental->save();

    return response()->json(['success' => true]);
}

public function viewCurrent()
{
    $today = now()->toDateString();

    $rentals = Rental::where('rental_Status', 'paid')
        ->whereDate('rental_EndDate', '>=', $today) // not yet ended
        ->orderBy('rental_StartDate', 'asc')        // soonest start date first
        ->with('item')                              // eager load item details
        ->get();

    return view('Rental.staff.viewRent', compact('rentals'));
}



 
    // =========================
    // CUSTOMER FUNCTIONS
    // =========================
    // In app/Http/Controllers/RentalController.php

public function indexCustomer(Request $request)
{
    // 1. Get the selected date, default to today
    $selectedDate = $request->input('rental_date', Carbon::now('Asia/Kuala_Lumpur')->toDateString());

    // 2. GET ONLY ITEMS THAT ARE PERMANENTLY "Available"
    //    This is the main fix.
    $availableItems = Item::where('item_Status', 'Available')->get();

    // 3. Get all rented quantities for the selected date
    $rentedQuantities = DB::table('rental')
        ->where('rental_Status', 'paid') // Only count paid rentals
        ->whereDate('rental_StartDate', '<=', $selectedDate)
        ->whereDate('rental_EndDate', '>=', $selectedDate)
        ->groupBy('itemID')
        ->select('itemID', DB::raw('SUM(quantity) as rented_quantity'))
        ->get()
        ->keyBy('itemID'); // Makes it easy to look up by itemID

    // 4. Calculate the *true* available quantity for each item
    $itemsForView = $availableItems->map(function ($item) use ($rentedQuantities) {
        $rented = $rentedQuantities->get($item->itemID);
        $rentedCount = $rented ? $rented->rented_quantity : 0;
        
        // Calculate the quantity available *for that day*
        $item->available_quantity = $item->item_Quantity - $rentedCount; 
        
        return $item;
    })
    ->filter(function ($item) {
        // 5. Only show items that have at least 1 available for rent today
        return $item->available_quantity > 0;
    });

    return view('Rental.customer.MainRentalPage', [
        'availableItems' => $itemsForView,
        'selectedDate' => $selectedDate,
        // We add this for the SweetAlerts
        'success' => session('success'),
        'error' => session('error'),
    ]);
}

    // Show Rent Page for a specific item
    public function rentPage(Request $request, $itemID)
    {
        $item = Item::findOrFail($itemID);

        $startDate = $request->query('start_date', date('Y-m-d'));
        $endDate   = $request->query('end_date', $startDate);

        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        $availableQuantities = [];
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');

            $bookedQuantity = Rental::where('itemID', $item->itemID)
                ->where('rental_Status', 'Pending')
                ->whereDate('rental_StartDate', '<=', $d)
                ->whereDate('rental_EndDate', '>=', $d)
                ->sum('quantity');

            $availableQuantities[] = $item->item_Quantity - $bookedQuantity;
        }

        $availableQuantity = min($availableQuantities);

        return view('Rental.customer.addPage', compact('item', 'availableQuantity', 'startDate', 'endDate'));
    }

    // Process Rent Form Submission (insert into DB immediately)
    public function processRent(Request $request, $itemID)
    {
        $request->validate([
            'rental_name'     => 'required|string|max:50',
            'rental_email'    => 'nullable|email|max:50',
            'rental_phone'    => 'required|string|max:20',
            'rental_backup'   => 'nullable|string|max:20',
            'rental_date'     => 'required|date',
            'rental_end_date' => 'required|date|after_or_equal:rental_date',
            'quantity'        => 'required|integer|min:1',
        ]);

        $item = Item::findOrFail($itemID);

        $bookedQuantity = Rental::where('itemID', $item->itemID)
            ->where('rental_Status', 'Pending')
            ->where(function ($query) use ($request) {
                $query->whereBetween('rental_StartDate', [$request->rental_date, $request->rental_end_date])
                      ->orWhereBetween('rental_EndDate', [$request->rental_date, $request->rental_end_date]);
            })
            ->sum('quantity');

        $availableQuantity = $item->item_Quantity - $bookedQuantity;

        if ($request->quantity > $availableQuantity) {
            return redirect()->back()->with('error', 'Requested quantity exceeds available items for the selected dates.');
        }

        // Insert rental directly into DB
        $rental = new Rental();
        $rental->rentalID          = 'RENT' . strtoupper(substr(md5(uniqid()), 0, 6));
        $rental->rental_Name       = $request->rental_name;
        $rental->rental_Email      = $request->rental_email ?? '';
        $rental->rental_PhoneNumber= $request->rental_phone;
        $rental->rental_BackupNumber = $request->rental_backup ?? '';
        $rental->rental_Status     = 'Pending';
        $rental->itemID            = $itemID;
        $rental->userID            = $request->session()->get('user_id');
        $rental->rental_StartDate  = $request->rental_date;
        $rental->rental_EndDate    = $request->rental_end_date;
        $rental->quantity          = $request->quantity;
        $rental->save();

        // Redirect to confirmation with rentalID
        return redirect()->route('customer.rental.confirmation', ['rentalID' => $rental->rentalID]);
    }

    // Show Confirmation Page (now fetch from DB)
    public function showConfirmation($rentalID)
    {
        $rentalData = Rental::with('item')->where('rentalID', $rentalID)->firstOrFail();

        // Calculate number of days and total price
        $start = Carbon::parse($rentalData->rental_StartDate);
        $end   = Carbon::parse($rentalData->rental_EndDate);
        $days  = $start->diffInDays($end) + 1;

        $quantity = $rentalData->quantity;
        $pricePerItem = $rentalData->item->item_Price ?? 0;
        $total = $quantity * $pricePerItem * $days;
        $deposit = $total * 0.20;
        

        return view('Rental.customer.confirmation', compact('rentalData', 'days', 'total', 'deposit'));
    }

    // Check Availability (AJAX support)
// Check Availability (AJAX support)
public function checkAvailability(Request $request, $itemID)
{
    try {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $rentalID  = $request->query('rental_id'); // optional

        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Start date and end date are required'], 400);
        }

        $item = Item::findOrFail($itemID);

        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        $availableQuantities = [];
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');

            // Count booked by others (exclude current rental if rental_id passed)
            $query = Rental::where('itemID', $itemID)
                ->whereIn('rental_Status', ['Pending', 'paid'])
                ->whereDate('rental_StartDate', '<=', $d)
                ->whereDate('rental_EndDate', '>=', $d);

            if ($rentalID) {
                $query->where('rentalID', '!=', $rentalID);
            }

            $bookedQuantity = $query->sum('quantity');

            // Available = total stock - booked by others for this day
            $availableForThisDay = $item->item_Quantity - $bookedQuantity;

            // Clamp within [0, total_stock]
            $availableForThisDay = max(min($availableForThisDay, $item->item_Quantity), 0);

            $availableQuantities[] = $availableForThisDay;
        }

        $maxAvailable = min($availableQuantities);

        return response()->json(['max_quantity' => $maxAvailable]);
    } catch (\Exception $e) {
        \Log::error('checkAvailability error: '.$e->getMessage());
        return response()->json(['error' => 'Could not check availability. Please try again later.'], 500);
    }
}


// Show edit form
public function editPage($rentalID)
{
    $rental = Rental::with('item')->findOrFail($rentalID);

    $item = $rental->item;
    $period = new \DatePeriod(
        new \DateTime($rental->rental_StartDate),
        new \DateInterval('P1D'),
        (new \DateTime($rental->rental_EndDate))->modify('+1 day')
    );

    $availableQuantities = [];
    foreach ($period as $date) {
        $d = $date->format('Y-m-d');

        // Booked by others (excluding this rental)
        $bookedQuantity = Rental::where('itemID', $item->itemID)
            ->where('rentalID', '!=', $rentalID)
            ->where('rental_Status', 'Pending')
            ->whereDate('rental_StartDate', '<=', $d)
            ->whereDate('rental_EndDate', '>=', $d)
            ->sum('quantity');

        // Availability for this day = total stock - booked by others
        $availableForThisDay = $item->item_Quantity - $bookedQuantity;

        // Clamp within [0, total_stock]
        $availableForThisDay = max(min($availableForThisDay, $item->item_Quantity), 0);

        $availableQuantities[] = $availableForThisDay;
    }

    // Take the min across the days
    $availableQuantity = min($availableQuantities);

    return view('Rental.customer.editPage', compact('rental', 'item', 'availableQuantity'));
}


// Handle edit submission (small suggested improvement to overlap check)
public function updateRent(Request $request, $rentalID)
{
    $request->validate([
        'rental_name'     => 'required|string|max:50',
        'rental_email'    => 'nullable|email|max:50',
        'rental_phone'    => 'required|string|max:20',
        'rental_backup'   => 'nullable|string|max:20',
        'rental_date'     => 'required|date',
        'rental_end_date' => 'required|date|after_or_equal:rental_date',
        'quantity'        => 'required|integer|min:1',
    ]);

    $rental = Rental::findOrFail($rentalID);
    $item   = Item::findOrFail($rental->itemID);

    // Check booked quantity by others using proper date-overlap test
    $bookedQuantity = Rental::where('itemID', $item->itemID)
        ->where('rentalID', '!=', $rentalID)
        ->where('rental_Status', 'Pending')
        ->whereDate('rental_StartDate', '<=', $request->rental_end_date)
        ->whereDate('rental_EndDate', '>=', $request->rental_date)
        ->sum('quantity');

    $availableQuantity = $item->item_Quantity - $bookedQuantity;

    if ($request->quantity > $availableQuantity) {
        return redirect()->back()->with('error', 'Requested quantity exceeds available items for the selected dates.');
    }

    // Update rental
    $rental->rental_Name        = $request->rental_name;
    $rental->rental_Email       = $request->rental_email ?? '';
    $rental->rental_PhoneNumber = $request->rental_phone;
    $rental->rental_BackupNumber= $request->rental_backup ?? '';
    $rental->rental_StartDate   = $request->rental_date;
    $rental->rental_EndDate     = $request->rental_end_date;
    $rental->quantity           = $request->quantity;
    $rental->save();

    // Redirect back to confirmation
    return redirect()->route('customer.rental.confirmation', ['rentalID' => $rental->rentalID]);
}

public function destroyCustomer($rentalID)
{
    \DB::table('rental')->where('rentalID', $rentalID)->delete();

    return redirect()
        ->route('customer.rental.main')
        ->with('success', 'Rental Cancelled successfully.');
}

// View Rental History

public function viewRentalHistory(Request $request)
{
    $userID = $request->session()->get('user_id');

    $rentals = Rental::with('item')
        ->where('userID', $userID)
        ->orderBy('rental_StartDate', 'desc')
        ->get()
        ->map(function ($rental) {
            if ($rental->item) {
                $days = Carbon::parse($rental->rental_StartDate)
                    ->diffInDays(Carbon::parse($rental->rental_EndDate)) + 1;

                $rental->total_cost = $days * $rental->quantity * $rental->item->item_Price;
                $rental->deposit    = $rental->total_cost * 0.20;
            } else {
                $rental->total_cost = 0;
                $rental->deposit    = 0;
            }
            return $rental;
        });

    return view('Rental.customer.viewRental', compact('rentals'));
}

public function requestApproval($rentalId)
{
    $rental = Rental::findOrFail($rentalId);
    $rental->return_Status = 'Requested'; // or 'Waiting Approval'
    $rental->save();

    return redirect()->back()->with('success', 'Your request has been sent for approval.');
}


public function viewCurrentAdmin()
{
    $today = now()->toDateString();

    $rentals = Rental::where('rental_Status', 'paid')
        ->whereDate('rental_EndDate', '>=', $today)
        ->orderBy('rental_StartDate', 'asc')
        ->with('item')
        ->get();

    // This points to a new view in the admin folder
    return view('Rental.admin.currentRentals', compact('rentals'));
}



}
