<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">User Blacklist</h1>
    <button onclick="openBlacklistModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fas fa-user-slash mr-2"></i>Add to Blacklist
    </button>
</div>

<!-- Stats -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-sm text-gray-500">Total Blacklisted</p>
        <p class="text-2xl font-bold text-red-600"><?= count($blacklist) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-sm text-gray-500">No-Show Violations</p>
        <p class="text-2xl font-bold text-purple-600"><?= $stats['no_show_count'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-sm text-gray-500">Expired Bans</p>
        <p class="text-2xl font-bold text-gray-600"><?= $stats['expired_count'] ?? 0 ?></p>
    </div>
</div>

<!-- Blacklist Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Blacklisted On</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Expires</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($blacklist)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-2"></i>
                        <p>No blacklisted users</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($blacklist as $entry): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-slash text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= $entry['user_name'] ?></p>
                                <p class="text-sm text-gray-500"><?= $entry['user_email'] ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-sm <?php
                            echo match($entry['reason']) {
                                'no_show' => 'bg-purple-100 text-purple-700',
                                'payment_fraud' => 'bg-red-100 text-red-700',
                                'misconduct' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        ?>">
                            <?= ucfirst(str_replace('_', ' ', $entry['reason'])) ?>
                        </span>
                        <?php if ($entry['notes']): ?>
                        <p class="text-sm text-gray-500 mt-1"><?= $entry['notes'] ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?= date('M d, Y', strtotime($entry['created_at'])) ?>
                        <br>
                        <span class="text-gray-500">by <?= $entry['blacklisted_by_name'] ?></span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?php if ($entry['expires_at']): ?>
                        <?= date('M d, Y', strtotime($entry['expires_at'])) ?>
                        <?php else: ?>
                        <span class="text-red-600 font-medium">Permanent</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $isActive = !$entry['expires_at'] || strtotime($entry['expires_at']) > time();
                        ?>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $isActive ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' ?>">
                            <?= $isActive ? 'Active' : 'Expired' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="<?= url('admin/blacklist/' . $entry['id'] . '/remove') ?>" method="POST" class="inline"
                              onsubmit="return confirm('Remove this user from blacklist?')">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="text-green-600 hover:text-green-700" title="Remove from Blacklist">
                                <i class="fas fa-user-check"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Auto-Blacklist Settings -->
<div class="bg-white rounded-xl shadow-sm p-6 mt-6">
    <h3 class="text-lg font-semibold mb-4">Auto-Blacklist Settings</h3>
    
    <form action="<?= url('admin/settings/blacklist') ?>" method="POST">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No-Show Threshold</label>
                <div class="flex items-center">
                    <input type="number" name="no_show_threshold" min="1" max="10" 
                           value="<?= $settings['no_show_threshold'] ?? 3 ?>"
                           class="w-24 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <span class="ml-2 text-gray-500">consecutive no-shows</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Users will be blacklisted after this many no-shows</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Auto-Ban Duration</label>
                <select name="auto_ban_duration" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <option value="7" <?= ($settings['auto_ban_duration'] ?? 30) == 7 ? 'selected' : '' ?>>7 days</option>
                    <option value="14" <?= ($settings['auto_ban_duration'] ?? 30) == 14 ? 'selected' : '' ?>>14 days</option>
                    <option value="30" <?= ($settings['auto_ban_duration'] ?? 30) == 30 ? 'selected' : '' ?>>30 days</option>
                    <option value="90" <?= ($settings['auto_ban_duration'] ?? 30) == 90 ? 'selected' : '' ?>>90 days</option>
                    <option value="0" <?= ($settings['auto_ban_duration'] ?? 30) == 0 ? 'selected' : '' ?>>Permanent</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Save Settings
            </button>
        </div>
    </form>
</div>

<!-- Add to Blacklist Modal -->
<div id="blacklistModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Add User to Blacklist</h3>
            <button onclick="closeBlacklistModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="<?= url('admin/blacklist') ?>" method="POST">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <option value="">Search user...</option>  
                    <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= $user['name'] ?> (<?= $user['email'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <select name="reason" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <option value="">Select reason...</option>
                    <option value="no_show">Multiple No-Shows</option>
                    <option value="payment_fraud">Payment Fraud</option>
                    <option value="misconduct">Misconduct</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                <select name="duration" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <option value="7">7 days</option>
                    <option value="14">14 days</option>
                    <option value="30" selected>30 days</option>
                    <option value="90">90 days</option>
                    <option value="0">Permanent</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                          placeholder="Additional details..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeBlacklistModal()" 
                        class="flex-1 border border-gray-300 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                    Add to Blacklist
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openBlacklistModal() {
    document.getElementById('blacklistModal').classList.remove('hidden');
}

function closeBlacklistModal() {
    document.getElementById('blacklistModal').classList.add('hidden');
}
</script>
