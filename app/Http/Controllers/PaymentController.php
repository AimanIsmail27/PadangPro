<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Payment;
use App\Models\Booking;

class PaymentController extends Controller
{
    /**
     * Create a ToyyibPay bill and redirect user to payment page
     */
    public function createPayment(Request $request, $bookingID)
    {
        $booking = Booking::with('slot', 'field')->where('bookingID', $bookingID)->firstOrFail();

        // Example: Payment amount from slot price
        // Only 20% deposit required
        // $deposit = $booking->slot->slot_Price * 0.20; // ACTUAL FORMULA
        $deposit = 1; // FOR TESTING PURPOSES
        $amount = $deposit * 100; // ToyyibPay uses cents

        // ToyyibPay API request
        $response = Http::asForm()->post('https://toyyibpay.com/index.php/api/createBill', [
            'userSecretKey'            => env('TOYYIBPAY_SECRET'),
            'categoryCode'             => env('TOYYIBPAY_CATEGORY'),
            'billName'                 => 'PadangPro Booking',
            'billDescription'          => 'Payment for booking ' . $booking->bookingID,
            'billPriceSetting'         => 1,
            'billPayorInfo'            => 1,
            'billAmount'               => $amount,
            'billReturnUrl'            => route('payment.return'),
            'billCallbackUrl'          => route('payment.callback'),
            'billExternalReferenceNo'  => $booking->bookingID,
            'billTo'                   => $booking->booking_Name,
            'billEmail'                => $booking->booking_Email,
            'billPhone'                => $booking->booking_PhoneNumber,
        ]);

        $bill = $response->json()[0];

        // Save into payment table
        Payment::create([
            'paymentID'         => 'PAY' . uniqid(),
            'payer_Name'        => $booking->booking_Name,
            'payer_BankAccount' => null, // will be updated later in callback with refno
            'payment_Amount'    => $deposit,
            'payment_Status'    => 'pending',
            'bookingID'         => $booking->bookingID,
            'userID'            => $booking->userID,
        ]);

        // Redirect user to ToyyibPay payment page
        return redirect('https://toyyibpay.com/' . $bill['BillCode']);
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
                        ->with('success', 'Payment successful!');
                } else {
                    $payment->update(['payment_Status' => 'failed']);
                    return redirect()->route('booking.confirmation', ['bookingID' => $bookingID])
                        ->with('error', 'Your payment was cancelled or failed. You can try again.');
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
}
