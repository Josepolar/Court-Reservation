<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Today's Bookings</p>
                <p class="text-3xl font-bold text-gray-900"><?= $stats['today']['total_bookings'] ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-day text-ph-blue text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-green-600 mt-2">
            <i class="fas fa-check-circle mr-1"></i>
            <?= $stats['today']['completed'] ?? 0 ?> completed
        </p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Monthly Revenue</p>
                <p class="text-3xl font-bold text-gray-900"><?= formatPrice($stats['month']['total_revenue'] ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-peso-sign text-green-600 text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            <i class="fas fa-chart-line mr-1"></i>
            <?= $stats['month']['total_bookings'] ?? 0 ?> bookings this month
        </p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pending Approvals</p>
                <p class="text-3xl font-bold text-gray-900"><?= count($pendingBookings) ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-yellow-600 mt-2">
            <i class="fas fa-exclamation-circle mr-1"></i>
            <?= count($pendingPayments) ?> payments to verify
        </p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Users</p>
                <p class="text-3xl font-bold text-gray-900"><?= $totalUsers ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            <i class="fas fa-user-plus mr-1"></i>
            Active members
        </p>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Pending Bookings -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Pending Bookings</h2>
                <a href="<?= url('admin/bookings?status=pending') ?>" class="text-ph-blue text-sm hover:underline">View All</a>
            </div>
            
            <div class="divide-y">
                <?php if (empty($pendingBookings)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-check-circle text-4xl mb-2"></i>
                    <p>No pending bookings</p>
                </div>
                <?php else: ?>
                <?php foreach (array_slice($pendingBookings, 0, 5) as $booking): ?>
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium"><?= $booking['booking_code'] ?></p>
                            <p class="text-sm text-gray-500"><?= $booking['court_name'] ?></p>
                            <p class="text-sm text-gray-500">
                                <?= date('M d, Y', strtotime($booking['booking_date'])) ?> at 
                                <?= date('g:i A', strtotime($booking['start_time'])) ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-ph-blue"><?= formatPrice($booking['total_amount']) ?></p>
                            <p class="text-sm text-gray-500"><?= $booking['user_name'] ?></p>
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-3">
                        <form action="<?= url('admin/bookings/' . $booking['id'] . '/confirm') ?>" method="POST" class="inline">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm hover:bg-green-200">
                                <i class="fas fa-check mr-1"></i>Confirm
                            </button>
                        </form>
                        <form action="<?= url('admin/bookings/' . $booking['id'] . '/cancel') ?>" method="POST" class="inline" onsubmit="return confirm('Cancel this booking?')">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm hover:bg-red-200">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Upcoming Today -->
        <div class="bg-white rounded-xl shadow-sm mt-6">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Today's Schedule</h2>
            </div>
            
            <div class="divide-y">
                <?php 
                $todayBookings = array_filter($upcomingBookings, fn($b) => $b['booking_date'] === date('Y-m-d'));
                ?>
                <?php if (empty($todayBookings)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-2"></i>
                    <p>No bookings for today</p>
                </div>
                <?php else: ?>
                <?php foreach ($todayBookings as $booking): ?>
                <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-ph-blue/10 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-basketball-ball text-ph-blue"></i>
                        </div>
                        <div>
                            <p class="font-medium"><?= $booking['court_name'] ?></p>
                            <p class="text-sm text-gray-500">
                                <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                                <?= date('g:i A', strtotime($booking['end_time'])) ?>
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm <?= $booking['status'] === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                        <?= ucfirst($booking['status']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Payment Verification</h2>
                <span class="bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs font-semibold">
                    <?= count($pendingPayments) ?>
                </span>
            </div>
            
            <div class="divide-y max-h-96 overflow-y-auto">
                <?php if (empty($pendingPayments)): ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-check-circle text-3xl mb-2"></i>
                    <p class="text-sm">All payments verified</p>
                </div>
                <?php else: ?>
                <?php foreach ($pendingPayments as $payment): ?>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-medium text-sm"><?= $payment['payment_reference'] ?></p>
                            <p class="text-xs text-gray-500"><?= $payment['user_name'] ?></p>
                        </div>
                        <span class="font-semibold text-ph-blue"><?= formatPrice($payment['amount']) ?></span>
                    </div>
                    <div class="flex space-x-2">
                        <form action="<?= url('admin/payments/' . $payment['id'] . '/verify') ?>" method="POST" class="flex-1">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="w-full bg-green-600 text-white py-1 rounded text-sm hover:bg-green-700">
                                Verify
                            </button>
                        </form>
                        <a href="#" class="px-3 py-1 border rounded text-sm hover:bg-gray-50">View</a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            
            <div class="space-y-3">
                <a href="<?= url('admin/courts/create') ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-10 h-10 bg-ph-blue/10 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-plus text-ph-blue"></i>
                    </div>
                    <span class="font-medium">Add New Court</span>
                </a>
                
                <a href="<?= url('admin/scanner') ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-qrcode text-green-600"></i>
                    </div>
                    <span class="font-medium">Scan Entry QR</span>
                </a>
                
                <a href="<?= url('admin/reports') ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                    </div>
                    <span class="font-medium">View Reports</span>
                </a>
                
                <a href="<?= url('admin/reports/export') ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-file-excel text-yellow-600"></i>
                    </div>
                    <span class="font-medium">Export Bookings</span>
                </a>
            </div>
        </div>
        
        <!-- Payment Stats -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Methods (Month)</h2>
            
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">GCash</span>
                        <span class="font-medium"><?= formatPrice($paymentStats['gcash_total'] ?? 0) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <?php $gcashPercent = ($paymentStats['total_paid'] ?? 0) > 0 ? (($paymentStats['gcash_total'] ?? 0) / $paymentStats['total_paid']) * 100 : 0; ?>
                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?= $gcashPercent ?>%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Maya</span>
                        <span class="font-medium"><?= formatPrice($paymentStats['maya_total'] ?? 0) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <?php $mayaPercent = ($paymentStats['total_paid'] ?? 0) > 0 ? (($paymentStats['maya_total'] ?? 0) / $paymentStats['total_paid']) * 100 : 0; ?>
                        <div class="bg-green-500 h-2 rounded-full" style="width: <?= $mayaPercent ?>%"></div>
                    </div>
                </div>

                    <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">QR Ph</span>
                        <span class="font-medium"><?= formatPrice($paymentStats['maya_total'] ?? 0) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <?php $mayaPercent = ($paymentStats['total_paid'] ?? 0) > 0 ? (($paymentStats['maya_total'] ?? 0) / $paymentStats['total_paid']) * 100 : 0; ?>
                        <div class="bg-green-500 h-2 rounded-full" style="width: <?= $mayaPercent ?>%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Cash</span>
                        <span class="font-medium"><?= formatPrice($paymentStats['cash_total'] ?? 0) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <?php $cashPercent = ($paymentStats['total_paid'] ?? 0) > 0 ? (($paymentStats['cash_total'] ?? 0) / $paymentStats['total_paid']) * 100 : 0; ?>
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: <?= $cashPercent ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
