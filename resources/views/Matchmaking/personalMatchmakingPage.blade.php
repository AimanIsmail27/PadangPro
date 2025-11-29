@extends('layout.customer')

@section('title', 'My Matchmaking Advertisements')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    /* Desktop: Default to table view */
    @media (min-width: 768px) {
        #cardView, #cardViewExpired {
            display: none;
        }
        #tableView, #tableViewExpired {
            display: block;
        }
    }

    /* Desktop: Show card view when view=card */
    @media (min-width: 768px) {
        body.view-card #cardView, 
        body.view-card #cardViewExpired {
            display: grid;
        }
        body.view-card #tableView, 
        body.view-card #tableViewExpired {
            display: none;
        }
    }

    /* Mobile: Always show card view */
    @media (max-width: 767px) {
        #cardView, #cardViewExpired {
            display: grid !important;
        }
        #tableView, #tableViewExpired {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
    <div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
        <h1 class="text-3xl font-bold">Your Matchmaking Ads</h1>
        <p class="mt-2 text-indigo-100">Manage your ads and review applications.</p>
    </div>

    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">
        
        {{-- HEADER ACTION BAR --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            
            <h2 class="text-2xl font-semibold text-gray-800">Your Posted Ads</h2>
            
            <div class="flex flex-col md:flex-row items-end md:items-center gap-3 w-full md:w-auto">
                
                {{-- View Toggle Buttons (Hidden on mobile) --}}
                <div class="flex gap-2 hidden md:flex w-full md:w-auto justify-end"> 
                    <button id="cardViewBtn" class="px-3 py-1.5 text-sm rounded-lg shadow font-medium bg-gray-200 text-gray-700">Card View</button>
                    <button id="tableViewBtn" class="px-3 py-1.5 text-sm rounded-lg shadow font-medium bg-indigo-600 text-white">Table View</button> 
                </div>
                
                {{-- Create Ad Button --}}
                <a href="{{ route('matchmaking.add') }}"
                   class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md font-semibold transition-all text-sm flex items-center justify-center gap-1 transform hover:scale-105">
                    <i class="bi bi-plus-circle-fill mr-1"></i> Create New Ad
                </a>
            </div>
        </div>
        {{-- END HEADER ACTION BAR --}}

        @php
            use Carbon\Carbon;
            $now = Carbon::now('Asia/Kuala_Lumpur');
            $activeAds = $ads->filter(fn($ad) => $ad->ads_Status !== 'Expired')->sortBy(fn($ad) => Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur'));
            $expiredAds = $ads->filter(fn($ad) => $ad->ads_Status === 'Expired')->sortByDesc(fn($ad) => Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur'));
            $view = request()->get('view', 'table'); 
        @endphp

        @if($ads->isEmpty())
            <p class="text-gray-500 italic text-center py-12 text-lg">🚀 You haven't posted any matchmaking ads yet.</p>
        @else
            <h3 class="text-xl font-bold text-gray-800 mb-4">Active & Upcoming Ads</h3>
            
            {{-- ACTIVE ADS - CARD VIEW --}}
            <div id="cardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($activeAds as $ad)
                    <div class="bg-white border rounded-xl shadow-lg p-6 hover:shadow-xl transition-all flex flex-col">
                        <div class="flex-grow">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $ad->ads_Name }}</h3>
                            <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Type:</span> <span class="font-medium @if($ad->ads_Type === 'Additional Player') text-blue-700 @else text-purple-700 @endif">{{ $ad->ads_Type }}</span></p>
                            <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Match Time:</span> {{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('D, M j | h:i A') }}</p>
                            <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Skill Target:</span> Level {{ $ad->ads_TargetSkillLevel ?? 'Any' }} ({{ $ad->ads_MatchIntensity ?? 'N/A' }})</p>
                            <p class="text-sm text-gray-600 mb-3"><span class="font-semibold">Seeking:</span> 
                                @php $positions = is_array($ad->ads_RequiredPosition) ? $ad->ads_RequiredPosition : json_decode($ad->ads_RequiredPosition); @endphp
                                @if(is_array($positions) && !empty($positions))
                                    @foreach($positions as $position)
                                        <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md text-xs">{{ $position }}</span>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="mt-3">
                                @if($ad->ads_Status === 'Active')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                                @elseif($ad->ads_Status === 'Filled')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">{{ $ad->ads_Status }}</span>
                                @endif
                            </p>
                        </div>
                        
                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
                            <a href="{{ route('matchmaking.view', $ad->adsID) }}" class="text-center bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-indigo-700 transition font-medium">View Requests</a>
                            <a href="{{ route('matchmaking.edit', $ad->adsID) }}" class="text-center bg-slate-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-slate-700 transition font-medium">Edit</a>
                            <form id="deleteForm-{{ $ad->adsID }}" action="{{ route('matchmaking.destroy', $ad->adsID) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ $ad->adsID }}')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-md transition">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 italic text-center py-6 lg:col-span-3">No active ads found.</p>
                @endforelse
            </div>

            {{-- ACTIVE ADS - TABLE VIEW --}}
            <div id="tableView" class="overflow-x-auto">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow-md border border-gray-100 mb-10">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Ad Title</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider hidden sm:table-cell">Type</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider hidden lg:table-cell">Required Position</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider hidden sm:table-cell">Max Players</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Slot Time</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Status</th>
                            <th class="p-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($activeAds as $ad)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 font-semibold text-gray-800">{{ $ad->ads_Name }}</td>
                                <td class="p-4 font-medium {{ $ad->ads_Type === 'Additional Player' ? 'text-blue-700' : 'text-purple-700' }} hidden sm:table-cell">{{ $ad->ads_Type }}</td>
                                <td class="p-4 hidden lg:table-cell">
                                    @php $positions = is_array($ad->ads_RequiredPosition) ? $ad->ads_RequiredPosition : json_decode($ad->ads_RequiredPosition); @endphp
                                    @if(is_array($positions) && !empty($positions))
                                        @foreach($positions as $position)
                                            <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded-lg text-sm">{{ $position }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-700 hidden sm:table-cell">{{ $ad->ads_MaxPlayers ?? 'N/A' }}</td>
                                <td class="p-4 text-gray-700">{{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('d M Y | h:i A') }}</td>
                                <td class="p-4">
                                    @if($ad->ads_Status === 'Active')
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                                    @elseif($ad->ads_Status === 'Pending')
                                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Pending Requests</span>
                                    @elseif($ad->ads_Status === 'Filled')
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                                    @endif
                                </td>
                                
                                {{-- Actions Column --}}
                                <td class="p-4 text-center">
                                    <div class="flex flex-col gap-1 items-stretch md:flex-row md:space-x-2"> 
                                        <a href="{{ route('matchmaking.view', $ad->adsID) }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-indigo-700 transition w-full md:w-auto">View Requests</a>
                                        <a href="{{ route('matchmaking.edit', $ad->adsID) }}" class="bg-slate-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-slate-700 transition w-full md:w-auto">Edit</a>
                                        <form id="deleteForm-{{ $ad->adsID }}-table" action="{{ route('matchmaking.destroy', $ad->adsID) }}" method="POST" class="w-full md:w-auto">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $ad->adsID }}-table')" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-700 transition w-full">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-gray-500 py-6">No active ads found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mt-12 mb-4">Expired Ads</h3>

            {{-- EXPIRED ADS - CARD VIEW --}}
            <div id="cardViewExpired" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-70">
                @forelse($expiredAds as $ad)
                    <div class="bg-gray-50 border rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-500 mb-2">{{ $ad->ads_Name }}</h3>
                        <p class="text-sm text-gray-500 mb-1"><span class="font-semibold">Type:</span> {{ $ad->ads_Type }}</p>
                        <p class="text-sm text-gray-500 mb-1"><span class="font-semibold">Slot Time:</span> {{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('l, d M Y | h:i A') }}</p>
                        <p class="mt-3">
                            <span class="bg-slate-200 text-slate-700 px-3 py-1 rounded-full text-xs font-semibold">Expired</span>
                        </p>
                        <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
                             <a href="{{ route('matchmaking.view', $ad->adsID) }}" class="text-center bg-gray-400 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">View Ad</a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 italic text-center py-6 lg:col-span-3">No expired ads found.</p>
                @endforelse
            </div>

            {{-- EXPIRED ADS - TABLE VIEW --}}
            <div id="tableViewExpired" class="overflow-x-auto opacity-70">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Ad Title</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider hidden sm:table-cell">Type</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Slot Time</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($expiredAds as $ad)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 font-semibold text-gray-500">{{ $ad->ads_Name }}</td>
                                <td class="p-4 font-medium text-gray-500 hidden sm:table-cell">{{ $ad->ads_Type }}</td>
                                <td class="p-4 text-gray-500">{{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('d M Y | h:i A') }}</td>
                                <td class="p-4">
                                    <span class="bg-slate-200 text-slate-700 px-3 py-1 rounded-full text-xs font-semibold">Expired</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-gray-500 py-6">No expired ads found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardViewBtn = document.getElementById('cardViewBtn');

        // Get current view from URL or default to table for desktop
        const urlParams = new URLSearchParams(window.location.search);
        const currentView = urlParams.get('view') || 'table';

        // Set initial state based on URL
        if (currentView === 'card') {
            document.body.classList.add('view-card');
            cardViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            cardViewBtn.classList.add('bg-indigo-600', 'text-white');
            tableViewBtn.classList.remove('bg-indigo-600', 'text-white');
            tableViewBtn.classList.add('bg-gray-200', 'text-gray-700');
        }

        // Button click handlers
        tableViewBtn.addEventListener('click', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('view', 'table');
            window.location.href = url.toString();
        });

        cardViewBtn.addEventListener('click', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('view', 'card');
            window.location.href = url.toString();
        });

        // Delete Confirmation Logic
        function confirmDelete(adId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This advertisement will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById(`deleteForm-${adId}`);
                    if (!form) {
                        form = document.getElementById(`deleteForm-${adId}-table`);
                    }
                    if (form) {
                        form.submit();
                    }
                }
            });
        }
        window.confirmDelete = confirmDelete;

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#4f46e5',
                    confirmButtonText: 'OK'
                });
            });
        @endif
    </script>
@endpush
