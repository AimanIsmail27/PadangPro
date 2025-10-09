<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Rating;
use App\Models\Customer; 
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Advertisement;
use App\Models\Applications;
use Carbon\Carbon; // ✅ Import Carbon for timezone handling

class RatingController extends Controller
{
    /**
     * Display the main customer rating and review page.
     */
    public function showCustomerRatings(Request $request)
    {
        $currentUserID = $request->session()->get('user_id');

        $yourSubmittedReview = Rating::with('customer')->where('userID', $currentUserID)->first();
        $allRatings = Rating::with('customer')->get();

        // ======================
        // Sorting Logic
        // ======================
        $sortBy = $request->get('filter', 'latest');

        switch ($sortBy) {
            case 'oldest':
                $allRatings = $allRatings->sortBy('review_Date');
                break;
            case 'high_rating':
                $allRatings = $allRatings->sortByDesc('rating_Score');
                break;
            case 'low_rating':
                $allRatings = $allRatings->sortBy('rating_Score');
                break;
            default: // latest
                $allRatings = $allRatings->sortByDesc('review_Date');
                break;
        }

        $allRatings = $allRatings->values();

        // ======================
        // Pagination Setup
        // ======================
        $perPage = 5;
        $page = $request->get('page', 1);
        $items = $allRatings->forPage($page, $perPage);
        $paginatedRatings = new LengthAwarePaginator(
            $items,
            $allRatings->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('Rating.customer.MainRatingPage', [
            'yourSubmittedReview' => $yourSubmittedReview,
            'allRatings' => $paginatedRatings,
            'currentSort' => $sortBy,
        ]);
    }

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

        // ✅ Retrieve related customerID
        $customer = Customer::where('userID', $currentUserID)->first();
        $customerID = $customer ? $customer->customerID : null;

        // ✅ Eligibility Check
        $hasBooking = Booking::where('userID', $currentUserID)
            ->where('booking_Status', 'completed')
            ->exists();

        $hasRental = Rental::where('userID', $currentUserID)
            ->where('rental_Status', 'paid')
            ->exists();

        $hasAdvertisement = false;
        $hasApplication = false;

        if ($customerID) {
            $hasAdvertisement = Advertisement::where('customerID', $customerID)->exists();
            $hasApplication = Applications::where('customerID', $customerID)->exists();
        }

        if (!$hasBooking && !$hasRental && !$hasAdvertisement && !$hasApplication) {
            return redirect()->back()->with('error', 'You must have at least one completed activity (booking, rental, or matchmaking) before posting a review.');
        }

        // ✅ Validate Input
        $validated = $request->validate([
            'rating_Score' => 'required|integer|min:1|max:5',
            'review_Given' => 'required|string|max:255',
        ]);

        // ✅ Kuala Lumpur timezone
        $nowKL = Carbon::now('Asia/Kuala_Lumpur');

        // ✅ Generate unique ratingID
        $uniqueID = 'RAT' . strtoupper(uniqid());

        // ✅ Store the review
        Rating::create([
            'ratingID' => $uniqueID,
            'rating_Score' => $validated['rating_Score'],
            'review_Given' => $validated['review_Given'],
            'review_Date' => $nowKL->toDateString(),
            'review_Time' => $nowKL->format('Y-m-d H:i:s'),
            'userID' => $currentUserID,
        ]);

        return redirect()->route('customer.rating.main')
            ->with('success', 'Your review has been submitted successfully!');
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

        // ✅ Kuala Lumpur timezone for update
        $nowKL = Carbon::now('Asia/Kuala_Lumpur');

        $review->update([
            'rating_Score' => $validated['rating_Score'],
            'review_Given' => $validated['review_Given'],
            'review_Date' => $nowKL->toDateString(),
            'review_Time' => $nowKL->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('customer.rating.main')->with('success', 'Your review has been updated successfully!');
    }

    /**
     * Delete user's own review.
     */
    public function deleteReview($ratingID, Request $request)
    {
        $currentUserID = $request->session()->get('user_id');

        $review = Rating::where('ratingID', $ratingID)
                        ->where('userID', $currentUserID)
                        ->first();

        if (!$review) {
            return redirect()->back()->with('error', 'Review not found or you are not authorized to delete it.');
        }

        $review->delete();

        return redirect()->route('customer.rating.main')
                         ->with('success', 'Your review has been deleted successfully.');
    }
}
