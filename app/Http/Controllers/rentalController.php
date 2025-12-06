<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Staff;
use App\Models\Rental;
use App\Models\User; 
use App\Models\Rating; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class RentalController extends Controller
{
    /**
     * Helper to determine user role from session and get the correct view context.
     */
    private function getViewContext()
    {
        $userType = session('user_type');
        
        if (session()->has('user_id')) {
            if ($userType === 'administrator') {
                return (object)[
                    'is_admin_or_staff' => true,
                    'user_type' => 'admin',
                    'path' => 'Rental.admin' 
                ];
            }
            if ($userType === 'staff') {
                return (object)[
                    'is_admin_or_staff' => true,
                    'user_type' => 'staff',
                    'path' => 'Rental.staff' 
                ];
            }
        }
        
        return (object)[
            'is_admin_or_staff' => false,
            'user_type' => 'customer',
            'path' => 'Rental.customer'
        ];
    }

    // =========================
    // STAFF / ADMIN FUNCTIONS
    // =========================

    public function index()
    {
        $viewContext = $this->getViewContext();
        $availableItems = Item::where('item_Status', 'Available')->get();
        $unavailableItems = Item::where('item_Status', 'Unavailable')->get();

        return view($viewContext->path . '.MainRentalPage', compact('availableItems', 'unavailableItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_Name' => 'required|string|max:50',
            'item_Quantity' => 'required|integer|min:1',
            'item_Price' => 'required|numeric|min:0',
            'item_Description' => 'required|string|max:255',
            'item_Status' => 'required|string|max:20',
            'item_Image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userID = $request->session()->get('user_id');
        $staff = Staff::where('userID', $userID)->first();
        $staffID = $staff ? $staff->staffID : 'ADMIN';

        $item = new Item();
        $item->itemID = 'ITEM' . strtoupper(substr(md5(uniqid()), 0, 6));
        $item->item_Name = $request->item_Name;
        $item->item_Quantity = $request->item_Quantity;
        $item->item_Price = $request->item_Price;
        $item->item_Description = $request->item_Description;
        $item->item_Status = $request->item_Status;
        $item->staffID = $staffID;

        if ($request->hasFile('item_Image')) {
            $path = $request->file('item_Image')->store('items', 'public');
            $item->item_Image = $path;
        }

        $item->save();

        $viewContext = $this->getViewContext();
        return redirect()->route($viewContext->user_type . '.rental.main')->with('success', 'Item added successfully!');
    }

    public function edit($itemID)
    {
        $viewContext = $this->getViewContext();
        $item = Item::where('itemID', $itemID)->firstOrFail();
        return view($viewContext->path . '.editPage', compact('item'));
    }

    public function update(Request $request, $itemID)
    {
        $request->validate([
            'item_Name' => 'required|string|max:50',
            'item_Quantity' => 'required|integer|min:1',
            'item_Price' => 'required|numeric|min:0',
            'item_Description' => 'required|string|max:255',
            'item_Status' => 'required|string|max:20',
            'item_Image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item = Item::where('itemID', $itemID)->firstOrFail();

        $item->item_Name = $request->item_Name;
        $item->item_Quantity = $request->item_Quantity;
        $item->item_Price = $request->item_Price;
        $item->item_Description = $request->item_Description;
        $item->item_Status = $request->item_Status;

        if ($request->hasFile('item_Image')) {
            if ($item->item_Image && Storage::disk('public')->exists($item->item_Image)) {
                Storage::disk('public')->delete($item->item_Image);
            }
            $path = $request->file('item_Image')->store('items', 'public');
            $item->item_Image = $path;
        }

        $item->save();

        $viewContext = $this->getViewContext();
        return redirect()->route($viewContext->user_type . '.rental.main')->with('success', 'Item updated successfully!');
    }

   public function destroy($itemID)
    {
        try {
            // Check if item has active rentals
            $isActive = Rental::where('itemID', $itemID)
                ->whereIn('rental_Status', ['paid', 'pending'])
                ->exists();
    
            if ($isActive) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete item with active rentals.'
                ], 400);
            }
    
            // Find the item
            $item = Item::where('itemID', $itemID)->firstOrFail();
    
            // Attempt to delete the image if exists
            if ($item->item_Image) {
                $imagePath = $item->item_Image;
    
                // Ensure the path is relative to storage/app/public
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                } else {
                    \Log::warning("Item image not found or path invalid: $imagePath");
                }
            }
    
            // Delete the item record
            $item->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.'
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error("Failed to delete item $itemID: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item.',
                'error' => $e->getMessage()
            ], 400);
        }
    }



    public function viewReturnApprovals()
    {
        $viewContext = $this->getViewContext();
        $rentals = Rental::with('item', 'customer.user')
            ->where('return_Status', 'requested')
            ->orderBy('rental_StartDate', 'desc')
            ->get();
        
        return view($viewContext->path . '.ApproveRent', compact('rentals'));
    }

    public function updateReturnApproval(Request $request, $rentalID)
    {
        $rental = Rental::findOrFail($rentalID);
        $rental->return_Status = $request->status;
        $rental->save();

        if ($request->status === 'approved') {
            $item = $rental->item;
            if ($item) {
                $item->item_Quantity += $rental->quantity;
                $item->save();
            }
        }

        return response()->json(['success' => true]);
    }

    // --- STAFF: View Rentals ---
   public function viewCurrent(Request $request)
{
    $viewContext = $this->getViewContext(); // your existing method
    $now = Carbon::now('Asia/Kuala_Lumpur');

    // Generate month list
    $monthList = [];
    for ($i = 0; $i <= 12; $i++) {
        $date = $now->copy()->subMonths($i);
        $monthList[$date->format('Y-m')] = $date->format('F Y');
    }

    $selectedMonth = $request->input('month', $now->format('Y-m'));
    $rentalDate = $request->input('rental_date'); // exact date filter

    $rentalsQuery = Rental::with(['item', 'user'])
        ->whereIn('rental_Status', ['paid', 'completed']);

    // Apply exact date filter if provided (overrides month)
    if ($rentalDate) {
        $rentalsQuery->whereDate('rental_StartDate', '<=', $rentalDate)
                    ->whereDate('rental_EndDate', '>=', $rentalDate);
    }
    // Else filter by month
    else {
        $year = Carbon::parse($selectedMonth)->year;
        $month = Carbon::parse($selectedMonth)->month;
        $rentalsQuery->whereYear('rental_StartDate', $year)
                     ->whereMonth('rental_StartDate', $month);
    }

    $rentals = $rentalsQuery->orderBy('rental_StartDate', 'desc')
                            ->paginate(5)
                            ->appends($request->query());

    // Calculate total cost and deposit
    $rentals->getCollection()->transform(function ($rental) {
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

    return view('Rental.staff.viewRent', compact('rentals', 'monthList', 'selectedMonth', 'rentalDate'));
}


    // --- ADMIN: View Rentals ---
    public function viewCurrentAdmin(Request $request)
    {
        $now = Carbon::now('Asia/Kuala_Lumpur');

        $monthList = [];
        for ($i = 0; $i <= 12; $i++) {
            $date = $now->copy()->subMonths($i);
            $monthList[$date->format('Y-m')] = $date->format('F Y');
        }
        $selectedMonth = $request->input('month', $now->format('Y-m'));
        $year = Carbon::parse($selectedMonth)->year;
        $month = Carbon::parse($selectedMonth)->month;

        $rentals = Rental::with(['item', 'user'])
            ->whereIn('rental_Status', ['paid', 'completed'])
            ->whereYear('rental_StartDate', $year)
            ->whereMonth('rental_StartDate', $month)
            ->orderBy('rental_StartDate', 'desc')
            ->paginate(5)
            ->appends($request->query());

        $rentals->getCollection()->transform(function ($rental) {
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

        return view('Rental.admin.currentRentals', compact('rentals', 'monthList', 'selectedMonth'));
    }


    // =========================
    // CUSTOMER FUNCTIONS
    // =========================

    public function indexCustomer(Request $request)
    {
        $selectedDate = $request->input('rental_date', Carbon::now('Asia/Kuala_Lumpur')->toDateString());
        $availableItems = Item::where('item_Status', 'Available')->get();

        $rentedQuantities = DB::table('rental')
            ->where('rental_Status', 'paid')
            ->whereDate('rental_StartDate', '<=', $selectedDate)
            ->whereDate('rental_EndDate', '>=', $selectedDate)
            ->groupBy('itemID')
            ->select('itemID', DB::raw('SUM(quantity) as rented_quantity'))
            ->get()
            ->keyBy('itemID');

        $itemsForView = $availableItems->map(function ($item) use ($rentedQuantities) {
            $rented = $rentedQuantities->get($item->itemID);
            $rentedCount = $rented ? $rented->rented_quantity : 0;
            $item->available_quantity = $item->item_Quantity - $rentedCount; 

            // Rating Calculation
            $item->avg_rating = Rating::whereHas('rental', function($q) use ($item) {
                $q->where('itemID', $item->itemID);
            })->avg('rating_Score') ?? 0;

            $item->rating_count = Rating::whereHas('rental', function($q) use ($item) {
                $q->where('itemID', $item->itemID);
            })->count();
            
            return $item;
        })
        ->filter(function ($item) {
            return $item->available_quantity > 0;
        });

        return view('Rental.customer.MainRentalPage', [
            'availableItems' => $itemsForView,
            'selectedDate' => $selectedDate,
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

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
                ->where('rental_Status', 'paid')
                ->whereDate('rental_StartDate', '<=', $d)
                ->whereDate('rental_EndDate', '>=', $d)
                ->sum('quantity');

            $availableQuantities[] = $item->item_Quantity - $bookedQuantity;
        }

        $availableQuantity = min($availableQuantities);
        if($availableQuantity < 0) $availableQuantity = 0;

        // Reviews
        $reviews = Rating::whereHas('rental', function ($query) use ($itemID) {
                $query->where('itemID', $itemID);
            })
            ->with('customer.user')
            ->latest('review_Date')
            ->paginate(3);

        $averageRating = Rating::whereHas('rental', function ($query) use ($itemID) {
                $query->where('itemID', $itemID);
            })->avg('rating_Score');

        $totalReviews = $reviews->total();

        return view('Rental.customer.addPage', compact('item', 'availableQuantity', 'startDate', 'endDate', 'reviews', 'averageRating', 'totalReviews'));
    }

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
            ->where('rental_Status', 'paid')
            ->where(function ($query) use ($request) {
                $query->whereBetween('rental_StartDate', [$request->rental_date, $request->rental_end_date])
                      ->orWhereBetween('rental_EndDate', [$request->rental_date, $request->rental_end_date]);
            })
            ->sum('quantity');

        $availableQuantity = $item->item_Quantity - $bookedQuantity;

        if ($request->quantity > $availableQuantity) {
            return redirect()->back()->with('error', 'Requested quantity exceeds available items for the selected dates.');
        }

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

        return redirect()->route('customer.rental.confirmation', ['rentalID' => $rental->rentalID]);
    }

    public function showConfirmation($rentalID)
    {
        $rentalData = Rental::with('item')->where('rentalID', $rentalID)->firstOrFail();

        $start = Carbon::parse($rentalData->rental_StartDate);
        $end   = Carbon::parse($rentalData->rental_EndDate);
        $days  = $start->diffInDays($end) + 1;

        $quantity = $rentalData->quantity;
        $pricePerItem = $rentalData->item->item_Price ?? 0;
        $total = $quantity * $pricePerItem * $days;
        $deposit = $total * 0.20;

        return view('Rental.customer.confirmation', compact('rentalData', 'days', 'total', 'deposit'));
    }

    public function checkAvailability(Request $request, $itemID)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $rentalID  = $request->query('rental_id'); 

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

                $query = Rental::where('itemID', $itemID)
                    ->whereIn('rental_Status', ['paid']) // Only confirmed rentals block stock
                    ->whereDate('rental_StartDate', '<=', $d)
                    ->whereDate('rental_EndDate', '>=', $d);

                if ($rentalID) {
                    $query->where('rentalID', '!=', $rentalID);
                }

                $bookedQuantity = $query->sum('quantity');
                $availableForThisDay = max($item->item_Quantity - $bookedQuantity, 0);
                $availableQuantities[] = $availableForThisDay;
            }

            $maxAvailable = min($availableQuantities);

            return response()->json(['max_quantity' => $maxAvailable]);
        } catch (\Exception $e) {
            \Log::error('checkAvailability error: '.$e->getMessage());
            return response()->json(['error' => 'Could not check availability. Please try again later.'], 500);
        }
    }

    public function editPage($rentalID)
    {
        $rental = Rental::with('item')->findOrFail($rentalID);
        $item = $rental->item;
        $availableQuantity = $item->item_Quantity;
        
        return view('Rental.customer.editPage', compact('rental', 'item', 'availableQuantity'));
    }

    public function updateRent(Request $request, $rentalID)
    {
        $rental = Rental::findOrFail($rentalID);
        $rental->update($request->all());
        return redirect()->route('customer.rental.confirmation', ['rentalID' => $rental->rentalID]);
    }

    public function destroyCustomer($rentalID)
    {
        \DB::table('rental')->where('rentalID', $rentalID)->delete();
        return redirect()->route('customer.rental.main')->with('success', 'Rental Cancelled successfully.');
    }

    public function viewRentalHistory(Request $request)
    {
        $userID = $request->session()->get('user_id');
        $now = Carbon::now('Asia/Kuala_Lumpur');

        // 1. Month List Generation
        $monthList = [];
        for ($i = 6; $i >= 0; $i--) { 
            $date = $now->copy()->subMonths($i); 
            $monthList[$date->format('Y-m')] = $date->format('F Y'); 
        }
        for ($i = 1; $i <= 6; $i++) { 
            $date = $now->copy()->addMonths($i); 
            $monthList[$date->format('Y-m')] = $date->format('F Y'); 
        }

        $selectedMonth = $request->input('month', $now->format('Y-m'));
        $year = Carbon::parse($selectedMonth)->year;
        $month = Carbon::parse($selectedMonth)->month;

        // 2. Active Query (Paid / Ongoing / Requested Return)
        $activeQuery = Rental::with('item')
            ->where('userID', $userID)
            ->where('rental_Status', 'paid') 
            ->where(function($q) {
                $q->whereNull('return_Status')
                  ->orWhere('return_Status', 'requested'); 
            })
            ->whereYear('rental_StartDate', $year)
            ->whereMonth('rental_StartDate', $month)
            ->orderBy('rental_StartDate', 'asc');

        $activeRentals = $activeQuery->paginate(5, ['*'], 'active_page')->appends($request->query());
        $activeRentals->getCollection()->transform(fn ($r) => $this->calculateRentalCosts($r));

        // 3. History Query (Completed Cycle: Paid Full, Approved Return, or Failed)
        $historyQuery = Rental::with('item')
            ->where('userID', $userID)
            ->where(function($q) {
                $q->whereIn('rental_Status', ['completed', 'failed']) // Completed/Failed payment is a history state
                  ->orWhereIn('return_Status', ['approved', 'rejected']); // Approved/Rejected return is a history state
            })
            ->whereYear('rental_StartDate', $year)
            ->whereMonth('rental_StartDate', $month)
            ->orderBy('rental_StartDate', 'desc');

        $rentalHistory = $historyQuery->paginate(5, ['*'], 'history_page')->appends($request->query());
        $rentalHistory->getCollection()->transform(fn ($r) => $this->calculateRentalCosts($r));

        return view('Rental.customer.viewRental', [
            'activeRentals' => $activeRentals,
            'rentalHistory' => $rentalHistory,
            'monthList'     => $monthList,
            'selectedMonth' => $selectedMonth
        ]);
    }

    private function calculateRentalCosts($rental)
    {
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
    }

    public function requestApproval($rentalId)
    {
        $rental = Rental::findOrFail($rentalId);
        $rental->return_Status = 'Requested'; 
        $rental->save();
        return redirect()->back()->with('success', 'Your request has been sent for approval.');
    }
}
