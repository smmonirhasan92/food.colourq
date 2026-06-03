<?php
/**
 * Get Menu Categories API
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
    
    // Fetch all categories
    $query = "SELECT id, name, slug FROM menu_categories ORDER BY id ASC";
    $stmt = $db->query($query);
    $categories = $stmt->fetchAll();
    
    // If no categories found, seed defaults dynamically as fallback
    if (empty($categories)) {
        $defaults = [
            ['name' => 'Starter', 'slug' => 'appetizer'],
            ['name' => 'Best Seller', 'slug' => 'main'],
            ['name' => 'Dessert', 'slug' => 'dessert'],
            ['name' => 'Drink', 'slug' => 'drink']
        ];
        
        $db->beginTransaction();
        $insertStmt = $db->prepare("INSERT INTO menu_categories (name, slug) VALUES (?, ?)");
        foreach ($defaults as $cat) {
            $insertStmt->execute([$cat['name'], $cat['slug']]);
        }
        $db->commit();
        
        // Re-fetch
        $stmt = $db->query($query);
        $categories = $stmt->fetchAll();
    }
    
    sendJsonResponse(true, "Categories retrieved successfully.", $categories);
    
} catch (Exception $e) {
    logCustomError("get-categories.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve categories: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
