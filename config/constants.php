<?php
/**
 * Application Constants and Configurations
 * Food Delivery & Real-Time Notification System
 */

// Set Default Timezone for Bangladesh
date_default_timezone_set('Asia/Dhaka');

// Define application base path if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Auto-detect environment based on server hostname
$isLocalhost = true;
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    $cleanHost = explode(':', $host)[0]; // Remove port if present
    
    // Check if loopback/localhost
    $isLoopback = ($cleanHost === 'localhost' || $cleanHost === '127.0.0.1' || $cleanHost === '::1' || preg_match('/^127\.\d+\.\d+\.\d+$/', $cleanHost));
    
    // Check for private IPv4 subnets (RFC 1918) and link-local ranges
    $isPrivateIP = (
        strpos($cleanHost, '192.168.') === 0 ||
        strpos($cleanHost, '10.') === 0 ||
        preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $cleanHost) ||
        strpos($cleanHost, '169.254.') === 0
    );
    
    // Check for local network domains (e.g. *.local)
    $isLocalDomain = (substr($cleanHost, -6) === '.local');
    
    if (!$isLoopback && !$isPrivateIP && !$isLocalDomain) {
        $isLocalhost = false;
    }
}

// Site Configurations
define('APP_NAME', 'Food ColourQ');
define('APP_VERSION', '1.0.0');
define('APP_ENV', $isLocalhost ? 'development' : 'production');

// Database Connection Type ('sqlite' or 'mysql')
// Auto-switch: SQLite for zero-config local testing, MySQL for live cPanel!
define('DB_TYPE', $isLocalhost ? 'sqlite' : 'mysql');

// SQLite Configuration
define('DB_SQLITE_FILE', BASE_PATH . '/database/food_delivery.db');

// MySQL Configuration
define('DB_MYSQL_HOST', 'localhost');
define('DB_MYSQL_PORT', '3306');
define('DB_MYSQL_DB', 'crispybd_food');
define('DB_MYSQL_USER', 'crispybd_admin');
define('DB_MYSQL_PASS', 'Sir@@@admin123');
define('DB_MYSQL_CHARSET', 'utf8mb4');

// Order Status definitions
define('STATUS_PENDING', 'pending');
define('STATUS_PREPARING', 'preparing');
define('STATUS_READY', 'ready');
define('STATUS_DELIVERING', 'delivering');
define('STATUS_DELIVERED', 'delivered');
define('STATUS_CANCELLED', 'cancelled');

// Valid Order Status List for validation
const VALID_ORDER_STATUSES = [
    STATUS_PENDING,
    STATUS_PREPARING,
    STATUS_READY,
    STATUS_DELIVERING,
    STATUS_DELIVERED,
    STATUS_CANCELLED
];

// User Role definitions
define('ROLE_ADMIN', 'admin');
define('ROLE_CUSTOMER', 'customer');

// Valid User Roles
const VALID_USER_ROLES = [
    ROLE_ADMIN,
    ROLE_CUSTOMER
];

// Food Category definitions
define('CAT_APPETIZER', 'appetizer');
define('CAT_MAIN', 'main');
define('CAT_DESSERT', 'dessert');
define('CAT_DRINK', 'drink');

const VALID_FOOD_CATEGORIES = [
    CAT_APPETIZER,
    CAT_MAIN,
    CAT_DESSERT,
    CAT_DRINK
];

// Security Configurations
define('MIN_PASSWORD_LENGTH', 6);
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
