<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Rental;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Create a ToyyibPay bill and redirect user to payment page
     */
    public function createPayment(Request $request, $bookingID)
    {
        $booking = Booking::with('slot', 'field')->where('bookingID', $bookingID)->firstOrFail();

        $deposit = $booking->slot->slot_Price * 0.20; // ACTUAL FORMULA
        //$deposit = 1; // FOR TESTING PURPOSES
        $amount = $deposit * 100; // ToyyibPay uses cents

        // ToyyibPay API request
        // --- CHANGE 1: Use the DEV URL ---
        $response = Http::asForm()->post(env('TOYYIBPAY_URL_DEV') . '/index.php/api/createBill', [
            // --- CHANGE 2: Use your DEV credentials ---
            'userSecretKey'         => env('TOYYIBPAY_SECRET_DEV'),
            'categoryCode'          => env('TOYYIBPAY_CATEGORY_RENTAL'), // Use your dev category
            // --- END CHANGES ---
            'billName'              => 'PadangPro Booking',
            'billDescription'       => 'Payment for booking ' . $booking->bookingID,
            'billPriceSetting'      => 1,
            'billPayorInfo'         => 1,
            'billAmount'            => $amount,
            'billReturnUrl'         => route('payment.return'),
            'billCallbackUrl'       => route('payment.callback'),
            'billExternalReferenceNo' => $booking->bookingID,
            'billTo'                => $booking->booking_Name,
            'billEmail'             => $booking->booking_Email,
            'billPhone'             => $booking->booking_PhoneNumber,
        ]);

        // Add error handling to prevent "array offset on value of type null"
        if (!$response->successful() || !isset($response->json()[0]['BillCode'])) {
            Log::error('ToyyibPay (DEV) initial deposit creation failed.', ['response' => $response->body()]);
            return redirect()->route('booking.confirmation', ['bookingID' => $bookingID])
                             ->with('error', 'Could not create payment. Please try again later.');
        }

        $bill = $response->json()[0];

        // Save into payment table
        Payment::create([
            'paymentID'       => 'PAY' . uniqid(),
            'payer_Name'      => $booking->booking_Name,
            'payer_BankAccount' => null,
            'payment_Amount'  => $deposit,
            'payment_Status'  => 'pending',
            'bookingID'       => $booking->bookingID,
            'userID'          => $booking->userID,
        ]);

        // --- CHANGE 3: Redirect user to the DEV ToyyibPay URL ---
        return redirect(env('TOYYIBPAY_URL_DEV') . '/' . $bill['BillCode']);
    }

    /**
     * Handle return (user-facing after payment)
     */
    public function paymentReturn(Request $request)
    {
        // ToyyibPay sends status back in the query string
        $status    = $request->status_id ?? $request->status; // sometimes it's status_id
        $bookingID = $request->order_id ?? $request->billExternalReferenceNo ?? null;

        if ($bookingID) {
            $payment = Payment::where('bookingID', $bookingID)->latest()->first();

            if ($payment) {
                if ($status == 1) {
                    $payment->update(['payment_Status' => 'paid']);
                    Booking::where('bookingID', $bookingID)->update(['booking_Status' => 'paid']);
                    return redirect()->route('booking.view')
                        ->with('payment_success', 'Payment successful!');
                } else {
                    $payment->update(['payment_Status' => 'failed']);
                    return redirect()->route('booking.view')
                        ->with('error', 'Your payment was cancelled or failed. Please try again.');
                }
            }
        }

        return redirect()->route('booking.confirmation', ['bookingID' => $bookingID ?? 'unknown'])
            ->with('error', 'Payment status could not be verified.');
    }

    /**
     * Handle callback (server-to-server confirmation)
     */
    public function paymentCallback(Request $request)
    {
        $bookingID = $request->billExternalReferenceNo;
        $status    = $request->status; // 1=paid, 0=unpaid
        $refNo     = $request->refno ?? null; // ToyyibPay transaction reference

        $payment = Payment::where('bookingID', $bookingID)->latest()->first();

        if ($payment) {
            if ($status == 1) {
                $payment->update([
                    'payment_Status'    => 'paid',
                    'payer_BankAccount' => $refNo // save transaction reference number
                ]);

                // Also update booking status
                Booking::where('bookingID', $bookingID)->update([
                    'booking_Status' => 'paid'
                ]);
            } else {
                $payment->update(['payment_Status' => 'failed']);
            }
        }

        return response()->json(['message' => 'Callback processed']);
    }


  /**
 * ==============================
 *  RENTAL PART
 * ==============================
 */

// Create ToyyibPay bill and redirect user for rental payment (DEV mode)
public function createRentalPayment(Request $request, $rentalID)
{
    $rental = Rental::with('item')->where('rentalID', $rentalID)->firstOrFail();

    $totalPrice = $request->input('total_amount');
    \Log::info('POST received for rentalID: ' . $rentalID, ['request' => $request->all()]);

    if (!$totalPrice || $totalPrice <= 0) {
        return redirect()->route('customer.rental.confirmation', ['rentalID' => $rentalID])
            ->with('error', 'Invalid total amount for payment.');
    }

    $amount = intval($totalPrice * 100); // convert to cents

    // Use dev environment
    $response = Http::asForm()->post(env('TOYYIBPAY_URL_DEV') . '/index.php/api/createBill', [
        'userSecretKey'           => env('TOYYIBPAY_SECRET_DEV'),
        'categoryCode'            => env('TOYYIBPAY_CATEGORY_RENTAL'),
        'billName'                => 'PadangPro Rental',
        'billDescription'         => 'Payment for rental ' . $rental->rentalID,
        'billPriceSetting'        => 1,
        'billPayorInfo'           => 1,
        'billAmount'              => $amount,
        'billReturnUrl'           => route('customer.rental.payment.return'),
        'billCallbackUrl'         => route('customer.rental.payment.callback'),
        'billExternalReferenceNo' => $rental->rentalID,
        'billTo'                  => $rental->rental_Name,
        'billEmail'               => $rental->rental_Email,
        'billPhone'               => $rental->rental_PhoneNumber,
    ]);

    \Log::info('ToyyibPay raw response for rentalID ' . $rentalID, ['body' => $response->body(), 'status' => $response->status()]);

    $billData = $response->json();
    \Log::info('ToyyibPay response for rentalID ' . $rentalID, ['response' => $billData]);

    if (!isset($billData[0]['BillCode'])) {
        return redirect()->route('customer.rental.confirmation', ['rentalID' => $rentalID])
            ->with('error', 'Failed to create payment. Please try again.');
    }

    $billCode = $billData[0]['BillCode'];

    Payment::create([
        'paymentID'         => 'PAY' . uniqid(),
        'payer_Name'        => $rental->rental_Name,
        'payer_BankAccount' => null,
        'payment_Amount'    => $totalPrice,
        'payment_Status'    => 'pending',
        'rentalID'          => $rental->rentalID,
        'userID'            => $rental->userID,
    ]);

    // Redirect to dev ToyyibPay payment page
    return redirect(env('TOYYIBPAY_URL_DEV') . '/' . $billCode);
}

// Handle return (user-facing after rental payment)
public function rentalPaymentReturn(Request $request)
{
    $status   = $request->status_id ?? $request->status;
    $rentalID = $request->order_id ?? $request->billExternalReferenceNo ?? null;

    if (!$rentalID) {
        return redirect()->route('customer.rental.main')
            ->with('error', 'Rental payment status could not be verified.');
    }

    $payment = Payment::where('rentalID', $rentalID)->latest()->first();

    if (!$payment) {
        return redirect()->route('customer.rental.confirmation', ['rentalID' => $rentalID])
            ->with('error', 'Payment record not found.');
    }

    if ($status == 1) {
        $payment->update(['payment_Status' => 'paid']);
        Rental::where('rentalID', $rentalID)->update(['rental_Status' => 'paid']);

        return redirect()->route('customer.rental.main')
            ->with('success', 'Rental payment successful!');
    } else {
        $payment->update(['payment_Status' => 'failed']);
        return redirect()->route('customer.rental.confirmation', ['rentalID' => $rentalID])
            ->with('error', 'Your rental payment was cancelled or failed. Please try again.');
    }
}

// Handle callback (server-to-server confirmation)
public function rentalPaymentCallback(Request $request)
{
    $rentalID = $request->billExternalReferenceNo;
    $status   = $request->status;
    $refNo    = $request->refno ?? null;

    $payment = Payment::where('rentalID', $rentalID)->latest()->first();

    if ($payment) {
        if ($status == 1) {
            $payment->update([
                'payment_Status'    => 'paid',
                'payer_BankAccount' => $refNo
            ]);
            Rental::where('rentalID', $rentalID)->update(['rental_Status' => 'paid']);
        } else {
            $payment->update(['payment_Status' => 'failed']);
        }
    }

    return response()->json(['message' => 'Rental callback processed']);
}

/////////////////////////////////////////////////
//BALANCE PAYMENT PART
/////////////////////////////////////////////////
/**
     * Create a ToyyibPay bill for the 80% BALANCE payment.
     */
    public function createBalancePayment(Request $request, $bookingID)
    {
        $booking = Booking::with('slot', 'field')->where('bookingID', $bookingID)->firstOrFail();

        // 1. Calculate the 80% balance
        $balance = $booking->slot->slot_Price * 0.80; // ACTUAL FORMULA
        //$balance = 1.00; // FOR TESTING (RM 1.00)
        
        $amount = $balance * 100; // ToyyibPay uses cents

        // 2. Create the ToyyibPay bill
         $response = Http::asForm()->post(env('TOYYIBPAY_URL_DEV') . '/index.php/api/createBill', [
            'userSecretKey'         => env('TOYYIBPAY_SECRET_DEV'),
            'categoryCode'          => env('TOYYIBPAY_CATEGORY_RENTAL'),
            'billName'              => 'PadangPro Balance Payment',
            'billDescription'       => 'Balance payment for booking ' . $booking->bookingID,
            'billPriceSetting'      => 1,
            'billPayorInfo'         => 1,
            'billAmount'            => $amount,
            'billReturnUrl'         => route('payment.return.balance'),
            'billCallbackUrl'       => route('payment.callback.balance'),
            'billExternalReferenceNo' => $booking->bookingID,
            'billTo'                => $booking->booking_Name,
            'billEmail'             => $booking->booking_Email,
            'billPhone'             => $booking->booking_PhoneNumber,
        ]);

        $bill = $response->json()[0];

        // 3. Create a NEW payment record for this balance
        Payment::create([
            'paymentID'       => 'PAY' . uniqid(),
            'payer_Name'      => $booking->booking_Name,
            'payment_Amount'  => $balance,
            'payment_Status'  => 'pending_balance', // A new status for this type of payment
            'bookingID'       => $booking->bookingID,
            'userID'          => $booking->userID,
        ]);

        // 4. Redirect to payment page
        return redirect(env('TOYYIBPAY_URL_DEV') .'/' . $bill['BillCode']);
        

    }

    /**
     * Handle return (user-facing) for BALANCE payment
     */
    public function paymentReturnBalance(Request $request)
    {
        $status    = $request->status_id ?? $request->status;
        $bookingID = $request->order_id ?? $request->billExternalReferenceNo ?? null;
        $refNo     = $request->refno ?? null; // Get the transaction ref no

        if (!$bookingID) {
            return redirect()->route('booking.view')->with('error', 'Payment status could not be verified.');
        }

        if ($status == 1) {
            // --- THIS IS THE FIX ---
            // Manually update the database on return, because callback won't work on localhost
            
            // 1. Find the 'pending_balance' payment
            $payment = Payment::where('bookingID', $bookingID)
                              ->where('payment_Status', 'pending_balance')
                              ->latest()->first();

            if ($payment) {
                // 2. Update the Payment record
                $payment->update([
                    'payment_Status'    => 'paid_balance',
                    'payer_BankAccount' => $refNo
                ]);

                // 3. Update the Booking status to 'completed'
                Booking::where('bookingID', $bookingID)->update([
                    'booking_Status' => 'completed'
                ]);
            }
            // --- END FIX ---
            
            // Success!
            return redirect()->route('booking.view')
                       ->with('success', 'Balance payment successful! Your booking is now complete.');
        } else {
            // Failed or Cancelled
            // Find and update the payment status to 'failed' for your records
            $payment = Payment::where('bookingID', $bookingID)
                              ->where('payment_Status', 'pending_balance')
                              ->latest()->first();
            if ($payment) {
                 $payment->update(['payment_Status' => 'failed']);
            }
            
            return redirect()->route('booking.view')
                       ->with('error', 'Your balance payment was cancelled or failed. Please try again.');
        }
    }

    /**
     * Handle callback (server-to-server) for BALANCE payment
     */
    public function paymentCallbackBalance(Request $request)
    {
        $bookingID = $request->billExternalReferenceNo;
        $status    = $request->status; // 1=paid
        $refNo     = $request->refno ?? null;

        $payment = Payment::where('bookingID', $bookingID)
                          ->where('payment_Status', 'pending_balance')
                          ->latest()->first();

        if ($payment && $status == 1) {
            // 1. Update the Payment record
            $payment->update([
                'payment_Status'    => 'paid_balance',
                'payer_BankAccount' => $refNo
            ]);

            // 2. Update the Booking status to 'completed'
            Booking::where('bookingID', $bookingID)->update([
                'booking_Status' => 'completed'
            ]);
        }

        return response()->json(['message' => 'Callback processed']);
    }


    public function markAsCompleted(Request $request, $bookingID)
    {
        // Get user type from session
        $userType = session('user_type');
        
        // Ensure only staff or admin can do this
        if ($userType !== 'staff' && $userType !== 'administrator') {
            return redirect()->route('customer.dashboard')->with('error', 'Unauthorized action.');
        }

        $booking = Booking::with('slot')->findOrFail($bookingID);

        // Check if the booking is in the correct status to be completed
        if ($booking->booking_Status == 'paid') {
            
            // Update the booking status
            $booking->update(['booking_Status' => 'completed']);

            // Create a new payment record for this cash payment
            Payment::create([
                'paymentID'       => 'PAY' . uniqid(),
                'payer_Name'      => $booking->booking_Name,
                'payment_Amount'  => $booking->slot->slot_Price * 0.80, // Record the 80% balance
                'payment_Status'  => 'paid_balance (cash)', // Special status for cash
                'bookingID'       => $booking->bookingID,
                'userID'          => $booking->userID,
                'payer_BankAccount' => 'CASH_AT_COUNTER' // Record how it was paid
            ]);
            
            $redirectRoute = ($userType === 'staff') ? 'staff.booking.viewAll' : 'admin.booking.viewAll';

            return redirect()->route($redirectRoute)
                             ->with('success', 'Booking ' . $bookingID . ' has been marked as completed.');

        } elseif ($booking->booking_Status == 'completed') {
             $redirectRoute = ($userType === 'staff') ? 'staff.booking.viewAll' : 'admin.booking.viewAll';
             return redirect()->route($redirectRoute)
                             ->with('error', 'This booking is already completed.');
        } else {
             $redirectRoute = ($userType === 'staff') ? 'staff.booking.viewAll' : 'admin.booking.viewAll';
             return redirect()->route($redirectRoute)
                             ->with('error', 'This booking is not in a valid state to be completed.');
        }
    }


    // =================================================================
    // RENTAL: BALANCE PAYMENT (Post-Return Approval)
    // =================================================================

    public function createRentalBalancePayment(Request $request, $rentalID)
    {
        $rental = Rental::with('item')->where('rentalID', $rentalID)->firstOrFail();
        
       
        $days = \Carbon\Carbon::parse($rental->rental_StartDate)
                    ->diffInDays(\Carbon\Carbon::parse($rental->rental_EndDate)) + 1;
        
        $totalCost = $days * $rental->quantity * $rental->item->item_Price;
        
        // Calculate 80% balance
        $balance = $totalCost * 0.80; 
       
        if ($balance < 1) {
             return redirect()->back()->with('error', 'Balance amount is too small to process online.');
        }

        $amount = intval($balance * 100); // Convert to cents

        // ... (The rest of your ToyyibPay code remains exactly the same) ...
        $response = Http::asForm()->post(env('TOYYIBPAY_URL_DEV') . '/index.php/api/createBill', [
            'userSecretKey'         => env('TOYYIBPAY_SECRET_DEV'),
            'categoryCode'          => env('TOYYIBPAY_CATEGORY_RENTAL'),
            'billName'              => 'PadangPro Rental Balance',
            'billDescription'       => 'Balance payment for rental ' . $rental->rentalID,
            'billPriceSetting'      => 1,
            'billPayorInfo'         => 1,
            'billAmount'            => $amount,
            'billReturnUrl'         => route('payment.rental.balance.return'), 
            'billCallbackUrl'       => route('payment.rental.balance.callback'),
            'billExternalReferenceNo' => $rental->rentalID,
            'billTo'                => $rental->rental_Name,
            'billEmail'             => $rental->rental_Email,
            'billPhone'             => $rental->rental_PhoneNumber,
        ]);

        if (!$response->successful() || !isset($response->json()[0]['BillCode'])) {
            \Log::error('ToyyibPay Error:', ['response' => $response->body()]);
            return redirect()->back()->with('error', 'Could not initiate balance payment. Please try again.');
        }

        $bill = $response->json()[0];

        Payment::create([
            'paymentID'       => 'PAY' . uniqid(),
            'payer_Name'      => $rental->rental_Name,
            'payment_Amount'  => $balance,
            'payment_Status'  => 'pending_balance', 
            'rentalID'        => $rental->rentalID,
            'userID'          => $rental->userID,
        ]);

        return redirect(env('TOYYIBPAY_URL_DEV') . '/' . $bill['BillCode']);
    }

    /**
     * 2. Handle User Return (Update DB here for localhost testing)
     */
    public function rentalBalanceReturn(Request $request)
    {
        $status   = $request->status_id ?? $request->status;
        $rentalID = $request->order_id ?? $request->billExternalReferenceNo;
        $refNo    = $request->refno ?? null;

        if (!$rentalID) {
            return redirect()->route('customer.rental.history')->with('error', 'Payment status unknown.');
        }

        if ($status == 1) {
            // Find the pending balance payment
            $payment = Payment::where('rentalID', $rentalID)
                              ->where('payment_Status', 'pending_balance')
                              ->latest()->first();

            if ($payment) {
                // Update Payment Status
                $payment->update([
                    'payment_Status'    => 'paid_balance',
                    'payer_BankAccount' => $refNo
                ]);

                // Update Rental Status to COMPLETED
                Rental::where('rentalID', $rentalID)->update([
                    'rental_Status' => 'completed'
                ]);
            }

            return redirect()->route('customer.rental.history')
                             ->with('success', 'Balance paid successfully! Rental completed.');
        } else {
            // Handle failure
            $payment = Payment::where('rentalID', $rentalID)
                              ->where('payment_Status', 'pending_balance')
                              ->latest()->first();
            
            if ($payment) {
                $payment->update(['payment_Status' => 'failed']);
            }

            return redirect()->route('customer.rental.history')
                             ->with('error', 'Balance payment failed or was cancelled.');
        }
    }

    /**
     * 3. Handle Server Callback (For production reliability)
     */
    public function rentalBalanceCallback(Request $request)
    {
        $rentalID = $request->billExternalReferenceNo;
        $status   = $request->status;
        $refNo    = $request->refno ?? null;

        $payment = Payment::where('rentalID', $rentalID)
                          ->where('payment_Status', 'pending_balance')
                          ->latest()->first();

        if ($payment && $status == 1) {
            $payment->update([
                'payment_Status'    => 'paid_balance',
                'payer_BankAccount' => $refNo
            ]);
            
            Rental::where('rentalID', $rentalID)->update([
                'rental_Status' => 'completed'
            ]);
        }

        return response()->json(['status' => 'OK']);
    }


/**
     * Allows Staff/Admin to manually mark a RENTAL as 'completed'
     * (e.g., when a customer pays the rental balance in cash).
     */
    public function markRentalAsCompleted(Request $request, $rentalID)
    {
        // Get user type from session
        $userType = session('user_type');
        
        // Ensure only staff or admin can do this
        if ($userType !== 'staff' && $userType !== 'administrator') {
            return redirect()->route('customer.dashboard')->with('error', 'Unauthorized action.');
        }

        $rental = Rental::with('item')->findOrFail($rentalID);

        // Check if the rental is in the correct status to be completed
        if ($rental->rental_Status == 'paid') {
            
            // 1. Calculate the 80% Balance (since it's not stored in DB)
            $days = \Carbon\Carbon::parse($rental->rental_StartDate)
                ->diffInDays(\Carbon\Carbon::parse($rental->rental_EndDate)) + 1;
            
            $totalCost = $days * $rental->quantity * $rental->item->item_Price;
            $balanceAmount = $totalCost * 0.80;

            // 2. Update the rental status
            $rental->update(['rental_Status' => 'completed']);

            // 3. Create a new payment record for this cash payment
            Payment::create([
                'paymentID'       => 'PAY' . uniqid(),
                'payer_Name'      => $rental->rental_Name,
                'payment_Amount'  => $balanceAmount,
                'payment_Status'  => 'paid_balance (cash)', // Special status for cash
                'rentalID'        => $rental->rentalID,
                'userID'          => $rental->userID,
                'payer_BankAccount' => 'CASH_AT_COUNTER' // Record how it was paid
            ]);
            
            // Redirect back to the previous page (works for both Admin and Staff views)
            return redirect()->back()->with('success', 'Rental ' . $rentalID . ' has been marked as completed.');

        } elseif ($rental->rental_Status == 'completed') {
             return redirect()->back()->with('error', 'This rental is already completed.');
        } else {
             return redirect()->back()->with('error', 'This rental is not in a valid state to be completed.');
        }
    }

        public function recordDeposit($bookingID, $depositAmount, $method = 'cash')
        {
            try {
                Payment::create([
                    'bookingID' => $bookingID,
                    'amount_paid' => $depositAmount,
                    'payment_type' => 'deposit',
                    'payment_method' => $method,
                    'payment_status' => 'paid',
                ]);
        
                return true;
            } catch (\Exception $e) {
                \Log::error('Failed to record deposit: ' . $e->getMessage());
                return false;
            }
        }


}
