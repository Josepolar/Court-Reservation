-- Court Types Table
CREATE TABLE IF NOT EXISTS court_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default court types (Philippine sports facilities)
INSERT INTO court_types (name, slug, icon, description) VALUES 
('Basketball Court', 'basketball', 'basketball', 'Full court and half court basketball facilities'),
('Badminton Court', 'badminton', 'badminton', 'Indoor badminton courts with proper flooring'),
('Tennis Court', 'tennis', 'tennis', 'Outdoor and indoor tennis courts'),
('Volleyball Court', 'volleyball', 'volleyball', 'Indoor and beach volleyball courts'),
('Pickleball Court', 'pickleball', 'pickleball', 'Dedicated pickleball court with proper markings'),
('Futsal Court', 'futsal', 'futsal', 'Indoor futsal/football courts'),
('Covered Court', 'covered', 'building', 'Multipurpose covered courts'),
('Barangay Gym', 'gym', 'gym', 'Local barangay gymnasium facilities'),
('Swimming Pool', 'swimming', 'swimming', 'Swimming pool facilities')
ON DUPLICATE KEY UPDATE id=id;

-- Courts Table
CREATE TABLE IF NOT EXISTS courts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    court_type_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    barangay VARCHAR(100),
    city VARCHAR(100),
    province VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    hourly_rate DECIMAL(10, 2) NOT NULL,
    peak_hour_rate DECIMAL(10, 2),
    half_court_rate DECIMAL(10, 2), -- For basketball
    downpayment_percent INT DEFAULT 50,
    min_booking_hours INT DEFAULT 1,
    max_booking_hours INT DEFAULT 4,
    capacity INT, -- Number of players
    amenities JSON, -- ["parking", "shower", "locker", "lights", "aircon"]
    rules TEXT,
    images JSON, -- Array of image paths
    thumbnail VARCHAR(255),
    rating DECIMAL(3, 2) DEFAULT 0,
    total_reviews INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    requires_approval BOOLEAN DEFAULT FALSE,
    operating_hours JSON, -- {"monday": {"open": "06:00", "close": "22:00"}, ...}
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (court_type_id) REFERENCES court_types(id),
    INDEX idx_type (court_type_id),
    INDEX idx_city (city),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample courts
INSERT INTO courts (court_type_id, name, description, location, barangay, city, province, hourly_rate, peak_hour_rate, capacity, amenities, rules, thumbnail) VALUES 
(2, 'QC Badminton Center - Court A', 'Professional badminton court with wooden flooring and proper lighting', 'Timog Avenue', 'Sacred Heart', 'Quezon City', 'Metro Manila', 300.00, 400.00, 4, '["parking", "shower", "aircon", "lights"]', 'Proper badminton shoes required. No food inside court.', '/images/courts/badminton-1.jpg'),
(2, 'QC Badminton Center - Court B', 'Professional badminton court with wooden flooring and proper lighting', 'Timog Avenue', 'Sacred Heart', 'Quezon City', 'Metro Manila', 300.00, 400.00, 4, '["parking", "shower", "aircon", "lights"]', 'Proper badminton shoes required. No food inside court.', '/images/courts/badminton-2.jpg'),
(1, 'Brgy. San Antonio Basketball Court', 'Full court outdoor basketball with lights', 'San Antonio Street', 'San Antonio', 'Makati City', 'Metro Manila', 500.00, 700.00, 20, '["lights", "parking"]', 'Respect barangay curfew (10PM). Proper attire required.', '/images/courts/basketball-1.jpg'),
(1, 'Makati Hoops Arena - Full Court', 'Indoor air-conditioned full court basketball', 'Jupiter Street', 'Bel-Air', 'Makati City', 'Metro Manila', 1500.00, 2000.00, 20, '["parking", "shower", "locker", "lights", "aircon"]', 'Rubber shoes only. No food or drinks on court.', '/images/courts/basketball-2.jpg'),
(5, 'Manila Futsal Club', 'Premium futsal court with artificial turf', 'Taft Avenue', 'Malate', 'Manila', 'Metro Manila', 800.00, 1000.00, 14, '["parking", "shower", "locker", "lights"]', 'Futsal shoes only. Shin guards recommended.', '/images/courts/futsal-1.jpg'),
(6, 'Barangay Covered Court - Pasig', 'Multipurpose covered court for various sports', 'Pasig Boulevard', 'Kapitolyo', 'Pasig City', 'Metro Manila', 400.00, 500.00, 30, '["lights", "parking"]', 'First come first served for equipment. Follow barangay rules.', '/images/courts/covered-1.jpg'),
(3, 'Rizal Tennis Club', 'Clay tennis court with professional maintenance', 'Vito Cruz', 'Malate', 'Manila', 'Metro Manila', 600.00, 800.00, 4, '["parking", "shower", "locker", "lights"]', 'Tennis shoes only. No metal spikes.', '/images/courts/tennis-1.jpg')
ON DUPLICATE KEY UPDATE id=id;
