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
    
    // Fetch active items sorted by category and name
    $query = "SELECT id, name, description, price, category, image_url 
              FROM menu_items 
              WHERE is_available = 1 
              ORDER BY category ASC, name ASC";
    
    $stmt = $db->query($query);
    $items = $stmt->fetchAll();

    // Categorize items
    $categorized = [
        'appetizer' => [],
        'main' => [],
        'dessert' => [],
        'drink' => []
    ];

    // Map database categories to standard storefront keys
    $categoryMap = [
        'appetizer' => 'appetizer',
        'main' => 'main',
        'dessert' => 'dessert',
        'drink' => 'drink'
    ];

    foreach ($items as $item) {
        $category = trim($item['category']);
        
        // Find mapped category, fallback to appetizer if unknown
        $targetCategory = isset($categoryMap[$category]) ? $categoryMap[$category] : 'appetizer';
        
        $categorized[$targetCategory][] = [
            'id' => (int)$item['id'],
            'name' => $item['name'],
            'description' => $item['description'],
            'price' => (float)$item['price'],
            'image_url' => $item['image_url']
        ];
    }

    sendJsonResponse(true, "Menu retrieved successfully.", $categorized);

} catch (Exception $e) {
    logCustomError("get-menu.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve menu. Please try again later.", null, 500);
}
