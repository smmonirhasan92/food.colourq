<?php
/**
 * Get Delivery Men Stats API
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
    
    // Fetch all delivery men and their completed delivery counts
    $query = "SELECT d.id, d.name, d.phone, d.status, COUNT(o.id) AS delivery_count
              FROM delivery_men d
              LEFT JOIN orders o ON d.id = o.delivery_man_id AND o.status = 'delivered'
              GROUP BY d.id, d.name, d.phone, d.status
              ORDER BY d.id ASC";
              
    $stmt = $db->query($query);
    $deliveryMen = $stmt->fetchAll();
    
    sendJsonResponse(true, "Delivery team retrieved successfully.", $deliveryMen);
    
} catch (Exception $e) {
    logCustomError("get-delivery-men.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve delivery team: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
