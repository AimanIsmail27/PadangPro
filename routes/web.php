<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Controllers\PaymentController; 
use App\Http\Controllers\RentalController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\RatingController;
use App\Http\Middleware\AuthSession; // Use your existing middleware


Route::view('/', 'landing.home')->name('home');
Route::view('/about', 'landing.about')->name('about');


// Root page (redirect to login)
//Route::get('/', function () {
  //  return redirect()->route('login');
//});

// Routes that should prevent going back after login/logout
Route::middleware([PreventBackHistory::class])->group(function () {

    // Login page with session check
    Route::get('/login', function () {
        if (session()->has('user_id') && session()->has('user_type')) {
            return redirect()->route(session('user_type') . '.dashboard');
        }
        return view('auth.login');
    })->name('login');

    // Handle login
    Route::post('/login', [loginController::class, 'login'])->name('login.post');

    // Logout
    Route::get('/logout', [loginController::class, 'logout'])->name('logout');
});

// Secure dashboards with session check + prevent back
Route::middleware([PreventBackHistory::class, AuthSession::class])
    ->prefix('profile')
    ->group(function () {
        Route::get('/staff/dashboard', function () {
            return view('Profile.staff.dashboard');
        })->name('staff.dashboard');

        Route::get('/staff/profile', [\App\Http\Controllers\ProfileController::class, 'showStaffProfile'])->name('staff.profile');

        Route::get('/administrator/dashboard', function () {
            return view('Profile.admin.dashboard');
        })->name('administrator.dashboard');

        Route::get('/admin/profile', [ProfileController::class, 'showAdminProfile'])->name('admin.profile');
        Route::get('/admin/profile/edit', [ProfileController::class, 'editAdmin'])->name('admin.profile.edit');
        Route::put('/admin/profile/update', [ProfileController::class, 'updateAdmin'])->name('admin.profile.update');
        Route::delete('/admin/profile/delete', [ProfileController::class, 'destroyAdmin'])->name('admin.profile.delete');
        
        Route::get('/customer/dashboard', function () {
            return view('Profile.customer.dashboard');
        })->name('customer.dashboard');

        Route::get('/customer/profile', [\App\Http\Controllers\ProfileController::class, 'showCustomerProfile'])->name('customer.profile');
        Route::get('/customer/profile/edit', [ProfileController::class, 'edit'])->name('customer.profile.edit');
        Route::post('/customer/profile/update', [ProfileController::class, 'update'])->name('customer.profile.update');
        Route::delete('/customer/profile/delete', [ProfileController::class, 'destroy'])->name('customer.profile.delete');


    });

// Google Login
Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Registration (not affected by prevent-back-history)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Staff registration (admin only)
Route::get('/staff/register', [RegisterController::class, 'showStaffRegistrationForm'])->name('staff.register');
Route::post('/register/staff', [RegisterController::class, 'registerStaff'])->name('staff.register.store');

Route::get('/booking/view', [BookingController::class, 'viewBookings'])->name('booking.view');

// Mini Pitch booking page
Route::get('/booking/mini', [BookingController::class, 'showMiniFieldBooking'])->name('booking.mini');


Route::get('/booking/{fieldID}', [BookingController::class, 'showBookingPage'])
    ->name('booking.page');

// Handle AJAX booking request
Route::post('/booking/book', [BookingController::class, 'bookSlot'])
    ->name('booking.book');



Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');

Route::get('/booking/{slotID}/add', [BookingController::class, 'add'])->name('booking.add');

Route::get('/booking/{booking}/edit', [BookingController::class, 'edit'])->name('booking.edit');

Route::post('/booking/{bookingID}/payment', [BookingController::class, 'payment'])->name('booking.payment');

Route::get('/booking/{bookingID}/confirmation', [BookingController::class, 'confirmation'])
     ->name('booking.confirmation');

Route::put('/booking/{bookingID}', [BookingController::class, 'update'])->name('booking.update');


Route::delete('/booking/{bookingID}/cancel', [BookingController::class, 'destroy'])->name('booking.cancel');



// Create ToyyibPay bill and redirect
Route::get('/payment/create/{bookingID}', [PaymentController::class, 'createPayment'])->name('payment.create');

// Handle return (user-facing after payment)
Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');

// Handle callback (server-to-server)
Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');

// JSON feed for FullCalendar
Route::get('/booking/{fieldID}/slots-json', [BookingController::class, 'getSlotsJson'])->name('booking.slots.json');
Route::get('/booking/mini/slots-json', [BookingController::class, 'getMiniSlotsJson'])->name('booking.mini.slots.json');


// Staff Rental Main Page
Route::get('/staff/rental', [RentalController::class, 'index'])->name('staff.rental.main');

// Add Rental Item Page
Route::get('/staff/rental/add', function () {
    return view('Rental.staff.addPage');
})->name('staff.rental.add');


Route::get('/staff/rentals/current', [RentalController::class, 'viewCurrent'])->name('staff.rentals.current');

// Store Rental Item
Route::post('/staff/rental/store', [RentalController::class, 'store'])->name('staff.rental.store');

// Show all rentals with return_Status = requested
Route::get('/staff/rentals/return-approval', [RentalController::class, 'viewReturnApprovals'])
    ->name('staff.rentals.returnApproval');

// Handle staff approval/rejection
Route::post('/staff/rentals/return-approval/{rentalID}', [RentalController::class, 'updateReturnApproval'])
    ->name('staff.rentals.updateReturnApproval');

// Edit Item Page
Route::get('/staff/rental/edit/{itemID}', [RentalController::class, 'edit'])->name('staff.rental.edit');

// Update Item
Route::put('/staff/rental/update/{itemID}', [RentalController::class, 'update'])->name('staff.rental.update');

// Delete Rental Item
Route::delete('/staff/rental/delete/{itemID}', [RentalController::class, 'destroy'])->name('staff.rental.delete');


// ---------------- CUSTOMER RENTAL ROUTES ----------------
Route::prefix('customer/rental')->name('customer.rental.')->group(function() {
    Route::get('/', [RentalController::class, 'indexCustomer'])->name('main');
    Route::get('/rent/{itemID}', [RentalController::class, 'rentPage'])->name('rent');
    Route::post('/rent/{itemID}', [RentalController::class, 'processRent'])->name('process');
    Route::get('/check-availability/{itemID}', [RentalController::class, 'checkAvailability'])->name('checkAvailability');
    Route::get('/confirmation/{rentalID}', [RentalController::class, 'showConfirmation'])->name('confirmation');
    Route::post('/confirm', [RentalController::class, 'confirmBooking'])->name('confirm');
    Route::get('/rental/{rentalID}/edit', [RentalController::class, 'editPage'])->name('edit');
    Route::post('/rental/{rentalID}/update', [RentalController::class, 'updateRent'])->name('update');
    Route::delete('/rental/{rentalID}', [RentalController::class, 'destroyCustomer'])->name('destroy');
    Route::post('/{rentalID}/pay', [PaymentController::class, 'createRentalPayment'])->name('pay');
    Route::get('/payment/return', [PaymentController::class, 'rentalPaymentReturn'])->name('payment.return');
    Route::post('/payment/callback', [PaymentController::class, 'rentalPaymentCallback'])->name('payment.callback');
    
    // ✅ Fixed history route
    Route::get('/history', [RentalController::class, 'viewRentalHistory'])->name('history');

    // ✅ Fixed request-approval route
    Route::post('/{rentalID}/request-approval', [RentalController::class, 'requestApproval'])->name('requestApproval');
});

// Matchmaking

// Personal Matchmaking Ads Page (list of user's ads)
Route::get('/matchmaking/personal', [MatchController::class, 'personalAds'])
     ->name('matchmaking.personal');

// Other advertisements page (view other ads)
Route::get('/matchmaking/other', [MatchController::class, 'otherAds'])
     ->name('matchmaking.other');

// Show the form to create new ad
Route::get('/matchmaking/add', function () {
    return view('Matchmaking.addOfferPage');
})->name('matchmaking.add');

// Store new advertisement (form submission)
Route::post('/matchmaking/store', [MatchController::class, 'store'])
     ->name('matchmaking.store');

     // View a single advertisement
Route::get('/matchmaking/view/{adsID}', [MatchController::class, 'view'])
     ->name('matchmaking.view');

// Edit an advertisement
Route::get('/matchmaking/edit/{adsID}', [MatchController::class, 'edit'])
     ->name('matchmaking.edit');

// Update an advertisement
Route::post('/matchmaking/update/{adsID}', [MatchController::class, 'update'])->name('matchmaking.update');

// Delete an advertisement
Route::delete('/matchmaking/destroy/{adsID}', [MatchController::class, 'destroy'])
     ->name('matchmaking.destroy');

// Show form to join an advertisement
Route::get('/matchmaking/join/{adsID}', [MatchController::class, 'joinForm'])
    ->name('matchmaking.joinForm');

// Submit request
Route::post('/matchmaking/join/{adsID}', [MatchController::class, 'joinStore'])
    ->name('matchmaking.joinStore');

Route::post('/applications/{id}/accept', [MatchController::class, 'accept'])
    ->name('applications.accept');

Route::post('/applications/{id}/reject', [MatchController::class, 'reject'])
    ->name('applications.reject');

// Customer Rating Routes
Route::get('/customer/rating', [RatingController::class, 'showCustomerRatings'])
    ->name('customer.rating.main');

// Show Add Review Page
Route::get('/customer/rating/add', [RatingController::class, 'showAddReviewForm'])
    ->name('customer.rating.add');

// Submit New Review
Route::post('/customer/rating/add', [RatingController::class, 'addNewReview'])
    ->name('customer.rating.store');

Route::get('/customer/rating/edit/{ratingID}', [RatingController::class, 'showEditReviewForm'])->name('customer.rating.edit');
Route::post('/customer/rating/update/{ratingID}', [RatingController::class, 'updateReview'])->name('customer.rating.update');

Route::get('/customer/rating/delete/{ratingID}', [RatingController::class, 'deleteReview'])
    ->name('customer.rating.delete');

