<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="<?= url('/') ?>" class="inline-flex items-center">
                <i class="fas fa-basketball-ball text-ph-blue text-3xl mr-2"></i>
                <span class="text-2xl font-bold text-ph-blue"><?= APP_NAME ?></span>
            </a>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Reset Password</h2>
            <p class="mt-2 text-gray-600">Set your new account password</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="<?= url('resetpassword') ?>" method="POST" class="space-y-5">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="token" value="<?= e($token ?? '') ?>">

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                               placeholder="Enter new password">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                               placeholder="Confirm new password">
                    </div>
                </div>

                <button type="submit" class="w-full bg-ph-blue text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-key mr-2"></i> Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
