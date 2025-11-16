<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController; 
use App\Http\Controllers\RentalController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportController; // Fixed casing
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\AuthSession;

// =================================================================
// PUBLIC ROUTES (Visible to Everyone)
// =================================================================

Route::view('/', 'landing.home')->name('home');
Route::view('/about', 'landing.about')->name('about');
Route::get('/latest-reviews', [RatingController::class, 'getLatestReviews'])->name('reviews.latest');

// --- Google Login ---
Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// --- Payment Callbacks (Must be public/exempt from CSRF) ---
Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');
Route::get('customer/rental/payment/return', [PaymentController::class, 'rentalPaymentReturn'])->name('customer.rental.payment.return');
Route::post('customer/rental/payment/callback', [PaymentController::class, 'rentalPaymentCallback'])->name('customer.rental.payment.callback');
Route::get('/payment/return-balance', [PaymentController::class, 'paymentReturnBalance'])->name('payment.return.balance');
Route::post('/payment/callback-balance', [PaymentController::class, 'paymentCallbackBalance'])->name('payment.callback.balance');

// =================================================================
// AUTHENTICATION ROUTES (Login, Register, Logout)
// =================================================================

// --- Login & Registration (Guests) ---
Route::middleware([PreventBackHistory::class])->group(function () {
    Route::get('/login', function () {
        if (session()->has('user_id') && session()->has('user_type')) {
            // Updated logic to handle admin dashboard name
            $dashboardName = session('user_type') === 'administrator' ? 'administrator.dashboard' : session('user_type') . '.dashboard';
            return redirect()->route($dashboardName);
        }
        return view('auth.login');
    })->name('login');

    Route::post('/login', [loginController::class, 'login'])->name('login.post');
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');


// =================================================================
// AUTHENTICATED ROUTES (All users must be logged in)
// =================================================================

Route::middleware([AuthSession::class, PreventBackHistory::class])->group(function () {

    // --- Logout ---
    Route::get('/logout', [loginController::class, 'logout'])->name('logout');

    // --- All Profile & Dashboard Routes ---
    Route::prefix('profile')->group(function () {
        
        // --- Staff Routes ---
        Route::middleware('role:staff')->group(function () {
            Route::get('/staff/dashboard', [ProfileController::class, 'dashboardStaff'])->name('staff.dashboard');
            Route::get('/staff/profile', [ProfileController::class, 'showStaffProfile'])->name('staff.profile');
            Route::get('/staff/profile/edit', [ProfileController::class, 'editStaff'])->name('staff.profile.edit');
            Route::post('/staff/profile/update', [ProfileController::class, 'updateStaff'])->name('staff.profile.update');
            Route::delete('/staff/profile/delete', [ProfileController::class, 'destroyStaff'])->name('staff.profile.delete');
            Route::post('/staff/password/update', [ProfileController::class, 'updateStaffPassword'])->name('staff.password.update');
        });

        // --- Admin Routes ---
        Route::middleware('role:administrator')->group(function () {
            Route::get('/administrator/dashboard', [ProfileController::class, 'dashboardAdmin'])->name('administrator.dashboard');
            Route::get('/admin/profile', [ProfileController::class, 'showAdminProfile'])->name('admin.profile');
            Route::get('/admin/profile/edit', [ProfileController::class, 'editAdmin'])->name('admin.profile.edit');
            Route::put('/admin/profile/update', [ProfileController::class, 'updateAdmin'])->name('admin.profile.update');
            Route::delete('/admin/profile/delete', [ProfileController::class, 'destroyAdmin'])->name('admin.profile.delete');
            Route::post('/admin/password/update', [ProfileController::class, 'updateAdminPassword'])->name('admin.password.update');
        });
        
        // --- Customer Routes ---
        Route::middleware('role:customer')->group(function () {
            Route::get('/customer/dashboard', [ProfileController::class, 'dashboard'])->name('customer.dashboard');
            Route::get('/customer/profile', [ProfileController::class, 'showCustomerProfile'])->name('customer.profile');
            Route::get('/customer/profile/edit', [ProfileController::class, 'edit'])->name('customer.profile.edit');
            Route::post('/customer/profile/update', [ProfileController::class, 'update'])->name('customer.profile.update');
            Route::delete('/customer/profile/delete', [ProfileController::class, 'destroy'])->name('customer.profile.delete');
            Route::post('/customer/password/update', [ProfileController::class, 'updateCustomerPassword'])->name('customer.password.update');
        });
    });

    // --- CUSTOMER: Booking Routes ---
    Route::prefix('booking')->name('booking.')->middleware('role:customer')->group(function () {
        Route::get('/view', [BookingController::class, 'viewBookings'])->name('view');
        Route::get('/mini', [BookingController::class, 'showMiniFieldBooking'])->name('mini');
        Route::get('/field/{fieldID}', [BookingController::class, 'showBookingPage'])->name('page');
        Route::post('/book', [BookingController::class, 'bookSlot'])->name('book');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/{slotID}/add', [BookingController::class, 'add'])->name('add');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::get('/{bookingID}/confirmation', [BookingController::class, 'confirmation'])->name('confirmation');
        Route::put('/{bookingID}', [BookingController::class, 'update'])->name('update');
        Route::delete('/{bookingID}/cancel', [BookingController::class, 'destroy'])->name('cancel');
        Route::get('/{fieldID}/slots-json', [BookingController::class, 'getSlotsJson'])->name('slots.json');
    });

    // --- CUSTOMER: Payment Route ---
    Route::middleware('role:customer')->group(function () {
        Route::get('/payment/create/{bookingID}', [PaymentController::class, 'createPayment'])->name('payment.create');
        Route::get('/payment/balance/{bookingID}', [PaymentController::class, 'createBalancePayment'])->name('payment.balance.create');
    });

    // --- CUSTOMER: Rental Routes ---
    Route::prefix('customer/rental')->name('customer.rental.')->middleware('role:customer')->group(function() {
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
        Route::get('/history', [RentalController::class, 'viewRentalHistory'])->name('history');
        Route::post('/{rentalID}/request-approval', [RentalController::class, 'requestApproval'])->name('requestApproval');
    });

    // --- CUSTOMER: Matchmaking Routes ---
    Route::prefix('matchmaking')->name('matchmaking.')->middleware('role:customer')->group(function () {
        Route::get('/personal', [MatchController::class, 'personalAds'])->name('personal');
        Route::get('/other', [MatchController::class, 'otherAds'])->name('other');
        Route::get('/add', fn() => view('Matchmaking.addOfferPage'))->name('add');
        Route::post('/store', [MatchController::class, 'store'])->name('store');
        Route::get('/view/{adsID}', [MatchController::class, 'view'])->name('view');
        Route::get('/edit/{adsID}', [MatchController::class, 'edit'])->name('edit');
        Route::post('/update/{adsID}', [MatchController::class, 'update'])->name('update');
        Route::delete('/destroy/{adsID}', [MatchController::class, 'destroy'])->name('destroy');
        Route::get('/join/{adsID}', [MatchController::class, 'joinForm'])->name('joinForm');
        Route::post('/join/{adsID}', [MatchController::class, 'joinStore'])->name('joinStore');
    });

    // --- CUSTOMER: Matchmaking Application Routes ---
    Route::middleware('role:customer')->group(function () {
        Route::post('/applications/{id}/accept', [MatchController::class, 'accept'])->name('applications.accept');
        Route::post('/applications/{id}/reject', [MatchController::class, 'reject'])->name('applications.reject');
    });

    // --- CUSTOMER: Rating & Review Routes ---
    Route::prefix('customer/rating')->name('customer.rating.')->middleware('role:customer')->group(function () {
        Route::get('/', [RatingController::class, 'showCustomerRatings'])->name('main');
        Route::get('/add', [RatingController::class, 'showAddReviewForm'])->name('add');
        Route::post('/add', [RatingController::class, 'addNewReview'])->name('store');
        Route::get('/edit/{ratingID}', [RatingController::class, 'showEditReviewForm'])->name('edit');
        Route::post('/update/{ratingID}', [RatingController::class, 'updateReview'])->name('update');
        Route::get('/delete/{ratingID}', [RatingController::class, 'deleteReview'])->name('delete');
    });

    // --- ADMIN: Booking Routes ---
    Route::prefix('admin/booking')->name('admin.booking.')->middleware('role:administrator')->group(function () {
        Route::get('/manage', [BookingController::class, 'showBookingPage'])->name('manage');
        Route::get('/view-all', [BookingController::class, 'viewBookings'])->name('viewAll');
        Route::get('/mini', [BookingController::class, 'showMiniFieldBooking'])->name('mini');
        Route::get('/{slotID}/add', [BookingController::class, 'add'])->name('add');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{bookingID}', [BookingController::class, 'update'])->name('update');
        Route::delete('/{bookingID}/cancel', [BookingController::class, 'destroy'])->name('cancel');
        Route::get('/{fieldID}/slots-json', [BookingController::class, 'getSlotsJson'])->name('slots.json');
    });

    // --- ADMIN: Rental Route ---
    Route::get('/admin/rentals/current', [RentalController::class, 'viewCurrentAdmin'])->name('admin.rentals.current')->middleware('role:administrator');
    
    // --- ADMIN: Rating & Review Routes ---
    Route::get('/admin/ratings', [RatingController::class, 'showCustomerRatings'])->name('admin.rating.view')->middleware('role:administrator');
    
    // --- ADMIN: Report Routes ---
    Route::prefix('admin/reports')->name('admin.reports.')->middleware('role:administrator')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::get('/show', [ReportController::class, 'show'])->name('show');
        Route::post('/publish', [ReportController::class, 'publish'])->name('publish');
        Route::get('/published', [ReportController::class, 'publishedList'])->name('published');
        Route::get('/forecast', [ReportController::class, 'getBookingForecast'])->name('forecast');
    });

    // --- ADMIN: Staff Management ---
    Route::middleware('role:administrator')->group(function () {
        Route::get('/staff/register', [RegisterController::class, 'showStaffRegistrationForm'])->name('staff.register');
        Route::post('/register/staff', [RegisterController::class, 'registerStaff'])->name('staff.register.store');
    });

    // --- STAFF: Booking Routes ---
    Route::prefix('staff/booking')->name('staff.booking.')->middleware('role:staff')->group(function () {
        Route::get('/manage', [BookingController::class, 'showBookingPage'])->name('manage');
        Route::get('/view-all', [BookingController::class, 'viewBookings'])->name('viewAll');
        Route::get('/mini', [BookingController::class, 'showMiniFieldBooking'])->name('mini');
        Route::get('/{slotID}/add', [BookingController::class, 'add'])->name('add');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{bookingID}', [BookingController::class, 'update'])->name('update');
        Route::delete('/{bookingID}/cancel', [BookingController::class, 'destroy'])->name('cancel');
        Route::get('/{fieldID}/slots-json', [BookingController::class, 'getSlotsJson'])->name('slots.json');
    });

    // --- STAFF: Payment Routes ---
    Route::prefix('staff/payment')->name('staff.payment.')->middleware('role:staff')->group(function () {
        Route::post('/mark-completed/{bookingID}', [PaymentController::class, 'markAsCompleted'])->name('markCompleted');
    });
    
    // --- STAFF: Rating & Review Routes ---
    Route::get('/staff/ratings', [RatingController::class, 'showCustomerRatings'])->name('staff.rating.view')->middleware('role:staff');

    // --- STAFF: Report Routes ---
    Route::prefix('staff/reports')->name('staff.reports.')->middleware('role:staff')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::get('/show', [ReportController::class, 'show'])->name('show');
        Route::post('/publish', [ReportController::class, 'publish'])->name('publish');
        Route::get('/published', [ReportController::class, 'publishedList'])->name('published');
        Route::get('/forecast', [ReportController::class, 'getBookingForecast'])->name('forecast');
    });

    // --- STAFF: Rental Routes ---
    Route::prefix('staff/rental')->name('staff.rental.')->middleware('role:staff')->group(function () {
        Route::get('/', [RentalController::class, 'index'])->name('main');
        Route::get('/add', fn() => view('Rental.staff.addPage'))->name('add');
        Route::post('/store', [RentalController::class, 'store'])->name('store');
        Route::get('/edit/{itemID}', [RentalController::class, 'edit'])->name('edit');
        Route::put('/update/{itemID}', [RentalController::class, 'update'])->name('update');
        Route::delete('/delete/{itemID}', [RentalController::class, 'destroy'])->name('delete');
    });
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/rentals/current', [RentalController::class, 'viewCurrent'])->name('staff.rentals.current');
        Route::get('/staff/rentals/return-approval', [RentalController::class, 'viewReturnApprovals'])->name('staff.rentals.returnApproval');
        Route::post('/staff/rentals/return-approval/{rentalID}', [RentalController::class, 'updateReturnApproval'])->name('staff.rentals.updateReturnApproval');
    });

});