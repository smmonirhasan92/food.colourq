<?php
/**
 * Add Delivery Man API
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

$name = isset($input['name']) ? trim($input['name']) : null;
$phone = isset($input['phone']) ? trim($input['phone']) : null;

if (!$name || !$phone) {
    sendJsonResponse(false, "Missing required rider name or phone number.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Insert new delivery man
    $stmt = $db->prepare("INSERT INTO delivery_men (name, phone, status) VALUES (?, ?, 'available')");
    $stmt->execute([$name, $phone]);
    
    $riderId = $db->lastInsertId();
    
    sendJsonResponse(true, "Rider added successfully.", [
        'id' => $riderId,
        'name' => $name,
        'phone' => $phone,
        'status' => 'available'
    ]);
    
} catch (Exception $e) {
    logCustomError("add-delivery-man.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to add rider: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
