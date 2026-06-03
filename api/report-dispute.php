<?php
/**
 * Report Order Dispute API
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
$category = isset($input['category']) ? trim($input['category']) : null;
$description = isset($input['description']) ? trim($input['description']) : null;

if (empty($orderNumber) || empty($category) || empty($description)) {
    sendJsonResponse(false, "Missing required fields. order_number, category, and description are required.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Fetch order to verify eligibility
    $stmt = $db->prepare("SELECT id, user_id, status, dispute_status FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    
    if (!$order) {
        sendJsonResponse(false, "Order '{$orderNumber}' not found.", null, 404);
    }
    
    if (strtolower($order['status']) !== 'delivered') {
        sendJsonResponse(false, "Disputes can only be filed for delivered orders. Current status: " . $order['status'], null, 400);
    }
    
    if (!empty($order['dispute_status'])) {
        sendJsonResponse(false, "A dispute has already been filed for this order (Status: " . $order['dispute_status'] . ").", null, 400);
    }
    
    $orderId = (int)$order['id'];
    $userId = (int)$order['user_id'];
    
    // Update order with dispute details
    $updateStmt = $db->prepare("
        UPDATE orders 
        SET dispute_status = 'pending',
            dispute_category = ?,
            dispute_description = ?,
            dispute_reported_at = CURRENT_TIMESTAMP,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $updateStmt->execute([$category, $description, $orderId]);
    
    // Log notifications for the dispute
    // Notification for customer
    $custMsg = "Your quality claim for order #" . $orderNumber . " has been received. Our team will resolve it shortly.";
    $notifStmt = $db->prepare("
        INSERT INTO notifications_log (user_id, order_id, message, is_read)
        VALUES (?, ?, ?, 0)
    ");
    $notifStmt->execute([$userId, $orderId, $custMsg]);
    
    // Get admins to notify them
    $adminStmt = $db->query("SELECT id FROM users WHERE role = 'admin'");
    $admins = $adminStmt->fetchAll();
    $adminMsg = "A new quality dispute has been filed for Order #" . $orderNumber . ": " . $category;
    foreach ($admins as $admin) {
        $notifStmt->execute([(int)$admin['id'], $orderId, $adminMsg]);
    }
    
    sendJsonResponse(true, "Dispute reported successfully. Admin review is pending.", [
        'order_number' => $orderNumber,
        'dispute_status' => 'pending',
        'dispute_category' => $category,
        'dispute_description' => $description
    ]);
    
} catch (Exception $e) {
    logCustomError("report-dispute.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to report dispute: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
