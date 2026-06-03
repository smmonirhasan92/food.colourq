<?php
/**
 * Delete Menu Category API
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

$slug = isset($input['slug']) ? trim($input['slug']) : null;

if (!$slug) {
    sendJsonResponse(false, "Missing required category slug.", null, 400);
}

// Do not allow deleting system default categories
$systemDefaults = ['appetizer', 'main', 'dessert', 'drink'];
if (in_array($slug, $systemDefaults)) {
    sendJsonResponse(false, "Cannot delete system default categories.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Check if category exists
    $checkStmt = $db->prepare("SELECT id FROM menu_categories WHERE slug = ? LIMIT 1");
    $checkStmt->execute([$slug]);
    if (!$checkStmt->fetch()) {
        sendJsonResponse(false, "Category not found.", null, 404);
    }
    
    // Start transaction to safely update menu items of this category
    $db->beginTransaction();
    
    // Set all menu items under this category to 'appetizer' (default) before deleting
    $updateStmt = $db->prepare("UPDATE menu_items SET category = 'appetizer' WHERE category = ?");
    $updateStmt->execute([$slug]);
    
    // Delete the category
    $stmt = $db->prepare("DELETE FROM menu_categories WHERE slug = ?");
    $stmt->execute([$slug]);
    
    $db->commit();
    
    sendJsonResponse(true, "Category deleted successfully.", ['slug' => $slug]);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    logCustomError("delete-category.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to delete category: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
