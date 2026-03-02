<?php
/**
 * Courts by Type View
 * Shows all courts of a specific type
 */
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="<?= url('/') ?>" class="text-gray-500 hover:text-ph-blue">Home</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="<?= url('courts') ?>" class="text-gray-500 hover:text-ph-blue">Courts</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-ph-blue font-medium"><?= e($courtType['name']) ?></li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-gradient-to-r from-ph-blue to-blue-700 rounded-xl p-8 mb-8 text-white">
        <div class="flex items-center mb-4">
            <?php
            $icon = match($courtType['slug']) {
                'basketball' => 'fa-basketball-ball text-orange-400',
                'badminton' => 'fa-table-tennis text-green-400',
                'tennis' => 'fa-baseball-ball text-yellow-400',
                'volleyball' => 'fa-volleyball-ball text-blue-400',
                'futsal' => 'fa-futbol text-purple-400',
                'swimming' => 'fa-swimming-pool text-cyan-400',
                'gym' => 'fa-dumbbell text-red-400',
                default => 'fa-building text-gray-400'
            };
            ?>

            <i class="fas <?= $icon ?> text-4xl mr-4"></i>
            <div>
                <h1 class="text-3xl font-bold"><?= e($courtType['name']) ?></h1>
                <p class="text-blue-200"><?= count($courts) ?> courts available</p>
            </div>
        </div>
        <?php if ($courtType['description']): ?>
        <p class="text-blue-100"><?= e($courtType['description']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Courts Grid -->
    <?php if (empty($courts)): ?>
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
        <h2 class="text-xl font-semibold text-gray-700 mb-2">No Courts Available</h2>
        <p class="text-gray-500 mb-6">There are no <?= e($courtType['name']) ?> courts available at the moment.</p>
        <a href="<?= url('courts') ?>" class="inline-block bg-ph-blue text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition">
            <i class="fas fa-arrow-left mr-2"></i> Browse All Courts
        </a>
    </div>
    <?php else: ?>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($courts as $court): ?>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
            <div class="h-48 bg-gradient-to-br from-ph-blue to-blue-700 relative">
                <?php if (!empty($court['thumbnail'])): ?>
                <img src="<?= url($court['thumbnail']) ?>" alt="<?= e($court['name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-basketball-ball text-white/30 text-6xl"></i>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($court['peak_hour_rate'])): ?>
                <span class="absolute top-3 right-3 bg-ph-yellow text-ph-blue text-xs font-semibold px-2 py-1 rounded">
                    Peak Hours Available
                </span>
                <?php endif; ?>
            </div>
            
            <div class="p-5">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 group-hover:text-ph-blue transition"><?= e($court['name']) ?></h3>
                    <?php if (!empty($court['rating']) && $court['rating'] > 0): ?>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                        <span><?= number_format($court['rating'], 1) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <p class="text-gray-500 text-sm mb-3">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    <?= e($court['barangay'] ?? '') ?><?= $court['barangay'] && $court['city'] ? ', ' : '' ?><?= e($court['city'] ?? '') ?>
                </p>
                
                <?php if (!empty($court['capacity'])): ?>
                <p class="text-gray-500 text-sm mb-3">
                    <i class="fas fa-users mr-1"></i>
                    Up to <?= $court['capacity'] ?> players
                </p>
                <?php endif; ?>
                
                <!-- Amenities -->
                <?php $amenities = json_decode($court['amenities'] ?? '[]', true); ?>
                <?php if (!empty($amenities)): ?>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach (array_slice($amenities, 0, 4) as $amenity): ?>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">
                        <?php
                        $amenityIcon = match($amenity) {
                            'parking' => 'fa-parking',
                            'shower' => 'fa-shower',
                            'locker' => 'fa-lock',
                            'lights' => 'fa-lightbulb',
                            'aircon' => 'fa-snowflake',
                            default => 'fa-check'
                        };
                        ?>
                        <i class="fas <?= $amenityIcon ?> mr-1"></i>
                        <?= ucfirst($amenity) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <div>
                        <span class="text-ph-blue font-bold text-xl"><?= formatPrice($court['hourly_rate']) ?></span>
                        <span class="text-gray-400 text-sm">/hour</span>
                    </div>
                    <a href="<?= url('courts/' . $court['id']) ?>" class="bg-ph-blue text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
