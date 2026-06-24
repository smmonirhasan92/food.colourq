<?php
/**
 * GET Active Menu Items API
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
    
    // Fetch active non-deleted items sorted by category and name
    $query = "SELECT id, name, description, price, discount_price, delivery_charge, category, image_url 
              FROM menu_items 
              WHERE is_available = 1 AND is_deleted = 0
              ORDER BY category ASC, name ASC";
    
    $stmt = $db->query($query);
    $items = $stmt->fetchAll();

    // Categorize items dynamically
    $categorized = [];
    $categoryMap = [];
    
    try {
        $categoriesStmt = $db->query("SELECT slug FROM menu_categories ORDER BY id ASC");
        $categories = $categoriesStmt->fetchAll();
        
        foreach ($categories as $cat) {
            $slug = trim($cat['slug']);
            $categorized[$slug] = [];
            $categoryMap[$slug] = $slug;
        }
    } catch (Exception $e) {
        // Fallback to defaults if table doesn't exist
        $categorized = [
            'appetizer' => [],
            'main' => [],
            'dessert' => [],
            'drink' => []
        ];
        $categoryMap = [
            'appetizer' => 'appetizer',
            'main' => 'main',
            'dessert' => 'dessert',
            'drink' => 'drink'
        ];
    }

    // Fetch all active variations
    $variationsQuery = "SELECT id, menu_item_id, name, price FROM menu_item_variations WHERE is_available = 1 ORDER BY id ASC";
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
            'price' => (float)$var['price']
        ];
    }

    foreach ($items as $item) {
        $category = trim($item['category']);
        
        // Find mapped category, fallback to appetizer/first category if unknown
        $targetCategory = isset($categoryMap[$category]) ? $categoryMap[$category] : (empty($categoryMap) ? 'appetizer' : array_key_first($categoryMap));
        
        // Ensure initialized array
        if (!isset($categorized[$targetCategory])) {
            $categorized[$targetCategory] = [];
        }
        
        $itemId = (int)$item['id'];
        $itemVars = isset($variationsMap[$itemId]) ? $variationsMap[$itemId] : [];

        $categorized[$targetCategory][] = [
            'id' => $itemId,
            'name' => $item['name'],
            'description' => $item['description'],
            'price' => (float)$item['price'],
            'discount_price' => $item['discount_price'] !== null ? (float)$item['discount_price'] : null,
            'delivery_charge' => isset($item['delivery_charge']) ? (int)$item['delivery_charge'] : 50,
            'image_url' => $item['image_url'],
            'variations' => $itemVars
        ];
    }

    sendJsonResponse(true, "Menu retrieved successfully.", $categorized);

} catch (Exception $e) {
    logCustomError("get-menu.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve menu. Please try again later.", null, 500);
}
