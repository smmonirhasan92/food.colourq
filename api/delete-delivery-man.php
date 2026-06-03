<?php
/**
 * Delete Delivery Man API
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

$riderId = isset($input['id']) ? (int)$input['id'] : null;

if (!$riderId) {
    sendJsonResponse(false, "Missing required rider ID.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Check if the rider exists
    $checkStmt = $db->prepare("SELECT id FROM delivery_men WHERE id = ? LIMIT 1");
    $checkStmt->execute([$riderId]);
    if (!$checkStmt->fetch()) {
        sendJsonResponse(false, "Rider not found.", null, 404);
    }
    
    // Delete the rider
    $stmt = $db->prepare("DELETE FROM delivery_men WHERE id = ?");
    $stmt->execute([$riderId]);
    
    sendJsonResponse(true, "Rider deleted successfully.", ['id' => $riderId]);
    
} catch (Exception $e) {
    logCustomError("delete-delivery-man.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to delete rider: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
