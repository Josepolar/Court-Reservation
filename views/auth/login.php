<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="<?= url('/') ?>" class="inline-flex items-center">
                <i class="fas fa-basketball-ball text-ph-blue text-3xl mr-2"></i>
                <span class="text-2xl font-bold text-ph-blue"><?= APP_NAME ?></span>
            </a>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Welcome back!</h2>
            <p class="mt-2 text-gray-600">Sign in to your account</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="<?= url('login') ?>" method="POST" class="space-y-6">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                
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
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" 
                            class="h-4 w-4 text-ph-blue focus:ring-ph-blue border-gray-300 rounded">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>
                    <a href="<?= url('forgot-password') ?>" class="text-sm text-ph-blue hover:text-blue-800">
                        Forgot password?
                    </a>
                </div>
                
                <button type="submit" class="w-full bg-ph-blue text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </button>
            </form>
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="<?= url('auth/facebook') ?>" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fab fa-facebook text-blue-600 text-xl mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Facebook</span>
                    </a>
                    <a href="<?= url('auth/google') ?>" 
                      class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                      <img src="./app/images/google.png" 
                      alt="Google" 
                      class="w-5 h-5 mr-2">
                      <span class="text-sm font-medium text-gray-700">Google</span>
                    </a>

                </div>
            </div>
        </div>
        
        <p class="mt-6 text-center text-gray-600">
            Don't have an account? 
            <a href="<?= url('register') ?>" class="text-ph-blue font-semibold hover:text-blue-800">Sign up</a>
        </p>
    </div>
</div>
