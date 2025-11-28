@extends('layout.customer')

@section('title', 'Find a Match')

@section('content')
    <div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
        <div class="text-white font-bold text-2xl px-8">
            Find a Match
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-2xl p-6 mb-10">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="bi bi-robot text-indigo-600"></i>
                    AI-Sorted Matches
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Showing the best matches for your profile first.
                </p>
            </div>
            {{-- View toggle buttons - hidden on mobile --}}
            <div class="hidden md:flex gap-3">
                <button id="tableViewBtn" class="px-4 py-2 rounded-lg shadow text-sm font-medium {{ $view === 'table' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Table View</button>
                <button id="cardViewBtn" class="px-4 py-2 rounded-lg shadow text-sm font-medium {{ $view === 'card' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Card View</button>
            </div>
        </div>

        @if($ads->isEmpty())
            <p class="text-gray-500 italic text-center py-12 text-lg">🚀 No matchmaking ads available right now.</p>
        @else
            {{-- Card View - Always visible on mobile, toggleable on desktop --}}
            <div id="cardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 {{ $view === 'card' ? '' : 'md:hidden' }}">
                @foreach($ads as $ad)
                    <div class="bg-white border rounded-xl shadow-lg p-6 hover:shadow-2xl transition-all relative overflow-hidden">
                        @php
                            $score = round($ad->compatibility_score ?? 0);
                            if ($score > 70) { $scoreColor = 'green'; $scoreIcon = 'bi-check-circle-fill'; }
                            elseif ($score > 40) { $scoreColor = 'yellow'; $scoreIcon = 'bi-exclamation-triangle-fill'; }
                            else { $scoreColor = 'red'; $scoreIcon = 'bi-dash-circle-fill'; }
                        @endphp
                        <div class="absolute top-0 right-0 bg-{{$scoreColor}}-500 text-white font-bold px-4 py-1 rounded-bl-lg shadow-md flex items-center gap-2" title="Based on your profile">
                            <i class="bi {{$scoreIcon}}"></i>
                            <span>{{ $score }}% Match</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-3 mt-4">{{ $ad->ads_Name }}</h3>
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <p class="flex items-center gap-2"><i class="bi bi-person-badge text-indigo-600 w-4"></i> <span class="font-semibold">Type:</span> 
                                <span class="font-medium @if($ad->ads_Type === 'Additional Player') text-blue-700 @else text-purple-700 @endif">{{ $ad->ads_Type }}</span>
                            </p>
                            <p class="flex items-center gap-2"><i class="bi bi-calendar-event text-indigo-600 w-4"></i> <span class="font-semibold">Match:</span> {{ \Carbon\Carbon::parse($ad->ads_SlotTime)->format('D, M j | h:i A') }}</p>
                            <p class="flex items-center gap-2"><i class="bi bi-bar-chart-fill text-indigo-600 w-4"></i> <span class="font-semibold">Skill:</span> Level {{ $ad->ads_TargetSkillLevel ?? 'Any' }} ({{ $ad->ads_MatchIntensity ?? 'N/A' }})</p>
                            <p class="flex items-start gap-2"><i class="bi bi-people-fill text-indigo-600 w-4 mt-1"></i> <span class="font-semibold">Seeking:</span> 
                                <span class="flex flex-wrap gap-1">
                                @php $positions = json_decode($ad->ads_RequiredPosition); @endphp
                                @if(!empty($positions))
                                    @foreach($positions as $position)
                                        <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md text-xs font-medium">{{ $position }}</span>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                                </span>
                            </p>
                        </div>
                        <div class="flex justify-between items-center mt-4 pt-4 border-t">
                            @if($ad->ads_Status === 'Active')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                            @endif
                            
                            @if($ad->ads_Status === 'Filled')
                                <span class="text-gray-500 italic text-sm">Unavailable</span>
                            @else
                                <a href="{{ route('matchmaking.joinForm', $ad->adsID) }}" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md text-sm font-medium">
                                    View & Join
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Table View - Hidden on mobile, toggleable on desktop --}}
            <div id="tableView" class="hidden md:block overflow-x-auto {{ $view === 'table' ? '' : 'md:hidden' }}">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow">
                    <thead class="bg-slate-50">
                        <tr class="text-slate-600 text-xs md:text-sm uppercase tracking-wide">
                            <th class="p-2 md:p-4 text-left">Match Score</th>
                            <th class="p-2 md:p-4 text-left">Ad Title</th>
                            <th class="p-2 md:p-4 text-left">Type</th>
                            <th class="p-2 md:p-4 text-left">Slot Time</th>
                            <th class="p-2 md:p-4 text-left">Status</th>
                            <th class="p-2 md:p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($ads as $ad)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-2 md:p-4 text-center">
                                    @php $score = round($ad->compatibility_score ?? 0); @endphp
                                    <span class="font-bold text-lg md:text-xl 
                                    @if($score > 70) text-green-600
                                    @elseif($score > 40) text-yellow-600
                                    @else text-red-600 @endif" title="Based on your profile">
                                    {{ $score }}%
                                    </span>
                                </td>
                                <td class="p-2 md:p-4 font-semibold text-gray-800 text-xs md:text-base">{{ $ad->ads_Name }}</td>
                                <td class="p-2 md:p-4 font-medium text-xs md:text-base @if($ad->ads_Type === 'Additional Player') text-blue-700 @else text-purple-700 @endif">{{ $ad->ads_Type }}</td>
                                <td class="p-2 md:p-4 text-gray-700 text-xs md:text-base">{{ \Carbon\Carbon::parse($ad->ads_SlotTime)->format('l, d M Y | h:i A') }}</td>
                                <td class="p-2 md:p-4">
                                    @if($ad->ads_Status === 'Active') <span class="bg-green-100 text-green-700 px-2 md:px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                                    @elseif($ad->ads_Status === 'Filled') <span class="bg-red-100 text-red-700 px-2 md:px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                                    @else <span class="bg-gray-100 text-gray-700 px-2 md:px-3 py-1 rounded-full text-xs font-semibold">{{ $ad->ads_Status }}</span>
                                    @endif
                                </td>
                                <td class="p-2 md:p-4 text-center">
                                    @if($ad->ads_Status === 'Filled')
                                        <span class="text-gray-500 text-sm italic">Filled</span>
                                    @else
                                        <a href="{{ route('matchmaking.joinForm', $ad->adsID) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm shadow-md whitespace-nowrap inline-block">
                                            View & Join
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $ads->links() }}
            </div>
        @endif
    </div>

    <div class="bg-white shadow-xl rounded-2xl p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">My Requests</h2>

        @if($applications->isEmpty())
            <p class="text-gray-500 italic text-center py-12 text-lg">📌 You have not applied to any ads yet.</p>
        @else
            
            {{-- =============================================== --}}
            {{-- MY REQUESTS - CARD VIEW (Always visible on mobile) --}}
            {{-- =============================================== --}}
            <div id="myRequestsCardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 {{ $view === 'card' ? '' : 'md:hidden' }}">
                @foreach($applications as $app)
                    @if($app->advertisement)
                        <div class="bg-white border rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $app->advertisement->ads_Name }}</h3>
                            <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Type:</span> {{ $app->advertisement->ads_Type }}</p>
                            <p class="text-sm text-gray-600 mb-3"><span class="font-semibold">Match Time:</span> {{ \Carbon\Carbon::parse($app->advertisement->ads_SlotTime)->format('D, M j | h:i A') }}</p>
                            
                            <div class="flex justify-between items-center mt-4 pt-4 border-t">
                                <span class="text-sm font-semibold text-gray-600">Ad Status:</span>
                                @if($app->advertisement->ads_Status === 'Active') <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                                @else <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Filled</span>
                                @endif
                            </div>
                            
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-sm font-semibold text-gray-600">My Status:</span>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if(strtolower($app->status) === 'approved') bg-green-100 text-green-700
                                    @elseif(strtolower($app->status) === 'rejected') bg-red-100 text-red-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </div>

                            {{-- =============================================== --}}
                            {{-- Contact Button (Only if Approved) --}}
                            {{-- =============================================== --}}
                            @if(strtolower($app->status) === 'approved' && $app->advertisement->customer)
                                @php
                                    $rawPhone = $app->advertisement->customer->customer_PhoneNumber;
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone); // Remove non-numeric chars
                                    if (substr($cleanPhone, 0, 1) === '0') {
                                        $cleanPhone = '6' . $cleanPhone; // Convert 01... to 601...
                                    }
                                @endphp
                                <div class="mt-4 pt-4 border-t text-center">
                                    <a href="https://wa.me/{{ $cleanPhone }}?text=Hi, I matched with your ad '{{ $app->advertisement->ads_Name }}' on PadangPro!" 
                                       target="_blank"
                                       class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-md text-sm font-bold w-full justify-center transition-all">
                                        <i class="bi bi-whatsapp"></i> Contact Organizer
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- =============================================== --}}
            {{-- MY REQUESTS - TABLE VIEW (Hidden on mobile) --}}
            {{-- =============================================== --}}
            <div id="myRequestsTableView" class="hidden md:block overflow-x-auto {{ $view === 'table' ? '' : 'md:hidden' }}">
                <table class="w-full border-collapse overflow-hidden rounded-xl shadow">
                    <thead class="bg-slate-50">
                        <tr class="text-slate-600 text-xs md:text-sm uppercase tracking-wide">
                            <th class="p-2 md:p-4 text-left">Ad Title</th>
                            <th class="p-2 md:p-4 text-left">Type</th>
                            <th class="p-2 md:p-4 text-left">Slot Time</th>
                            <th class="p-2 md:p-4 text-left">Ad Status</th>
                            <th class="p-2 md:p-4 text-left">My Request Status</th>
                            <th class="p-2 md:p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($applications as $app)
                            @if($app->advertisement)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-2 md:p-4 font-semibold text-gray-800 text-xs md:text-base">{{ $app->advertisement->ads_Name }}</td>
                                    <td class="p-2 md:p-4 text-gray-600 text-xs md:text-base">{{ $app->advertisement->ads_Type }}</td>
                                    <td class="p-2 md:p-4 text-gray-600 text-xs md:text-base">{{ \Carbon\Carbon::parse($app->advertisement->ads_SlotTime)->format('l, d M Y | h:i A') }}</td>
                                    <td class="p-2 md:p-4 text-gray-600 text-xs md:text-base">{{ $app->advertisement->ads_Status ?? 'Open' }}</td>
                                    <td class="p-2 md:p-4">
                                        <span class="px-2 md:px-3 py-1 rounded-full text-xs font-semibold
                                            @if(strtolower($app->status) === 'approved') bg-green-100 text-green-700
                                            @elseif(strtolower($app->status) === 'rejected') bg-red-100 text-red-700
                                            @else bg-yellow-100 text-yellow-700 @endif">
                                            {{ ucfirst($app->status) }}
                                        </span>
                                    </td>
                                    <td class="p-2 md:p-4 text-center">
                                        {{-- =============================================== --}}
                                        {{-- WhatsApp Action (Table View) --}}
                                        {{-- =============================================== --}}
                                        @if(strtolower($app->status) === 'approved' && $app->advertisement->customer)
                                            @php
                                                $rawPhone = $app->advertisement->customer->customer_PhoneNumber;
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                                                if (substr($cleanPhone, 0, 1) === '0') {
                                                    $cleanPhone = '6' . $cleanPhone;
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text=Hi, I matched with your ad '{{ $app->advertisement->ads_Name }}' on PadangPro!" 
                                               target="_blank"
                                               class="text-green-600 hover:text-green-800 font-bold text-xl md:text-2xl transition-transform hover:scale-110 inline-block"
                                               title="Contact Organizer on WhatsApp">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardViewBtn = document.getElementById('cardViewBtn');
        
        // Get all four view containers
        const adsTableView = document.getElementById('tableView');
        const adsCardView = document.getElementById('cardView');
        const requestsTableView = document.getElementById('myRequestsTableView');
        const requestsCardView = document.getElementById('myRequestsCardView');

        // This helper function safely adds/updates a URL parameter
        function setView(viewType) {
            const url = new URL(window.location.href);
            url.searchParams.set('view', viewType);
            window.location.href = url.toString();
        }

        // Only add event listeners if buttons exist (desktop view)
        if (tableViewBtn && cardViewBtn) {
            tableViewBtn.addEventListener('click', () => {
                if ('{{ $view }}' !== 'table') {
                    setView('table');
                }
            });

            cardViewBtn.addEventListener('click', () => {
                if ('{{ $view }}' !== 'card') {
                    setView('card');
                }
            });
        }

        function showFullDescription(text) {
            Swal.fire({
                title: 'Full Description',
                html: `<div style="text-align: left; white-space: pre-line;">${text}</div>`,
                icon: 'info',
                confirmButtonText: 'Close',
                confirmButtonColor: '#312e81'
            });
        }
    </script>
@endsection