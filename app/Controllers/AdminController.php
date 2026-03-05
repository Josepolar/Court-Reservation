<?php
/**
 * Admin Controller
 */
class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
    }
    
    public function dashboard() {
        $this->requireAdmin();
        
        $booking = new Booking();
        $payment = new Payment();
        $user = new User();
        
        $stats = [
            'today' => $booking->getStats('today'),
            'week' => $booking->getStats('week'),
            'month' => $booking->getStats('month'),
        ];
        
        $paymentStats = $payment->getStats('month');
        $pendingBookings = $booking->getPending();
        $pendingPayments = $payment->getPending();
        $upcomingBookings = $booking->getUpcoming();
        
        $totalUsers = $user->count();
        
        $this->renderWithLayout('admin.dashboard', [
            'title' => 'Admin Dashboard - ' . APP_NAME,
            'stats' => $stats,
            'paymentStats' => $paymentStats,
            'pendingBookings' => $pendingBookings,
            'pendingPayments' => $pendingPayments,
            'upcomingBookings' => array_slice($upcomingBookings, 0, 10),
            'totalUsers' => $totalUsers,
        ], 'layouts.admin');
    }
    
    // Booking Management
    public function bookings() {
        $this->requireAdmin();
        
        $booking = new Booking();
        $page = (int)($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? '';
        $date = $_GET['date'] ?? '';
        
        $conditions = [];
        if ($status) $conditions['status'] = $status;
        
        $bookings = $booking->paginate($page, 20, $conditions, 'created_at', 'DESC');
        
        // Enrich items with details
        $enrichedItems = [];
        foreach ($bookings['items'] as $b) {
            $details = $booking->findWithDetails($b['id']);
            if ($details) {
                $enrichedItems[] = $details;
            }
        }
        $bookings['items'] = $enrichedItems;
        
        $this->renderWithLayout('admin.bookings.index', [
            'title' => 'Manage Bookings - ' . APP_NAME,
            'bookings' => $bookings,
            'currentStatus' => $status,
            'currentDate' => $date,
            'currentPage' => $bookings['page'],
            'totalPages' => $bookings['total_pages'],
            'totalBookings' => $bookings['total'],
        ], 'layouts.admin');
    }
    
    public function showBooking($id) {
        $this->requireAdmin();
        
        $bookingModel = new Booking();
        $booking = $bookingModel->findWithDetails($id);
        
        if (!$booking) {
            flash('error', 'Booking not found.');
            $this->redirect('admin/bookings');
        }
        
        // Get payment info
        $paymentModel = new Payment();
        $payments = $paymentModel->getByBooking($id);
        
        $this->renderWithLayout('admin.bookings.show', [
            'title' => 'Booking #' . $booking['booking_code'] . ' - ' . APP_NAME,
            'booking' => $booking,
            'payments' => $payments,
        ], 'layouts.admin');
    }
    
    public function confirmBooking($id) {
        $this->requireAdmin();
        
        $booking = new Booking();
        $adminNotes = sanitize($_POST['admin_notes'] ?? '');
        
        $booking->confirm($id, $adminNotes);
        
        $bookingData = $booking->findWithDetails($id);
        
        // Notify user
        $notification = new Notification();
        $notification->createNotification(
            $bookingData['user_id'],
            'booking_confirmed',
            'Booking Confirmed',
            "Your booking #{$bookingData['booking_code']} has been confirmed!",
            ['booking_id' => $id],
            'web'
        );
        
        // Log
        $log = new ActivityLog();
        $log->log('booking_confirmed', 'Booking confirmed by admin', 'booking', $id);
        
        flash('success', 'Booking confirmed successfully.');
        $this->redirect('admin/bookings');
    }
    
    public function cancelBooking($id) {
        $this->requireAdmin();
        
        $booking = new Booking();
        $reason = sanitize($_POST['reason'] ?? 'Cancelled by admin');
        
        $bookingData = $booking->findWithDetails($id);
        $booking->cancel($id, $reason);
        
        // Notify user
        $notification = new Notification();
        $notification->createNotification(
            $bookingData['user_id'],
            'booking_cancelled',
            'Booking Cancelled',
            "Your booking #{$bookingData['booking_code']} has been cancelled. Reason: {$reason}",
            ['booking_id' => $id],
            'web'
        );
        
        // Log
        $log = new ActivityLog();
        $log->log('booking_cancelled_admin', 'Booking cancelled by admin: ' . $reason, 'booking', $id);
        
        flash('success', 'Booking cancelled.');
        $this->redirect('admin/bookings');
    }
    
    public function markNoShow($id) {
        $this->requireAdmin();
        
        $booking = new Booking();
        $booking->markNoShow($id);
        
        // Log
        $log = new ActivityLog();
        $log->log('booking_no_show', 'Booking marked as no-show', 'booking', $id);
        
        flash('success', 'Booking marked as no-show.');
        $this->redirect('admin/bookings');
    }
    
    public function completeBooking($id) {
        $this->requireAdmin();
        
        $booking = new Booking();
        $booking->complete($id);
        
        // Log
        $log = new ActivityLog();
        $log->log('booking_completed', 'Booking marked as completed', 'booking', $id);
        
        flash('success', 'Booking completed.');
        $this->redirect('admin/bookings');
    }
    
    // Payment Management
    public function payments() {
        $this->requireAdmin();
        
        $payment = new Payment();
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        
        $conditions = [];
        if ($status) $conditions['status'] = $status;
        
        $payments = $payment->paginateWithDetails($page, 20, $conditions, 'created_at', 'DESC');
        $stats = $payment->getStats();
        
        $this->renderWithLayout('admin.payments.index', [
            'title' => 'Manage Payments - ' . APP_NAME,
            'payments' => $payments,
            'currentStatus' => $status,
            'stats' => $stats,
            'totalPages' => $payments['total_pages'],
            'currentPage' => $payments['page'],
            'totalPayments' => $payments['total'],
        ], 'layouts.admin');
    }
    
    public function verifyPayment($id) {
        $this->requireAdmin();
        
        $payment = new Payment();
        $admin = currentUser();
        
        $payment->verify($id, $admin['id']);
        
        // Log
        $log = new ActivityLog();
        $log->log('payment_verified', 'Payment verified by admin', 'payment', $id);
        
        flash('success', 'Payment verified.');
        $this->redirect('admin/payments');
    }
    
    public function refundPayment($id) {
        $this->requireAdmin();
        
        $payment = new Payment();
        $admin = currentUser();
        
        $paymentData = $payment->find($id);
        $amount = $_POST['refund_amount'] ?? $paymentData['amount'];
        $reason = sanitize($_POST['reason'] ?? 'Admin refund');
        
        $payment->refund($id, $amount, $reason, $admin['id']);
        
        // Log
        $log = new ActivityLog();
        $log->log('payment_refunded', 'Payment refunded: ' . $reason, 'payment', $id);
        
        flash('success', 'Payment refunded.');
        $this->redirect('admin/payments');
    }
    
    public function rejectPayment($id) {
        $this->requireAdmin();
        
        $payment = new Payment();
        $admin = currentUser();
        
        $reason = sanitize($_POST['reason'] ?? 'Payment rejected by admin');
        
        $payment->reject($id, $admin['id'], $reason);
        
        // Log
        $log = new ActivityLog();
        $log->log('payment_rejected', 'Payment rejected: ' . $reason, 'payment', $id);
        
        flash('success', 'Payment has been rejected.');
        $this->redirect('admin/payments');
    }
    
    // Court Management
    public function courts() {
        $this->requireAdmin();
        
        $court = new Court();
        $courtType = new CourtType();
        $courts = $court->allWithType();
        $courtTypes = $courtType->all();
        
        $this->renderWithLayout('admin.courts.index', [
            'title' => 'Manage Courts - ' . APP_NAME,
            'courts' => $courts,
            'courtTypes' => $courtTypes,
            'totalPages' => 1,
            'currentPage' => 1,
        ], 'layouts.admin');
    }
    
    public function createCourt() {
        $this->requireAdmin();
        
        $courtType = new CourtType();
        $courtTypes = $courtType->all();
        
        $this->renderWithLayout('admin.courts.create', [
            'title' => 'Add New Court - ' . APP_NAME,
            'courtTypes' => $courtTypes,
        ], 'layouts.admin');
    }
    
    public function storeCourt() {
        $this->requireAdmin();
        
        $errors = $this->validate($_POST, [
            'name' => 'required|min:3',
            'court_type_id' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'location' => 'required',
            'city' => 'required',
        ]);
        
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('admin/courts/create');
        }
        
        $court = new Court();
        $courtId = $court->create([
            'court_type_id' => (int)$_POST['court_type_id'],
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description'] ?? ''),
            'location' => sanitize($_POST['location']),
            'barangay' => sanitize($_POST['barangay'] ?? ''),
            'city' => sanitize($_POST['city']),
            'province' => sanitize($_POST['province'] ?? 'Metro Manila'),
            'hourly_rate' => (float)$_POST['hourly_rate'],
            'peak_hour_rate' => (float)($_POST['peak_hour_rate'] ?? 0),
            'half_court_rate' => (float)($_POST['half_court_rate'] ?? 0),
            'downpayment_percent' => (int)($_POST['downpayment_percent'] ?? 50),
            'capacity' => (int)($_POST['capacity'] ?? 0),
            'amenities' => json_encode($_POST['amenities'] ?? []),
            'rules' => sanitize($_POST['rules'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);
        
        // Handle image upload
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $this->uploadCourtImage($courtId, $_FILES['thumbnail']);
        }
        
        // Log
        $log = new ActivityLog();
        $log->log('court_created', 'New court created', 'court', $courtId);
        
        flash('success', 'Court created successfully.');
        $this->redirect('admin/courts');
    }
    
    public function editCourt($id) {
        $this->requireAdmin();
        
        $court = new Court();
        $courtData = $court->findWithType($id);
        
        if (!$courtData) {
            $this->redirect('admin/courts', 'Court not found.', 'error');
        }
        
        $courtType = new CourtType();
        $courtTypes = $courtType->all();
        
        $this->renderWithLayout('admin.courts.edit', [
            'title' => 'Edit Court - ' . APP_NAME,
            'court' => $courtData,
            'courtTypes' => $courtTypes,
        ], 'layouts.admin');
    }
    
    public function updateCourt($id) {
        $this->requireAdmin();
        
        $court = new Court();
        
        $court->update($id, [
            'court_type_id' => (int)$_POST['court_type_id'],
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description'] ?? ''),
            'location' => sanitize($_POST['location']),
            'barangay' => sanitize($_POST['barangay'] ?? ''),
            'city' => sanitize($_POST['city']),
            'province' => sanitize($_POST['province'] ?? 'Metro Manila'),
            'hourly_rate' => (float)$_POST['hourly_rate'],
            'peak_hour_rate' => (float)($_POST['peak_hour_rate'] ?? 0),
            'half_court_rate' => (float)($_POST['half_court_rate'] ?? 0),
            'downpayment_percent' => (int)($_POST['downpayment_percent'] ?? 50),
            'capacity' => (int)($_POST['capacity'] ?? 0),
            'amenities' => json_encode($_POST['amenities'] ?? []),
            'rules' => sanitize($_POST['rules'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);
        
        // Handle image upload
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $this->uploadCourtImage($id, $_FILES['thumbnail']);
        }
        
        // Log
        $log = new ActivityLog();
        $log->log('court_updated', 'Court updated', 'court', $id);
        
        flash('success', 'Court updated successfully.');
        $this->redirect('admin/courts');
    }
    
    public function deleteCourt($id) {
        $this->requireAdmin();
        
        $court = new Court();
        $court->update($id, ['is_active' => 0]);
        
        // Log
        $log = new ActivityLog();
        $log->log('court_deleted', 'Court deactivated', 'court', $id);
        
        flash('success', 'Court deactivated.');
        $this->redirect('admin/courts');
    }
    
    // User Management
    public function users() {
        $this->requireAdmin();
        
        $user = new User();
        $page = $_GET['page'] ?? 1;
        
        $users = $user->paginate($page, 20, [], 'created_at', 'DESC');
        $stats = $user->getStats();
        
        $this->renderWithLayout('admin.users.index', [
            'title' => 'Manage Users - ' . APP_NAME,
            'users' => $users,
            'stats' => $stats,
            'totalUsers' => $users['total'],
            'totalPages' => $users['total_pages'],
            'currentPage' => $users['page'],
        ], 'layouts.admin');
    }
    
    public function blacklistUser($id) {
        $this->requireAdmin();
        
        $user = new User();
        $reason = sanitize($_POST['reason'] ?? 'Blacklisted by admin');
        
        $user->blacklist($id, $reason);
        
        // Log
        $log = new ActivityLog();
        $log->log('user_blacklisted', 'User blacklisted: ' . $reason, 'user', $id);
        
        flash('success', 'User blacklisted.');
        $this->redirect('admin/users');
    }
    
    public function unblacklistUser($id) {
        $this->requireAdmin();
        
        $user = new User();
        $user->unblacklist($id);
        
        // Log
        $log = new ActivityLog();
        $log->log('user_unblacklisted', 'User removed from blacklist', 'user', $id);
        
        flash('success', 'User removed from blacklist.');
        $this->redirect('admin/users');
    }
    
    // Reports
    public function reports() {
        $this->requireAdmin();
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $payment = new Payment();
        $revenueData = $payment->getRevenueByDate($startDate, $endDate);
        
        $booking = new Booking();
        $bookingStats = $booking->getStats('month');
        
        $court = new Court();
        $courts = $court->allWithType();
        
        // Get top courts by bookings
        $topCourts = $booking->getTopCourts(5);
        
        // Get peak hours
        $peakHours = $booking->getPeakHours();
        
        // Get recent transactions
        $recentTransactions = $payment->getRecent(10);
        
        $this->renderWithLayout('admin.reports.index', [
            'title' => 'Reports - ' . APP_NAME,
            'revenueData' => $revenueData,
            'bookingStats' => $bookingStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'courts' => $courts,
            'topCourts' => $topCourts ?? [],
            'peakHours' => $peakHours ?? [],
            'recentTransactions' => $recentTransactions ?? [],
        ], 'layouts.admin');
    }
    
    public function exportBookings() {
        $this->requireAdmin();
        
        $booking = new Booking();
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $bookings = $this->db->fetchAll(
            "SELECT b.*, c.name as court_name, u.name as user_name, u.email, u.phone
             FROM bookings b
             JOIN courts c ON b.court_id = c.id
             JOIN users u ON b.user_id = u.id
             WHERE b.booking_date BETWEEN ? AND ?
             ORDER BY b.booking_date, b.start_time",
            [$startDate, $endDate]
        );
        
        // Export as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bookings_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Booking Code', 'Date', 'Time', 'Court', 'User', 'Email', 'Phone', 'Amount', 'Status', 'Payment Status']);
        
        foreach ($bookings as $b) {
            fputcsv($output, [
                $b['booking_code'],
                $b['booking_date'],
                $b['start_time'] . ' - ' . $b['end_time'],
                $b['court_name'],
                $b['user_name'],
                $b['email'],
                $b['phone'],
                $b['total_amount'],
                $b['status'],
                $b['payment_status'],
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    // Activity Logs
    public function logs() {
        $this->requireAdmin();
        
        $log = new ActivityLog();
        $logs = $log->getRecent(100);
        
        $this->renderWithLayout('admin.logs', [
            'title' => 'Activity Logs - ' . APP_NAME,
            'logs' => $logs,
        ], 'layouts.admin');
    }
    
    // Settings
    public function settings() {
        $this->requireAdmin();
        
        $setting = new Setting();
        $settings = $setting->getAll();
        
        $this->renderWithLayout('admin.settings', [
            'title' => 'Settings - ' . APP_NAME,
            'settings' => $settings,
        ], 'layouts.admin');
    }
    
    public function updateSettings() {
        $this->requireAdmin();
        
        $setting = new Setting();
        
        foreach ($_POST as $key => $value) {
            if ($key === '_token') continue;
            $setting->set($key, $value);
        }
        
        // Log
        $log = new ActivityLog();
        $log->log('settings_updated', 'System settings updated');
        
        flash('success', 'Settings updated.');
        $this->redirect('admin/settings');
    }
    
    // QR Scanner for entry verification
    public function scanner() {
        $this->requireAdmin();
        
        // Get recent scans for today
        $booking = new Booking();
        $recentScans = $booking->getRecentScans();
        
        $this->renderWithLayout('admin.scanner', [
            'title' => 'QR Scanner - ' . APP_NAME,
            'recentScans' => $recentScans ?? [],
        ], 'layouts.admin');
    }
    
 private function uploadCourtImage($courtId, $file)
{
    if ($file['error'] !== 0) {
        return;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return;
    }

    $uploadDir = "uploads/courts/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = "court_" . $courtId . "_" . time() . "." . $ext;

    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {

        $court = new Court();

        $court->update($courtId, [
            'thumbnail' => $filepath
        ]);
    }
}

public function deleteCourtImage($courtId){

    $court = new Court();

    $court->update($courtId, [
        'thumbnail' => null,
        'thumbnail_type' => null
    ]);

    if (!empty($_FILES['thumbnail']['name'])) {
    $this->uploadCourtImage($courtId, $_FILES['thumbnail']);
}

}


    }

