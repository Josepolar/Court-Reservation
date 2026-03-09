<?php
/**
 * API Routes
 */

$router = Router::getInstance();

// Court API
$router->get('/api/courts/{id}/slots', [CourtController::class, 'getSlots']);
$router->get('/api/courts/{id}/price', [CourtController::class, 'calculatePrice']);
$router->get('/api/courts/{id}/availability', [CourtController::class, 'checkAvailability']);

// Payment API
$router->post('/api/payments/create/{bookingId}', [PaymentController::class, 'create']);
$router->post('/api/payments/{ref}/upload-proof', [PaymentController::class, 'uploadProof']);
$router->get('/api/payments/{ref}/status', [PaymentController::class, 'checkStatus']);
$router->post('/api/payments/webhook', [PaymentController::class, 'webhook']);

// Booking API
$router->post('/api/bookings/verify-qr', [BookingController::class, 'verifyQR']);
$router->get('/api/bookings/verify-qr', [BookingController::class, 'verifyQR']);

// Notifications API
$router->get('/api/notifications', function() {
    if (!isLoggedIn()) {
        jsonResponse(['error' => 'Unauthorized'], 401);
    }
    $notification = new Notification();
    $notifications = $notification->getUnread($_SESSION['user_id']);
    jsonResponse(['notifications' => $notifications]);
});

$router->post('/api/notifications/{id}/read', function($id) {
    if (!isLoggedIn()) {
        jsonResponse(['error' => 'Unauthorized'], 401);
    }
    $notification = new Notification();
    $notification->markRead($id);
    jsonResponse(['success' => true]);
});

$router->post('/api/notifications/read-all', function() {
    if (!isLoggedIn()) {
        jsonResponse(['error' => 'Unauthorized'], 401);
    }
    $notification = new Notification();
    $notification->markAllRead($_SESSION['user_id']);
    jsonResponse(['success' => true]);
});

