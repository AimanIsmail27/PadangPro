<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\Slot;
use App\Models\Booking;
use App\Models\User;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Helper to determine user role from session and get the correct view context.
     * @return object
     */
    private function getViewContext()
    {
        $userType = session('user_type');
        
        if (session()->has('user_id')) {
            if ($userType === 'administrator') {
                return (object)[
                    'is_admin_or_staff' => true,
                    'user_type' => 'admin', // Use 'admin' to match your route name 'admin.booking.viewAll'
                    'path' => 'Booking.admin' // resources/views/booking/admin/
                ];
            }
            if ($userType === 'staff') {
                return (object)[
                    'is_admin_or_staff' => true,
                    'user_type' => 'staff', // Use 'staff' to match your route name 'staff.booking.viewAll'
                    'path' => 'Booking.staff' // resources/views/booking/staff/
                ];
            }
        }
        
        return (object)[
            'is_admin_or_staff' => false,
            'user_type' => 'customer',
            'path' => 'Booking.customer' // Views folder: resources/views/booking/customer/
        ];
    }

    public function showBookingPage($fieldID = null)
    {
        $viewContext = $this->getViewContext();

        if (!$fieldID) {
            $field = Field::firstOrFail();
            $fieldID = $field->fieldID;
        } else {
            $field = Field::where('fieldID', $fieldID)->firstOrFail();
        }

        $slotsForCalendar = $this->prepareSlotsForCalendar($fieldID);
        $allFields = Field::all();

        // --- NEW: Fetch Reviews for this Field ---
        // We need to find Ratings -> linked to Bookings -> linked to this Field
        $reviews = Rating::whereHas('booking', function ($query) use ($fieldID) {
                $query->where('fieldID', $fieldID);
            })
            ->with('customer.user') // Eager load customer name
            ->latest('review_Date')
            ->paginate(4);

        // Calculate Average Rating
        $averageRating = $reviews->avg('rating_Score');
        $totalReviews = $reviews->count();

        return view($viewContext->path . '.MainSlotBookingPage', [
            'field' => $field,
            'slotsForCalendar' => $slotsForCalendar,
            'allFields' => $allFields,
            'date' => Carbon::today('Asia/Kuala_Lumpur')->toDateString(),
            // Pass the new review data
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
        ]);
    }

    public function showMiniFieldBooking()
    {
        $viewContext = $this->getViewContext();
        
        // 1. Get the Mini Field
        $field = Field::where('field_Size', "MINI SIZED FOOTBALL PITCH(9'S)")->first();
        if (!$field) abort(404, 'Field not found.');

        $slotsForCalendar = $this->prepareSlotsForCalendar($field->fieldID);

        // 2. NEW: Fetch Reviews for this specific Field
        $reviews = \App\Models\Rating::whereHas('booking', function ($query) use ($field) {
                $query->where('fieldID', $field->fieldID);
            })
            ->with('customer.user') // Eager load customer info
            ->latest('review_Date')
            ->paginate(4); // Pagination

        // 3. Calculate Average Rating
        $averageRating = \App\Models\Rating::whereHas('booking', function ($query) use ($field) {
                $query->where('fieldID', $field->fieldID);
            })->avg('rating_Score');
            
        $totalReviews = $reviews->total();

        return view($viewContext->path . '.MiniSlotBookingPage', [
            'field' => $field,
            'slotsForCalendar' => $slotsForCalendar,
            'date' => Carbon::today('Asia/Kuala_Lumpur')->toDateString(),
            // Pass the new data to the view
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
        ]);
    }

    public function add($slotID)
    {
        $viewContext = $this->getViewContext();
        $slot = Slot::with('field')->findOrFail($slotID);
        $field = $slot->field;
        return view($viewContext->path . '.addPage', compact('slot', 'field'));
    }

   public function store(Request $request)
    {
        $viewContext = $this->getViewContext();
        $bookingID = 'BOOK' . uniqid();
    
        // 1. Availability Check for the slot
        $isBooked = Booking::where('slotID', $request->slotID)
            ->where(function ($q) {
                $q->whereIn('booking_Status', ['paid', 'confirmed', 'completed'])
                  ->orWhere(function ($q2) {
                      $q2->where('booking_Status', 'pending')
                         ->where('booking_CreatedAt', '>=', now()->subMinutes(10));
                  });
            })->exists();
    
        if ($isBooked) {
            return redirect()->back()->with('error', 'This slot has already been booked.');
        }
    
        // 2. Validate input
        $request->validate([
            'booking_Name' => 'required',
            'booking_Email' => 'required|email',
            'booking_PhoneNumber' => 'required',
        ]);
    
        // 3. Determine booking status and redirect route based on user type
        if ($viewContext->is_admin_or_staff) {
            $userID = session('user_id');
            $bookingStatus = 'paid'; 
            $redirectRoute = $viewContext->user_type . '.booking.viewAll';
        } else {
            $userID = session('user_id');
            $bookingStatus = 'pending';
            $redirectRoute = 'booking.confirmation';
        }
    
        // 4. Create the booking record
        $booking = Booking::create([
            'bookingID' => $bookingID,
            'booking_Name' => $request->booking_Name,
            'booking_Email' => $request->booking_Email,
            'booking_PhoneNumber' => $request->booking_PhoneNumber,
            'booking_BackupNumber' => $request->booking_BackupNumber,
            'booking_Status' => $bookingStatus,
            'fieldID' => $request->fieldID,
            'slotID' => $request->slotID,
            'userID' => $userID,
            'booking_CreatedAt' => now(),
        ]);
    
        // 5. If admin/staff: record deposit payment automatically
        if ($viewContext->is_admin_or_staff) {
            // Get field price
            $field = Field::where('fieldID', $request->fieldID)->first();
    
            if ($field) {
                // Calculate deposit (20% of field price)
                 $depositAmount = $booking->slot->slot_Price * 0.2;
    
                // Record deposit payment as cash
                app(\App\Http\Controllers\PaymentController::class)
                    ->recordDeposit($bookingID, $depositAmount, 'cash');
            }
        }
    
        // 6. Redirect user accordingly
        if ($viewContext->is_admin_or_staff) {
            return redirect()->route($redirectRoute)->with('success', 'Walk-in booking created successfully and deposit recorded!');
        }
    
        return redirect()->route($redirectRoute, $booking->bookingID);
    }

    
   // In app/Http/Controllers/BookingController.php

    public function viewBookings(Request $request)
{
    $viewContext = $this->getViewContext();
    $now = Carbon::now('Asia/Kuala_Lumpur');

    if ($viewContext->is_admin_or_staff) {

        // --- ADMIN/STAFF LOGIC ---

        // 1. Get Month List (PAST + FUTURE)
        $monthList = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthList[$date->format('Y-m')] = $date->format('F Y');
        }

        for ($i = 1; $i <= 6; $i++) {
            $date = $now->copy()->addMonths($i);
            $monthList[$date->format('Y-m')] = $date->format('F Y');
        }

        // 2. Get Filters
        $selectedMonth  = $request->input('month', $now->format('Y-m'));
        $searchDate     = $request->input('search_date');
        $selectedStatus = $request->input('status', 'all');

        // --- Query 1: Admin/Staff Bookings ---
        $adminQuery = Booking::with(['slot', 'field', 'user'])
            ->whereHas('user', function ($query) {
                $query->whereIn('user_Type', ['administrator', 'staff']);
            });

        // --- Query 2: Customer Bookings ---
        $customerQuery = Booking::with(['slot', 'field', 'user'])
            ->whereHas('user', function ($query) {
                $query->where('user_Type', 'customer');
            });

        // 3. APPLY DATE / MONTH FILTERS (✅ SLOT DATE)
        if ($searchDate) {

            $adminQuery->whereHas('slot', function ($q) use ($searchDate) {
                $q->whereDate('slot_Date', $searchDate);
            });

            $customerQuery->whereHas('slot', function ($q) use ($searchDate) {
                $q->whereDate('slot_Date', $searchDate);
            });

            $selectedMonth = null;

        } else {

            $year  = Carbon::parse($selectedMonth)->year;
            $month = Carbon::parse($selectedMonth)->month;

            $adminQuery->whereHas('slot', function ($q) use ($year, $month) {
                $q->whereYear('slot_Date', $year)
                  ->whereMonth('slot_Date', $month);
            });

            $customerQuery->whereHas('slot', function ($q) use ($year, $month) {
                $q->whereYear('slot_Date', $year)
                  ->whereMonth('slot_Date', $month);
            });
        }

        // 4. APPLY STATUS FILTER
        if ($selectedStatus !== 'all') {
            $adminQuery->where('booking_Status', $selectedStatus);
            $customerQuery->where('booking_Status', $selectedStatus);
        } else {
            $adminQuery->whereIn('booking_Status', ['paid', 'completed']);
            $customerQuery->whereIn('booking_Status', ['paid', 'completed']);
        }

        // 5. Execute Queries
        $adminBookings = $adminQuery
            ->latest('booking_CreatedAt')
            ->paginate(5, ['*'], 'admin_page')
            ->appends($request->query());

        $customerBookings = $customerQuery
            ->latest('booking_CreatedAt')
            ->paginate(5, ['*'], 'customer_page')
            ->appends($request->query());

        // 6. Format Results
        $this->formatBookingCollection($adminBookings->getCollection());
        $this->formatBookingCollection($customerBookings->getCollection());

        return view(
            $viewContext->path . '.ViewBooking',
            compact(
                'adminBookings',
                'customerBookings',
                'monthList',
                'selectedMonth',
                'selectedStatus'
            )
        );

    } else {

        // --- CUSTOMER LOGIC (UNCHANGED) ---

        $userId    = session('user_id');
        $userEmail = session('user_email');

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
        $year  = Carbon::parse($selectedMonth)->year;
        $month = Carbon::parse($selectedMonth)->month;

        $pendingBookingsQuery = Booking::with(['slot', 'field'])
            ->where(function ($q) use ($userId, $userEmail) {
                $q->where('booking.userID', $userId)
                  ->orWhere('booking.booking_Email', $userEmail);
            })
            ->whereIn('booking.booking_Status', ['paid'])
            ->whereHas('slot', function ($q) use ($year, $month) {
                $q->whereYear('slot_Date', $year)
                  ->whereMonth('slot_Date', $month);
            })
            ->orderBy('booking_CreatedAt', 'asc');

        $pendingBookings = $pendingBookingsQuery
            ->paginate(5, ['*'], 'pending_page')
            ->appends($request->query());

        $this->formatBookingCollection($pendingBookings->getCollection());

        $completedBookingsQuery = Booking::with(['slot', 'field'])
            ->where(function ($q) use ($userId, $userEmail) {
                $q->where('booking.userID', $userId)
                  ->orWhere('booking.booking_Email', $userEmail);
            })
            ->where('booking.booking_Status', 'completed')
            ->whereHas('slot', function ($q) use ($year, $month) {
                $q->whereYear('slot_Date', $year)
                  ->whereMonth('slot_Date', $month);
            })
            ->orderBy('booking_CreatedAt', 'desc');

        $completedBookings = $completedBookingsQuery
            ->paginate(5, ['*'], 'completed_page')
            ->appends($request->query());

        $this->formatBookingCollection($completedBookings->getCollection());

        return view($viewContext->path . '.ViewBooking', [
            'pendingBookings'   => $pendingBookings,
            'completedBookings' => $completedBookings,
            'monthList'         => $monthList,
            'selectedMonth'     => $selectedMonth
        ]);
    }
}


    private function formatBookingCollection($collection)
    {
        $collection->transform(function ($booking) {
            $slotDateTime = null;
            if ($booking->slot && $booking->slot->slot_Date && $booking->slot->slot_Time) {
                $slotDateTime = Carbon::parse($booking->slot->slot_Date . ' ' . $booking->slot->slot_Time, 'Asia/Kuala_Lumpur');
            }

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $hasPassed = $slotDateTime ? $slotDateTime->copy()->addHours(2)->lt($now) : false;

            if ($booking->booking_Status === 'paid') {
                $booking->display_Status = 'Awaiting Balance';
            
            } elseif ($booking->booking_Status === 'completed') {
                $booking->display_Status = 'Completed';
            
            } elseif ($booking->booking_Status === 'pending' && $hasPassed) {
                $booking->display_Status = 'Expired';
            
            } else {
                $booking->display_Status = ucfirst($booking->booking_Status);
            }

            $booking->formattedDate = $booking->slot && $booking->slot->slot_Date ? Carbon::parse($booking->slot->slot_Date)->format('d M Y') : 'N/A';
            $booking->formattedTime = $booking->slot && !empty($booking->slot->slot_Time) ? Carbon::parse($booking->slot->slot_Time)->format('h:i A') : 'N/A';
            $booking->formattedPrice = 'RM ' . number_format($booking->slot->slot_Price ?? 0, 2);
            
            return $booking;
        });
    }
   
    public function getSlotsForField($fieldID)
    {
        $field = Field::where('fieldID', $fieldID)->first();
        if (!$field) {
            return response()->json(['error' => 'Field not found'], 404);
        }
        $slotsForCalendar = $this->prepareSlotsForCalendar($fieldID);
        return response()->json([
            'field_Label' => $field->field_Label,
            'slots' => $slotsForCalendar
        ]);
    }

    public function bookSlot(Request $request)
    {
        $request->validate([
            'fieldID' => 'required|string',
            'date' => 'required|date',
            'slotTime' => 'required'
        ]);
        $slot = Slot::where('fieldID', $request->fieldID)->where('slot_Date', $request->date)->where('slot_Time', $request->slotTime)->first();
        if (!$slot) {
            return response()->json(['message' => 'Slot not found.'], 404);
        }
        $isBooked = Booking::where('slotID', $slot->slotID)
            ->where(function ($q) {
                $q->whereIn('booking_Status', ['paid', 'confirmed', 'completed'])
                  ->orWhere(function ($q2) {
                      $q2->where('booking_Status', 'pending')
                         ->where('booking_CreatedAt', '>=', now()->subMinutes(10));
                  });
            })->exists();
        if ($isBooked) {
            return response()->json(['message' => 'This slot is already booked.'], 400);
        }
        return response()->json(['message' => 'Slot is available.']);
    }

    private function prepareSlotsForCalendar($fieldID)
    {
        $slotsForCalendar = [];
        $field = Field::where('fieldID', $fieldID)->first();
        $now = Carbon::now('Asia/Kuala_Lumpur');
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::today('Asia/Kuala_Lumpur')->addDays($i)->toDateString();
            $slots = Slot::where('fieldID', $fieldID)->where('slot_Date', $date)->orderBy('slot_Time')->get();
            if ($slots->isEmpty()) {
                $slots = $this->generateSlots($fieldID, $date);
            }
            foreach ($slots as $slot) {
                $slotStart = Carbon::parse($slot->slot_Date . ' ' . $slot->slot_Time, 'Asia/Kuala_Lumpur');
                $slotEnd   = (clone $slotStart)->addHours(2);
                $status = 'available';
                $color  = '#28a745';
                if ($slotStart->lt($now)) {
                    $status = 'past';
                    $color  = '#6c757d';
                }
                if ($status === 'available') {
                    $isBooked = Booking::where('slotID', $slot->slotID)
                        ->where(function ($q) {
                            $q->whereIn('booking_Status', ['paid', 'completed'])
                              ->orWhere(function ($q2) {
                                  $q2->where('booking_Status', 'pending')
                                     ->where('booking_CreatedAt', '>=', now()->subMinutes(10));
                              });
                        })->exists();
                    if ($isBooked) {
                        $status = 'occupied';
                        $color  = '#dc3545';
                    }
                }
                $slotsForCalendar[] = [
                    'id' => $slot->slotID,
                    'title' => $status === 'past' ? 'Past Slot' : ucfirst($status),
                    'start' => $slotStart->format('Y-m-d\TH:i'),
                    'end' => $slotEnd->format('Y-m-d\TH:i'),
                    'color' => $color,
                    'status' => $status,
                    'slotId' => $slot->slotID,
                    'price' => $slot->slot_Price,
                    'time' => $slot->slot_Time,
                    'date' => $slot->slot_Date,
                    'field' => $field ? $field->field_Label : ''
                ];
            }
        }
        return $slotsForCalendar;
    }

    private function generateSlots($fieldID, $date)
    {
        $defaultTimes = [
            '08:00:00', '10:00:00', '12:00:00', '14:00:00',
            '16:00:00', '18:00:00', '20:00:00', '22:00:00'
        ];

        $field = Field::find($fieldID);
        $generatedSlots = [];

        DB::transaction(function () use ($fieldID, $date, $defaultTimes, $field, &$generatedSlots) {
            foreach ($defaultTimes as $time) {
                $hour = intval(substr($time, 0, 2));

                if ($field && $field->field_Size === "MINI SIZED FOOTBALL PITCH(9'S)") {
                    $price = ($hour >= 8 && $hour < 16) ? 350 : 400;
                } else {
                    if ($hour >= 8 && $hour < 16) $price = 450;
                    elseif ($hour >= 16 && $hour < 20) $price = 500;
                    else $price = 550;
                }

                $slotData = [
                    'slotID'     => uniqid('SLOT'),
                    'slot_Date'  => $date,
                    'slot_Time'  => $time,
                    'slot_Price' => $price,
                    'fieldID'    => $fieldID,
                    'slot_Status'=> 'available',
                ];
                
                $slot = Slot::create($slotData);
                $generatedSlots[] = $slot;
            }
        });

        return collect($generatedSlots);
    }

    private function addTwoHours($time)
    {
        $end = strtotime($time . ' +2 hours');
        $formatted = date('H:i', $end);
        return $formatted === '00:00' ? '24:00:00' : $formatted . ':00';
    }

    public function edit($bookingID)
    {
        $viewContext = $this->getViewContext();
        $booking = Booking::with('slot', 'field')->findOrFail($bookingID);
        
        return view($viewContext->path . '.editBookingPage', compact('booking'));
    }

    public function update(Request $request, $bookingID)
    {
        $viewContext = $this->getViewContext();
        $booking = Booking::findOrFail($bookingID);

        $request->validate([
            'booking_Name' => 'required|string|max:50',
            'booking_Email' => 'required|email|max:50',
            'booking_PhoneNumber' => 'required|string|max:20',
            'booking_BackupNumber' => 'nullable|string|max:20',
        ]);

        $booking->update([
            'booking_Name' => $request->booking_Name,
            'booking_Email' => $request->booking_Email,
            'booking_PhoneNumber' => $request->booking_PhoneNumber,
            'booking_BackupNumber' => $request->booking_BackupNumber,
        ]);

        if ($viewContext->is_admin_or_staff) {
            return redirect()->route($viewContext->user_type . '.booking.viewAll')->with('success', 'Booking updated successfully!');
        } else {
            return redirect()->route('booking.confirmation', $booking->bookingID)->with('success', 'Booking updated successfully!');
        }
    }

    public function confirmation($bookingID)
    {
        $booking = Booking::with(['field', 'slot'])->findOrFail($bookingID);
        return view('Booking.customer.editPage', compact('booking'));
    }

    public function destroy($bookingID)
    {
        $viewContext = $this->getViewContext();
        $booking = Booking::findOrFail($bookingID);
        $booking->delete();


        if ($viewContext->is_admin_or_staff) {
            $redirectRoute = $viewContext->user_type . '.booking.viewAll'; // 'admin.booking.viewAll' or 'staff.booking.viewAll'
        } else {
            $redirectRoute = 'customer.dashboard'; // Customers go to their main dashboard
        }
        
        return redirect()->route($redirectRoute)->with('success', 'The booking has been successfully cancelled.');
    }

    public function getSlotsJson($fieldID)
    {
        $field = Field::where('fieldID', $fieldID)->first();
        if (!$field) {
            return response()->json(['error' => 'Field not found'], 404);
        }
        $slotsForCalendar = $this->prepareSlotsForCalendar($fieldID);
        return response()->json($slotsForCalendar);
    }
    
}
