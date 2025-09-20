<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\Slot;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show booking page for a given field (Standard Field).
     */
    public function showBookingPage($fieldID)
    {
        $field = Field::where('fieldID', $fieldID)->first();
        if (!$field) {
            return redirect()->back()->with('error', 'Field not found.');
        }

        $slotsForCalendar = $this->prepareSlotsForCalendar($fieldID);
        $allFields = Field::all();

        return view('booking.customer.MainSlotBookingPage', [
            'field' => $field,
            'slotsForCalendar' => $slotsForCalendar,
            'allFields' => $allFields,
            'date' => Carbon::today()->toDateString(),
        ]);
    }

    /**
     * Show booking page for Mini Pitch field.
     */
    public function showMiniFieldBooking()
    {
        $field = Field::where('field_Size', "MINI SIZED FOOTBALL PITCH(9'S)")->first();
        if (!$field) abort(404, 'Field not found.');

        $slotsForCalendar = $this->prepareSlotsForCalendar($field->fieldID);

        return view('booking.customer.MiniSlotBookingPage', [
            'field' => $field,
            'slotsForCalendar' => $slotsForCalendar,
            'date' => Carbon::today()->toDateString(),
        ]);
    }

    /**
     * Handle AJAX request to get slots for a specific field.
     */
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

    /**
     * Handle AJAX booking request.
     */
    public function bookSlot(Request $request)
    {
        $request->validate([
            'fieldID' => 'required|string',
            'date' => 'required|date',
            'slotTime' => 'required'
        ]);

        $slot = Slot::where('fieldID', $request->fieldID)
                    ->where('slot_Date', $request->date)
                    ->where('slot_Time', $request->slotTime)
                    ->first();

        if (!$slot) {
            return response()->json(['message' => 'Slot not found.'], 404);
        }

        // ✅ Check if this slot is already booked (confirmed OR valid pending < 10 mins)
        $isBooked = Booking::where('slotID', $slot->slotID)
            ->where(function ($q) {
                $q->where('booking_Status', 'confirmed')
                  ->orWhere(function ($q2) {
                      $q2->where('booking_Status', 'pending')
                         ->where('booking_CreatedAt', '>=', now()->subMinutes(10));
                  });
            })
            ->exists();

        if ($isBooked) {
            return response()->json(['message' => 'This slot is already booked.'], 400);
        }

        return response()->json(['message' => 'Slot is available.']);
    }

    /**
     * Helper: Prepare slots array for FullCalendar for a given fieldID.
     */
 
    private function prepareSlotsForCalendar($fieldID)
{
    $slotsForCalendar = [];
    $field = Field::where('fieldID', $fieldID)->first();

    // ✅ Always use Malaysia timezone
    $now = Carbon::now('Asia/Kuala_Lumpur');

    for ($i = 0; $i < 30; $i++) {
        $date = Carbon::today('Asia/Kuala_Lumpur')->addDays($i)->toDateString();

        $slots = Slot::where('fieldID', $fieldID)
                     ->where('slot_Date', $date)
                     ->orderBy('slot_Time')
                     ->get();

        if ($slots->isEmpty()) {
            $slots = $this->generateSlots($fieldID, $date);
        }

        foreach ($slots as $slot) {
            // ✅ Parse slot time as Malaysia time
            $slotStart = Carbon::parse($slot->slot_Date . ' ' . $slot->slot_Time, 'Asia/Kuala_Lumpur');
            $slotEnd   = (clone $slotStart)->addHours(2);

            $status = 'available';
            $color  = '#28a745'; // green

            // ✅ Mark as past if already started
            if ($slotStart->lt($now)) {
                $status = 'past';
                $color  = '#6c757d'; // grey
            }

            // ✅ Only check bookings if still available
            if ($status === 'available') {
                $isBooked = Booking::where('slotID', $slot->slotID)
                    ->where(function ($q) {
                        $q->where('booking_Status', 'paid')
                          ->orWhere(function ($q2) {
                              $q2->where('booking_Status', 'pending')
                                 ->where('booking_CreatedAt', '>=', now()->subMinutes(10));
                          });
                    })
                    ->exists();

                if ($isBooked) {
                    $status = 'occupied';
                    $color  = '#dc3545'; // red
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



    /**
     * Helper: Generate default slots for a given field and date.
     */
    private function generateSlots($fieldID, $date)
    {
        $defaultTimes = [
            '08:00:00', '10:00:00', '12:00:00', '14:00:00',
            '16:00:00', '18:00:00', '20:00:00', '22:00:00'
        ];

        $slots = [];
        foreach ($defaultTimes as $time) {
            // Decide price based on time range
            $hour = intval(substr($time, 0, 2)); // get hour as int

            if ($hour >= 8 && $hour < 16) {
                $price = 450; // 8AM - 4PM
            } elseif ($hour >= 16 && $hour < 20) {
                $price = 500; // 4PM - 8PM
            } else {
                $price = 550; // After 8PM
            }

            $slot = Slot::create([
                'slotID'     => uniqid('SLOT'),
                'slot_Date'  => $date,
                'slot_Time'  => $time,
                'slot_Price' => $price,
                'fieldID'    => $fieldID,
                'slot_Status'=> 'available',
            ]);

            $slots[] = $slot;
        }

        return collect($slots);
    }

    /**
     * Helper: Add 2 hours to a time string (HH:MM:SS).
     */
    private function addTwoHours($time)
    {
        $end = strtotime($time . ' +2 hours');
        $formatted = date('H:i', $end);

        return $formatted === '00:00' ? '24:00:00' : $formatted . ':00';
    }

    /**
     * Store booking (when form is submitted).
     */
    public function store(Request $request)
    {
        $bookingID = 'BOOK' . uniqid();

        // ✅ Check again with 10-minute logic
        $isBooked = Booking::where('slotID', $request->slotID)
            ->where(function ($q) {
                $q->where('booking_Status', 'confirmed')
                  ->orWhere(function ($q2) {
                      $q2->where('booking_Status', 'pending')
                         ->where('booking_CreatedAt', '>=', now()->subMinutes(10));
                  });
            })
            ->exists();

        if ($isBooked) {
            return redirect()->back()->with('error', 'This slot has already been booked.');
        }

        $booking = Booking::create([
            'bookingID' => $bookingID,
            'booking_Name' => $request->booking_Name,
            'booking_Email' => $request->booking_Email,
            'booking_PhoneNumber' => $request->booking_PhoneNumber,
            'booking_BackupNumber' => $request->booking_BackupNumber,
            'booking_Status' => 'pending',
            'fieldID' => $request->fieldID,
            'slotID' => $request->slotID,
            'userID' => session('user_id'),
            'booking_CreatedAt' => now(),
        ]);

        return redirect()->route('booking.confirmation', $booking->bookingID);
    }

    /**
     * Show add booking page (form).
     */
    public function add($slotID)
    {
        $slot = Slot::with('field')->findOrFail($slotID);
        $field = $slot->field;

        return view('booking.customer.addPage', compact('slot', 'field'));
    }

    public function edit($bookingID)
    {
        $booking = Booking::with('slot', 'field')->findOrFail($bookingID);
        return view('booking.customer.editBookingPage', compact('booking'));
    }

    public function update(Request $request, $bookingID)
    {
        $booking = Booking::findOrFail($bookingID);

        $booking->update([
            'booking_Name' => $request->fullName,
            'booking_Email' => $request->email,
            'booking_PhoneNumber' => $request->phoneNumber,
            'booking_BackupNumber' => $request->backupPhone,
        ]);

        return redirect()->route('booking.confirmation', $booking->bookingID)
                         ->with('success', 'Booking updated successfully!');
    }

    public function confirmation($bookingID)
    {
        $booking = Booking::with(['field', 'slot'])->findOrFail($bookingID);
        return view('booking.customer.editPage', compact('booking'));
    }

    public function destroy($bookingID)
    {
        $booking = Booking::findOrFail($bookingID);
        $booking->delete();

        return redirect()->route('customer.dashboard')
                         ->with('success', 'Your booking has been cancelled successfully.');
    }

    /**
     * Return fresh slot data in JSON (for FullCalendar AJAX refresh).
     */
    public function getSlotsJson($fieldID)
    {
        $field = Field::where('fieldID', $fieldID)->first();
        if (!$field) {
            return response()->json(['error' => 'Field not found'], 404);
        }

        $slotsForCalendar = $this->prepareSlotsForCalendar($fieldID);

        return response()->json($slotsForCalendar);
    }

   public function viewBookings()
{
    $userId = session('user_id');
    

    $bookings = Booking::with('slot')
        ->where('userID', $userId)
        ->get();

    return view('booking.customer.viewBooking', compact('bookings'));
}



}
