<?php
/**
 * Setup Script for Court Reservation System
 * Run this once to set up the database and create necessary directories
 */

echo "<html><head><title>Court Reservation System - Setup</title>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
    h1 { color: #0038a8; }
    .success { color: #10b981; }
    .error { color: #ef4444; }
    .info { color: #3b82f6; }
    pre { background: #f3f4f6; padding: 15px; border-radius: 8px; overflow-x: auto; }
    .btn { display: inline-block; background: #0038a8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; margin-top: 20px; }
    .btn:hover { background: #002d86; }
</style></head><body>";

echo "<h1>🏀 Court Reservation System Setup</h1>";

// Step 1: Check PHP version
echo "<h2>1. Checking PHP Version</h2>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<p class='success'>✓ PHP " . PHP_VERSION . " is installed</p>";
} else {
    echo "<p class='error'>✗ PHP 7.4+ required. Current: " . PHP_VERSION . "</p>";
}

// Step 2: Check required extensions
echo "<h2>2. Checking PHP Extensions</h2>";
$required = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'fileinfo'];
$missing = [];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✓ $ext extension loaded</p>";
    } else {
        echo "<p class='error'>✗ $ext extension missing</p>";
        $missing[] = $ext;
    }
}

// Step 3: Create directories
echo "<h2>3. Creating Directories</h2>";
$dirs = [
    __DIR__ . '/storage',
    __DIR__ . '/storage/logs',
    __DIR__ . '/storage/courts',
    __DIR__ . '/storage/proofs',
    __DIR__ . '/storage/avatars',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p class='success'>✓ Created: " . str_replace(__DIR__, '', $dir) . "</p>";
        } else {
            echo "<p class='error'>✗ Failed to create: " . str_replace(__DIR__, '', $dir) . "</p>";
        }
    } else {
        echo "<p class='info'>○ Already exists: " . str_replace(__DIR__, '', $dir) . "</p>";
    }
}

// Step 4: Database setup
echo "<h2>4. Database Setup</h2>";
echo "<p class='info'>ℹ️ Make sure MySQL is running in XAMPP</p>";

try {
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $user = $_ENV[DB_USER];
    $pass = $_ENV[DB_PASS];
    
    // Connect without database first to create it
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p class='success'>✓ Database '$dbname' ready</p>";
    
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Run migrations
    echo "<h3>Running Migrations...</h3>";
    
    $migrations = glob(__DIR__ . '/database/migrations/*.sql');
    sort($migrations);
    
    foreach ($migrations as $migration) {
        $sql = file_get_contents($migration);
        $pdo->exec($sql);
        echo "<p class='success'>✓ Executed: " . basename($migration) . "</p>";
    }
    
    // Check if data exists
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    if ($userCount == 0) {
        echo "<h3>Seeding Database...</h3>";
        
        // Seed court types
        $types = [
            ['Basketball Court', 'Standard basketball court', 'fa-basketball-ball'],
            ['Badminton Court', 'Professional badminton court', 'fa-shuttlecock'],
            ['Volleyball Court', 'Indoor volleyball court', 'fa-volleyball-ball'],
            ['Tennis Court', 'Hard court tennis facility', 'fa-baseball-ball'],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO court_types (name, description, icon) VALUES (?, ?, ?)");
        foreach ($types as $type) {
            $stmt->execute($type);
        }
        echo "<p class='success'>✓ Court types seeded</p>";
        
        // Seed sample courts
        $courts = [
            [1, 'Court A - Makati Sports Center', 'Premium indoor basketball court', '123 Ayala Ave, Makati City', 500, 700, 600, 10],
            [1, 'Court B - BGC Arena', 'Outdoor basketball court with lights', '456 Bonifacio High Street, Taguig', 400, 550, 500, 10],
            [2, 'Badminton Court 1', 'Air-conditioned badminton facility', '321 Taft Avenue, Manila', 300, 400, 350, 4],
            [3, 'Volleyball Arena', 'Standard indoor volleyball court', '890 Shaw Boulevard, Mandaluyong', 450, 600, 550, 12],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO courts (court_type_id, name, description, location, price_per_hour, peak_price_per_hour, weekend_price_per_hour, max_players, amenities, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, '[]', 'active')");
        foreach ($courts as $court) {
            $stmt->execute($court);
        }
        echo "<p class='success'>✓ Sample courts seeded</p>";
        
        // Seed court schedules
        $courtIds = $pdo->query("SELECT id FROM courts")->fetchAll(PDO::FETCH_COLUMN);
        $stmt = $pdo->prepare("INSERT INTO court_schedules (court_id, day_of_week, open_time, close_time, is_available) VALUES (?, ?, ?, ?, 1)");
        foreach ($courtIds as $courtId) {
            for ($day = 0; $day <= 6; $day++) {
                $stmt->execute([$courtId, $day, '06:00:00', '22:00:00']);
            }
        }
        echo "<p class='success'>✓ Court schedules seeded</p>";
        
        // Create admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password, phone, role, email_verified_at) VALUES ('Admin User', 'admin@courtreserve.ph', '$adminPassword', '09171234567', 'admin', NOW())");
        echo "<p class='success'>✓ Admin user created</p>";
        
        // Create test user
        $userPassword = password_hash('password123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password, phone, role) VALUES ('Test User', 'test@example.com', '$userPassword', '09181234567', 'user')");
        echo "<p class='success'>✓ Test user created</p>";
    } else {
        echo "<p class='info'>○ Database already has data, skipping seed</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Step 5: Show credentials
echo "<h2>5. Login Credentials</h2>";
echo "<pre>";
echo "Admin Login:\n";
echo "  Email: admin@courtreserve.ph\n";
echo "  Password: admin123\n\n";
echo "Test User Login:\n";
echo "  Email: test@example.com\n";
echo "  Password: password123\n";
echo "</pre>";

echo "<h2>✅ Setup Complete!</h2>";
echo "<p>Your Court Reservation System is ready to use.</p>";
echo "<a href='index.php' class='btn'>Go to Homepage</a>";
echo " <a href='index.php?url=admin' class='btn' style='background: #fbbf24; color: #1f2937;'>Admin Dashboard</a>";

echo "</body></html>";
