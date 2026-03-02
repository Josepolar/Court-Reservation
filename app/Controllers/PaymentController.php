<?php
/**
 * Payment Controller
 * Handles QR payments via GCash, Maya, Bank QR
 */
class PaymentController extends Controller {    
    private $payment;
    private $booking;
    
    public function __construct() {
        parent::__construct();
        $this->payment = new Payment();
        $this->booking = new Booking();
    }
    
    public function create($bookingId) {
        $this->requireAuth();
        
        $booking = $this->booking->findWithDetails($bookingId);
        if (!$booking) {
            $this->json(['error' => 'Booking not found'], 404);
        }
        
        $user = currentUser();
        if ($booking['user_id'] !== $user['id']) {
            $this->json(['error' => 'Access denied'], 403);
        }
        
        $paymentMethod = $_POST['payment_method'] ?? 'gcash';
        $paymentType = $_POST['payment_type'] ?? 'full'; // full, downpayment
        
        $amount = $paymentType === 'downpayment' 
            ? $booking['downpayment_amount'] 
            : $booking['total_amount'];
        
        // Create payment record
        $paymentId = $this->payment->createPayment([
            'booking_id' => $bookingId,
            'user_id' => $user['id'],
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_type' => $paymentType,
            'status' => 'pending',
        ]);
        
        $paymentRecord = $this->payment->find($paymentId);
        
        // Generate QR code based on payment method
        $qrData = $this->generatePaymentQR($paymentRecord, $booking);
        
        // Update payment with QR data
        $this->db->query(
            "UPDATE payments SET qr_code_data = ?, qr_code_url = ? WHERE id = ?",
            [json_encode($qrData), $qrData['qr_image'] ?? null, $paymentId]
        );
        
        $this->json([
            'success' => true,
            'payment_reference' => $paymentRecord['payment_reference'],
            'amount' => $amount,
            'formatted_amount' => formatPrice($amount),
            'qr_data' => $qrData,
        ]);
    }
    
    public function uploadProof($paymentRef) {
        $this->requireAuth();
        
        $payment = $this->payment->findByReference($paymentRef);
        if (!$payment) {
            $this->json(['error' => 'Payment not found'], 404);
        }
        
        $user = currentUser();
        if ($payment['user_id'] !== $user['id']) {
            $this->json(['error' => 'Access denied'], 403);
        }
        
        if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'No file uploaded'], 400);
        }
        
        $file = $_FILES['proof'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            $this->json(['error' => 'Invalid file type. Allowed: jpg, jpeg, png, gif'], 400);
        }
        
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $this->json(['error' => 'File too large. Maximum 5MB.'], 400);
        }
        
        // Create upload directory
        $uploadDir = UPLOAD_PATH . 'proofs/' . date('Y/m/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = $paymentRef . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $this->json(['error' => 'Failed to upload file'], 500);
        }
        
        // Update payment
        $relativePath = 'proofs/' . date('Y/m/') . $filename;
        $this->payment->uploadProof($payment['id'], $relativePath);
        
        // Log activity
        $log = new ActivityLog();
        $log->log('payment_proof_uploaded', 'Payment proof uploaded', 'payment', $payment['id']);
        
        // Notify admin
        $notification = new Notification();
        $admins = $this->db->fetchAll("SELECT id FROM users WHERE role = 'admin'");
        foreach ($admins as $admin) {
            $notification->createNotification(
                $admin['id'],
                'payment_proof',
                'Payment Proof Uploaded',
                "Payment proof uploaded for booking #{$payment['booking_code']}. Please verify.",
                ['payment_id' => $payment['id'], 'booking_id' => $payment['booking_id']],
                'web'
            );
        }
        
        $this->json([
            'success' => true,
            'message' => 'Proof uploaded successfully. Please wait for admin verification.',
        ]);
    }
    
    public function checkStatus($paymentRef) {
        $payment = $this->payment->findByReference($paymentRef);
        if (!$payment) {
            $this->json(['error' => 'Payment not found'], 404);
        }
        
        $this->json([
            'success' => true,
            'status' => $payment['status'],
            'paid_at' => $payment['paid_at'],
        ]);
    }
    
    // Webhook for payment gateway callbacks
    public function webhook() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        // Log webhook
        $log = new ActivityLog();
        $log->log('payment_webhook', 'Payment webhook received', null, null, null, $data);
        
        // Verify webhook signature (for PayMongo)
        $signature = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';
        if (!$this->verifyWebhookSignature($input, $signature)) {
            http_response_code(401);
            exit;
        }
        
        // Process based on event type
        $eventType = $data['data']['attributes']['type'] ?? '';
        
        switch ($eventType) {
            case 'payment.paid':
                $this->handlePaymentPaid($data);
                break;
            case 'payment.failed':
                $this->handlePaymentFailed($data);
                break;
        }
        
        http_response_code(200);
        echo json_encode(['received' => true]);
    }
    
    private function generatePaymentQR($payment, $booking) {
        $amount = $payment['amount'];
        $reference = $payment['payment_reference'];
        
        // Generate QR Ph standard format
        // This is a simplified version - in production, use proper QR Ph API
        
        $qrData = [
            'merchant_name' => APP_NAME,
            'reference' => $reference,
            'amount' => $amount,
            'currency' => 'PHP',
            'description' => "Booking #{$booking['booking_code']} - {$booking['court_name']}",
        ];
        
        // For GCash/Maya, generate QR image URL
        // In production, integrate with PayMongo or Maya Business API
        $qrString = json_encode($qrData);
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrString);
        
        return [
            'qr_string' => $qrString,
            'qr_image' => $qrImageUrl,
            'merchant' => APP_NAME,
            'amount' => $amount,
            'reference' => $reference,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'instructions' => $this->getPaymentInstructions($payment['payment_method']),
        ];
    }
    
    private function getPaymentInstructions($method) {
        return match($method) {
            'gcash' => [
                '1. Open your GCash app',
                '2. Tap "Scan QR"',
                '3. Scan the QR code above',
                '4. Confirm the payment amount',
                '5. Complete the payment',
                '6. Take a screenshot of the confirmation',
                '7. Upload the screenshot as proof of payment',
            ],
            'maya' => [
                '1. Open your Maya app',
                '2. Tap "Scan to Pay"',
                '3. Scan the QR code above',
                '4. Verify the payment details',
                '5. Enter your PIN to confirm',
                '6. Screenshot the confirmation',
                '7. Upload the screenshot as proof',
            ],
            'bank_qr' => [
                '1. Open your banking app',
                '2. Select "Scan QR" or "QR Ph"',
                '3. Scan the QR code above',
                '4. Confirm the payment amount',
                '5. Authorize the transaction',
                '6. Take a screenshot',
                '7. Upload the screenshot as proof',
            ],
            default => [
                '1. Scan the QR code with your payment app',
                '2. Confirm the payment',
                '3. Upload proof of payment',
            ],
        };
    }
    
    private function verifyWebhookSignature($payload, $signature) {
        if (empty(PAYMONGO_WEBHOOK_SECRET)) {
            return true; // Skip verification in dev
        }
        
        $computed = hash_hmac('sha256', $payload, PAYMONGO_WEBHOOK_SECRET);
        return hash_equals($computed, $signature);
    }
    
    private function handlePaymentPaid($data) {
        $attributes = $data['data']['attributes']['data']['attributes'] ?? [];
        $reference = $attributes['external_reference_number'] ?? '';
        
        $payment = $this->payment->findByReference($reference);
        if (!$payment) return;
        
        $this->payment->markPaid($payment['id'], $attributes['id'] ?? null);
        $this->booking->markPaid($payment['booking_id']);
        
        // Notify user
        $notification = new Notification();
        $notification->createNotification(
            $payment['user_id'],
            'payment_confirmed',
            'Payment Confirmed',
            "Your payment of " . formatPrice($payment['amount']) . " has been confirmed!",
            ['payment_id' => $payment['id'], 'booking_id' => $payment['booking_id']],
            'web'
        );
    }
    
    private function handlePaymentFailed($data) {
        $attributes = $data['data']['attributes']['data']['attributes'] ?? [];
        $reference = $attributes['external_reference_number'] ?? '';
        
        $payment = $this->payment->findByReference($reference);
        if (!$payment) return;
        
        $this->db->query(
            "UPDATE payments SET status = 'failed' WHERE id = ?",
            [$payment['id']]
        );
    }
}
