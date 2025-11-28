{{-- resources/views/booking/customer/partials/rating_button.blade.php --}}

@php
    // We check here if the rating exists so it works for both Mobile and Desktop views
    $hasRated = \App\Models\Rating::where('bookingID', $booking->bookingID)->exists();
@endphp

@if(!$hasRated)
    <a href="{{ route('customer.rating.booking', $booking->bookingID) }}" 
       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-yellow-400 text-yellow-900 hover:bg-yellow-500 transition shadow-sm transform active:scale-95">
        <i class="bi bi-star-fill mr-1"></i> Rate Now
    </a>
@else
    <span class="inline-flex items-center text-gray-400 text-xs italic font-medium">
        <i class="bi bi-check2-all mr-1"></i> Rated
    </span>
@endif