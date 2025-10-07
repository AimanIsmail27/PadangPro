@extends('layout.customer')

@section('title', 'Other Matchmaking Advertisements')

@section('content')
    <!-- Page Header -->
    <div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
        <div class="text-white font-bold text-2xl px-8">
            Other Matchmaking Ads
        </div>
    </div>

    <!-- ================= AVAILABLE ADS ================= -->
    <div class="bg-white shadow-xl rounded-2xl p-6 mb-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Available Ads</h2>
            <div class="flex gap-3">
                <button id="tableViewBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 text-sm font-medium">Table View</button>
                <button id="cardViewBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 text-sm font-medium">Card View</button>
            </div>
        </div>

        @if($ads->isEmpty())
            <p class="text-gray-500 italic text-center py-12 text-lg">🚀 No matchmaking ads available right now.</p>
        @else
            <!-- TABLE VIEW -->
            <div id="tableView" class="overflow-x-auto">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 text-sm uppercase tracking-wide">
                            <th class="p-4 text-left">Ad Title</th>
                            <th class="p-4 text-left">Type</th>
                            <th class="p-4 text-left">Description</th>
                            <th class="p-4 text-left">Required Position</th>
                            <th class="p-4 text-left">Max Players</th>
                            <th class="p-4 text-left">Slot Time</th>
                            <th class="p-4 text-left">Status</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($ads as $ad)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-semibold text-gray-800">{{ $ad->ads_Name }}</td>
                                <td class="p-4 font-medium @if($ad->ads_Type === 'Additional Player') text-blue-700 @else text-purple-700 @endif">{{ $ad->ads_Type }}</td>
                                <td class="p-4 text-gray-600">
                                    @php $shortDesc = \Illuminate\Support\Str::limit($ad->ads_Description, 30); @endphp
                                    {{ $shortDesc }}
                                    @if(strlen($ad->ads_Description) > 30)
                                        <button onclick="showFullDescription(`{{ addslashes($ad->ads_Description) }}`)" class="text-blue-600 text-sm ml-2 hover:underline">View More</button>
                                    @endif
                                </td>
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
                                <td class="p-4 text-gray-700">{{ \Carbon\Carbon::parse($ad->ads_SlotTime)->format('l, d M Y | h:i A') }}</td>
                                <td class="p-4">
                                    @if($ad->ads_Status === 'Active')
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                                    @elseif($ad->ads_Status === 'Filled')
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">{{ $ad->ads_Status }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    @if($ad->ads_Status === 'Filled')
                                        -
                                    @else
                                        <a href="{{ route('matchmaking.joinForm', $ad->adsID) }}" 
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm">
                                            Request to Join
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- CARD VIEW -->
            <div id="cardView" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                @foreach($ads as $ad)
                    <div class="bg-white border rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $ad->ads_Name }}</h3>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Type:</span> {{ $ad->ads_Type }}</p>
                        <p class="text-sm text-gray-600 mb-1">
                            <span class="font-semibold">Description:</span>
                            @php $shortDesc = \Illuminate\Support\Str::limit($ad->ads_Description, 30); @endphp
                            {{ $shortDesc }}
                            @if(strlen($ad->ads_Description) > 30)
                                <button onclick="showFullDescription(`{{ addslashes($ad->ads_Description) }}`)" class="text-blue-600 text-xs ml-2 hover:underline">View More</button>
                            @endif
                        </p>
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
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Slot Time:</span> {{ \Carbon\Carbon::parse($ad->ads_SlotTime)->format('l, d M Y | h:i A') }}</p>
                        <p class="mt-3">
                            @if($ad->ads_Status === 'Active')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                            @elseif($ad->ads_Status === 'Filled')
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                            @else
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">{{ $ad->ads_Status }}</span>
                            @endif
                        </p>
                        <div class="flex justify-end mt-4">
                            @if($ad->ads_Status === 'Filled')
                                <span class="text-gray-500 italic text-sm">No Actions</span>
                            @else
                                <a href="{{ route('matchmaking.joinForm', $ad->adsID) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md text-sm">
                                    Request to Join
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ================= MY REQUESTS ================= -->
    <div class="bg-white shadow-xl rounded-2xl p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">My Requests</h2>

        @if($applications->isEmpty())
            <p class="text-gray-500 italic text-center py-12 text-lg">📌 You have not applied to any ads yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 text-sm uppercase tracking-wide">
                            <th class="p-4 text-left">Ad Title</th>
                            <th class="p-4 text-left">Type</th>
                            <th class="p-4 text-left">Slot Time</th>
                            <th class="p-4 text-left">Ad Status</th>
                            <th class="p-4 text-left">My Request Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($applications as $app)
                            @if($app->advertisement)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 font-semibold text-gray-800">{{ $app->advertisement->ads_Name }}</td>
                                    <td class="p-4">{{ $app->advertisement->ads_Type }}</td>
                                    <td class="p-4 text-gray-700">{{ \Carbon\Carbon::parse($app->advertisement->ads_SlotTime)->format('l, d M Y | h:i A') }}</td>
                                    <td class="p-4">{{ $app->advertisement->ads_Status ?? 'Open' }}</td>
                                    <td class="p-4">
                                        <span class="bg-{{ $app->status === 'Approved' ? 'green' : ($app->status === 'Rejected' ? 'red' : 'yellow') }}-100 
                                                     text-{{ $app->status === 'Approved' ? 'green' : ($app->status === 'Rejected' ? 'red' : 'yellow') }}-700 
                                                     px-3 py-1 rounded-full text-xs font-semibold">
                                            {{ $app->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle between table and card view
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardViewBtn = document.getElementById('cardViewBtn');
        const tableView = document.getElementById('tableView');
        const cardView = document.getElementById('cardView');

        tableViewBtn.addEventListener('click', () => {
            tableView.classList.remove('hidden');
            cardView.classList.add('hidden');
            tableViewBtn.classList.add('bg-blue-600', 'text-white');
            tableViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            cardViewBtn.classList.add('bg-gray-200', 'text-gray-700');
            cardViewBtn.classList.remove('bg-blue-600', 'text-white');
        });

        cardViewBtn.addEventListener('click', () => {
            tableView.classList.add('hidden');
            cardView.classList.remove('hidden');
            cardViewBtn.classList.add('bg-blue-600', 'text-white');
            cardViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            tableViewBtn.classList.add('bg-gray-200', 'text-gray-700');
            tableViewBtn.classList.remove('bg-blue-600', 'text-white');
        });

        // Show full description popup
        function showFullDescription(text) {
            Swal.fire({
                title: 'Full Description',
                html: `<div style="text-align: left; white-space: pre-line;">${text}</div>`,
                icon: 'info',
                confirmButtonText: 'Close'
            });
        }
    </script>
@endsection
