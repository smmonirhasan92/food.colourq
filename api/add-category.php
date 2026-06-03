<?php
/**
 * Add Menu Category API
 * Food Delivery & Real-Time Notification System
 * Securely protected by admin session checks!
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Enable CORS
handleCORS();

// Enable Sessions
session_start();

// Security session check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    sendJsonResponse(false, "Unauthorized access. Admin privileges required.", null, 401);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed. Only POST is accepted.", null, 405);
}

// Get JSON raw input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    sendJsonResponse(false, "Invalid JSON payload.", null, 400);
}

// Sanitize inputs
$input = sanitizeInput($input);

$name = isset($input['name']) ? trim($input['name']) : null;

if (!$name) {
    sendJsonResponse(false, "Missing required category name.", null, 400);
}

// Generate lowercase web-safe slug
$slug = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '-', $name));
$slug = preg_replace('/-+/', '-', $slug); // trim multiple dashes
$slug = trim($slug, '-');

if (empty($slug)) {
    sendJsonResponse(false, "Invalid category name structure.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Check if category name or slug already exists
    $checkStmt = $db->prepare("SELECT id FROM menu_categories WHERE name = ? OR slug = ? LIMIT 1");
    $checkStmt->execute([$name, $slug]);
    if ($checkStmt->fetch()) {
        sendJsonResponse(false, "Category name or slug already exists.", null, 400);
    }
    
    // Insert new category
    $stmt = $db->prepare("INSERT INTO menu_categories (name, slug) VALUES (?, ?)");
    $stmt->execute([$name, $slug]);
    
    $catId = $db->lastInsertId();
    
    sendJsonResponse(true, "Category created successfully.", [
        'id' => $catId,
        'name' => $name,
        'slug' => $slug
    ]);
    
} catch (Exception $e) {
    logCustomError("add-category.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to create category: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
