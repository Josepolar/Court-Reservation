<!-- Hero Section -->
<section class="hero-gradient text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    Book Sports Courts <br>
                    <span class="text-ph-yellow">Anywhere in the Philippines</span>
                </h1>
                <p class="text-xl text-blue-100 mb-8">
                    Basketball, Badminton, Tennis, Futsal and more. 
                    Pay easily with GCash, Maya, or Bank QR.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?= url('courts') ?>" class="bg-ph-yellow text-ph-blue font-semibold px-8 py-3 rounded-lg hover:bg-yellow-400 transition text-center">
                        <i class="fas fa-search mr-2"></i> Browse Courts
                    </a>
                    <?php if (!isLoggedIn()): ?>
                    <a href="<?= url('register') ?>" class="bg-white/20 text-white font-semibold px-8 py-3 rounded-lg hover:bg-white/30 transition text-center">
                        <i class="fas fa-user-plus mr-2"></i> Sign Up Free
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Payment Methods -->
<div class="mt-8 flex items-center space-x-4">
    <span class="text-blue-200 text-sm">Pay with:</span>

    <div class="flex space-x-2 items-center">

        <!-- GCash -->
        <div class="flex flex-col items-center p-2 border rounded-lg w-20 bg-white/20 hover:shadow-md transition">
            <img src="./app/images/gcash-logo.png" alt="GCash Icon" class="w-11 h-9 mb-1">
            <p class="font-medium text-xs text-center">GCash</p>
        </div>

        <!-- Maya -->
        <div class="flex flex-col items-center p-2 border rounded-lg w-20 bg-white/20 hover:shadow-md transition">
            <img src="./app/images/maya.png" alt="Maya Icon" class="w-10 h-9 mb-1">
            <p class="font-medium text-xs text-center">Maya</p>
        </div>

        <!-- QR Ph -->
        <div class="flex flex-col items-center p-2 border rounded-lg w-20 bg-white/20 hover:shadow-md transition">
            <img src="./app/images/qr_ph.png" alt="QR Ph Icon" class="w-10 h-10 mb-1">
            <p class="font-medium text-xs text-center">QR Ph</p>
        </div>

    </div>
</div>
                    </div>

            
            <div class="hidden md:block">
                <div class="relative">
                    <div class="bg-white/10 rounded-2xl p-6 backdrop-blur-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-4 text-gray-800">
                                <i class="fas fa-basketball-ball text-orange-500 text-3xl mb-2"></i>
                                <h3 class="font-semibold">Basketball</h3>
                                <p class="text-sm text-gray-500">Full & Half Courts</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 text-gray-800">
                                <i class="fas fa-table-tennis text-green-500 text-3xl mb-2"></i>
                                <h3 class="font-semibold">Badminton</h3>
                                <p class="text-sm text-gray-500">Indoor Courts</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 text-gray-800">
                                <i class="fas fa-baseball-ball text-yellow-500 text-3xl mb-2"></i>
                                <h3 class="font-semibold">Tennis</h3>
                                <p class="text-sm text-gray-500">Clay & Hard Courts</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 text-gray-800">
                                <i class="fas fa-futbol text-blue-500 text-3xl mb-2"></i>
                                <h3 class="font-semibold">Futsal</h3>
                                <p class="text-sm text-gray-500">Indoor Football</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Court Types Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Find Your Sport</h2>
            <p class="text-gray-600">Choose from various court types across Metro Manila and beyond</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($courtTypes as $type): ?>
            <a href="<?= url('courts/type/' . $type['slug']) ?>" class="group">
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-ph-blue hover:text-white transition group-hover:shadow-lg">
                    <?php
                    $icon = match($type['slug']) {
                        'basketball' => 'fa-basketball-ball text-orange-500',
                        'badminton' => 'fa-table-tennis text-green-500',
                        'tennis' => 'fa-baseball-ball text-yellow-500',
                        'volleyball' => 'fa-volleyball-ball text-blue-500',
                        'futsal' => 'fa-futbol text-purple-500',
                        'covered' => 'fa-warehouse text-gray-500',
                        'gym' => 'fa-dumbbell text-red-500',
                        'swimming' => 'fa-swimming-pool text-cyan-500',
                        default => 'fa-map-marker-alt text-gray-500'
                    };
                    ?>
                    <i class="fas <?= $icon ?> text-4xl mb-3 group-hover:text-white"></i>
                    <h3 class="font-semibold group-hover:text-white"><?= $type['name'] ?></h3>
                    <p class="text-sm text-gray-500 group-hover:text-blue-200"><?= $type['court_count'] ?> courts</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Courts -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Featured Courts</h2>
                <p class="text-gray-600">Top-rated sports facilities</p>
            </div>
            <a href="<?= url('courts') ?>" class="text-ph-blue hover:text-blue-800 font-semibold">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featuredCourts as $court): ?>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                <div class="h-48 bg-gradient-to-br from-ph-blue to-blue-700 flex items-center justify-center">
                    <i class="fas fa-basketball-ball text-white/30 text-6xl"></i>
                </div>
                
                <div class="p-5">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="text-xs text-ph-blue font-semibold uppercase"><?= $court['court_type'] ?? 'Court' ?></span>
                            <h3 class="font-semibold text-gray-900"><?= $court['name'] ?></h3>
                        </div>
                        <?php if ($court['rating'] > 0): ?>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                            <span><?= number_format($court['rating'], 1) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-gray-500 text-sm mb-3">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <?= $court['barangay'] ? $court['barangay'] . ', ' : '' ?><?= $court['city'] ?>
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-ph-blue font-bold text-lg"><?= formatPrice($court['hourly_rate']) ?></span>
                            <span class="text-gray-400 text-sm">/hour</span>
                        </div>
                        <a href="<?= url('courts/' . $court['id']) ?>" class="bg-ph-blue text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-16 bg-yellow-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-gray-600">Book a court in 4 easy steps</p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-ph-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-ph-blue font-bold text-2xl">1</span>
                </div>
                <h3 class="font-semibold mb-2">Choose a Court</h3>
                <p class="text-gray-500 text-sm">Browse and select from our available courts</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-ph-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-ph-blue font-bold text-2xl">2</span>
                </div>
                <h3 class="font-semibold mb-2">Pick Date & Time</h3>
                <p class="text-gray-500 text-sm">Select your preferred schedule from available slots</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-ph-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-ph-blue font-bold text-2xl">3</span>
                </div>
                <h3 class="font-semibold mb-2">Pay via QR</h3>
                <p class="text-gray-500 text-sm">Scan QR code and pay with GCash, Maya, or Bank</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-ph-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-ph-blue font-bold text-2xl">4</span>
                </div>
                <h3 class="font-semibold mb-2">Get QR Entry Pass</h3>
                <p class="text-gray-500 text-sm">Receive your booking QR code for venue entry</p>
            </div>
        </div>
    </div>
</section>

<!-- Cities Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Available Cities</h2>
            <p class="text-gray-600">Find courts near you</p>
        </div>
        
        <div class="flex flex-wrap justify-center gap-3">
            <?php foreach ($cities as $city): ?>
            <?php if ($city): ?>
            <a href="<?= url('courts?city=' . urlencode($city)) ?>" class="bg-white px-6 py-3 rounded-full shadow-sm hover:shadow-md transition text-gray-700 hover:text-ph-blue">
                <i class="fas fa-map-marker-alt mr-2"></i><?= e($city) ?>
            </a>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 hero-gradient text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Play?</h2>
        <p class="text-xl text-blue-100 mb-8">
            Join thousands of Filipinos booking courts online. Fast, easy, and secure.
        </p>
        <a href="<?= url('register') ?>" class="bg-ph-yellow text-ph-blue font-semibold px-8 py-4 rounded-lg hover:bg-yellow-400 transition inline-block">
            <i class="fas fa-user-plus mr-2"></i> Create Free Account
        </a>
    </div>
</section>
