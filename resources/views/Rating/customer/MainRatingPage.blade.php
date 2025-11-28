@extends('layout.customer')

@section('title', 'My Reviews')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

{{-- HEADER: Standard Indigo-Slate Gradient --}}
<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">My Reviews</h1>
    <p class="mt-2 text-indigo-100">Manage the feedback you've shared with the community.</p>
</div>

{{-- CONTENT CONTAINER: Light Slate Background to make white cards pop --}}
<div class="bg-slate-50 rounded-xl shadow-xl border border-slate-200 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative min-h-[500px]">

    @if($myReviews->isEmpty())
        {{-- EMPTY STATE --}}
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="bg-white p-8 rounded-full mb-6 shadow-sm">
                <i class="bi bi-chat-square-heart text-6xl text-slate-300"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-700">No Reviews Yet</h3>
            <p class="text-slate-500 mt-2 max-w-md mx-auto text-lg">Your voice matters! Book a field or rent some gear, then come back to share your experience.</p>
            
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ route('booking.view') }}" class="px-8 py-3 rounded-xl bg-white border border-indigo-200 text-indigo-700 font-bold shadow-sm hover:shadow-md hover:border-indigo-400 transition">
                    <i class="bi bi-calendar-event mr-2"></i> Review a Booking
                </a>
                <a href="{{ route('customer.rental.history') }}" class="px-8 py-3 rounded-xl bg-white border border-purple-200 text-purple-700 font-bold shadow-sm hover:shadow-md hover:border-purple-400 transition">
                    <i class="bi bi-box-seam mr-2"></i> Review a Rental
                </a>
            </div>
        </div>
    @else
        {{-- LIST CONTAINER --}}
        <div class="flex flex-col gap-6">
            @foreach($myReviews as $review)
                {{-- CHEERFUL CARD START --}}
                <div class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden relative
                            {{ $review->booking ? 'border-t-4 border-t-blue-500' : 'border-t-4 border-t-purple-500' }}">
                    
                    {{-- Context Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white">
                        <div class="flex items-center gap-4">
                            
                            {{-- Lively Icons --}}
                            @if($review->booking)
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner">
                                    <i class="bi bi-calendar-check-fill text-2xl"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-extrabold text-blue-600 uppercase tracking-wider mb-0.5">Field Booking</span>
                                    <h3 class="text-lg font-bold text-slate-800 leading-tight">
                                        {{ $review->booking->field->field_Label ?? 'Unknown Field' }}
                                    </h3>
                                </div>
                            @elseif($review->rental)
                                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-inner">
                                    <i class="bi bi-box-seam-fill text-2xl"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-extrabold text-purple-600 uppercase tracking-wider mb-0.5">Equipment Rental</span>
                                    <h3 class="text-lg font-bold text-slate-800 leading-tight">
                                        {{ $review->rental->item->item_Name ?? 'Unknown Item' }}
                                    </h3>
                                </div>
                            @endif
                        </div>

                        <div class="text-right hidden md:block">
                            <span class="text-xs text-slate-400 font-medium block mb-1">
                                Posted on {{ \Carbon\Carbon::parse($review->review_Date)->format('d M Y') }}
                            </span>
                            {{-- Rating Badge --}}
                            <div class="inline-flex items-center bg-yellow-50 px-3 py-1.5 rounded-lg border border-yellow-100">
                                <span class="text-base font-black text-yellow-600 mr-2">{{ $review->rating_Score }}.0</span>
                                <div class="flex text-yellow-400 text-xs gap-0.5">
                                    @for($i=1; $i<=5; $i++)
                                        @if($i <= $review->rating_Score) <i class="bi bi-star-fill"></i>
                                        @else <i class="bi bi-star text-yellow-200"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-b from-white to-slate-50/30">
                        
                        {{-- Mobile Date/Rating (Visible only on small screens) --}}
                        <div class="md:hidden flex justify-between items-center mb-2">
                            <div class="flex text-yellow-400 text-sm">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $review->rating_Score ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($review->review_Date)->format('d M Y') }}</span>
                        </div>

                        <div class="relative pl-6 flex-grow">
                            <div class="absolute left-0 top-0 bottom-0 w-1 rounded-full {{ $review->booking ? 'bg-blue-200' : 'bg-purple-200' }}"></div>
                            <i class="bi bi-quote absolute -top-2 left-4 text-gray-200 text-2xl"></i>
                            <p class="text-slate-600 text-base italic leading-relaxed pt-1 pl-2">
                                "{{ $review->review_Given }}"
                            </p>
                        </div>

                        {{-- Footer Actions (Always Visible) --}}
                        <div class="flex items-center gap-3 self-end md:self-center flex-shrink-0 mt-4 md:mt-0">
                            <a href="{{ route('customer.rating.edit', $review->ratingID) }}" 
                               class="text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 border border-transparent hover:border-indigo-100">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button data-href="{{ route('customer.rating.delete', $review->ratingID) }}" 
                               class="text-slate-500 hover:text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 border border-transparent hover:border-red-100 delete-review-btn">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                {{-- CARD END --}}
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(method_exists($myReviews, 'links'))
            <div class="mt-10 flex justify-center">
                {{ $myReviews->links() }}
            </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    @endif

    // Delete Confirmation Logic
    document.querySelectorAll('.delete-review-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-href');

            Swal.fire({
                title: 'Delete Review?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
@endpush