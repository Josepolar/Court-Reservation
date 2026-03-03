<?php
/**
 * Application Configuration
 * Court Reservation System for Philippines
 */

// Prevent double loading Fetch sample
if (defined('APP_NAME')) {
    return [
        'name' => APP_NAME,
        'url' => APP_URL,
        'timezone' => APP_TIMEZONE,
        'currency' => APP_CURRENCY,
        'currency_code' => APP_CURRENCY_CODE,
    ];
}

// Application Settings
define('APP_NAME', $_ENV['APP_NAME']);
define('APP_URL', $_ENV['APP_URL']);
define('APP_TIMEZONE', $_ENV['APP_TIMEZONE']);
define('APP_CURRENCY', '₱');
define('APP_CURRENCY_CODE', 'PHP');

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Payment Gateway Settings (PayMongo - Popular in PH)
define('PAYMONGO_PUBLIC_KEY', 'pk_test_your_key_here');
define('PAYMONGO_SECRET_KEY', 'sk_test_your_key_here');
define('PAYMONGO_WEBHOOK_SECRET', 'whsec_your_webhook_secret');

// GCash/Maya Direct (if using their APIs directly)
define('GCASH_MERCHANT_ID', '');
define('MAYA_PUBLIC_KEY', '');
define('MAYA_SECRET_KEY', '');

// SMS Settings (Semaphore - PH SMS Gateway)
define('SEMAPHORE_API_KEY', 'your_semaphore_api_key');
define('SEMAPHORE_SENDER_NAME', 'CourtRes');

// Booking Settings
define('BOOKING_MIN_HOURS', 1);
define('BOOKING_MAX_HOURS', 8);
define('BOOKING_ADVANCE_DAYS', 30);
define('RESERVATION_EXPIRY_MINUTES', 30);
define('OPERATING_START_HOUR', 6);
define('OPERATING_END_HOUR', 22);
define('PEAK_HOURS_START', 17);
define('PEAK_HOURS_END', 21);

// Pricing
define('DEFAULT_HOURLY_RATE', 500);
define('PEAK_HOUR_RATE', 700);
define('WEEKEND_RATE', 600);

// File Upload Settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_PATH', __DIR__ . '/../storage/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// QR Code Settings
define('QR_CODE_SIZE', 300);

// No-show and blacklist settings
define('NO_SHOW_THRESHOLD', 3);
define('AUTO_BAN_DURATION', 30); // days

// Return config array for config() helper function
return [
    'name' => APP_NAME,
    'url' => APP_URL,
    'timezone' => APP_TIMEZONE,
    'currency' => APP_CURRENCY,
    'currency_code' => APP_CURRENCY_CODE,
];