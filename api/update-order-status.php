<?php
/**
 * Update Order Status API
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

// Validate parameters (supports order_id or order_number lookup)
$status = isset($input['status']) ? trim($input['status']) : null;
$orderId = isset($input['order_id']) ? $input['order_id'] : null;
$orderNumber = isset($input['order_number']) ? trim($input['order_number']) : null;
$deliveryManId = isset($input['delivery_man_id']) ? (int)$input['delivery_man_id'] : null;

if (!$status || (!$orderId && !$orderNumber)) {
    sendJsonResponse(false, "Missing required status, or order_id/order_number.", null, 400);
}

// Validate status against constants
if (!isValidOrderStatus($status)) {
    sendJsonResponse(false, "Invalid status state. Allowed states: pending, preparing, ready, delivering, delivered, cancelled.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Look up active order details
    if ($orderId) {
        $stmt = $db->prepare("SELECT id, user_id, order_number, status FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$orderId]);
    } else {
        $stmt = $db->prepare("SELECT id, user_id, order_number, status FROM orders WHERE order_number = ? LIMIT 1");
        $stmt->execute([$orderNumber]);
    }
    
    $order = $stmt->fetch();
    if (!$order) {
        sendJsonResponse(false, "Order not found.", null, 404);
    }
    
    $oId = (int)$order['id'];
    $uId = (int)$order['user_id'];
    $oNum = $order['order_number'];
    $currentStatus = $order['status'];
    
    // Business logic safety check: Do not update already completed/cancelled orders
    if ($currentStatus === STATUS_DELIVERED || $currentStatus === STATUS_CANCELLED) {
        sendJsonResponse(false, "Cannot modify completed or cancelled orders.", null, 400);
    }
    
    // Map status transition to chronological tracking columns
    $timestampColumn = null;
    if ($status === STATUS_PREPARING) {
        $timestampColumn = 'confirmed_at';
    } elseif ($status === STATUS_READY || $status === STATUS_DELIVERING) {
        $timestampColumn = 'prepared_at';
    } elseif ($status === STATUS_DELIVERED) {
        $timestampColumn = 'delivered_at';
    }
    
    // Start transaction
    $db->beginTransaction();
    
    // Prepare update query (supporting optional delivery_man_id)
    if ($deliveryManId) {
        if ($timestampColumn) {
            $updateQuery = "UPDATE orders 
                            SET status = ?, delivery_man_id = ?, {$timestampColumn} = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP 
                            WHERE id = ?";
            $params = [$status, $deliveryManId, $oId];
        } else {
            $updateQuery = "UPDATE orders 
                            SET status = ?, delivery_man_id = ?, updated_at = CURRENT_TIMESTAMP 
                            WHERE id = ?";
            $params = [$status, $deliveryManId, $oId];
        }
    } else {
        if ($timestampColumn) {
            $updateQuery = "UPDATE orders 
                            SET status = ?, {$timestampColumn} = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP 
                            WHERE id = ?";
            $params = [$status, $oId];
        } else {
            $updateQuery = "UPDATE orders 
                            SET status = ?, updated_at = CURRENT_TIMESTAMP 
                            WHERE id = ?";
            $params = [$status, $oId];
        }
    }
    
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute($params);
    
    // Formulate a user-friendly notification message based on status
    $message = "Your order {$oNum} status has been updated to {$status}.";
    if ($status === STATUS_PREPARING) {
        $message = "Your order {$oNum} has been accepted and is now being prepared in the kitchen.";
    } elseif ($status === STATUS_READY) {
        $message = "Great news! Your order {$oNum} is ready and is now out for delivery.";
    } elseif ($status === STATUS_DELIVERING) {
        $message = "Great news! Your order {$oNum} is on the way. Our delivery rider is coming to your location.";
    } elseif ($status === STATUS_DELIVERED) {
        $message = "Your order {$oNum} has been delivered successfully. Enjoy your premium meal!";
    } elseif ($status === STATUS_CANCELLED) {
        $message = "Your order {$oNum} has been cancelled by the system.";
    }
    
    // Log user-facing alert
    $notifyStmt = $db->prepare("
        INSERT INTO notifications_log (user_id, order_id, message, is_read) 
        VALUES (?, ?, ?, 0)
    ");
    $notifyStmt->execute([$uId, $oId, $message]);
    
    $db->commit();
    
    sendJsonResponse(true, "Order status updated successfully.", [
        'order_id' => $oId,
        'order_number' => $oNum,
        'status' => $status
    ]);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    logCustomError("update-order-status.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to update status: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
