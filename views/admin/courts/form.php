<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900"><?= isset($court) ? 'Edit Court' : 'Add New Court' ?></h1>
        <a href="<?= url('admin/courts') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Courts
        </a>
    </div>
    
    <form action="<?= isset($court) ? url('admin/courts/' . $court['id']) : url('admin/courts') ?>" method="POST" enctype="multipart/form-data" 
          class="bg-white rounded-xl shadow-sm p-6">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <?php if (isset($court)): ?>
        <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>
        
        <!-- Basic Info -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Basic Information</h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Court Name *</label>
                    <input type="text" name="name" required value="<?= $court['name'] ?? '' ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                           placeholder="e.g., Court A - Makati">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Court Type *</label>
                    <select name="court_type_id" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        <option value="">Select Type</option>
                        <?php foreach ($courtTypes as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= ($court['court_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                            <?= $type['name'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                    <input type="text" name="location" required value="<?= $court['location'] ?? '' ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                           placeholder="e.g., 123 Sports Ave, Makati City">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                              placeholder="Describe the court facilities and amenities..."><?= $court['description'] ?? '' ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Pricing -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Pricing</h2>
            
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Regular Price (per hour) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₱</span>
                        <input type="number" name="price_per_hour" required step="0.01" min="0" 
                               value="<?= $court['price_per_hour'] ?? config('app.default_hourly_rate') ?>"
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peak Hour Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₱</span>
                        <input type="number" name="peak_price_per_hour" required step="0.01" min="0"
                               value="<?= $court['peak_price_per_hour'] ?? config('app.peak_hour_rate') ?>"
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weekend Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₱</span>
                        <input type="number" name="weekend_price_per_hour" required step="0.01" min="0"
                               value="<?= $court['weekend_price_per_hour'] ?? config('app.weekend_rate') ?>"
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                Peak hours are typically 5 PM - 9 PM on weekdays
            </p>
        </div>
        
        <!-- Capacity & Details -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Capacity & Details</h2>
            
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Players *</label>
                    <input type="number" name="max_players" required min="1" value="<?= $court['max_players'] ?? 10 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Booking Hours</label>
                    <input type="number" name="min_booking_hours" min="1" value="<?= $court['min_booking_hours'] ?? 1 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Booking Hours</label>
                    <input type="number" name="max_booking_hours" min="1" value="<?= $court['max_booking_hours'] ?? 4 ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
            </div>
        </div>
        
        <!-- Amenities -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Amenities</h2>
            
            <?php 
            $amenities = json_decode($court['amenities'] ?? '[]', true);
            $allAmenities = [
                'wifi' => ['icon' => 'fa-wifi', 'label' => 'Free WiFi'],
                'parking' => ['icon' => 'fa-car', 'label' => 'Parking'],
                'aircon' => ['icon' => 'fa-wind', 'label' => 'Air Conditioning'],
                'lockers' => ['icon' => 'fa-lock', 'label' => 'Lockers'],
                'showers' => ['icon' => 'fa-shower', 'label' => 'Showers'],
                'lights' => ['icon' => 'fa-lightbulb', 'label' => 'Night Lights'],
                'scoreboard' => ['icon' => 'fa-tv', 'label' => 'Scoreboard'],
                'water' => ['icon' => 'fa-tint', 'label' => 'Free Water'],
                'equipment' => ['icon' => 'fa-basketball-ball', 'label' => 'Equipment Rental'],
                'seating' => ['icon' => 'fa-chair', 'label' => 'Spectator Seating'],
            ];
            ?>
            
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <?php foreach ($allAmenities as $key => $amenity): ?>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                    <input type="checkbox" name="amenities[]" value="<?= $key ?>" 
                           class="w-4 h-4 text-ph-blue rounded"
                           <?= in_array($key, $amenities) ? 'checked' : '' ?>>
                    <span class="ml-2 text-sm">
                        <i class="fas <?= $amenity['icon'] ?> text-gray-400 mr-1"></i>
                        <?= $amenity['label'] ?>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Image Upload -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Court Image</h2>
            
            <div class="flex items-center space-x-4">
                <div class="w-32 h-32 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center" id="imagePreview">
                    <?php if ($court['image'] ?? null): ?>
                  <img 
src="data:<?= htmlspecialchars($court['thumbnail_type']) ?>;base64,<?= base64_encode($court['thumbnail']) ?>" 
alt="<?= htmlspecialchars($court['name']) ?>" 
class="w-32 h-32 object-cover rounded-lg">

                    <?php else: ?>
                    <i class="fas fa-camera text-gray-400 text-2xl"></i>
                    <?php endif; ?>
                </div>
                
                <div class="flex-1">
                    <input type="file" name="image" accept="image/*" id="imageInput" class="hidden"
                           onchange="previewImage(this)">
                    <label for="imageInput" class="inline-block bg-gray-100 text-gray-700 px-4 py-2 rounded-lg cursor-pointer hover:bg-gray-200 transition">
                        <i class="fas fa-upload mr-2"></i>Choose Image
                    </label>
                    <p class="text-sm text-gray-500 mt-2">Recommended: 800x600px, JPG or PNG</p>
                </div>
            </div>
        </div>
        
        <!-- Status -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Status</h2>
            
            <div class="flex space-x-4">
                <label class="flex items-center">
                    <input type="radio" name="status" value="active" class="w-4 h-4 text-ph-blue"
                           <?= ($court['status'] ?? 'active') === 'active' ? 'checked' : '' ?>>
                    <span class="ml-2">Active</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="inactive" class="w-4 h-4 text-ph-blue"
                           <?= ($court['status'] ?? '') === 'inactive' ? 'checked' : '' ?>>
                    <span class="ml-2">Inactive</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="maintenance" class="w-4 h-4 text-ph-blue"
                           <?= ($court['status'] ?? '') === 'maintenance' ? 'checked' : '' ?>>
                    <span class="ml-2">Under Maintenance</span>
                </label>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="<?= url('admin/courts') ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i><?= isset($court) ? 'Update Court' : 'Create Court' ?>
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
