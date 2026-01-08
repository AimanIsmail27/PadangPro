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

// ✅ NEW (email receipt)
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReceiptMail;

class PaymentController extends Controller
{
    /**
     * ✅ Helper: send receipt email (won’t break payment flow if email fails)
     */
    private function sendReceiptEmail(?string $toEmail, array $data): void
    {
        if (!$toEmail) return;

        try {
            Mail::to($toEmail)->send(new PaymentReceiptMail($data));
        } catch (\Exception $e) {
            Log::error('Receipt email failed: ' . $e->getMessage(), [
                'to' => $toEmail,
                'data' => $data,
            ]);
        }
    }

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
        $response = Http::asForm()->post(env('TOYYIBPAY_URL_DEV') . '/index.php/api/createBill', [
            'userSecretKey'         => env('TOYYIBPAY_SECRET_DEV'),
            'categoryCode'          => env('TOYYIBPAY_CATEGORY_RENTAL'),
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

        if (!$response->successful() || !isset($response->json()[0]['BillCode'])) {
            Log::error('ToyyibPay (DEV) initial deposit creation failed.', ['response' => $response->body()]);
            return redirect()->route('booking.confirmation', ['bookingID' => $bookingID])
                             ->with('error', 'Could not create payment. Please try again later.');
        }

        $bill = $response->json()[0];

        Payment::create([
            'paymentID'       => 'PAY' . uniqid(),
            'payer_Name'      => $booking->booking_Name,
            'payer_BankAccount' => null,
            'payment_Amount'  => $deposit,
            'payment_Status'  => 'pending',
            'bookingID'       => $booking->bookingID,
            'userID'          => $booking->userID,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect(env('TOYYIBPAY_URL_DEV') . '/' . $bill['BillCode']);
    }

    /**
     * Handle return (user-facing after payment)
     */
    public function paymentReturn(Request $request)
    {
        $status    = $request->status_id ?? $request->status;
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
        $refNo     = $request->refno ?? null;

        $payment = Payment::where('bookingID', $bookingID)->latest()->first();

        if ($payment) {
            if ($status == 1) {

                // ✅ prevent duplicate email if callback hits multiple times
                if ($payment->payment_Status !== 'paid') {
                    $payment->update([
                        'payment_Status'    => 'paid',
                        'payer_BankAccount' => $refNo
                    ]);

                    Booking::where('bookingID', $bookingID)->update([
                        'booking_Status' => 'paid'
                    ]);

                    // ✅ send receipt (deposit)
                    $booking = Booking::with('slot', 'field')->where('bookingID', $bookingID)->first();
                    $this->sendReceiptEmail($booking?->booking_Email, [
                        'subject' => 'PadangPro Receipt - Booking Deposit',
                        'name'    => $booking?->booking_Name,
                        'type'    => 'Booking Deposit (20%)',
                        'ref'     => $refNo ?? $bookingID,
                        'amount'  => $payment->payment_Amount,
                        'date'    => now()->format('Y-m-d H:i'),
                        'details' => [
                            'Booking ID' => $bookingID,
                            'Field'      => $booking?->field?->field_Name ?? '-',
                            'Slot Date'  => $booking?->slot?->slot_Date ?? '-',
                            'Slot Time'  => $booking?->slot?->slot_Time ?? '-',
                        ],
                    ]);
                }

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

    public function createRentalPayment(Request $request, $rentalID)
    {
        $rental = Rental::with('item')->where('rentalID', $rentalID)->firstOrFail();

        $totalPrice = $request->input('total_amount');
        \Log::info('POST received for rentalID: ' . $rentalID, ['request' => $request->all()]);

        if (!$totalPrice || $totalPrice <= 0) {
            return redirect()->route('customer.rental.confirmation', ['rentalID' => $rentalID])
                ->with('error', 'Invalid total amount for payment.');
        }

        $amount = intval($totalPrice * 100);

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
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return redirect(env('TOYYIBPAY_URL_DEV') . '/' . $billCode);
    }

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

    public function rentalPaymentCallback(Request $request)
    {
        $rentalID = $request->billExternalReferenceNo;
        $status   = $request->status;
        $refNo    = $request->refno ?? null;

        $payment = Payment::where('rentalID', $rentalID)->latest()->first();

        if ($payment) {
            if ($status == 1) {

                // ✅ prevent duplicate email
                if ($payment->payment_Status !== 'paid') {
                    $payment->update([
                        'payment_Status'    => 'paid',
                        'payer_BankAccount' => $refNo
                    ]);

                    Rental::where('rentalID', $rentalID)->update(['rental_Status' => 'paid']);

                    $rental = Rental::with('item')->where('rentalID', $rentalID)->first();
                    $this->sendReceiptEmail($rental?->rental_Email, [
                        'subject' => 'PadangPro Receipt - Rental Payment',
                        'name'    => $rental?->rental_Name,
                        'type'    => 'Rental Payment',
                        'ref'     => $refNo ?? $rentalID,
                        'amount'  => $payment->payment_Amount,
                        'date'    => now()->format('Y-m-d H:i'),
                        'details' => [
                            'Rental ID'  => $rentalID,
                            'Item'       => $rental?->item?->item_Name ?? '-',
                            'Quantity'   => $rental?->quantity ?? '-',
                            'Start Date' => $rental?->rental_StartDate ?? '-',
                            'End Date'   => $rental?->rental_EndDate ?? '-',
                        ],
                    ]);
                }

            } else {
                $payment->update(['payment_Status' => 'failed']);
            }
        }

        return response()->json(['message' => 'Rental callback processed']);
    }

    /////////////////////////////////////////////////////////////////
    // BALANCE PAYMENT PART (BOOKING)
    /////////////////////////////////////////////////////////////////

    public function createBalancePayment(Request $request, $bookingID)
    {
        $booking = Booking::with('slot', 'field')->where('bookingID', $bookingID)->firstOrFail();

        $balance = $booking->slot->slot_Price * 0.80;
        //$balance = 1.00;

        $amount = $balance * 100;

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

        Payment::create([
            'paymentID'       => 'PAY' . uniqid(),
            'payer_Name'      => $booking->booking_Name,
            'payment_Amount'  => $balance,
            'payment_Status'  => 'pending_balance',
            'bookingID'       => $booking->bookingID,
            'userID'          => $booking->userID,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect(env('TOYYIBPAY_URL_DEV') .'/' . $bill['BillCode']);
    }

    public function paymentReturnBalance(Request $request)
    {
        $status    = $request->status_id ?? $request->status;
        $bookingID = $request->order_id ?? $request->billExternalReferenceNo ?? null;
        $refNo     = $request->refno ?? null;

        if (!$bookingID) {
            return redirect()->route('booking.view')->with('error', 'Payment status could not be verified.');
        }

        if ($status == 1) {
            $payment = Payment::where('bookingID', $bookingID)
                              ->where('payment_Status', 'pending_balance')
                              ->latest()->first();

            if ($payment) {
                // ✅ prevent duplicate send if return page refreshed
                if ($payment->payment_Status !== 'paid_balance') {
                    $payment->update([
                        'payment_Status'    => 'paid_balance',
                        'payer_BankAccount' => $refNo
                    ]);

                    Booking::where('bookingID', $bookingID)->update([
                        'booking_Status' => 'completed'
                    ]);

                    $booking = Booking::with('slot', 'field')->where('bookingID', $bookingID)->first();
                    $this->sendReceiptEmail($booking?->booking_Email, [
                        'subject' => 'PadangPro Receipt - Booking Balance',
                        'name'    => $booking?->booking_Name,
                        'type'    => 'Booking Balance (80%)',
                        'ref'     => $refNo ?? $bookingID,
                        'amount'  => $payment->payment_Amount,
                        'date'    => now()->format('Y-m-d H:i'),
                        'details' => [
                            'Booking ID'     => $bookingID,
                            'Field'          => $booking?->field?->field_Name ?? '-',
                            'Slot Date'      => $booking?->slot?->slot_Date ?? '-',
                            'Slot Time'      => $booking?->slot?->slot_Time ?? '-',
                            'Booking Status' => 'completed',
                        ],
                    ]);
                }
            }

            return redirect()->route('booking.view')
                       ->with('success', 'Balance payment successful! Your booking is now complete.');
        } else {
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

    public function paymentCallbackBalance(Request $request)
    {
        $bookingID = $request->billExternalReferenceNo;
        $status    = $request->status;
        $refNo     = $request->refno ?? null;

        $payment = Payment::where('bookingID', $bookingID)
                          ->where('payment_Status', 'pending_balance')
                          ->latest()->first();

        if ($payment && $status == 1) {

            // ✅ prevent duplicate email
            if ($payment->payment_Status !== 'paid_balance') {
                $payment->update([
                    'payment_Status'    => 'paid_balance',
                    'payer_BankAccount' => $refNo
                ]);

                Booking::where('bookingID', $bookingID)->update([
                    'booking_Status' => 'completed'
                ]);

                $booking = Booking::with('slot', 'field')->where('bookingID', $bookingID)->first();
                $this->sendReceiptEmail($booking?->booking_Email, [
                    'subject' => 'PadangPro Receipt - Booking Balance',
                    'name'    => $booking?->booking_Name,
                    'type'    => 'Booking Balance (80%)',
                    'ref'     => $refNo ?? $bookingID,
                    'amount'  => $payment->payment_Amount,
                    'date'    => now()->format('Y-m-d H:i'),
                    'details' => [
                        'Booking ID'     => $bookingID,
                        'Field'          => $booking?->field?->field_Name ?? '-',
                        'Slot Date'      => $booking?->slot?->slot_Date ?? '-',
                        'Slot Time'      => $booking?->slot?->slot_Time ?? '-',
                        'Booking Status' => 'completed',
                    ],
                ]);
            }
        }

        return response()->json(['message' => 'Callback processed']);
    }

    public function markAsCompleted(Request $request, $bookingID)
    {
        $userType = session('user_type');

        if ($userType !== 'staff' && $userType !== 'administrator') {
            return redirect()->route('customer.dashboard')->with('error', 'Unauthorized action.');
        }

        $booking = Booking::with('slot', 'field')->findOrFail($bookingID);

        if ($booking->booking_Status == 'paid') {

            $booking->update(['booking_Status' => 'completed']);

            $cashPayment = Payment::create([
                'paymentID'       => 'PAY' . uniqid(),
                'payer_Name'      => $booking->booking_Name,
                'payment_Amount'  => $booking->slot->slot_Price * 0.80,
                'payment_Status'  => 'paid_balance (cash)',
                'bookingID'       => $booking->bookingID,
                'userID'          => $booking->userID,
                'payer_BankAccount' => 'CASH_AT_COUNTER',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // ✅ cash receipt
            $this->sendReceiptEmail($booking?->booking_Email, [
                'subject' => 'PadangPro Receipt - Booking Balance (Cash)',
                'name'    => $booking?->booking_Name,
                'type'    => 'Booking Balance (Cash)',
                'ref'     => 'CASH_AT_COUNTER',
                'amount'  => $cashPayment->payment_Amount,
                'date'    => now()->format('Y-m-d H:i'),
                'details' => [
                    'Booking ID'     => $bookingID,
                    'Field'          => $booking?->field?->field_Name ?? '-',
                    'Booking Status' => 'completed',
                ],
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

        $balance = $totalCost * 0.80;

        if ($balance < 1) {
             return redirect()->back()->with('error', 'Balance amount is too small to process online.');
        }

        $amount = intval($balance * 100);

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
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect(env('TOYYIBPAY_URL_DEV') . '/' . $bill['BillCode']);
    }

    public function rentalBalanceReturn(Request $request)
    {
        $status   = $request->status_id ?? $request->status;
        $rentalID = $request->order_id ?? $request->billExternalReferenceNo;
        $refNo    = $request->refno ?? null;

        if (!$rentalID) {
            return redirect()->route('customer.rental.history')->with('error', 'Payment status unknown.');
        }

        if ($status == 1) {
            $payment = Payment::where('rentalID', $rentalID)
                              ->where('payment_Status', 'pending_balance')
                              ->latest()->first();

            if ($payment) {
                // ✅ prevent duplicate send
                if ($payment->payment_Status !== 'paid_balance') {
                    $payment->update([
                        'payment_Status'    => 'paid_balance',
                        'payer_BankAccount' => $refNo
                    ]);

                    Rental::where('rentalID', $rentalID)->update([
                        'rental_Status' => 'completed'
                    ]);

                    $rental = Rental::with('item')->where('rentalID', $rentalID)->first();
                    $this->sendReceiptEmail($rental?->rental_Email, [
                        'subject' => 'PadangPro Receipt - Rental Balance',
                        'name'    => $rental?->rental_Name,
                        'type'    => 'Rental Balance (80%)',
                        'ref'     => $refNo ?? $rentalID,
                        'amount'  => $payment->payment_Amount,
                        'date'    => now()->format('Y-m-d H:i'),
                        'details' => [
                            'Rental ID'     => $rentalID,
                            'Item'          => $rental?->item?->item_Name ?? '-',
                            'Quantity'      => $rental?->quantity ?? '-',
                            'Start Date'    => $rental?->rental_StartDate ?? '-',
                            'End Date'      => $rental?->rental_EndDate ?? '-',
                            'Rental Status' => 'completed',
                        ],
                    ]);
                }
            }

            return redirect()->route('customer.rental.history')
                             ->with('success', 'Balance paid successfully! Rental completed.');
        } else {
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

    public function rentalBalanceCallback(Request $request)
    {
        $rentalID = $request->billExternalReferenceNo;
        $status   = $request->status;
        $refNo    = $request->refno ?? null;

        $payment = Payment::where('rentalID', $rentalID)
                          ->where('payment_Status', 'pending_balance')
                          ->latest()->first();

        if ($payment && $status == 1) {

            // ✅ prevent duplicate email
            if ($payment->payment_Status !== 'paid_balance') {
                $payment->update([
                    'payment_Status'    => 'paid_balance',
                    'payer_BankAccount' => $refNo
                ]);

                Rental::where('rentalID', $rentalID)->update([
                    'rental_Status' => 'completed'
                ]);

                $rental = Rental::with('item')->where('rentalID', $rentalID)->first();
                $this->sendReceiptEmail($rental?->rental_Email, [
                    'subject' => 'PadangPro Receipt - Rental Balance',
                    'name'    => $rental?->rental_Name,
                    'type'    => 'Rental Balance (80%)',
                    'ref'     => $refNo ?? $rentalID,
                    'amount'  => $payment->payment_Amount,
                    'date'    => now()->format('Y-m-d H:i'),
                    'details' => [
                        'Rental ID'     => $rentalID,
                        'Item'          => $rental?->item?->item_Name ?? '-',
                        'Quantity'      => $rental?->quantity ?? '-',
                        'Start Date'    => $rental?->rental_StartDate ?? '-',
                        'End Date'      => $rental?->rental_EndDate ?? '-',
                        'Rental Status' => 'completed',
                    ],
                ]);
            }
        }

        return response()->json(['status' => 'OK']);
    }

    public function markRentalAsCompleted(Request $request, $rentalID)
    {
        $userType = session('user_type');

        if ($userType !== 'staff' && $userType !== 'administrator') {
            return redirect()->route('customer.dashboard')->with('error', 'Unauthorized action.');
        }

        $rental = Rental::with('item')->findOrFail($rentalID);

        if ($rental->rental_Status == 'paid') {

            $days = \Carbon\Carbon::parse($rental->rental_StartDate)
                ->diffInDays(\Carbon\Carbon::parse($rental->rental_EndDate)) + 1;

            $totalCost = $days * $rental->quantity * $rental->item->item_Price;
            $balanceAmount = $totalCost * 0.80;

            $rental->update(['rental_Status' => 'completed']);

            $cashPayment = Payment::create([
                'paymentID'       => 'PAY' . uniqid(),
                'payer_Name'      => $rental->rental_Name,
                'payment_Amount'  => $balanceAmount,
                'payment_Status'  => 'paid_balance (cash)',
                'rentalID'        => $rental->rentalID,
                'userID'          => $rental->userID,
                'payer_BankAccount' => 'CASH_AT_COUNTER',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // ✅ cash receipt
            $this->sendReceiptEmail($rental?->rental_Email, [
                'subject' => 'PadangPro Receipt - Rental Balance (Cash)',
                'name'    => $rental?->rental_Name,
                'type'    => 'Rental Balance (Cash)',
                'ref'     => 'CASH_AT_COUNTER',
                'amount'  => $cashPayment->payment_Amount,
                'date'    => now()->format('Y-m-d H:i'),
                'details' => [
                    'Rental ID'     => $rentalID,
                    'Item'          => $rental?->item?->item_Name ?? '-',
                    'Quantity'      => $rental?->quantity ?? '-',
                    'Rental Status' => 'completed',
                ],
            ]);

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
            $booking = \App\Models\Booking::where('bookingID', $bookingID)->first();

            if (!$booking) {
                \Log::error("Booking not found for deposit: " . $bookingID);
                return false;
            }

            $payment = Payment::create([
                'paymentID'        => 'PAY' . uniqid(),
                'payer_Name'       => $booking->booking_Name,
                'payment_Amount'   => $depositAmount,
                'payment_Status'   => 'paid',
                'bookingID'        => $booking->bookingID,
                'userID'           => $booking->userID,
                'payer_BankAccount'=> strtoupper($method) === 'CASH' ? 'CASH_AT_COUNTER' : null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // ✅ optional: send cash deposit receipt too
            $this->sendReceiptEmail($booking->booking_Email ?? null, [
                'subject' => 'PadangPro Receipt - Booking Deposit (Cash)',
                'name'    => $booking->booking_Name ?? 'Customer',
                'type'    => 'Booking Deposit (Cash)',
                'ref'     => strtoupper($method) === 'CASH' ? 'CASH_AT_COUNTER' : '-',
                'amount'  => $payment->payment_Amount,
                'date'    => now()->format('Y-m-d H:i'),
                'details' => [
                    'Booking ID' => $bookingID,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to record deposit: ' . $e->getMessage());
            return false;
        }
    }
}
