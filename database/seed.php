<?php
/**
 * Database Seeder for Court Reservation System
 * Run this file once to populate the database with initial data
 * Command: php database/seed.php
 */

require_once __DIR__ . '/../config/database.php';

class DatabaseSeeder
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function run()
    {
        echo "Starting database seeding...\n\n";
        
        $this->seedCourtTypes();
        $this->seedCourts();
        $this->seedCourtSchedules();
        $this->seedAdminUser();
        $this->seedTestUsers();
        
        echo "\n✅ Database seeding completed!\n";
    }
    
    private function seedCourtTypes()
    {
        echo "Seeding court types...\n";
        
        $types = [
            ['name' => 'Basketball Court', 'description' => 'Standard indoor/outdoor basketball court', 'icon' => 'fa-basketball-ball'],
            ['name' => 'Badminton Court', 'description' => 'Professional badminton court with proper flooring', 'icon' => 'fa-shuttlecock'],
            ['name' => 'Volleyball Court', 'description' => 'Indoor volleyball court with net', 'icon' => 'fa-volleyball-ball'],
            ['name' => 'Tennis Court', 'description' => 'Hard court tennis facility', 'icon' => 'fa-baseball-ball'],
            ['name' => 'Table Tennis', 'description' => 'Indoor table tennis area', 'icon' => 'fa-table-tennis'],
            ['name' => 'Pickleball Court', 'description' => 'Dedicated pickleball court with proper markings', 'icon' => 'fa-table-tennis'],
            ['name' => 'Multi-Purpose', 'description' => 'Versatile court for multiple sports', 'icon' => 'fa-running'],
        ];
        
        foreach ($types as $type) {
            $stmt = $this->db->prepare("INSERT IGNORE INTO court_types (name, description, icon) VALUES (?, ?, ?)");
            $stmt->execute([$type['name'], $type['description'], $type['icon']]);
        }
        
        echo "  ✓ Court types seeded\n";
    }
    
    private function seedCourts()
    {
        echo "Seeding courts...\n";
        
        $courts = [
            // Basketball Courts
            [
                'court_type_id' => 1,
                'name' => 'Court A - Makati Sports Center',
                'description' => 'Full-size indoor basketball court with air conditioning and premium wooden flooring. Professional lighting system installed.',
                'location' => '123 Ayala Avenue, Makati City',
                'price_per_hour' => 500.00,
                'peak_price_per_hour' => 700.00,
                'weekend_price_per_hour' => 600.00,
                'max_players' => 10,
                'amenities' => json_encode(['aircon', 'lockers', 'showers', 'parking', 'wifi', 'scoreboard']),
                'status' => 'active'
            ],
            [
                'court_type_id' => 1,
                'name' => 'Court B - BGC Arena',
                'description' => 'Outdoor basketball court with night lights and rubber flooring.',
                'location' => '456 Bonifacio High Street, Taguig City',
                'price_per_hour' => 400.00,
                'peak_price_per_hour' => 550.00,
                'weekend_price_per_hour' => 500.00,
                'max_players' => 10,
                'amenities' => json_encode(['lights', 'parking', 'water', 'seating']),
                'status' => 'active'
            ],
            [
                'court_type_id' => 1,
                'name' => 'Court C - QC Sports Hub',
                'description' => 'Indoor basketball court perfect for practice and friendly games.',
                'location' => '789 Commonwealth Ave, Quezon City',
                'price_per_hour' => 350.00,
                'peak_price_per_hour' => 500.00,
                'weekend_price_per_hour' => 450.00,
                'max_players' => 10,
                'amenities' => json_encode(['lockers', 'water', 'seating']),
                'status' => 'active'
            ],
            
            // Badminton Courts
            [
                'court_type_id' => 2,
                'name' => 'Badminton Court 1 - Manila',
                'description' => 'Professional badminton court with synthetic flooring and proper height ceiling.',
                'location' => '321 Taft Avenue, Manila',
                'price_per_hour' => 300.00,
                'peak_price_per_hour' => 400.00,
                'weekend_price_per_hour' => 350.00,
                'max_players' => 4,
                'amenities' => json_encode(['aircon', 'lockers', 'equipment', 'wifi']),
                'status' => 'active'
            ],
            [
                'court_type_id' => 2,
                'name' => 'Badminton Court 2 - Pasig',
                'description' => 'Air-conditioned badminton facility with equipment rental available.',
                'location' => '567 Ortigas Center, Pasig City',
                'price_per_hour' => 350.00,
                'peak_price_per_hour' => 450.00,
                'weekend_price_per_hour' => 400.00,
                'max_players' => 4,
                'amenities' => json_encode(['aircon', 'equipment', 'showers', 'parking']),
                'status' => 'active'
            ],
            
            // Volleyball Court
            [
                'court_type_id' => 3,
                'name' => 'Indoor Volleyball Arena',
                'description' => 'Standard volleyball court with proper ceiling height and cushioned flooring.',
                'location' => '890 Shaw Boulevard, Mandaluyong City',
                'price_per_hour' => 450.00,
                'peak_price_per_hour' => 600.00,
                'weekend_price_per_hour' => 550.00,
                'max_players' => 12,
                'amenities' => json_encode(['aircon', 'lockers', 'showers', 'equipment', 'seating']),
                'status' => 'active'
            ],
            
            // Tennis Court
            [
                'court_type_id' => 4,
                'name' => 'Tennis Court - Alabang',
                'description' => 'Well-maintained hard court tennis facility with night lights.',
                'location' => '234 Alabang-Zapote Road, Muntinlupa City',
                'price_per_hour' => 600.00,
                'peak_price_per_hour' => 800.00,
                'weekend_price_per_hour' => 700.00,
                'max_players' => 4,
                'amenities' => json_encode(['lights', 'parking', 'lockers', 'equipment']),
                'status' => 'active'
            ],
        ];
        
        foreach ($courts as $court) {
            $stmt = $this->db->prepare("
                INSERT INTO courts (court_type_id, name, description, location, price_per_hour, peak_price_per_hour, weekend_price_per_hour, max_players, amenities, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $court['court_type_id'],
                $court['name'],
                $court['description'],
                $court['location'],
                $court['price_per_hour'],
                $court['peak_price_per_hour'],
                $court['weekend_price_per_hour'],
                $court['max_players'],
                $court['amenities'],
                $court['status']
            ]);
        }
        
        echo "  ✓ " . count($courts) . " courts seeded\n";
    }
    
    private function seedCourtSchedules()
    {
        echo "Seeding court schedules...\n";
        
        // Get all courts
        $courts = $this->db->query("SELECT id FROM courts")->fetchAll(PDO::FETCH_COLUMN);
        
        $count = 0;
        foreach ($courts as $courtId) {
            // Add schedules for each day of the week
            for ($day = 0; $day <= 6; $day++) {
                $stmt = $this->db->prepare("
                    INSERT INTO court_schedules (court_id, day_of_week, open_time, close_time, is_available)
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                // Different hours for weekdays vs weekends
                if ($day >= 1 && $day <= 5) {
                    $stmt->execute([$courtId, $day, '06:00:00', '22:00:00', 1]);
                } else {
                    $stmt->execute([$courtId, $day, '07:00:00', '21:00:00', 1]);
                }
                $count++;
            }
        }
        
        echo "  ✓ " . $count . " schedules seeded\n";
    }
    
    private function seedAdminUser()
    {
        echo "Seeding admin user...\n";
        
        $admin = [
            'name' => 'Admin User',
            'email' => 'admin@courtreserve.ph',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'phone' => '09171234567',
            'role' => 'admin',
            'email_verified_at' => date('Y-m-d H:i:s')
        ];
        
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO users (name, email, password, phone, role, email_verified_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $admin['name'],
            $admin['email'],
            $admin['password'],
            $admin['phone'],
            $admin['role'],
            $admin['email_verified_at']
        ]);
        
        echo "  ✓ Admin user created\n";
        echo "    Email: admin@courtreserve.ph\n";
        echo "    Password: admin123\n";
    }
    
    private function seedTestUsers()
    {
        echo "Seeding test users...\n";
        
        $users = [
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'phone' => '09181234567',
                'role' => 'user'
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'phone' => '09191234567',
                'role' => 'user'
            ],
            [
                'name' => 'Pedro Reyes',
                'email' => 'pedro@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'phone' => '09201234567',
                'role' => 'user'
            ],
        ];
        
        foreach ($users as $user) {
            $stmt = $this->db->prepare("
                INSERT IGNORE INTO users (name, email, password, phone, role)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user['name'],
                $user['email'],
                $user['password'],
                $user['phone'],
                $user['role']
            ]);
        }
        
        echo "  ✓ " . count($users) . " test users seeded\n";
        echo "    Test user password: password123\n";
    }
}

// Run seeder
try {
    $seeder = new DatabaseSeeder($db);
    $seeder->run();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
