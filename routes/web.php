<?php
/**
 * Web Routes
 */

require_once __DIR__ . '/../app/Router.php';

$router = new Router();

// Home Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->get('/terms', [HomeController::class, 'terms']);
$router->get('/privacy', [HomeController::class, 'privacy']);

// Auth Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/forgotpassword', [AuthController::class, 'showForgotPassword']);
$router->post('/forgotpassword', [AuthController::class, 'forgotPassword']);
$router->get('/resetpassword', [AuthController::class, 'showResetPassword']);
$router->post('/resetpassword', [AuthController::class, 'resetPassword']);
$router->get('/profile', [AuthController::class, 'profile']);
$router->post('/profile', [AuthController::class, 'updateProfile']);
$router->post('/profile/password', [AuthController::class, 'changePassword']);

// Social Login
$router->get('/auth/facebook', [AuthController::class, 'redirectToFacebook']);
$router->get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
$router->get('/auth/google', [AuthController::class, 'redirectToGoogle']);
$router->get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Court Routes
$router->get('/courts', [CourtController::class, 'index']);
$router->get('/courts/type/{slug}', [CourtController::class, 'byType']);
$router->get('/courts/{id}', [CourtController::class, 'show']);

// Booking Routes
$router->get('/bookings', [BookingController::class, 'index']);
$router->get('/bookings/create/{courtId}', [BookingController::class, 'create']);
$router->post('/bookings', [BookingController::class, 'store']);
$router->get('/bookings/{id}', [BookingController::class, 'show']);
$router->get('/bookings/{id}/pay', [BookingController::class, 'pay']);
$router->post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
$router->get('/bookings/{id}/confirmation', [BookingController::class, 'confirmation']);
$router->get('/bookings/{id}/review', [BookingController::class, 'review']);
$router->post('/bookings/{id}/review', [BookingController::class, 'storeReview']);
$router->get('/bookings/calendar/{courtId}', [BookingController::class, 'calendar']);

// Admin Routes
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/bookings', [AdminController::class, 'bookings']);
$router->get('/admin/bookings/{id}', [AdminController::class, 'showBooking']);
$router->post('/admin/bookings/{id}/confirm', [AdminController::class, 'confirmBooking']);
$router->post('/admin/bookings/{id}/cancel', [AdminController::class, 'cancelBooking']);
$router->post('/admin/bookings/{id}/no-show', [AdminController::class, 'markNoShow']);
$router->post('/admin/bookings/{id}/complete', [AdminController::class, 'completeBooking']);

$router->get('/admin/payments', [AdminController::class, 'payments']);
$router->post('/admin/payments/{id}/verify', [AdminController::class, 'verifyPayment']);
$router->post('/admin/payments/{id}/reject', [AdminController::class, 'rejectPayment']);
$router->post('/admin/payments/{id}/refund', [AdminController::class, 'refundPayment']);

$router->get('/admin/courts', [AdminController::class, 'courts']);
$router->get('/admin/courts/create', [AdminController::class, 'createCourt']);
$router->post('/admin/courts', [AdminController::class, 'storeCourt']);
$router->get('/admin/courts/{id}/edit', [AdminController::class, 'editCourt']);
$router->post('/admin/courts/{id}', [AdminController::class, 'updateCourt']);
$router->post('/admin/courts/{id}/delete', [AdminController::class, 'deleteCourt']);

$router->get('/admin/users', [AdminController::class, 'users']);
$router->post('/admin/users/{id}/blacklist', [AdminController::class, 'blacklistUser']);
$router->post('/admin/users/{id}/unblacklist', [AdminController::class, 'unblacklistUser']);

$router->get('/admin/reports', [AdminController::class, 'reports']);
$router->get('/admin/reports/export', [AdminController::class, 'exportBookings']);
$router->get('/admin/logs', [AdminController::class, 'logs']);
$router->get('/admin/settings', [AdminController::class, 'settings']);
$router->post('/admin/settings', [AdminController::class, 'updateSettings']);
$router->get('/admin/scanner', [AdminController::class, 'scanner']);
