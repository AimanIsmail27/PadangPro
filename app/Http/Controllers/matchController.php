<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\Customer;
use App\Models\Applications;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\MatchRequestStatusMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class MatchController extends Controller
{
    /**
     * Show the form to create a new matchmaking advertisement.
     */
    public function create()
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to create an ad.');
        }

        return view('Matchmaking.addOfferPage');
    }

    public function store(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to post an ad.');
        }

        $customer = Customer::where('userID', $userId)->first();
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer profile not found.');
        }

        $rules = [
            'ads_Name'              => 'required|string|max:255',
            'ads_Type'              => 'required|string|in:Additional Player,Opponent Search',
            'ads_Price'             => 'nullable|numeric|min:0',
            'ads_Description'       => 'required|string|max:65535',
            'ads_SlotTime'          => 'required|date|after:now',
            'ads_TargetSkillLevel'  => 'required|integer|min:1|max:5',
            'ads_MatchIntensity'    => 'required|string|in:Fun,Competitive',
        ];

        if ($request->ads_Type === 'Additional Player') {
            $rules['ads_RequiredPosition']   = 'required|array|min:1';
            $rules['ads_RequiredPosition.*'] = 'string|max:10';
            $rules['ads_MaxPlayers']         = 'required|integer|min:1';
        }

        $request->validate($rules);

        $adsID = 'ADS' . strtoupper(uniqid());

        $positions = $request->ads_Type === 'Additional Player'
            ? json_encode($request->ads_RequiredPosition)
            : null;

        Advertisement::create([
            'adsID'                 => $adsID,
            'ads_Name'              => $request->ads_Name,
            'ads_Type'              => $request->ads_Type,
            'ads_Price'             => $request->ads_Price ?? 0,
            'ads_Description'       => $request->ads_Description,
            'ads_Status'            => 'Active',
            'ads_RequiredPosition'  => $positions,
            'ads_MaxPlayers'        => $request->ads_Type === 'Additional Player' ? $request->ads_MaxPlayers : null,
            'ads_SlotTime'          => $request->ads_SlotTime,
            'customerID'            => $customer->customerID,
            'ads_TargetSkillLevel'  => $request->ads_TargetSkillLevel,
            'ads_MatchIntensity'    => $request->ads_MatchIntensity,
        ]);

        return redirect()->route('matchmaking.personal')
            ->with('success', 'Your matchmaking advertisement has been posted successfully!');
    }

    /**
     * Display all ads posted by the logged-in customer.
     */
    public function personalAds()
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $customer = Customer::where('userID', $userId)->first();
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer profile not found.');
        }

        $ads = Advertisement::where('customerID', $customer->customerID)->get();

        foreach ($ads as $ad) {
            $slotTime = Carbon::parse($ad->ads_SlotTime)->timezone('Asia/Kuala_Lumpur');
            $currentTime = Carbon::now('Asia/Kuala_Lumpur');

            if ($currentTime->greaterThan($slotTime)) {
                $ad->ads_Status = 'Expired';
                continue;
            }

            $approvedCount = Applications::where('adsID', $ad->adsID)
                ->where('status', 'Approved')
                ->count();

            $maxPlayers = $ad->ads_Type === 'Additional Player' ? $ad->ads_MaxPlayers : 1;

            if ($approvedCount >= $maxPlayers) {
                $ad->ads_Status = 'Filled';
            }
        }

        return view('Matchmaking.personalMatchmakingPage', compact('ads'));
    }

    /**
     * Show edit form for a specific advertisement.
     */
    public function edit($adsID)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $customer = Customer::where('userID', $userId)->first();
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer profile not found.');
        }

        $ad = Advertisement::where('adsID', $adsID)
            ->where('customerID', $customer->customerID)
            ->first();

        if (!$ad) {
            return redirect()->route('matchmaking.personal')->with('error', 'Ad not found or not owned by you.');
        }

        $ad->ads_RequiredPosition = is_string($ad->ads_RequiredPosition)
            ? json_decode($ad->ads_RequiredPosition, true)
            : ($ad->ads_RequiredPosition ?? []);

        return view('Matchmaking.editOfferPage', compact('ad'));
    }

    public function update(Request $request, $adsID)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $customer = Customer::where('userID', $userId)->first();
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer profile not found.');
        }

        $ad = Advertisement::where('adsID', $adsID)
            ->where('customerID', $customer->customerID)
            ->first();

        if (!$ad) {
            return redirect()->route('matchmaking.personal')->with('error', 'Ad not found or not owned by you.');
        }

        $rules = [
            'ads_Name'              => 'required|string|max:255',
            'ads_Description'       => 'required|string|max:65535',
            'ads_Price'             => 'nullable|numeric|min:0',
            'ads_SlotTime'          => 'required|date|after:now',
            'ads_TargetSkillLevel'  => 'required|integer|min:1|max:5',
            'ads_MatchIntensity'    => 'required|string|in:Fun,Competitive',
        ];

        if ($ad->ads_Type === 'Additional Player') {
            $rules['ads_RequiredPosition']   = 'required|array|min:1';
            $rules['ads_RequiredPosition.*'] = 'string|max:10';
            $rules['ads_MaxPlayers']         = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        $ad->ads_Name = $validated['ads_Name'];
        $ad->ads_Description = $validated['ads_Description'];
        $ad->ads_Price = $validated['ads_Price'] ?? 0;
        $ad->ads_SlotTime = $validated['ads_SlotTime'];
        $ad->ads_TargetSkillLevel = $validated['ads_TargetSkillLevel'];
        $ad->ads_MatchIntensity = $validated['ads_MatchIntensity'];

        if ($ad->ads_Type === 'Additional Player') {
            $ad->ads_RequiredPosition = isset($validated['ads_RequiredPosition'])
                ? json_encode($validated['ads_RequiredPosition'])
                : json_encode([]);
            $ad->ads_MaxPlayers = $validated['ads_MaxPlayers'];
        }

        $ad->save();

        return redirect()->route('matchmaking.personal')->with('success', 'Ad updated successfully.');
    }

    public function view($adsID)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $customer = Customer::where('userID', $userId)->first();
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer profile not found.');
        }

        $ad = Advertisement::where('adsID', $adsID)
            ->where('customerID', $customer->customerID)
            ->first();

        if (!$ad) {
            return redirect()->route('matchmaking.personal')->with('error', 'Ad not found or not owned by you.');
        }

        $requests = Applications::with('customer')
            ->where('adsID', $adsID)
            ->orderBy('created_at', 'asc')
            ->get();

        $maxPlayers = $ad->ads_Type === 'Additional Player' ? $ad->ads_MaxPlayers : 1;

        $approvedCount = $requests->where('status', 'approved')->count();

        foreach ($requests as $r) {
            $r->canApprove = strtolower($r->status) === 'pending' && $approvedCount < $maxPlayers;
        }

        return view('Matchmaking.viewRequest', compact('ad', 'requests'));
    }

    public function destroy($adsID)
    {
        $ad = Advertisement::findOrFail($adsID);
        $ad->delete();

        return redirect()->route('matchmaking.personal')
            ->with('success', 'Advertisement deleted successfully!');
    }

    /**
     * OTHER ADS (AI sorted) + FILTERS
     */
    /**
 * OTHER ADS (AI sorted) + FILTERS
 */
public function otherAds(Request $request)
{
    $userId = session('user_id');
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Please log in.');
    }

    $customer = Customer::where('userID', $userId)->first();
    if (!$customer) {
        return redirect()->back()->with('error', 'Customer profile not found.');
    }

    // view toggle
    $view = $request->input('view', 'table');

    // filters
    $filterType = $request->input('type');           // "Opponent Search" | "Additional Player" | null
    $filterIntensity = $request->input('intensity'); // "Fun" | "Competitive" | null
    $minScore = $request->input('min_score');        // 30/50/70 | null

    $currentTime = Carbon::now('Asia/Kuala_Lumpur');
    $ads = new LengthAwarePaginator([], 0, 5);

    // ✅ Use env-based Match API URL (Railway + Localhost friendly)
    // Local .env: MATCH_API_URL=http://127.0.0.1:5001
    // Railway:     MATCH_API_URL=https://python-production-701e.up.railway.app
    $baseUrl = rtrim(env('MATCH_API_URL', ''), '/');

    try {
        if (empty($baseUrl)) {
            Log::error('MATCH_API_URL is not set. Falling back to old method.');
            $ads = $this->getAdsTheOldWay($customer->customerID, $currentTime, $view, $filterType, $filterIntensity);
        } else {
            $response = Http::timeout(8)->post($baseUrl . '/match', [
                'customerID' => $customer->customerID,
                'customer_SkillLevel' => $customer->customer_SkillLevel,
                'customer_Availability' => $customer->customer_Availability,
                'customer_Intensity' => $customer->customer_Intensity ?? 'Fun',
            ]);

            if ($response->successful()) {
                $scoredAds = $response->json();

                if (!is_array($scoredAds)) {
                    Log::error('Fuzzy Match API returned invalid JSON. Body: ' . $response->body());
                    $ads = $this->getAdsTheOldWay($customer->customerID, $currentTime, $view, $filterType, $filterIntensity);
                } else {
                    // Map: adsID => compatibility_score
                    $scoreMap = collect($scoredAds)->pluck('compatibility_score', 'adsID');
                    $sortedAdIds = $scoreMap->keys();

                    if ($sortedAdIds->isNotEmpty()) {
                        $orderString = "FIELD(adsID, '" . implode("','", $sortedAdIds->all()) . "')";

                        $query = Advertisement::with('customer.user')
                            ->whereIn('adsID', $sortedAdIds);

                        // Apply DB-level filters
                        if (!empty($filterType)) {
                            $query->where('ads_Type', $filterType);
                        }
                        if (!empty($filterIntensity)) {
                            $query->where('ads_MatchIntensity', $filterIntensity);
                        }

                        $ads = $query->orderByRaw($orderString)
                            ->paginate(5)
                            ->appends([
                                'view' => $view,
                                'type' => $filterType,
                                'intensity' => $filterIntensity,
                                'min_score' => $minScore,
                            ]);

                        // attach scores
                        foreach ($ads as $ad) {
                            $ad->compatibility_score = $scoreMap[$ad->adsID] ?? 0;
                        }

                        // Apply min_score AFTER scores are attached
                        if (!empty($minScore)) {
                            $filtered = $ads->getCollection()
                                ->filter(function ($ad) use ($minScore) {
                                    return (float)($ad->compatibility_score ?? 0) >= (float)$minScore;
                                })
                                ->values();

                            $ads = new LengthAwarePaginator(
                                $filtered,
                                $filtered->count(),
                                5,
                                $ads->currentPage(),
                                [
                                    'path' => request()->url(),
                                    'query' => request()->query(),
                                ]
                            );
                        }
                    } else {
                        // API returned empty list -> fallback (or keep empty if you prefer)
                        Log::warning('Fuzzy Match API returned empty results. Falling back to old method.');
                        $ads = $this->getAdsTheOldWay($customer->customerID, $currentTime, $view, $filterType, $filterIntensity);
                    }
                }
            } else {
                Log::error('Fuzzy Match API call failed: ' . $response->status() . ' ' . $response->body());
                $ads = $this->getAdsTheOldWay($customer->customerID, $currentTime, $view, $filterType, $filterIntensity);
            }
        }
    } catch (\Throwable $e) {
        Log::error('Fuzzy Match API exception: ' . $e->getMessage());
        $ads = $this->getAdsTheOldWay($customer->customerID, $currentTime, $view, $filterType, $filterIntensity);
    }

    // mark filled (on visible list)
    foreach ($ads as $ad) {
        $approvedCount = Applications::where('adsID', $ad->adsID)->where('status', 'Approved')->count();
        $maxPlayers = $ad->ads_Type === 'Additional Player' ? $ad->ads_MaxPlayers : 1;
        if ($approvedCount >= $maxPlayers) {
            $ad->ads_Status = 'Filled';
        }
    }

    // my requests
    $applications = Applications::where('customerID', $customer->customerID)
        ->whereHas('advertisement', fn($q) => $q->where('ads_SlotTime', '>', $currentTime))
        ->with('advertisement.customer')
        ->orderByDesc('created_at')
        ->get();

    foreach ($applications as $application) {
        if ($application->advertisement) {
            $ad = $application->advertisement;
            $approvedCount = Applications::where('adsID', $ad->adsID)->where('status', 'Approved')->count();
            $maxPlayers = $ad->ads_Type === 'Additional Player' ? $ad->ads_MaxPlayers : 1;
            if ($approvedCount >= $maxPlayers) {
                $ad->ads_Status = 'Filled';
            }
        }
    }

    return view('Matchmaking.otherOfferPage', compact('ads', 'applications', 'view'));
}


    /**
     * Fallback (no AI)
     */
    private function getAdsTheOldWay($customerID, $currentTime, $view = 'table', $filterType = null, $filterIntensity = null)
    {
        $query = Advertisement::with('customer.user')
            ->where('customerID', '!=', $customerID)
            ->where('ads_SlotTime', '>', $currentTime);

        if (!empty($filterType)) {
            $query->where('ads_Type', $filterType);
        }
        if (!empty($filterIntensity)) {
            $query->where('ads_MatchIntensity', $filterIntensity);
        }

        $ads = $query->orderBy('ads_SlotTime', 'asc')
            ->paginate(5)
            ->appends([
                'view' => $view,
                'type' => $filterType,
                'intensity' => $filterIntensity,
                'min_score' => request('min_score'),
            ]);

        foreach ($ads as $ad) {
            $ad->compatibility_score = 0;
        }

        return $ads;
    }

    /**
     * Show form for joining an advertisement
     */
    public function joinForm($adsID)
{
    $userId = session('user_id');

    if (!$userId) {
        return redirect()->route('login')->with('error', 'Please log in first.');
    }

    $customer = Customer::where('userID', $userId)->first();
    if (!$customer) {
        return redirect()->back()->with('error', 'Customer profile not found.');
    }

    $ad = Advertisement::where('adsID', $adsID)->firstOrFail();

    // -------------------------------
    // Build a nice default message
    // -------------------------------
    $position = $customer->customer_Position ?? 'N/A';
    $skill = $customer->customer_SkillLevel ? "Level {$customer->customer_SkillLevel}" : 'N/A';

    // Parse availability JSON like {"days":["Thursday"],"time":["Night"]}
    $availDays = [];
    $availTimes = [];
    if (!empty($customer->customer_Availability)) {
        $decoded = json_decode($customer->customer_Availability, true);
        if (is_array($decoded)) {
            $availDays = $decoded['days'] ?? [];
            $availTimes = $decoded['time'] ?? [];
        }
    }

    $daysText = !empty($availDays) ? implode(', ', $availDays) : 'Not set';
    $timesText = !empty($availTimes) ? implode(', ', $availTimes) : 'Not set';

    $slotText = !empty($ad->ads_SlotTime)
        ? \Carbon\Carbon::parse($ad->ads_SlotTime)->format('D, M j | h:i A')
        : 'N/A';

    $defaultNote =
        "Hi! I’m interested to join your ad \"{$ad->ads_Name}\".\n\n" .
        "My profile:\n" .
        "- Position: {$position}\n" .
        "- Skill: {$skill}\n" .
        "- Availability: {$daysText} ({$timesText})\n\n" .
        "I’m available for the slot: {$slotText}. Looking forward to play together. Thanks!";

    return view('Matchmaking.joinOfferPage', compact('ad', 'defaultNote'));
}


    /**
     * Store join request
     */
    public function joinStore(Request $request, $adsID)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        $customer = Customer::where('userID', $userId)->first();
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer profile not found.');
        }

        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $existing = Applications::where('adsID', $adsID)
            ->where('customerID', $customer->customerID)
            ->first();

        if ($existing) {
            return redirect()->route('matchmaking.other')->with('error', 'You already applied to this ad.');
        }

        $applicationID = 'APP' . strtoupper(Str::random(6));

        Applications::create([
            'applicationID' => $applicationID,
            'adsID' => $adsID,
            'customerID' => $customer->customerID,
            'status' => 'pending',
            'note' => $request->note,
        ]);

        return redirect()->route('matchmaking.other')->with('success', 'Request submitted successfully!');
    }

    public function accept($id)
{
    $application = Applications::with('customer.user')->findOrFail($id);

    $ad = Advertisement::where('adsID', $application->adsID)->first();
    if (!$ad) {
        return redirect()->back()->with('error', 'Advertisement not found.');
    }

    // --- Capacity checks ---
    if ($ad->ads_Type === 'Additional Player') {
        $approvedCount = Applications::where('adsID', $ad->adsID)
            ->where('status', 'Approved')
            ->count();

        if ($approvedCount >= $ad->ads_MaxPlayers) {
            return redirect()->back()->with('error', 'Maximum players already approved for this advertisement.');
        }
    }

    if ($ad->ads_Type === 'Opponent Search') {
        $approvedOpponent = Applications::where('adsID', $ad->adsID)
            ->where('status', 'Approved')
            ->exists();

        if ($approvedOpponent) {
            return redirect()->back()->with('error', 'An opponent has already been approved for this advertisement.');
        }
    }

    // Approve
    $application->status = 'Approved';
    $application->save();

    // ✅ Send email (FIXED FIELD NAME)
    $email = optional(optional($application->customer)->user)->user_Email;

    if (!$email) {
        Log::warning("Approval email not sent: missing user_Email", [
            'application_id' => $application->id ?? null,
            'customer_id' => optional($application->customer)->customerID ?? null,
            'userID' => optional($application->customer)->userID ?? null,
        ]);
    } else {
        try {
            Mail::to($email)->send(new MatchRequestStatusMail($application, $ad, 'Approved'));
            Log::info("Approval email sent", ['to' => $email, 'adsID' => $ad->adsID]);
        } catch (\Exception $e) {
            Log::error("Approval email failed: " . $e->getMessage(), ['to' => $email]);
        }
    }

    return redirect()->back()->with('swal_success', 'Application approved successfully!');
}

public function reject($id)
{
    $application = Applications::with('customer.user')->findOrFail($id);

    $ad = Advertisement::where('adsID', $application->adsID)->first();
    if (!$ad) {
        return redirect()->back()->with('error', 'Advertisement not found.');
    }

    $application->status = 'Rejected';
    $application->save();

    // ✅ Send email (FIXED FIELD NAME)
    $email = optional(optional($application->customer)->user)->user_Email;

    if (!$email) {
        Log::warning("Rejection email not sent: missing user_Email", [
            'application_id' => $application->id ?? null,
            'customer_id' => optional($application->customer)->customerID ?? null,
            'userID' => optional($application->customer)->userID ?? null,
        ]);
    } else {
        try {
            Mail::to($email)->send(new MatchRequestStatusMail($application, $ad, 'Rejected'));
            Log::info("Rejection email sent", ['to' => $email, 'adsID' => $ad->adsID]);
        } catch (\Exception $e) {
            Log::error("Rejection email failed: " . $e->getMessage(), ['to' => $email]);
        }
    }

    return redirect()->back()->with('swal_success', 'Application rejected successfully!');
}
}
