<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Court</h1>
        <a href="<?= url('admin/courts') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Courts
        </a>
    </div>
    
    <form action="<?= url('admin/courts') ?>" method="POST" enctype="multipart/form-data" 
          class="bg-white rounded-xl shadow-sm p-6">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        
        <!-- Basic Info -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Basic Information</h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Court Name *</label>
                    <input type="text" name="name" required 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                           placeholder="e.g., Court A - Makati">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Court Type *</label>
                    <select name="court_type_id" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                        <option value="">Select Type</option>
                        <?php foreach ($courtTypes as $type): ?>
                        <option value="<?= $type['id'] ?>">
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                    <input type="text" name="location" required 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                           placeholder="e.g., 123 Sports Ave">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                    <input type="text" name="city" required 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                           placeholder="e.g., Makati City">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                              placeholder="Describe the court facilities and amenities..."></textarea>
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
                        <input type="number" name="hourly_rate" required step="0.01" min="0" value="500"
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peak Hour Price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₱</span>
                        <input type="number" name="peak_hour_rate" step="0.01" min="0" value="700"
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Half Court Rate</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₱</span>
                        <input type="number" name="half_court_rate" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                               placeholder="Optional">
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
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Capacity</h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Players *</label>
                    <input type="number" name="capacity" required min="1" value="10"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                </div>
            </div>
        </div>
        
        <!-- Amenities -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Amenities</h2>
            
            <?php 
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
                           class="w-4 h-4 text-ph-blue rounded">
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
                <div class="w-32 h-32 bg-gray-100 rounded-lg over flow-hidden flex items-center justify-center" id="imagePreview">
                    <i class="fas fa-camera text-gray-400 text-2xl"></i>
                </div>
                
                <div class="flex-1">
                    <input type="file" name="thumbnail" accept="image/*" id="imageInput" class="hidden"
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
                    <input type="radio" name="is_active" value="1" class="w-4 h-4 text-ph-blue" checked>
                    <span class="ml-2">Active</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="is_active" value="0" class="w-4 h-4 text-ph-blue">
                    <span class="ml-2">Inactive</span>
                </label>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="<?= url('admin/courts') ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Create Court
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

function previewImage(input) {

    const preview = document.getElementById("imagePreview");

    if (input.files && input.files[0]) {

        const reader = new FileReader();

        reader.onload = function(e) {
            preview.innerHTML =
                '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
        };

        reader.readAsDataURL(input.files[0]);
    }
}

</script>
