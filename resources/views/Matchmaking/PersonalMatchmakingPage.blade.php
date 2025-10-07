@extends('layout.customer')

@section('title', 'My Matchmaking Advertisements')

@section('content')
    <!-- Page Header -->
    <div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
        <div class="text-white font-bold text-2xl px-8">
            Your Matchmaking Ads
        </div>
    </div>

    <!-- Ads Section -->
    <div class="bg-white shadow-xl rounded-2xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Your Posted Ads</h2>
            <div class="flex gap-3">
                <button id="tableViewBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 text-sm font-medium">Table View</button>
                <button id="cardViewBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 text-sm font-medium">Card View</button>
                <a href="{{ route('matchmaking.add') }}"
                   class="bg-gradient-to-r from-[#1E2A78] to-blue-600 text-white px-5 py-2 rounded-lg shadow hover:opacity-90 transition">
                    + Create New Ad
                </a>
            </div>
        </div>

        @php
            use Carbon\Carbon;
            $now = Carbon::now('Asia/Kuala_Lumpur');

            // separate ads
            $activeAds = $ads->filter(fn($ad) => $ad->ads_Status !== 'Expired')
                             ->sortBy(fn($ad) => Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur'));
            $expiredAds = $ads->filter(fn($ad) => $ad->ads_Status === 'Expired')
                              ->sortByDesc(fn($ad) => Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur'));
        @endphp

        @if($ads->isEmpty())
            <p class="text-gray-500 italic text-center py-12 text-lg">🚀 You haven’t posted any matchmaking ads yet.</p>
        @else
            <!-- ================= ACTIVE / PENDING / FILLED ADS ================= -->
            <h3 class="text-xl font-bold text-gray-800 mb-4">Active / Upcoming Ads</h3>

            <!-- TABLE VIEW -->
            <div id="tableView" class="overflow-x-auto">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow mb-10">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 text-sm uppercase tracking-wide">
                            <th class="p-4 text-left">Ad Title</th>
                            <th class="p-4 text-left">Type</th>
                            <th class="p-4 text-left">Required Position</th>
                            <th class="p-4 text-left">Max Players</th>
                            <th class="p-4 text-left">Slot Time</th>
                            <th class="p-4 text-left">Status</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($activeAds as $ad)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-semibold text-gray-800">{{ $ad->ads_Name }}</td>
                                <td class="p-4 font-medium {{ $ad->ads_Type === 'Additional Player' ? 'text-blue-700' : 'text-purple-700' }}">{{ $ad->ads_Type }}</td>
                                <td class="p-4">
                                    @if($ad->ads_RequiredPosition)
                                        @foreach(json_decode($ad->ads_RequiredPosition) as $position)
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-sm">{{ $position }}</span>
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
                                    <a href="{{ route('matchmaking.view', $ad->adsID) }}" class="inline-block bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition">View Request</a>
                                    <a href="{{ route('matchmaking.edit', $ad->adsID) }}" class="inline-block bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600 transition">Edit</a>
                                    <form id="deleteForm-{{ $ad->adsID }}" action="{{ route('matchmaking.destroy', $ad->adsID) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="confirmDelete('{{ $ad->adsID }}')"
                                                class="inline-block bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600 transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-gray-500 py-6">No active ads found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- CARD VIEW -->
            <div id="cardView" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                @foreach($activeAds as $ad)
                    <div class="bg-white border rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $ad->ads_Name }}</h3>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Type:</span> {{ $ad->ads_Type }}</p>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Required:</span>
                            @if($ad->ads_RequiredPosition)
                                @foreach(json_decode($ad->ads_RequiredPosition) as $position)
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-xs">{{ $position }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Max Players:</span> {{ $ad->ads_MaxPlayers ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Slot Time:</span> {{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('l, d M Y | h:i A') }}</p>
                        <p class="mt-3">
                            @if($ad->ads_Status === 'Active')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                            @elseif($ad->ads_Status === 'Pending')
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Pending Requests</span>
                            @elseif($ad->ads_Status === 'Filled')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                            @endif
                        </p>
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('matchmaking.view', $ad->adsID) }}" class="text-center bg-blue-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-600 transition">View Request</a>
                            <a href="{{ route('matchmaking.edit', $ad->adsID) }}" class="text-center bg-green-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-600 transition">Edit</a>
                            <form id="deleteForm-{{ $ad->adsID }}" action="{{ route('matchmaking.destroy', $ad->adsID) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        onclick="confirmDelete('{{ $ad->adsID }}')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm shadow-md transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ================= EXPIRED ADS ================= -->
            <h3 class="text-xl font-bold text-gray-800 mt-12 mb-4">Expired Ads</h3>

            <!-- TABLE VIEW -->
            <div id="tableViewExpired" class="overflow-x-auto">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow mb-10">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 text-sm uppercase tracking-wide">
                            <th class="p-4 text-left">Ad Title</th>
                            <th class="p-4 text-left">Type</th>
                            <th class="p-4 text-left">Required Position</th>
                            <th class="p-4 text-left">Max Players</th>
                            <th class="p-4 text-left">Slot Time</th>
                            <th class="p-4 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($expiredAds as $ad)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-semibold text-gray-800">{{ $ad->ads_Name }}</td>
                                <td class="p-4 font-medium text-gray-600">{{ $ad->ads_Type }}</td>
                                <td class="p-4">
                                    @if($ad->ads_RequiredPosition)
                                        @foreach(json_decode($ad->ads_RequiredPosition) as $position)
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-lg text-sm">{{ $position }}</span>
                                        @endforeach
                                    @else
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-lg text-sm">-</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-700">{{ $ad->ads_MaxPlayers ?? 'N/A' }}</td>
                                <td class="p-4 text-gray-700">{{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('l, d M Y | h:i A') }}</td>
                                <td class="p-4">
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">Expired</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-gray-500 py-6">No expired ads found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- CARD VIEW -->
            <div id="cardViewExpired" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                @foreach($expiredAds as $ad)
                    <div class="bg-white border rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $ad->ads_Name }}</h3>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Type:</span> {{ $ad->ads_Type }}</p>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Required:</span>
                            @if($ad->ads_RequiredPosition)
                                @foreach(json_decode($ad->ads_RequiredPosition) as $position)
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-lg text-xs">{{ $position }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Max Players:</span> {{ $ad->ads_MaxPlayers ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Slot Time:</span> {{ Carbon::parse($ad->ads_SlotTime, 'Asia/Kuala_Lumpur')->format('l, d M Y | h:i A') }}</p>
                        <p class="mt-3">
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">Expired</span>
                        </p>
                        <!-- no action buttons -->
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Toggle Script + SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardViewBtn = document.getElementById('cardViewBtn');
        const tableView = document.getElementById('tableView');
        const cardView = document.getElementById('cardView');
        const tableViewExpired = document.getElementById('tableViewExpired');
        const cardViewExpired = document.getElementById('cardViewExpired');

        tableViewBtn.addEventListener('click', () => {
            tableView.classList.remove('hidden');
            tableViewExpired.classList.remove('hidden');
            cardView.classList.add('hidden');
            cardViewExpired.classList.add('hidden');
            tableViewBtn.classList.add('bg-blue-600', 'text-white');
            tableViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            cardViewBtn.classList.add('bg-gray-200', 'text-gray-700');
            cardViewBtn.classList.remove('bg-blue-600', 'text-white');
        });

        cardViewBtn.addEventListener('click', () => {
            cardView.classList.remove('hidden');
            cardViewExpired.classList.remove('hidden');
            tableView.classList.add('hidden');
            tableViewExpired.classList.add('hidden');
            cardViewBtn.classList.add('bg-blue-600', 'text-white');
            cardViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            tableViewBtn.classList.add('bg-gray-200', 'text-gray-700');
            tableViewBtn.classList.remove('bg-blue-600', 'text-white');
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
                    document.getElementById(`deleteForm-${adId}`).submit();
                }
            });
        }

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#1E2A78',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('matchmaking.personal') }}";
                    }
                });
            });
        @endif
    </script>
@endsection
