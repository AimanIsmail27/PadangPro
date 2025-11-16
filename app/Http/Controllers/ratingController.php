<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// We no longer need the manual paginator
// use Illuminate\Pagination\LengthAwarePaginator; 
use App\Models\Rating;
use App\Models\Customer; 
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Advertisement;
use App\Models\Applications;
use Carbon\Carbon;

class RatingController extends Controller
{
    /**
     * Display the main rating and review page for either a customer or an admin.
     */
    public function showCustomerRatings(Request $request)
    {
        $currentUserID = $request->session()->get('user_id');
        $userType = $request->session()->get('user_type');

        // Start a query to fetch all ratings and eager load the related customer and user details
        $allRatingsQuery = Rating::with('customer.user');

        // ======================
        // EFFICIENT Sorting Logic (at database level)
        // ======================
        $sortBy = $request->get('filter', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $allRatingsQuery->orderBy('review_Date', 'asc')->orderBy('review_Time', 'asc');
                break;
            case 'high_rating':
                $allRatingsQuery->orderBy('rating_Score', 'desc');
                break;
            case 'low_rating':
                $allRatingsQuery->orderBy('rating_Score', 'asc');
                break;
            default: // 'latest'
                $allRatingsQuery->orderBy('review_Date', 'desc')->orderBy('review_Time', 'desc');
                break;
        }

        // ======================
        // EFFICIENT Pagination (at database level)
        // ======================
        // Paginate the results and append the filter query to the pagination links
        $paginatedRatings = $allRatingsQuery->paginate(5)->appends($request->query());

        // --- ROLE-SPECIFIC LOGIC ---
        if ($userType === 'administrator') {
            // Admin View: Simply show all ratings.
            return view('Rating.admin.MainRatingPage', [
                'allRatings' => $paginatedRatings,
                'currentSort' => $sortBy,
            ]);
        } 
         else if ($userType === 'staff') {
            // Admin View: Simply show all ratings.
            return view('Rating.staff.MainRatingPage', [
                'allRatings' => $paginatedRatings,
                'currentSort' => $sortBy,
            ]);
        }
        else {
            // Customer View: Also fetch their own review to display separately.
            $yourSubmittedReview = Rating::with('customer')->where('userID', $currentUserID)->first();
            
            return view('Rating.customer.MainRatingPage', [
                'yourSubmittedReview' => $yourSubmittedReview,
                'allRatings' => $paginatedRatings,
                'currentSort' => $sortBy,
            ]);
        }
    }

    // ======================================================================
    // ALL OTHER METHODS BELOW ARE CUSTOMER-SPECIFIC AND REMAIN UNCHANGED
    // ======================================================================

    /**
     * Show Add New Review Form
     */
    public function showAddReviewForm()
    {
        return view('Rating.customer.addPage');
    }

    /**
     * Store a new rating and review with eligibility check.
     */
    public function addNewReview(Request $request)
    {
        $currentUserID = $request->session()->get('user_id');

        $customer = Customer::where('userID', $currentUserID)->first();
        $customerID = $customer ? $customer->customerID : null;

        // Eligibility Check
        $hasBooking = Booking::where('userID', $currentUserID)->where('booking_Status', 'completed')->exists();
        $hasRental = Rental::where('userID', $currentUserID)->where('rental_Status', 'paid')->exists();
        $hasAdvertisement = false;
        $hasApplication = false;
        if ($customerID) {
            $hasAdvertisement = Advertisement::where('customerID', $customerID)->exists();
            $hasApplication = Applications::where('customerID', $customerID)->exists();
        }
        if (!$hasBooking && !$hasRental && !$hasAdvertisement && !$hasApplication) {
            return redirect()->back()->with('error', 'You must have at least one completed activity (booking, rental, or matchmaking) before posting a review.');
        }

        $validated = $request->validate([
            'rating_Score' => 'required|integer|min:1|max:5',
            'review_Given' => 'required|string|max:255',
        ]);

        $nowKL = Carbon::now('Asia/Kuala_Lumpur');
        $uniqueID = 'RAT' . strtoupper(uniqid());

        Rating::create([
            'ratingID' => $uniqueID,
            'rating_Score' => $validated['rating_Score'],
            'review_Given' => $validated['review_Given'],
            'review_Date' => $nowKL->toDateString(),
            'review_Time' => $nowKL->toDateTimeString(),
            'userID' => $currentUserID,
        ]);

        return redirect()->route('customer.rating.main')->with('success', 'Your review has been submitted successfully!');
    }

    /**
     * Show the edit review form for a specific rating.
     */
    public function showEditReviewForm($ratingID)
    {
        $review = Rating::where('ratingID', $ratingID)->first();
        if (!$review) {
            return redirect()->route('customer.rating.main')->with('error', 'Review not found.');
        }
        return view('Rating.customer.editPage', compact('review'));
    }

    /**
     * Handle review update request.
     */
    public function updateReview(Request $request, $ratingID)
    {
        $review = Rating::where('ratingID', $ratingID)->first();
        if (!$review) {
            return redirect()->route('customer.rating.main')->with('error', 'Review not found.');
        }

        $validated = $request->validate([
            'rating_Score' => 'required|integer|min:1|max:5',
            'review_Given' => 'required|string|max:255',
        ]);

        $nowKL = Carbon::now('Asia/Kuala_Lumpur');

        $review->update([
            'rating_Score' => $validated['rating_Score'],
            'review_Given' => $validated['review_Given'],
            'review_Date' => $nowKL->toDateString(),
            'review_Time' => $nowKL->toDateTimeString(),
        ]);

        return redirect()->route('customer.rating.main')->with('success', 'Your review has been updated successfully!');
    }

    /**
     * Delete user's own review.
     */
    public function deleteReview($ratingID, Request $request)
    {
        $currentUserID = $request->session()->get('user_id');
        $review = Rating::where('ratingID', $ratingID)->where('userID', $currentUserID)->first();

        if (!$review) {
            return redirect()->back()->with('error', 'Review not found or you are not authorized to delete it.');
        }

        $review->delete();

        return redirect()->route('customer.rating.main')->with('success', 'Your review has been deleted successfully.');
    }

   // In app/Http/Controllers/RatingController.php

public function getLatestReviews()
{
    // 1. Use the Rating MODEL to load the customer relationship
    $reviews = Rating::with('customer')
        ->whereNotNull('review_Given')   // Only get actual reviews
        ->where('review_Given', '!=', '') // Ensure review is not empty
        ->latest('review_Date')          // Get the newest first
        ->take(3)                        // Limit to 3
        ->get();

    // 2. Format the data for the JavaScript
    $formattedReviews = $reviews->map(function ($review) {
        
        // --- THIS IS THE NEW LOGIC ---
        $displayName = 'Anonymous Player'; // Default fallback

        if ($review->customer && !empty($review->customer->customer_FullName)) {
            // Split the full name by spaces
            $nameParts = explode(' ', $review->customer->customer_FullName);
            // Get just the first part (the first name)
            $displayName = $nameParts[0];
        }
        // --- END NEW LOGIC ---

        return [
            'rating_Score' => $review->rating_Score,
            'review_Given' => $review->review_Given,
            'review_Date' => \Carbon\Carbon::parse($review->review_Date)->format('d M Y'),
            
            // Use the new $displayName variable
            'customer_FullName' => $displayName 
        ];
    });

    return response()->json($formattedReviews);
}
}