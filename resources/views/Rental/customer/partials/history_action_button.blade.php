{{-- resources/views/rental/customer/partials/history_action_button.blade.php --}}

@php
    $isFullyCompleted = strtolower($rental->rental_Status) == 'completed';
    $isReturnApproved = strtolower($rental->return_Status) == 'approved';
    
    // Check if user has already rated this specific rental
    $hasRated = $isFullyCompleted && $isReturnApproved && \App\Models\Rating::where('rentalID', $rental->rentalID)->exists();
@endphp

@if($isReturnApproved && !$isFullyCompleted)
    {{-- Awaiting Balance Payment --}}
    <a href="{{ route('payment.rental.balance.create', $rental->rentalID) }}" 
       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-green-600 text-white hover:bg-green-700 transition shadow-sm">
        <i class="bi bi-credit-card-fill mr-1.5"></i> Pay Balance
    </a>

@elseif($isFullyCompleted && $isReturnApproved && !$hasRated)
    {{-- Completed and ready to rate --}}
    <a href="{{ route('customer.rating.rental', $rental->rentalID) }}" 
       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-yellow-400 text-yellow-900 hover:bg-yellow-500 transition shadow-sm">
        <i class="bi bi-star-fill mr-1"></i> Rate Now
    </a>

@elseif($isFullyCompleted && $hasRated)
    {{-- Completed and already rated --}}
    <span class="text-gray-400 text-xs italic font-medium">
        <i class="bi bi-check2-all mr-1"></i> Rated
    </span>

@else
    {{-- Rejected or Failed (No further action) --}}
    <span class="text-gray-300 text-xs">-</span>
@endif