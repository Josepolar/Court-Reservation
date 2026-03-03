<?php
/**
 * Database Configuration
 * Court Reservation System
 */

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']); // Default XAMPP has no password

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            // If database doesn't exist, create it
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                $this->createDatabase();
            } else {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }
    
    private function createDatabase() {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST,
                DB_USER,
                DB_PASS
            );
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            
            // Run migrations
            $this->runMigrations();
        } catch (PDOException $e) {
            die("Failed to create database: " . $e->getMessage());
        }
    }
    
    private function runMigrations() {
        $migrations = glob(__DIR__ . '/../database/migrations/*.sql');
        sort($migrations);
        
        foreach ($migrations as $migration) {
            $sql = file_get_contents($migration);
            $this->connection->exec($sql);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }
}

// Create global PDO instance for easy access
$db = Database::getInstance()->getConnection();