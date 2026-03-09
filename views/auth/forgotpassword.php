<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="<?= url('/') ?>" class="inline-flex items-center">
                <i class="fas fa-basketball-ball text-ph-blue text-3xl mr-2"></i>
                <span class="text-2xl font-bold text-ph-blue"><?= APP_NAME ?></span>
            </a>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Forgot Password</h2>
            <p class="mt-2 text-gray-600">Enter your email to reset your password</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="<?= url('forgotpassword') ?>" method="POST" class="space-y-5">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="juan@example.com">
                    </div>
                </div>

                <button type="submit" class="w-full bg-ph-blue text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-envelope-open-text mr-2"></i> Send Reset Link
                </button>
            </form>

            <p class="mt-6 text-center text-gray-600">
                Remember your password? 
                <a href="<?= url('login') ?>" class="text-ph-blue font-semibold hover:text-blue-800">Sign in</a>
            </p>
        </div>
    </div>
</div>