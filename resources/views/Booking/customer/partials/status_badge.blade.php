{{-- resources/views/booking/customer/partials/status_badge.blade.php --}}

@if($booking->display_Status === 'Awaiting Balance')
    <a href="{{ route('payment.balance.create', $booking->bookingID) }}" 
       class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-green-600 text-white hover:bg-green-700 transition shadow-sm">
        <i class="bi bi-credit-card-fill mr-1.5"></i> Pay Balance
    </a>
@elseif($booking->display_Status === 'Paid (Deposit)')
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
        <i class="bi bi-calendar-check mr-1.5"></i> Deposit Paid
    </span>
@elseif($booking->display_Status === 'Expired')
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
        <i class="bi bi-clock-history mr-1.5"></i> Expired
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
        {{ $booking->display_Status }}
    </span>
@endif