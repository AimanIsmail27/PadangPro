<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RatingController extends Controller
{
    /**
     * Display the main customer rating and review page with dummy data.
     */
    public function showCustomerRatings(Request $request)
    {
        // Simulated logged-in user ID
        $currentUserID = 'U001';

        // Dummy data for the user's own submitted review
        $yourSubmittedReview = [
            'ratingID' => 'R001',
            'rating_Score' => 4,
            'review_Given' => 'Great experience! Booking was smooth and the field was well maintained.',
            'review_Date' => '2025-10-06',
            'review_Time' => '2025-10-06 14:30:00',
            'userID' => $currentUserID,
        ];

        // Dummy data for all system-wide ratings
        $allRatings = collect([
            ['ratingID' => 'R002', 'rating_Score' => 5, 'review_Given' => 'Amazing service and good facilities!', 'review_Date' => '2025-10-05', 'review_Time' => '2025-10-05 09:15:00', 'userID' => 'U002'],
            ['ratingID' => 'R003', 'rating_Score' => 3, 'review_Given' => 'It was okay, but the lighting could be better.', 'review_Date' => '2025-10-04', 'review_Time' => '2025-10-04 18:45:00', 'userID' => 'U003'],
            ['ratingID' => 'R004', 'rating_Score' => 5, 'review_Given' => 'Best pitch I’ve ever played on!', 'review_Date' => '2025-10-03', 'review_Time' => '2025-10-03 20:00:00', 'userID' => 'U004'],
            ['ratingID' => 'R005', 'rating_Score' => 2, 'review_Given' => 'Ground was slippery and dirty.', 'review_Date' => '2025-10-02', 'review_Time' => '2025-10-02 13:20:00', 'userID' => 'U005'],
            ['ratingID' => 'R006', 'rating_Score' => 4, 'review_Given' => 'Friendly staff and easy process!', 'review_Date' => '2025-10-01', 'review_Time' => '2025-10-01 16:40:00', 'userID' => 'U006'],
            ['ratingID' => 'R007', 'rating_Score' => 3, 'review_Given' => 'Average experience, could improve the booking UI.', 'review_Date' => '2025-09-30', 'review_Time' => '2025-09-30 10:50:00', 'userID' => 'U007'],
            ['ratingID' => 'R008', 'rating_Score' => 5, 'review_Given' => 'Fantastic atmosphere!', 'review_Date' => '2025-09-29', 'review_Time' => '2025-09-29 12:00:00', 'userID' => 'U008'],
            ['ratingID' => 'R009', 'rating_Score' => 1, 'review_Given' => 'Very poor management. Not recommended.', 'review_Date' => '2025-09-28', 'review_Time' => '2025-09-28 17:10:00', 'userID' => 'U009'],
            ['ratingID' => 'R010', 'rating_Score' => 4, 'review_Given' => 'Good facilities and smooth process overall.', 'review_Date' => '2025-09-27', 'review_Time' => '2025-09-27 11:00:00', 'userID' => 'U010'],
            ['ratingID' => 'R011', 'rating_Score' => 2, 'review_Given' => 'Was okay, but staff seemed unorganized.', 'review_Date' => '2025-09-26', 'review_Time' => '2025-09-26 15:30:00', 'userID' => 'U011'],
        ]);

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

        // Reindex after sorting
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

        // ======================
        // Return to Blade View
        // ======================
        return view('Rating.customer.MainRatingPage', [
            'yourSubmittedReview' => $yourSubmittedReview,
            'allRatings' => $paginatedRatings,
            'currentSort' => $sortBy,
        ]);
    }
}
