<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Controllers\PaymentController; 
use App\Http\Middleware\AuthSession; // Use your existing middleware

// Root page (redirect to login)
Route::get('/', function () {
    return redirect()->route('login');
});

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

