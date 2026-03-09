<?php
$payment = $payment ?? null;
$paymentProof = $paymentProof ?? null;
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
        <a href="<?= url('admin/bookings') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
        </a>
    </div>
    
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Booking Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-lg font-semibold"><?= $booking['booking_code'] ?></h2>
                        <p class="text-sm text-gray-500">Created <?= date('M d, Y g:i A', strtotime($booking['created_at'])) ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold <?php
                        echo match($booking['status']) {
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'confirmed' => 'bg-blue-100 text-blue-700',
                            'paid' => 'bg-green-100 text-green-700',
                            'completed' => 'bg-gray-100 text-gray-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700'
                        };
                    ?>">
                        <?= ucfirst($booking['status']) ?>
                    </span>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Court</p>
                        <p class="font-medium"><?= $booking['court_name'] ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-medium"><?= date('l, F d, Y', strtotime($booking['booking_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Time</p>
                        <p class="font-medium">
                            <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                            <?= date('g:i A', strtotime($booking['end_time'])) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Duration</p>
                        <p class="font-medium"><?= $booking['duration_hours'] ?> hour(s)</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Number of Players</p>
                        <p class="font-medium">
                            <?= isset($booking['num_players']) ? $booking['num_players'] . ' players' : 'N/A' ?>
                        </p>
                    </div>
                </div>
                
                <?php if ($booking['notes']): ?>
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-500">Notes</p>
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($booking['notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Customer Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-ph-blue/10 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-ph-blue text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium"><?= $booking['user_name'] ?? 'Unknown User' ?></p>
                        <p class="text-sm text-gray-500"><?= $booking['user_email'] ?? 'No email provided' ?></p>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-medium"><?= $booking['user_phone'] ?? 'Not provided' ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Bookings</p>
                        <p class="font-medium"><?= $userStats['total_bookings'] ?? 0 ?> bookings</p>
                    </div>
                </div>
                
                <?php if ($userStats['is_blacklisted'] ?? false): ?>
                <div class="mt-4 p-3 bg-red-50 rounded-lg">
                    <p class="text-red-700 text-sm">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        This user is currently blacklisted
                    </p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Payment Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Payment Information</h3>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p class="text-2xl font-bold text-ph-blue"><?= formatPrice($booking['total_amount']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <p class="font-medium"><?= strtoupper($booking['payment_method'] ?? 'Not selected') ?></p>
                    </div>
                </div>
                
                <?php if (!empty($payment)): ?>
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Payment Status</span>
                        <span class="px-2 py-1 rounded text-sm <?= $payment['status'] === 'verified' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                            <?= ucfirst($payment['status']) ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Reference</span>
                        <span class="font-mono text-sm"><?= $payment['payment_reference'] ?></span>
                    </div>
                    <?php if ($payment['verified_at']): ?>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Verified At</span>
                        <span><?= date('M d, Y g:i A', strtotime($payment['verified_at'])) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($paymentProof)): ?>
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm font-medium text-gray-700 mb-2">Payment Proof</p>
                    <img src="<?= url('storage/proofs/' . $paymentProof['image_path']) ?>" 
                         alt="Payment Proof" 
                         class="w-full max-w-md rounded-lg border cursor-pointer"
                         onclick="window.open(this.src, '_blank')">
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Actions</h3>
                
                <div class="space-y-3">
                    <?php if ($booking['status'] === 'pending'): ?>
                    <form action="<?= url('admin/bookings/' . $booking['id'] . '/confirm') ?>" method="POST">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i>Confirm Booking
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                    <form action="<?= url('admin/bookings/' . $booking['id'] . '/cancel') ?>" method="POST" 
                          onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i>Cancel Booking
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if ($booking['status'] === 'paid'): ?>
                    <form action="<?= url('admin/bookings/' . $booking['id'] . '/complete') ?>" method="POST">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-flag-checkered mr-2"></i>Mark as Complete
                        </button>
                    </form>
                    
                    <form action="<?= url('admin/bookings/' . $booking['id'] . '/no-show') ?>" method="POST"
                          onsubmit="return confirm('Mark as no-show? This may blacklist the user.')">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-user-slash mr-2"></i>Mark as No-Show
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if ($payment && $payment['status'] === 'pending'): ?>
                    <form action="<?= url('admin/payments/' . $payment['id'] . '/verify') ?>" method="POST">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check-double mr-2"></i>Verify Payment
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <button onclick="sendNotification()" class="w-full border border-gray-300 py-2 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-bell mr-2"></i>Send Notification
                    </button>
                </div>
            </div>
            
            <!-- Entry QR Code -->
            <?php if (in_array($booking['status'], ['paid', 'completed'])): ?>
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <h3 class="text-lg font-semibold mb-4">Entry QR Code</h3>
                <div class="bg-gray-50 p-4 rounded-lg inline-block">
                    <img src="<?= $booking['entry_qr_code'] ?>" alt="Entry QR" class="w-48 h-48">
                </div>
                <p class="text-sm text-gray-500 mt-2">Scan to verify entry</p>
            </div>
            <?php endif; ?>
            
            <!-- Activity Log -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Activity Log</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-plus text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Booking Created</p>
                            <p class="text-xs text-gray-500"><?= date('M d, Y g:i A', strtotime($booking['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if ($payment): ?>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-credit-card text-yellow-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Payment Submitted</p>
                            <p class="text-xs text-gray-500"><?= date('M d, Y g:i A', strtotime($payment['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if ($payment['verified_at']): ?>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Payment Verified</p>
                            <p class="text-xs text-gray-500"><?= date('M d, Y g:i A', strtotime($payment['verified_at'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($booking['status'] === 'cancelled'): ?>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-times text-red-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Booking Cancelled</p>
                            <p class="text-xs text-gray-500"><?= date('M d, Y g:i A', strtotime($booking['updated_at'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendNotification() {
    const message = prompt('Enter notification message:');
    if (message) {
        fetch('<?= url('api/notifications/send') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: <?= $booking['user_id'] ?>,
                message: message,
                _token: '<?= csrf_token() ?>'
            })
        }).then(response => response.json())
          .then(data => {
              alert(data.message);
          });
    }
}
</script>
