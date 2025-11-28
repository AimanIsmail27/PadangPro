{{-- resources/views/rental/customer/partials/history_status_badge.blade.php --}}

@if(strtolower($rental->rental_Status) == 'completed')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
        <i class="bi bi-check-all mr-1.5"></i> Completed
    </span>

@elseif(strtolower($rental->return_Status) == 'rejected')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
        <i class="bi bi-x-circle-fill mr-1.5"></i> Rejected
    </span>

@elseif(strtolower($rental->rental_Status) == 'failed')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
        <i class="bi bi-exclamation-circle-fill mr-1.5"></i> Failed Payment
    </span>
@else
    <span class="inline-flex items-center text-gray-400 text-xs italic font-medium">
        - Error -
    </span>
@endif