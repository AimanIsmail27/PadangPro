@extends('layout.customer')

@section('title', 'Rental Items')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
    <div class="text-white font-bold text-2xl px-8">
        ITEM FOR RENTAL
    </div>
</div>

{{-- FIX: Responsive Action Bar --}}
<div class="flex flex-col md:flex-row items-stretch md:items-center justify-between mb-8 gap-3 md:gap-6">
    
    {{-- 1. Availability Form (Wider on Mobile, using flex-grow) --}}
    <form action="{{ route('customer.rental.main') }}" method="GET" class="flex items-center w-full md:w-auto space-x-2">
        <input type="date" name="rental_date" 
               value="{{ request()->get('rental_date', \Carbon\Carbon::now('Asia/Kuala_Lumpur')->toDateString()) }}" 
               class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm text-sm font-medium text-slate-600 flex-grow">
        
        <button type="submit" 
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-md text-sm font-bold flex-shrink-0">
            Check
        </button>
    </form>

    {{-- 2. Rental History Link (Stacks full width below the form on mobile) --}}
    <a href="{{ route('customer.rental.history') }}"
       class="w-full md:w-auto bg-white text-indigo-600 border border-indigo-100 font-bold px-4 py-2 rounded-lg shadow-sm hover:shadow-md hover:bg-indigo-50 transition text-sm flex items-center justify-center gap-2 flex-shrink-0">
       <i class="bi bi-clock-history"></i> Rental History
    </a>
</div>
{{-- END FIX --}}

@if(count($availableItems) > 0)
<div class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-slate-800">Available Gear</h3>
        <span class="text-xs font-medium bg-slate-100 text-slate-500 px-3 py-1 rounded-full">{{ count($availableItems) }} items</span>
    </div>

    {{-- =============================================== --}}
    {{-- GRID LAYOUT (3 Columns) --}}
    {{-- =============================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($availableItems as $item)
            
            {{-- MODERN CARD --}}
            <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col h-full relative">
                
                {{-- 1. IMMERSIVE IMAGE HEADER --}}
                <div class="relative h-56 overflow-hidden bg-gray-200">
                    
                    {{-- Image Logic --}}
                    <img src="{{ $item->item_Image ? asset('storage/' . $item->item_Image) : 'https://images.unsplash.com/photo-1599058945522-28d584b6f0ff?q=80&w=800&auto=format&fit=crop' }}" 
                         alt="{{ $item->item_Name }}" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    {{-- Overlay Gradient --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>

                    {{-- Floating Badges (Top) --}}
                    <div class="absolute top-3 left-3 z-10">
                        <span class="px-2.5 py-1 bg-emerald-500/90 backdrop-blur text-white rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-sm flex items-center gap-1">
                            <i class="bi bi-check-circle-fill"></i> Available
                        </span>
                    </div>

                    <div class="absolute top-3 right-3 z-10">
                        <a href="{{ route('customer.rental.rent', ['itemID' => $item->itemID, 'rental_date' => request()->get('rental_date', \Carbon\Carbon::now('Asia/Kuala_Lumpur')->toDateString())]) }}#reviews-section" 
                           class="bg-white/90 backdrop-blur px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1 hover:bg-white transition cursor-pointer"
                           title="Rating">
                            <i class="bi bi-star-fill text-yellow-400 text-xs"></i>
                            <span class="text-xs font-bold text-slate-800">{{ number_format($item->avg_rating, 1) }}</span>
                            <span class="text-[10px] text-slate-400">({{ $item->rating_count }})</span>
                        </a>
                    </div>

                    {{-- Title Overlay (Bottom) --}}
                    <div class="absolute bottom-3 left-4 right-4">
                        <h4 class="text-xl font-bold text-white drop-shadow-md leading-tight">{{ strtoupper($item->item_Name) }}</h4>
                    </div>
                </div>

                {{-- 2. CARD CONTENT --}}
                <div class="p-5 flex flex-col flex-grow">
                    
                    {{-- Description --}}
                    <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 mb-6 h-10">
                        {{ strtoupper($item->item_Description) }}
                    </p>
                    
                    {{-- Divider --}}
                    <div class="w-full h-px bg-slate-100 mb-4"></div>

                    {{-- Price & Action Row --}}
                    <div class="flex items-center justify-between mt-auto">
                        
                        {{-- Price Info --}}
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-0.5">Daily Rate</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-extrabold text-indigo-600">RM {{ number_format($item->item_Price, 0) }}</span>
                            </div>
                            <p class="text-xs text-emerald-600 font-medium mt-1">
                                {{ $item->available_quantity }} units left
                            </p>
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('customer.rental.rent', ['itemID' => $item->itemID, 'rental_date' => request()->get('rental_date', \Carbon\Carbon::now('Asia/Kuala_Lumpur')->toDateString())]) }}"
                           class="group/btn bg-slate-900 text-white px-5 py-3 rounded-xl shadow-lg hover:bg-indigo-600 transition-all duration-300 flex items-center gap-2 transform active:scale-95">
                            <span class="text-sm font-bold">Rent</span>
                            <i class="bi bi-arrow-right group-hover/btn:translate-x-1 transition-transform text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
    <div class="flex flex-col items-center justify-center py-16 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
        <div class="bg-white p-4 rounded-full shadow-sm mb-3">
            <i class="bi bi-box-seam text-4xl text-slate-300"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-700">No Items Found</h3>
        <p class="text-slate-500 text-sm mt-1">There are no rental items available for this date.</p>
    </div>
@endif

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
            confirmButtonText: 'Awesome!'
        });
    @endif
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Try Again'
        });
    @endif
</script>
@endpush