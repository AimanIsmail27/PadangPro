<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\Customer; // make sure Customer model exists
use App\Models\Applications;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


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

    /**
     * Store a new matchmaking advertisement.
     */
    public function store(Request $request)
    {
        $userId = session('user_id'); // current logged-in user

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to post an ad.');
        }

        // Get the customerID from userID
        $customer = Customer::where('userID', $userId)->first();
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer profile not found.');
        }

        // Validation rules
        $rules = [
            'ads_Name'        => 'required|string|max:255',
            'ads_Type'        => 'required|string|in:Additional Player,Opponent Search',
            'ads_Price'       => 'nullable|numeric|min:0',
            'ads_Description' => 'required|string|max:1000',
            'ads_SlotTime'    => 'required|date',
        ];

        if ($request->ads_Type === 'Additional Player') {
            $rules['ads_RequiredPosition'] = 'required|array|min:1';
            $rules['ads_RequiredPosition.*'] = 'string|max:10';
            $rules['ads_MaxPlayers'] = 'required|integer|min:1';
        }

        $request->validate($rules);

        // Generate unique adsID
        $adsID = 'ADS' . strtoupper(uniqid());

        $positions = $request->ads_Type === 'Additional Player' 
                     ? json_encode($request->ads_RequiredPosition) 
                     : null;

        Advertisement::create([
            'adsID'                => $adsID,
            'ads_Name'             => $request->ads_Name,
            'ads_Type'             => $request->ads_Type,
            'ads_Price'            => $request->ads_Price ?? 0,
            'ads_Description'      => $request->ads_Description,
            'ads_Status'           => 'Active',
            'ads_RequiredPosition' => $positions,
            'ads_MaxPlayers'       => $request->ads_Type === 'Additional Player' ? $request->ads_MaxPlayers : null,
            'ads_SlotTime'         => $request->ads_SlotTime,
            'customerID'           => $customer->customerID,
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
        // Convert ad slot time to Kuala Lumpur timezone
        $slotTime = Carbon::parse($ad->ads_SlotTime)->timezone('Asia/Kuala_Lumpur');
        $currentTime = Carbon::now('Asia/Kuala_Lumpur');

        // Check if slot time is in the past
        if ($currentTime->greaterThan($slotTime)) {
            $ad->ads_Status = 'Expired';
            continue; // skip other checks if expired
        }

        // Count approved applications
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

        // Safely decode ads_RequiredPosition
        $ad->ads_RequiredPosition = is_string($ad->ads_RequiredPosition)
            ? json_decode($ad->ads_RequiredPosition, true)
            : ($ad->ads_RequiredPosition ?? []);

        return view('Matchmaking.editOfferPage', compact('ad'));
    }

    /**
     * Update a specific advertisement.
     */
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

        // ✅ Validation
        $rules = [
            'ads_Name'        => 'required|string|max:255',
            'ads_Description' => 'required|string|max:1000',
            'ads_Price'       => 'nullable|numeric|min:0',
            'ads_SlotTime'    => 'required|date',
        ];

        if ($ad->ads_Type === 'Additional Player') {
            $rules['ads_RequiredPosition'] = 'nullable|array';
            $rules['ads_RequiredPosition.*'] = 'string|max:10';
            $rules['ads_MaxPlayers'] = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules);

        // ✅ Save data
        $ad->ads_Name = $validated['ads_Name'];
        $ad->ads_Description = $validated['ads_Description'];
        $ad->ads_Price = $validated['ads_Price'] ?? 0;
        $ad->ads_SlotTime = $validated['ads_SlotTime'];

        if ($ad->ads_Type === 'Additional Player') {
            $ad->ads_RequiredPosition = isset($validated['ads_RequiredPosition'])
                ? json_encode($validated['ads_RequiredPosition'])
                : json_encode([]);
            $ad->ads_MaxPlayers = $validated['ads_MaxPlayers'] ?? null;
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

    // Load applications with related customer data
    $requests = Applications::with('customer')
        ->where('adsID', $adsID)
        ->orderBy('created_at', 'asc')
        ->get();

    $maxPlayers = $ad->ads_Type === 'Additional Player' 
                  ? $ad->ads_MaxPlayers 
                  : 1; // For opponent search, max = 1

    // Count how many are already approved
    $approvedCount = $requests->where('status', 'approved')->count();

    // Attach canApprove flag based only on current approved count
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

   public function otherAds()
{
    $userId = session('user_id');

    if (!$userId) {
        return redirect()->route('login')->with('error', 'Please log in.');
    }

    // Get current customer linked to this user
    $customer = Customer::where('userID', $userId)->first();
    if (!$customer) {
        return redirect()->back()->with('error', 'Customer profile not found.');
    }

    $currentTime = \Carbon\Carbon::now('Asia/Kuala_Lumpur');

    // Get ads not created by this customer and slot time in the future
    $ads = Advertisement::where('customerID', '!=', $customer->customerID)
        ->where('ads_SlotTime', '>', $currentTime)
        ->orderBy('ads_SlotTime', 'asc')
        ->get();

    // Mark filled ads based on approved applications
    foreach ($ads as $ad) {
        $approvedCount = Applications::where('adsID', $ad->adsID)
            ->where('status', 'Approved')
            ->count();

        $maxPlayers = $ad->ads_Type === 'Additional Player' ? $ad->ads_MaxPlayers : 1;

        if ($approvedCount >= $maxPlayers) {
            $ad->ads_Status = 'Filled';
        }
    }

    // Fetch user’s applications (with ads relationship)
    $applications = Applications::where('customerID', $customer->customerID)
        ->whereHas('advertisement', function ($query) use ($currentTime) {
            $query->where('ads_SlotTime', '>', $currentTime);
        })
        ->with('advertisement')
        ->orderByDesc('created_at')
        ->get();

    // Update ad status in each application
    foreach ($applications as $application) {
        $reqAd = $application->advertisement;

        if ($reqAd) {
            $approvedCount = Applications::where('adsID', $reqAd->adsID)
                ->where('status', 'Approved')
                ->count();

            $maxPlayers = $reqAd->ads_Type === 'Additional Player' ? $reqAd->ads_MaxPlayers : 1;

            if ($approvedCount >= $maxPlayers) {
                $reqAd->ads_Status = 'Filled';
            }
        }
    }

    return view('Matchmaking.otherOfferPage', compact('ads', 'applications'));
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

        return view('Matchmaking.joinOfferPage', compact('ad'));
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

        // Check if already applied
        $existing = Applications::where('adsID', $adsID)
            ->where('customerID', $customer->customerID)
            ->first();

        if ($existing) {
            return redirect()->route('matchmaking.other')->with('error', 'You already applied to this ad.');
        }

        // Generate applicationID (custom, you can change style)
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
    $application = Applications::findOrFail($id);

    // Get related ad
    $ad = Advertisement::where('adsID', $application->adsID)->first();

    if (!$ad) {
        return redirect()->back()->with('error', 'Advertisement not found.');
    }

    if ($ad->ads_Type === 'Additional Player') {
        // Count already approved
        $approvedCount = Applications::where('adsID', $ad->adsID)
            ->where('status', 'Approved')
            ->count();

        if ($approvedCount >= $ad->ads_MaxPlayers) {
            return redirect()->back()->with('error', 'Maximum players already approved for this advertisement.');
        }
    }

    if ($ad->ads_Type === 'Opponent Search') {
        // For opponent search, only 1 team can be approved
        $approvedOpponent = Applications::where('adsID', $ad->adsID)
            ->where('status', 'Approved')
            ->exists();

        if ($approvedOpponent) {
            return redirect()->back()->with('error', 'An opponent has already been approved for this advertisement.');
        }
    }

    // If checks passed, approve
    $application->status = 'Approved';
    $application->save();

    return redirect()->back()->with('swal_success', 'Application approved successfully!');
}


public function reject($id)
{
    $application = Applications::findOrFail($id);
    $application->status = 'Rejected';
    $application->save();

    return redirect()->back()->with('swal_success', 'Application rejected successfully!');
}



}
