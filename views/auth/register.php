<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="<?= url('/') ?>" class="inline-flex items-center">
                <i class="fas fa-basketball-ball text-ph-blue text-3xl mr-2"></i>
                <span class="text-2xl font-bold text-ph-blue"><?= APP_NAME ?></span>
            </a>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Create Account</h2>
            <p class="mt-2 text-gray-600">Join and start booking courts</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="<?= url('register') ?>" method="POST" class="space-y-5">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="name" name="name" value="<?= old('name') ?>" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="Juan Dela Cruz">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" value="<?= old('email') ?>" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="juan@example.com">
                    </div>
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="tel" id="phone" name="phone" value="<?= old('phone') ?>" required  pattern="\d{11}"
                            maxlength="11"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="09171234567">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Philippine mobile number format</p>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required minlength="6"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">At least 6 characters</p>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                </div>
                
                <div class="flex items-start">
                    <input type="checkbox" id="terms" name="terms" required
                        class="h-4 w-4 mt-1 text-ph-blue focus:ring-ph-blue border-gray-300 rounded">
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="<?= url('terms') ?>" class="text-ph-blue hover:underline">Terms of Service</a> 
                        and <a href="<?= url('privacy') ?>" class="text-ph-blue hover:underline">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-ph-blue text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-user-plus mr-2"></i> Create Account
                </button>
            </form>
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or sign up with</span>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="<?= url('auth/facebook') ?>" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fab fa-facebook text-blue-600 text-xl mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Facebook</span>
                    </a>
                    <a href="<?= url('auth/google') ?>" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fab fa-google text-red-500 text-xl mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Google</span>
                    </a>
                </div>
            </div>
        </div>
        
        <p class="mt-6 text-center text-gray-600">
            Already have an account? 
            <a href="<?= url('login') ?>" class="text-ph-blue font-semibold hover:text-blue-800">Sign in</a>
        </p>
    </div>
</div>
