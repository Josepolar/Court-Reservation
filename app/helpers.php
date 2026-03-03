<?php
/**
 * Helper Functions for Court Reservation System
 */

/**
 * Get application URL
 */
function url($path = '')
{
    $base = rtrim(config('app.url'), '/');
    return $base . '/' . ltrim($path, '/');
}

/**
 * Get asset URL
 */
function asset($path)
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Get configuration value
 */
function config($key, $default = null)
{
    static $config = [];
    
    $parts = explode('.', $key);
    $file = array_shift($parts);
    
    if (!isset($config[$file])) {
        $path = __DIR__ . '/../config/' . $file . '.php';
        if (file_exists($path)) {
            $config[$file] = require $path;
        } else {
            return $default;
        }
    }
    
    $value = $config[$file];
    foreach ($parts as $part) {
        if (!isset($value[$part])) {
            return $default;
        }
        $value = $value[$part];
    }
    
    return $value;
}

/**
 * Generate CSRF token
 */
function csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format price in Philippine Peso
 */
function formatPrice($amount)
{
    return '₱' . number_format($amount ?? 0, 2);
}

/**
 * Get authenticated user
 */
function auth()
{
    return $_SESSION['user'] ?? null;
}

/**
 * Check if user is authenticated (alias: isLoggedIn)
 */
function isAuthenticated()
{
    return isset($_SESSION['user']);
}

/**
 * Alias for isAuthenticated
 */
function isLoggedIn()
{
    return isAuthenticated();
}

/**
 * Get current user data
 */
function currentUser()
{
    return auth();
}

/**
 * Check if user is admin
 */
function isAdmin()
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Redirect to URL
 */
function redirect($url)
{
    header('Location: ' . url($url));
    exit;
}

/**
 * Set flash message
 */
function flash($key, $message = null)
{
    if ($message === null) {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get old input value
 */
function old($key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

/**
 * Store old input
 */
function storeOldInput($data)
{
    $_SESSION['old'] = $data;
}

/**
 * Clear old input
 */
function clearOldInput()
{
    unset($_SESSION['old']);
}

/**
 * Sanitize input string
 */
function sanitize($string)
{
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

/**
 * Escape HTML
 */
if (!function_exists( 'e')){
   function e($string){
       return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
   }
}

/**
 * Generate random booking code
 */
function generateBookingCode()
{
    return 'BK-' . strtoupper(substr(uniqid(), -8)) . '-' . rand(100, 999);
}

/**
 * Generate payment reference
 */
function generatePaymentReference()
{
    return 'PAY-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
}

/**
 * Generate QR code URL
 */
function generateQRCode($data, $size = 200)
{
    return "https://quickchart.io/qr?text=" . urlencode($data) . "&size=" . $size;
}

/**
 * Calculate time slots for a date
 */
function getTimeSlots($openTime, $closeTime, $duration = 60)
{
    $slots = [];
    $start = strtotime($openTime);
    $end = strtotime($closeTime);
    
    while ($start < $end) {
        $slotEnd = $start + ($duration * 60);
        if ($slotEnd <= $end) {
            $slots[] = [
                'start' => date('H:i:s', $start),
                'end' => date('H:i:s', $slotEnd),
                'label' => date('g:i A', $start) . ' - ' . date('g:i A', $slotEnd)
            ];
        }
        $start = $slotEnd;
    }
    
    return $slots;
}

/**
 * Check if time is peak hour
 */
function isPeakHour($time)
{
    $hour = (int)date('G', strtotime($time));
    // Peak hours: 5 PM - 9 PM
    return $hour >= 17 && $hour < 21;
}

/**
 * Check if date is weekend
 */
function isWeekend($date)
{
    $dayOfWeek = date('N', strtotime($date));
    return $dayOfWeek >= 6; // Saturday = 6, Sunday = 7
}

/**
 * Calculate booking price
 */
function calculateBookingPrice($basePrice, $peakPrice, $weekendPrice, $date, $startTime, $hours)
{
    $total = 0;
    
    for ($i = 0; $i < $hours; $i++) {
        $slotTime = date('H:i:s', strtotime($startTime) + ($i * 3600));
        
        if (isWeekend($date)) {
            $total += $weekendPrice;
        } elseif (isPeakHour($slotTime)) {
            $total += $peakPrice;
        } else {
            $total += $basePrice;
        }
    }
    
    return $total;
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'F d, Y')
{
    return date($format, strtotime($date));
}

/**
 * Format time for display
 */
function formatTime($time, $format = 'g:i A')
{
    return date($format, strtotime($time));
}

/**
 * Get day name from number
 */
function getDayName($dayNumber)
{
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return $days[$dayNumber] ?? '';
}

/**
 * Validate Philippine phone number
 */
function validatePhoneNumber($phone)
{
    // Remove spaces and dashes
    $phone = preg_replace('/[\s-]/', '', $phone);
    
    // Philippine mobile number patterns
    // +63 9XX XXX XXXX or 09XX XXX XXXX
    return preg_match('/^(\+63|0)?9\d{9}$/', $phone);
}

/**
 * Format phone number for display
 */
function formatPhoneNumber($phone)
{
    $phone = preg_replace('/[\s-]/', '', $phone);
    if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
        return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
    }
    return $phone;
}

/**
 * Send JSON response
 */
function jsonResponse($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Log error
 */
function logError($message, $context = [])
{
    $logFile = __DIR__ . '/../storage/logs/error.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
    $logMessage = "[{$timestamp}] {$message}{$contextStr}\n";
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename)
{
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
    return strtolower($filename);
}

/**
 * Upload file
 */
function uploadFile($file, $directory, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'])
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }
    
    $uploadDir = __DIR__ . '/../storage/' . $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = uniqid() . '_' . sanitizeFilename($file['name']);
    $filepath = $uploadDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to save file'];
}
