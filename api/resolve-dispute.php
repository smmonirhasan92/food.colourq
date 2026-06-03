<?php
/**
 * Resolve Order Dispute API
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
$resolution = isset($input['resolution']) ? trim($input['resolution']) : null; // 'refund' or 'replace'

if (empty($orderNumber) || empty($resolution) || !in_array($resolution, ['refund', 'replace'])) {
    sendJsonResponse(false, "Missing or invalid fields. order_number and resolution ('refund' or 'replace') are required.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Fetch order to verify eligibility
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    
    if (!$order) {
        sendJsonResponse(false, "Order '{$orderNumber}' not found.", null, 404);
    }
    
    if ($order['dispute_status'] !== 'pending') {
        sendJsonResponse(false, "This order does not have a pending dispute to resolve. Current dispute status: " . ($order['dispute_status'] ?: 'None'), null, 400);
    }
    
    $orderId = (int)$order['id'];
    $userId = (int)$order['user_id'];
    
    $db->beginTransaction();
    
    if ($resolution === 'refund') {
        // 1. Resolve with Refund
        $updateStmt = $db->prepare("
            UPDATE orders 
            SET dispute_status = 'refunded',
                status = 'cancelled', -- Refunded order is visually marked as cancelled/refunded
                updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
        ");
        $updateStmt->execute([$orderId]);
        
        // Log notification for customer
        $custMsg = "Your refund for order #" . $orderNumber . " due to quality issues has been successfully processed.";
        $notifStmt = $db->prepare("
            INSERT INTO notifications_log (user_id, order_id, message, is_read)
            VALUES (?, ?, ?, 0)
        ");
        $notifStmt->execute([$userId, $orderId, $custMsg]);
        
        // Log notification for admin
        $adminStmt = $db->query("SELECT id FROM users WHERE role = 'admin'");
        $admins = $adminStmt->fetchAll();
        $adminMsg = "Refund approved for order #" . $orderNumber . " due to quality issues.";
        foreach ($admins as $admin) {
            $notifStmt->execute([(int)$admin['id'], $orderId, $adminMsg]);
        }
        
        $db->commit();
        
        sendJsonResponse(true, "Dispute resolved successfully with Full Refund.", [
            'order_number' => $orderNumber,
            'dispute_status' => 'refunded',
            'resolution' => 'refund'
        ]);
        
    } else {
        // 2. Resolve with Replacement Order
        // Update original order dispute status
        $updateStmt = $db->prepare("
            UPDATE orders 
            SET dispute_status = 'replaced',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $updateStmt->execute([$orderId]);
        
        // Generate new unique order number with replacement suffix
        $newOrderNumber = $orderNumber . '-R';
        
        // Ensure uniqueness
        $checkOrder = $db->prepare("SELECT id FROM orders WHERE order_number = ? LIMIT 1");
        $checkOrder->execute([$newOrderNumber]);
        if ($checkOrder->fetch()) {
            $newOrderNumber = $orderNumber . '-R' . rand(10, 99);
        }
        
        // Fetch order items to copy
        $itemsStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$orderId]);
        $orderItems = $itemsStmt->fetchAll();
        
        // Insert new order (marked as replacement in address)
        $newAddress = "[REPLACEMENT ORDER - Original ID: #" . $orderNumber . "] " . $order['delivery_address'];
        $insertOrder = $db->prepare("
            INSERT INTO orders (user_id, order_number, total_price, status, delivery_address, phone, is_notified) 
            VALUES (?, ?, ?, 'pending', ?, ?, 0)
        ");
        $insertOrder->execute([
            $userId, 
            $newOrderNumber, 
            $order['total_price'], 
            $newAddress, 
            $order['phone']
        ]);
        $newOrderId = (int)$db->lastInsertId();
        
        // Insert order items
        $insertItem = $db->prepare("
            INSERT INTO order_items (order_id, menu_item_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        foreach ($orderItems as $item) {
            $insertItem->execute([
                $newOrderId, 
                (int)$item['menu_item_id'], 
                (int)$item['quantity'], 
                (float)$item['price']
            ]);
        }
        
        // Log notifications for Customer
        $custMsg = "Your quality claim for order #" . $orderNumber . " has been resolved! A free replacement order #" . $newOrderNumber . " has been sent to the kitchen.";
        $notifStmt = $db->prepare("
            INSERT INTO notifications_log (user_id, order_id, message, is_read)
            VALUES (?, ?, ?, 0)
        ");
        $notifStmt->execute([$userId, $newOrderId, $custMsg]);
        
        // Log notifications for Admin
        $adminStmt = $db->query("SELECT id FROM users WHERE role = 'admin'");
        $admins = $adminStmt->fetchAll();
        $adminMsg = "Replacement order #" . $newOrderNumber . " dispatched to the kitchen for resolving order #" . $orderNumber . " dispute.";
        foreach ($admins as $admin) {
            $notifStmt->execute([(int)$admin['id'], $newOrderId, $adminMsg]);
        }
        
        $db->commit();
        
        sendJsonResponse(true, "Dispute resolved successfully with Replacement Order dispatch.", [
            'order_number' => $orderNumber,
            'dispute_status' => 'replaced',
            'resolution' => 'replace',
            'replacement_order_number' => $newOrderNumber
        ]);
    }
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    logCustomError("resolve-dispute.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to resolve dispute: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
