<?php
/**
 * GET All Menu Items API (Including Unavailable)
 * Food Delivery & Real-Time Notification System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Enable CORS
handleCORS();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(false, "Method not allowed. Only GET is accepted.", null, 405);
}

try {
    $db = Database::getConnection();
    
    // Fetch all non-deleted items sorted by category and name
    $query = "SELECT id, name, description, price, cost_price, discount_price, category, image_url, is_available 
              FROM menu_items 
              WHERE is_deleted = 0
              ORDER BY category ASC, name ASC";
    
    $stmt = $db->query($query);
    $items = $stmt->fetchAll();

    // Typecast numeric fields
    foreach ($items as &$item) {
        $item['id'] = (int)$item['id'];
        $item['price'] = (float)$item['price'];
        $item['cost_price'] = (float)($item['cost_price'] ?? 0.00);
        $item['discount_price'] = $item['discount_price'] !== null ? (float)$item['discount_price'] : null;
        $item['is_available'] = (int)$item['is_available'];
    }
    unset($item);

    sendJsonResponse(true, "All menu items retrieved successfully.", $items);

} catch (Exception $e) {
    logCustomError("get-all-menu-items.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve all menu items. Please try again later.", null, 500);
}
