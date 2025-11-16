@extends('layout.customer')

@section('title', 'My Matchmaking Advertisements')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')
    <div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
        <h1 class="text-3xl font-bold">Your Matchmaking Ads</h1>
        <p class="mt-2 text-indigo-100">Manage your ads and review applications.</p>
    </div>

    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Your Posted Ads</h2>
            <div class="flex items-center gap-3">
                <button id="cardViewBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 text-sm font-medium">Card View</button>
                <button id="tableViewBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 text-sm font-medium">Table View</button>
                <a href="{{ route('matchmaking.add') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md font-semibold transition-all transform hover:scale-105">
                    <i class="bi bi-plus-circle-fill mr-1"></i> Create New Ad
                </a>
            </div>
        </div>

        @php
            use Carbon\Carbon;
            $now = Carbon::now('Asia/Kuala_Lumpur');
            $activeAds = $ads->filter(fn($ad) => $ad->ads_Status !== 'Expired')->sortBy(fn($ad) => Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur'));
            $expiredAds = $ads->filter(fn($ad) => $ad->ads_Status === 'Expired')->sortByDesc(fn($ad) => Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur'));
        @endphp

        @if($ads->isEmpty())
            <p class="text-gray-500 italic text-center py-12 text-lg">🚀 You haven’t posted any matchmaking ads yet.</p>
        @else
            <h3 class="text-xl font-bold text-gray-800 mb-4">Active & Upcoming Ads</h3>

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
                        
                        {{-- =============================================== --}}
                        {{-- UPDATED BUTTONS SECTION --}}
                        {{-- =============================================== --}}
                        <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
                            <a href="{{ route('matchmaking.view', $ad->adsID) }}" class="text-center bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-indigo-700 transition font-medium">View Requests</a>
                            <a href="{{ route('matchmaking.edit', $ad->adsID) }}" class="text-center bg-slate-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-slate-700 transition font-medium">Edit</a>
                            <form id="deleteForm-{{ $ad->adsID }}" action="{{ route('matchmaking.destroy', $ad->adsID) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ $ad->adsID }}')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm shadow-md transition">Delete</button>
                            </form>
                        </div>
                        {{-- =============================================== --}}

                    </div>
                @empty
                    <p class="text-gray-500 italic text-center py-6 lg:col-span-3">No active ads found.</p>
                @endforelse
            </div>

            <div id="tableView" class="overflow-x-auto hidden">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow-md border border-gray-100 mb-10">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Ad Title</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Type</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Required Position</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Max Players</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Slot Time</th>
                            <th class="p-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Status</th>
                            <th class="p-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($activeAds as $ad)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 font-semibold text-gray-800">{{ $ad->ads_Name }}</td>
                                <td class="p-4 font-medium {{ $ad->ads_Type === 'Additional Player' ? 'text-blue-700' : 'text-purple-700' }}">{{ $ad->ads_Type }}</td>
                                <td class="p-4">
                                    @php $positions = is_array($ad->ads_RequiredPosition) ? $ad->ads_RequiredPosition : json_decode($ad->ads_RequiredPosition); @endphp
                                    @if(is_array($positions) && !empty($positions))
                                        @foreach($positions as $position)
                                            <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded-lg text-sm">{{ $position }}</span>
                                        @endforeach
                                    @else
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-lg text-sm">-</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-700">{{ $ad->ads_MaxPlayers ?? 'N/A' }}</td>
                                <td class="p-4 text-gray-700">{{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('l, d M Y | h:i A') }}</td>
                                <td class="p-4">
                                    @if($ad->ads_Status === 'Active')
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                                    @elseif($ad->ads_Status === 'Pending')
                                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Pending Requests</span>
                                    @elseif($ad->ads_Status === 'Filled')
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center space-x-2">
                                    <a href="{{ route('matchmaking.view', $ad->adsID) }}" class="inline-block bg-indigo-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-indigo-700 transition">View</a>
                                    <a href="{{ route('matchmaking.edit', $ad->adsID) }}" class="inline-block bg-slate-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-slate-700 transition">Edit</a>
                                    <form id="deleteForm-{{ $ad->adsID }}-table" action="{{ route('matchmaking.destroy', $ad->adsID) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $ad->adsID }}-table')" class="inline-block bg-red-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-700 transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-gray-500 py-6">No active ads found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mt-12 mb-4">Expired Ads</h3>

            <div id="cardViewExpired" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-70">
                @forelse($expiredAds as $ad)
                    <div class="bg-gray-50 border rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-500 mb-2">{{ $ad->ads_Name }}</h3>
                        <p class="text-sm text-gray-500 mb-1"><span class="font-semibold">Type:</span> {{ $ad->ads_Type }}</p>
                        <p class="text-sm text-gray-500 mb-1"><span class="font-semibold">Required:</span>
                            @php $positions = is_array($ad->ads_RequiredPosition) ? $ad->ads_RequiredPosition : json_decode($ad->ads_RequiredPosition); @endphp
                            @if(is_array($positions) && !empty($positions))
                                @foreach($positions as $position)
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-lg text-xs">{{ $position }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 mb-1"><span class="font-semibold">Max Players:</span> {{ $ad->ads_MaxPlayers ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500 mb-1"><span class="font-semibold">Slot Time:</span> {{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('l, d M Y | h:i A') }}</p>
                        <p class="mt-3">
                            <span class="bg-slate-200 text-slate-700 px-3 py-1 rounded-full text-xs font-semibold">Expired</span>
                        </p>
                    </div>
                @empty
                     <p class="text-gray-500 italic text-center py-6 lg:col-span-3">No expired ads found.</p>
                @endforelse
            </div>

            <div id="tableViewExpired" class="overflow-x-auto opacity-70">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow">
                    <thead class="bg-slate-50">
                        <tr class="text-slate-600 text-sm uppercase tracking-wide">
                            <th class="p-4 text-left">Ad Title</th>
                            <th class="p-4 text-left">Type</th>
                            <th class="p-4 text-left">Slot Time</th>
                            <th class="p-4 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($expiredAds as $ad)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 font-semibold text-gray-500">{{ $ad->ads_Name }}</td>
                                <td class="p-4 font-medium text-gray-500">{{ $ad->ads_Type }}</td>
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
        const tableView = document.getElementById('tableView');
        const cardView = document.getElementById('cardView');
        const tableViewExpired = document.getElementById('tableViewExpired');
        const cardViewExpired = document.getElementById('cardViewExpired');

        // Set Card View as default
        cardView.classList.remove('hidden');
        cardViewExpired.classList.remove('hidden');
        tableView.classList.add('hidden');
        tableViewExpired.classList.add('hidden');

        tableViewBtn.addEventListener('click', () => {
            tableView.classList.remove('hidden');
            tableViewExpired.classList.remove('hidden');
            cardView.classList.add('hidden');
            cardViewExpired.classList.add('hidden');
            
            tableViewBtn.classList.add('bg-indigo-600', 'text-white');
            tableViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            cardViewBtn.classList.add('bg-gray-200', 'text-gray-700');
            cardViewBtn.classList.remove('bg-indigo-600', 'text-white');
        });

        cardViewBtn.addEventListener('click', () => {
            cardView.classList.remove('hidden');
            cardViewExpired.classList.remove('hidden');
            tableView.classList.add('hidden');
            tableViewExpired.classList.add('hidden');

            cardViewBtn.classList.add('bg-indigo-600', 'text-white');
            cardViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            tableViewBtn.classList.add('bg-gray-200', 'text-gray-700');
            tableViewBtn.classList.remove('bg-indigo-600', 'text-white');
        });

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
                    // Find the correct form to submit
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

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#4f46e5', // Indigo-600
                    confirmButtonText: 'OK'
                });
            });
        @endif
    </script>
@endpush