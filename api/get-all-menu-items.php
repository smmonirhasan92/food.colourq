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
    $query = "SELECT id, name, description, price, cost_price, discount_price, delivery_charge, category, image_url, is_available 
              FROM menu_items 
              WHERE is_deleted = 0
              ORDER BY category ASC, name ASC";
    
    $stmt = $db->query($query);
    $items = $stmt->fetchAll();

    // Fetch all variations for admin editing
    $variationsQuery = "SELECT id, menu_item_id, name, price, is_available FROM menu_item_variations ORDER BY id ASC";
    $varsStmt = $db->query($variationsQuery);
    $variationsList = $varsStmt->fetchAll();
    
    $variationsMap = [];
    foreach ($variationsList as $var) {
        $itemId = (int)$var['menu_item_id'];
        if (!isset($variationsMap[$itemId])) {
            $variationsMap[$itemId] = [];
        }
        $variationsMap[$itemId][] = [
            'id' => (int)$var['id'],
            'name' => $var['name'],
            'price' => (float)$var['price'],
            'is_available' => (int)$var['is_available']
        ];
    }

    // Typecast numeric fields and map variations
    foreach ($items as &$item) {
        $itemId = (int)$item['id'];
        $item['id'] = $itemId;
        $item['price'] = (float)$item['price'];
        $item['cost_price'] = (float)($item['cost_price'] ?? 0.00);
        $item['discount_price'] = $item['discount_price'] !== null ? (float)$item['discount_price'] : null;
        $item['delivery_charge'] = isset($item['delivery_charge']) ? (int)$item['delivery_charge'] : 50;
        $item['is_available'] = (int)$item['is_available'];
        $item['variations'] = isset($variationsMap[$itemId]) ? $variationsMap[$itemId] : [];
    }
    unset($item);


    sendJsonResponse(true, "All menu items retrieved successfully.", $items);

} catch (Exception $e) {
    logCustomError("get-all-menu-items.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve all menu items. Please try again later.", null, 500);
}
