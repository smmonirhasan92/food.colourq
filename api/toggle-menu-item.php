<?php
/**
 * Toggle Menu Item Availability API
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
$isAvailable = isset($input['is_available']) ? (int)$input['is_available'] : null;

if ($itemId === null || $isAvailable === null) {
    sendJsonResponse(false, "Missing required fields. Please ensure id and is_available are provided.", null, 400);
}

// Validate is_available parameter
if ($isAvailable !== 0 && $isAvailable !== 1) {
    sendJsonResponse(false, "Invalid is_available value. Must be 0 or 1.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Check if menu item exists
    $checkStmt = $db->prepare("SELECT id, name FROM menu_items WHERE id = ? LIMIT 1");
    $checkStmt->execute([$itemId]);
    $item = $checkStmt->fetch();
    
    if (!$item) {
        sendJsonResponse(false, "Menu item not found.", null, 404);
    }
    
    // Update availability
    $updateStmt = $db->prepare("
        UPDATE menu_items 
        SET is_available = ?, created_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    $updateStmt->execute([$isAvailable, $itemId]);
    
    sendJsonResponse(true, "Menu item availability updated successfully.", [
        'id' => $itemId,
        'name' => $item['name'],
        'is_available' => $isAvailable
    ]);

} catch (Exception $e) {
    logCustomError("toggle-menu-item.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to toggle menu item availability: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
