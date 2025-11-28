<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Customer; 
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Advertisement;
use App\Models\Applications;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * Display the main rating and review page for customer, admin, or staff.
     */
    public function showCustomerRatings(Request $request)
    {
        $currentUserID = $request->session()->get('user_id');
        $userType = $request->session()->get('user_type');

        // Start a query to fetch all ratings
        $allRatingsQuery = Rating::with(['customer.user', 'booking.field', 'rental.item']);

        // Sorting Logic
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

        // Paginate the results
        $paginatedRatings = $allRatingsQuery->paginate(5)->appends($request->query());

        if ($userType === 'administrator' || $userType === 'staff') {
            // Admin/Staff View: Show all ratings
            $viewPath = 'Rating.' . $userType . '.MainRatingPage';
            return view($viewPath, [
                'allRatings' => $paginatedRatings,
                'currentSort' => $sortBy,
            ]);
        } else {
            // Customer View: Show "My Reviews" list for management
            // We fetch ALL reviews by this user so they can see/edit/delete them
            $myReviews = Rating::with(['booking.field', 'rental.item'])
                ->where('userID', $currentUserID)
                ->latest('review_Date')
                ->get();
            
            return view('Rating.customer.MainRatingPage', [
                'myReviews' => $myReviews, // Pass the collection of user's reviews
                'allRatings' => $paginatedRatings, // General list (optional, if you still want to show others')
                'currentSort' => $sortBy,
            ]);
        }
    }

    // =================================================================
    // NEW: TRANSACTION-SPECIFIC REVIEWS
    // =================================================================

    /**
     * Show form to rate a specific Booking.
     */
    public function rateBooking($bookingID)
    {
        $userId = session('user_id');
        
        // Verify ownership and status
        $booking = Booking::with('field')
            ->where('bookingID', $bookingID)
            ->where('userID', $userId)
            ->where('booking_Status', 'completed')
            ->firstOrFail();

        // Check if already rated
        if (Rating::where('bookingID', $bookingID)->exists()) {
            return redirect()->route('booking.view')->with('error', 'You have already rated this booking.');
        }

        return view('Rating.customer.createSpecific', [
            'target' => $booking,
            'type' => 'booking',
            'name' => $booking->field->field_Label
        ]);
    }

    /**
     * Show form to rate a specific Rental.
     */
    public function rateRental($rentalID)
    {
        $userId = session('user_id');

        // Verify ownership and status
        $rental = Rental::with('item')
            ->where('rentalID', $rentalID)
            ->where('userID', $userId)
            ->where('rental_Status', 'completed')
            ->where('return_Status', 'approved')
            ->firstOrFail();

        // Check if already rated
        if (Rating::where('rentalID', $rentalID)->exists()) {
            return redirect()->route('customer.rental.history')->with('error', 'You have already rated this rental.');
        }

        return view('Rating.customer.createSpecific', [
            'target' => $rental,
            'type' => 'rental',
            'name' => $rental->item->item_Name
        ]);
    }

    /**
     * Store the specific review.
     */
    public function storeSpecificReview(Request $request)
    {
        $request->validate([
            'rating_Score' => 'required|integer|min:1|max:5',
            'review_Given' => 'required|string|max:255',
            'type'         => 'required|in:booking,rental',
            'id'           => 'required|string'
        ]);

        $userId = session('user_id');
        $nowKL = Carbon::now('Asia/Kuala_Lumpur');
        $uniqueID = 'RAT' . strtoupper(uniqid());

        $data = [
            'ratingID' => $uniqueID,
            'rating_Score' => $request->rating_Score,
            'review_Given' => $request->review_Given,
            'review_Date' => $nowKL->toDateString(),
            'review_Time' => $nowKL->toDateTimeString(),
            'userID' => $userId,
        ];

        // Attach the correct foreign key
        if ($request->type === 'booking') {
            $data['bookingID'] = $request->id;
            $redirectRoute = 'booking.view';
        } else {
            $data['rentalID'] = $request->id;
            $redirectRoute = 'customer.rental.history';
        }

        Rating::create($data);

        return redirect()->route($redirectRoute)->with('success', 'Thank you! Your review has been submitted.');
    }

    // =================================================================
    // EXISTING METHODS (Edit, Update, Delete, API)
    // =================================================================

    /**
     * Show the edit review form.
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

    /**
     * API for Landing Page
     */
    public function getLatestReviews()
    {
        $reviews = Rating::with('customer')
            ->whereNotNull('review_Given')
            ->where('review_Given', '!=', '')
            ->latest('review_Date')
            ->take(3)
            ->get();

        $formattedReviews = $reviews->map(function ($review) {
            $displayName = 'Anonymous Player';
            if ($review->customer && !empty($review->customer->customer_FullName)) {
                $nameParts = explode(' ', $review->customer->customer_FullName);
                $displayName = $nameParts[0];
            }
            return [
                'rating_Score' => $review->rating_Score,
                'review_Given' => $review->review_Given,
                'review_Date' => \Carbon\Carbon::parse($review->review_Date)->format('d M Y'),
                'customer_FullName' => $displayName 
            ];
        });

        return response()->json($formattedReviews);
    }
}