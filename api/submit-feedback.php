<?php
/**
 * Submit Order Feedback API
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

// Get JSON input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    sendJsonResponse(false, "Invalid JSON payload.", null, 400);
}

$input = sanitizeInput($input);
$orderNumber = isset($input['order_number']) ? trim($input['order_number']) : null;
$rating = isset($input['rating']) ? (int)$input['rating'] : null;
$comment = isset($input['comment']) ? trim($input['comment']) : '';

if (empty($orderNumber) || $rating === null || $rating < 1 || $rating > 5) {
    sendJsonResponse(false, "Missing or invalid required fields. order_number and rating (1-5) are required.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Fetch order to verify eligibility
    $stmt = $db->prepare("SELECT id, user_id, status FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    
    if (!$order) {
        sendJsonResponse(false, "Order '{$orderNumber}' not found.", null, 404);
    }
    
    if (strtolower($order['status']) !== 'delivered') {
        sendJsonResponse(false, "Feedback can only be submitted for delivered orders. Current status: " . $order['status'], null, 400);
    }
    
    $orderId = (int)$order['id'];
    $userId = (int)$order['user_id'];
    
    // Update order with feedback details
    $updateStmt = $db->prepare("
        UPDATE orders 
        SET feedback_rating = ?,
            feedback_comment = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $updateStmt->execute([$rating, $comment, $orderId]);
    
    // Log a notification for the feedback
    $custMsg = "Thank you for sharing your feedback for order #" . $orderNumber . "!";
    $notifStmt = $db->prepare("
        INSERT INTO notifications_log (user_id, order_id, message, is_read)
        VALUES (?, ?, ?, 0)
    ");
    $notifStmt->execute([$userId, $orderId, $custMsg]);
    
    sendJsonResponse(true, "Feedback submitted successfully. Thank you!", [
        'order_number' => $orderNumber,
        'feedback_rating' => $rating,
        'feedback_comment' => $comment
    ]);
    
} catch (Exception $e) {
    logCustomError("submit-feedback.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to submit feedback: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
