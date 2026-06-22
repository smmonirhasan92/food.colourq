<?php
/**
 * Delete Menu Item API (Soft Delete)
 * Food Delivery & Real-Time Notification System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Enable CORS
handleCORS();

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

$itemId = isset($input['id']) ? (int)$input['id'] : null;

if ($itemId === null) {
    sendJsonResponse(false, "Missing required fields. Please ensure 'id' is provided.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Check if menu item exists
    $checkStmt = $db->prepare("SELECT id, name FROM menu_items WHERE id = ? AND is_deleted = 0 LIMIT 1");
    $checkStmt->execute([$itemId]);
    $item = $checkStmt->fetch();
    
    if (!$item) {
        sendJsonResponse(false, "Menu item not found or already deleted.", null, 404);
    }
    
    // Soft delete menu item
    $updateStmt = $db->prepare("
        UPDATE menu_items 
        SET is_deleted = 1, is_available = 0, created_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    $updateStmt->execute([$itemId]);
    
    sendJsonResponse(true, "Menu item '{$item['name']}' deleted successfully from catalog.");
    
} catch (Exception $e) {
    logCustomError("delete-menu-item.php error: " . $e->getMessage());
    sendJsonResponse(false, "Failed to delete menu item. Please try again later.", null, 500);
}
