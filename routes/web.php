<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\registerController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\bookingController;
use App\Http\Controllers\PaymentController; 
use App\Http\Controllers\rentalController;
use App\Http\Controllers\matchController;
use App\Http\Controllers\ratingController;
use App\Http\Controllers\reportController; 
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\AuthSession;

// =================================================================
// PUBLIC ROUTES (Visible to Everyone)
// =================================================================

Route::view('/', 'landing.home')->name('home');
Route::view('/about', 'landing.about')->name('about');
Route::get('/latest-reviews', [ratingController::class, 'getLatestReviews'])->name('reviews.latest');
Route::get('/_debug/mail', function () {
    return response()->json([
        'php_version' => PHP_VERSION,
        'mail_default' => config('mail.default'),
        'brevo_transport_factory_exists' => class_exists(\Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory::class),
        'http_client_exists' => class_exists(\Symfony\Component\HttpClient\HttpClient::class),
        'brevo_key_is_set' => !empty(config('services.brevo.key')),
        'loaded_mailers' => array_keys(config('mail.mailers', [])),
    ]);
});

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
// --- NEW: Rental Balance Payment Callbacks ---
Route::get('/payment/rental/balance/return', [PaymentController::class, 'rentalBalanceReturn'])->name('payment.rental.balance.return');
Route::post('/payment/rental/balance/callback', [PaymentController::class, 'rentalBalanceCallback'])->name('payment.rental.balance.callback');
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
    // --- Forgot Password Routes ---
    Route::get('/forgot-password', [loginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [loginController::class, 'sendResetLinkEmail'])->name('password.email');

    // --- NEW: Reset Password Routes (The link from the email) ---
    Route::get('/reset-password/{token}', [loginController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [loginController::class, 'updatePassword'])->name('password.update');
});

Route::get('/register', [registerController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [registerController::class, 'register'])->name('register.submit');


// =================================================================
// AUTHENTICATED ROUTES (All users must be logged in)
// =================================================================

Route::middleware([AuthSession::class, PreventBackHistory::class])->group(function () {

    // --- Logout ---
    Route::get('/logout', [loginController::class, 'logout'])->name('logout');

    Route::get('/set-password', [GoogleController::class, 'showSetPasswordForm'])
    ->name('google.password.form');

    Route::post('/set-password', [GoogleController::class, 'saveSetPassword'])
    ->name('google.password.save');


    // --- All Profile & Dashboard Routes ---
    Route::prefix('profile')->group(function () {
        
        // --- Staff Routes ---
        Route::middleware('role:staff')->group(function () {
            Route::get('/staff/dashboard', [profileController::class, 'dashboardStaff'])->name('staff.dashboard');
            Route::get('/staff/profile', [profileController::class, 'showStaffProfile'])->name('staff.profile');
            Route::get('/staff/profile/edit', [profileController::class, 'editStaff'])->name('staff.profile.edit');
            Route::post('/staff/profile/update', [profileController::class, 'updateStaff'])->name('staff.profile.update');
            Route::delete('/staff/profile/delete', [profileController::class, 'destroyStaff'])->name('staff.profile.delete');
            Route::post('/staff/password/update', [profileController::class, 'updateStaffPassword'])->name('staff.password.update');
        });

        // --- Admin Routes ---
        Route::middleware('role:administrator')->group(function () {
            Route::get('/administrator/dashboard', [profileController::class, 'dashboardAdmin'])->name('administrator.dashboard');
            Route::get('/admin/profile', [profileController::class, 'showAdminProfile'])->name('admin.profile');
            Route::get('/admin/profile/edit', [profileController::class, 'editAdmin'])->name('admin.profile.edit');
            Route::put('/admin/profile/update', [profileController::class, 'updateAdmin'])->name('admin.profile.update');
            Route::delete('/admin/profile/delete', [profileController::class, 'destroyAdmin'])->name('admin.profile.delete');
            Route::post('/admin/password/update', [profileController::class, 'updateAdminPassword'])->name('admin.password.update');
        });
        
        // --- Customer Routes ---
        Route::middleware('role:customer')->group(function () {
            Route::get('/customer/dashboard', [profileController::class, 'dashboard'])->name('customer.dashboard');
            Route::get('/customer/profile', [profileController::class, 'showCustomerProfile'])->name('customer.profile');
            Route::get('/customer/profile/edit', [profileController::class, 'edit'])->name('customer.profile.edit');
            Route::post('/customer/profile/update', [profileController::class, 'update'])->name('customer.profile.update');
            Route::delete('/customer/profile/delete', [profileController::class, 'destroy'])->name('customer.profile.delete');
            Route::post('/customer/password/update', [profileController::class, 'updateCustomerPassword'])->name('customer.password.update');
        });
    });

    // --- CUSTOMER: Booking Routes ---
    Route::prefix('booking')->name('booking.')->middleware('role:customer')->group(function () {
        Route::get('/view', [bookingController::class, 'viewBookings'])->name('view');
        Route::get('/mini', [bookingController::class, 'showMiniFieldBooking'])->name('mini');
        Route::get('/field/{fieldID}', [bookingController::class, 'showBookingPage'])->name('page');
        Route::post('/book', [bookingController::class, 'bookSlot'])->name('book');
        Route::post('/store', [bookingController::class, 'store'])->name('store');
        Route::get('/{slotID}/add', [bookingController::class, 'add'])->name('add');
        Route::get('/{booking}/edit', [bookingController::class, 'edit'])->name('edit');
        Route::get('/{bookingID}/confirmation', [bookingController::class, 'confirmation'])->name('confirmation');
        Route::put('/{bookingID}', [bookingController::class, 'update'])->name('update');
        Route::delete('/{bookingID}/cancel', [bookingController::class, 'destroy'])->name('cancel');
        Route::get('/{fieldID}/slots-json', [bookingController::class, 'getSlotsJson'])->name('slots.json');
    });

    // --- CUSTOMER: Payment Route ---
    Route::middleware('role:customer')->group(function () {
        Route::get('/payment/create/{bookingID}', [PaymentController::class, 'createPayment'])->name('payment.create');
        Route::get('/payment/balance/{bookingID}', [PaymentController::class, 'createBalancePayment'])->name('payment.balance.create');
Route::get('/payment/rental/balance/{rentalID}', [PaymentController::class, 'createRentalBalancePayment'])->name('payment.rental.balance.create');    });

    // --- CUSTOMER: Rental Routes ---
    Route::prefix('customer/rental')->name('customer.rental.')->middleware('role:customer')->group(function() {
        Route::get('/', [rentalController::class, 'indexCustomer'])->name('main');
        Route::get('/rent/{itemID}', [rentalController::class, 'rentPage'])->name('rent');
        Route::post('/rent/{itemID}', [rentalController::class, 'processRent'])->name('process');
        Route::get('/check-availability/{itemID}', [rentalController::class, 'checkAvailability'])->name('checkAvailability');
        Route::get('/confirmation/{rentalID}', [rentalController::class, 'showConfirmation'])->name('confirmation');
        Route::post('/confirm', [rentalController::class, 'confirmBooking'])->name('confirm');
        Route::get('/rental/{rentalID}/edit', [rentalController::class, 'editPage'])->name('edit');
        Route::post('/rental/{rentalID}/update', [rentalController::class, 'updateRent'])->name('update');
        Route::delete('/rental/{rentalID}', [rentalController::class, 'destroyCustomer'])->name('destroy');
        Route::post('/{rentalID}/pay', [PaymentController::class, 'createRentalPayment'])->name('pay');
        Route::get('/history', [rentalController::class, 'viewRentalHistory'])->name('history');
        Route::post('/{rentalID}/request-approval', [rentalController::class, 'requestApproval'])->name('requestApproval');
    });

    // --- CUSTOMER: Matchmaking Routes ---
    Route::prefix('matchmaking')->name('matchmaking.')->middleware('role:customer')->group(function () {
        Route::get('/personal', [matchController::class, 'personalAds'])->name('personal');
        Route::get('/other', [matchController::class, 'otherAds'])->name('other');
        Route::get('/add', fn() => view('Matchmaking.addOfferPage'))->name('add');
        Route::post('/store', [matchController::class, 'store'])->name('store');
        Route::get('/view/{adsID}', [matchController::class, 'view'])->name('view');
        Route::get('/edit/{adsID}', [matchController::class, 'edit'])->name('edit');
        Route::post('/update/{adsID}', [matchController::class, 'update'])->name('update');
        Route::delete('/destroy/{adsID}', [matchController::class, 'destroy'])->name('destroy');
        Route::get('/join/{adsID}', [matchController::class, 'joinForm'])->name('joinForm');
        Route::post('/join/{adsID}', [matchController::class, 'joinStore'])->name('joinStore');
    });

    // --- CUSTOMER: Matchmaking Application Routes ---
    Route::middleware('role:customer')->group(function () {
        Route::post('/applications/{id}/accept', [matchController::class, 'accept'])->name('applications.accept');
        Route::post('/applications/{id}/reject', [matchController::class, 'reject'])->name('applications.reject');
    });

   // --- CUSTOMER: Rating & Review Routes ---
    Route::prefix('customer/rating')->name('customer.rating.')->middleware('role:customer')->group(function () {
        Route::get('/', [ratingController::class, 'showCustomerRatings'])->name('main');
        Route::get('/add', [ratingController::class, 'showAddReviewForm'])->name('add');
        Route::post('/add', [ratingController::class, 'addNewReview'])->name('store');
        Route::get('/edit/{ratingID}', [ratingController::class, 'showEditReviewForm'])->name('edit');
        Route::post('/update/{ratingID}', [ratingController::class, 'updateReview'])->name('update');
        Route::get('/delete/{ratingID}', [ratingController::class, 'deleteReview'])->name('delete');
        Route::get('/booking/{bookingID}', [ratingController::class, 'rateBooking'])->name('booking');
        Route::get('/rental/{rentalID}', [ratingController::class, 'rateRental'])->name('rental');
        Route::post('/store-specific', [ratingController::class, 'storeSpecificReview'])->name('store_specific');
    });

    // --- ADMIN: Booking Routes ---
    Route::prefix('admin/booking')->name('admin.booking.')->middleware('role:administrator')->group(function () {
        Route::get('/manage', [bookingController::class, 'showBookingPage'])->name('manage');
        Route::get('/view-all', [bookingController::class, 'viewBookings'])->name('viewAll');
        Route::get('/mini', [bookingController::class, 'showMiniFieldBooking'])->name('mini');
        Route::get('/{slotID}/add', [bookingController::class, 'add'])->name('add');
        Route::post('/store', [bookingController::class, 'store'])->name('store');
        Route::get('/{booking}/edit', [bookingController::class, 'edit'])->name('edit');
        Route::put('/{bookingID}', [bookingController::class, 'update'])->name('update');
        Route::delete('/{bookingID}/cancel', [bookingController::class, 'destroy'])->name('cancel');
        Route::get('/{fieldID}/slots-json', [bookingController::class, 'getSlotsJson'])->name('slots.json');
        Route::post('/{bookingID}/mark-completed', [PaymentController::class, 'markAsCompleted'])
        ->name('payment.markCompleted');
    });

    // --- ADMIN: Rental Route ---
    Route::get('/admin/rentals/current', [rentalController::class, 'viewCurrentAdmin'])->name('admin.rentals.current')->middleware('role:administrator');
    
    // --- ADMIN: Rating & Review Routes ---
    Route::get('/admin/ratings', [ratingController::class, 'showCustomerRatings'])->name('admin.rating.view')->middleware('role:administrator');
    // --- ADMIN: Review Moderation Routes ---
    Route::middleware('role:administrator')->prefix('admin/reviews')->group(function () {
        // View all flagged/under-review reviews
        Route::get('/moderation', [ratingController::class, 'adminModeration'])->name('admin.reviews.moderation');

        // Approve a flagged review
        Route::post('/{review}/approve', [ratingController::class, 'approveReview'])->name('admin.reviews.approve');

        // Remove a flagged review
        Route::post('/{review}/remove', [ratingController::class, 'removeReview'])->name('admin.reviews.remove');
    });

    // --- ADMIN: Report Routes ---
    Route::prefix('admin/reports')->name('admin.reports.')->middleware('role:administrator')->group(function () {
        Route::get('/', [reportController::class, 'index'])->name('index');
        Route::get('/create', [reportController::class, 'create'])->name('create');
        Route::get('/show', [reportController::class, 'show'])->name('show');
        Route::post('/publish', [reportController::class, 'publish'])->name('publish');
        Route::get('/published', [reportController::class, 'publishedList'])->name('published');
        Route::get('/forecast', [reportController::class, 'getBookingForecast'])->name('forecast');
    });

    // --- ADMIN: Staff Management ---
    Route::middleware('role:administrator')->group(function () {
        Route::get('/staff/register', [registerController::class, 'showStaffRegistrationForm'])->name('staff.register');
        Route::post('/register/staff', [registerController::class, 'registerStaff'])->name('staff.register.store');
    });

    // --- STAFF: Booking Routes ---
    Route::prefix('staff/booking')->name('staff.booking.')->middleware('role:staff')->group(function () {
        Route::get('/manage', [bookingController::class, 'showBookingPage'])->name('manage');
        Route::get('/view-all', [bookingController::class, 'viewBookings'])->name('viewAll');
        Route::get('/mini', [bookingController::class, 'showMiniFieldBooking'])->name('mini');
        Route::get('/{slotID}/add', [bookingController::class, 'add'])->name('add');
        Route::post('/store', [bookingController::class, 'store'])->name('store');
        Route::get('/{booking}/edit', [bookingController::class, 'edit'])->name('edit');
        Route::put('/{bookingID}', [bookingController::class, 'update'])->name('update');
        Route::delete('/{bookingID}/cancel', [bookingController::class, 'destroy'])->name('cancel');
        Route::get('/{fieldID}/slots-json', [bookingController::class, 'getSlotsJson'])->name('slots.json');
    });

    // --- STAFF: Payment Routes ---
    Route::prefix('staff/payment')->name('staff.payment.')->middleware('role:staff')->group(function () {
        Route::post('/mark-completed/{bookingID}', [PaymentController::class, 'markAsCompleted'])->name('markCompleted');
        Route::post('/mark-rental-completed/{rentalID}', [PaymentController::class, 'markRentalAsCompleted'])->name('markRentalCompleted');
    });
    
    // --- STAFF: Rating & Review Routes ---
    Route::get('/staff/ratings', [ratingController::class, 'showCustomerRatings'])->name('staff.rating.view')->middleware('role:staff');

    // --- STAFF: Report Routes ---
    Route::prefix('staff/reports')->name('staff.reports.')->middleware('role:staff')->group(function () {
        Route::get('/', [reportController::class, 'index'])->name('index');
        Route::get('/create', [reportController::class, 'create'])->name('create');
        Route::get('/show', [reportController::class, 'show'])->name('show');
        Route::post('/publish', [reportController::class, 'publish'])->name('publish');
        Route::get('/published', [reportController::class, 'publishedList'])->name('published');
        Route::get('/forecast', [reportController::class, 'getBookingForecast'])->name('forecast');
    });

    // --- STAFF: Rental Routes ---
    Route::prefix('staff/rental')->name('staff.rental.')->middleware('role:staff')->group(function () {
        Route::get('/', [rentalController::class, 'index'])->name('main');
        Route::get('/add', fn() => view('Rental.staff.addPage'))->name('add');
        Route::post('/store', [rentalController::class, 'store'])->name('store');
        Route::get('/edit/{itemID}', [rentalController::class, 'edit'])->name('edit');
        Route::put('/update/{itemID}', [rentalController::class, 'update'])->name('update');
        Route::delete('/delete/{itemID}', [rentalController::class, 'destroy'])->name('delete');
    });
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/rentals/current', [rentalController::class, 'viewCurrent'])->name('staff.rentals.current');
        Route::get('/staff/rentals/return-approval', [rentalController::class, 'viewReturnApprovals'])->name('staff.rentals.returnApproval');
        Route::post('/staff/rentals/return-approval/{rentalID}', [rentalController::class, 'updateReturnApproval'])->name('staff.rentals.updateReturnApproval');
    });

});
