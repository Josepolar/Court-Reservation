<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Book sports courts in the Philippines - Basketball, Badminton, Tennis, Futsal and more">
    <title><?= $title ?? APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ph-blue': '#0038A8',
                        'ph-red': '#CE1126',
                        'ph-yellow': '#FCD116',
                        'primary': '#0038A8',
                        'secondary': '#CE1126',
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Google Icons -->
 <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #0038A8 0%, #1e40af 50%, #0038A8 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?= url('/') ?>" class="flex items-center">
                        <i class="fas fa-basketball-ball text-ph-blue text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-ph-blue"><?= APP_NAME ?></span>
                    </a>
                    
             <div class="hidden md:flex ml-10 items-center space-x-4">
    
    <!-- Browse Courts -->
    <a href="<?= url('courts') ?>" 
       class="text-gray-700 hover:text-ph-blue px-3 py-2 rounded-md text-sm font-medium transition flex items-center">
        <i class="fas fa-map-marker-alt mr-1"></i> 
        Browse Courts
    </a>

  <div class="relative inline-block text-left">

    <!-- Button -->
    <button onclick="toggleDropdown()" 
        class="flex items-center text-gray-700 hover:text-ph-blue px-3 py-2 text-sm font-medium transition">
        
        <span class="material-symbols-outlined mr-1 text-base">
            sports_and_outdoors
        </span>
        All Sports
    </button>

     <!-- Dropdown Menu -->
<div id="sportsDropdown" 
     class="hidden absolute left-0 mt-2 w-40
            bg-white rounded-lg shadow-lg
            border border-gray-100
            overflow-hidden z-50">

       <!-- All Sports -->
    
    <div class="border-t border-gray-100"></div>

      <!-- Badminton -->
    <a href="<?= url('courts/type/badminton') ?>" 
       class="flex items-center px-3 py-2 text-xs text-gray-700 
              hover:bg-gray-50 hover:text-ph-blue transition duration-150">
        <span class="material-symbols-outlined mr-2 text-base text-ph-blue">
            sports_handball
        </span>
        Badminton
    </a>

       <!-- Basketball -->
    <a href="<?= url('courts/type/basketball') ?>" 
       class="flex items-center px-3 py-2 text-xs text-gray-700 
              hover:bg-gray-50 hover:text-ph-blue transition duration-150">
        <span class="material-symbols-outlined mr-2 text-base text-ph-blue">
            sports_basketball
        </span>
        Basketball
    </a>

       <!-- Futsal -->
    <a href="<?= url('courts/type/futsal') ?>" 
       class="flex items-center px-3 py-2 text-xs text-gray-700 
              hover:bg-gray-50 hover:text-ph-blue transition duration-150">
        <span class="material-symbols-outlined mr-2 text-base text-ph-blue">
            sports_soccer   
        </span>
        Futsal
    </a>

       <!-- Tennis -->
    <a href="<?= url('courts/type/tennis') ?>" 
       class="flex items-center px-3 py-2 text-xs text-gray-700 
              hover:bg-gray-50 hover:text-ph-blue transition duration-150">
        <span class="material-symbols-outlined mr-2 text-base text-ph-blue">
            sports_tennis
        </span>
        Tennis
    </a>

        <!-- Volleyball -->
    <a href="<?= url('courts/type/volleyball') ?>" 
       class="flex items-center px-3 py-2 text-xs text-gray-700 
              hover:bg-gray-50 hover:text-ph-blue transition duration-150">
        <span class="material-symbols-outlined mr-2 text-base text-ph-blue">
            sports_volleyball
        </span>
        Volleyball
    </a>
</div>


</div>
</div>

<script>
function toggleDropdown() {
    document.getElementById("sportsDropdown").classList.toggle("hidden");
}
</script>


</div>

                
                <div class="flex items-center space-x-4">
                    <?php if (isLoggedIn()): ?>
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-600 hover:text-ph-blue relative">
                                <i class="fas fa-bell text-xl"></i>
                                <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-ph-red text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                            </button>
                        </div>
                        
                        <a href="<?= url('bookings') ?>" class="text-gray-700 hover:text-ph-blue px-3 py-2 rounded-md text-sm font-medium transition">
                            <i class="fas fa-calendar-check mr-1"></i> My Bookings
                        </a>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-ph-blue">
                                <i class="fas fa-user-circle text-2xl mr-2"></i>
                                <span class="text-sm font-medium"><?= $_SESSION['user_name'] ?? 'User' ?></span>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="<?= url('profile') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <?php if (isAdmin()): ?>
                                <a href="<?= url('admin') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Admin Panel
                                </a>
                                <?php endif; ?>
                                <hr class="my-1">
                                <a href="<?= url('logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="text-gray-700 hover:text-ph-blue px-3 py-2 rounded-md text-sm font-medium transition">
                            Login
                        </a>
                        <a href="<?= url('register') ?>" class="bg-ph-blue text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <?php if ($message = flash('success')): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <p><i class="fas fa-check-circle mr-2"></i><?= $message ?></p>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($message = flash('error')): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <p><i class="fas fa-exclamation-circle mr-2"></i><?= $message ?></p>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($message = flash('warning')): ?>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <p><i class="fas fa-exclamation-triangle mr-2"></i><?= $message ?></p>
            <button onclick="this.parentElement.parentElement.remove()" class="text-yellow-700 hover:text-yellow-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-grow">
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-basketball-ball text-ph-yellow text-2xl mr-2"></i>
                        <span class="text-xl font-bold"><?= APP_NAME ?></span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Book sports courts easily in the Philippines. Basketball, badminton, tennis, and more.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="<?= url('courts') ?>" class="hover:text-white transition">Browse Courts</a></li>
                        <li><a href="<?= url('about') ?>" class="hover:text-white transition">About Us</a></li>
                        <li><a href="<?= url('contact') ?>" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Sports</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="<?= url('courts/type/badminton') ?>" class="hover:text-white transition">Badminton</a></li>
                        <li><a href="<?= url('courts/type/basketball') ?>" class="hover:text-white transition">Basketball</a></li>
                        <li><a href="<?= url('courts/type/futsal') ?>" class="hover:text-white transition">Futsal</a></li>
                        <li><a href="<?= url('courts/type/tennis') ?>" class="hover:text-white transition">Tennis</a></li>
                        <li><a href="<?= url('courts/type/volleyball') ?>" class="hover:text-white transition">Volleyball</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Payment Methods</h3>
                    <div class="flex space-x-3 mb-4">
                        <img src="./app/images/gcash-logo.png" alt="GCash" class="h-12  rounded p-1">
                        <img src="./app/images/maya.png" alt="Maya" class="h-9  rounded p-1">
                        <img src="./app/images/qr_ph.png" alt="QR Ph" class="h-9 rounded p-1">
                    </div>
                  <!--  <p class="text-gray-400 text-sm">We accept QR Ph payments</p> -->
                </div>
            </div>
            
            <hr class="border-gray-800 my-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    © <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
                </p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="<?= url('terms') ?>" class="text-gray-400 hover:text-white text-sm transition">Terms of Service</a>
                    <a href="<?= url('privacy') ?>" class="text-gray-400 hover:text-white text-sm transition">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('[role="alert"]').forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
        
        // Fetch notifications
        <?php if (isLoggedIn()): ?>
        fetch('<?= url('api/notifications') ?>')
            .then(r => r.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    const badge = document.getElementById('notification-badge');
                    badge.textContent = data.notifications.length;
                    badge.classList.remove('hidden');
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>
