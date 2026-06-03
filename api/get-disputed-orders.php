<?php
/**
 * Get Disputed Orders API
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
    
    // Fetch all disputed orders (where dispute_status is not null)
    $query = "SELECT o.id, o.order_number, o.total_price, o.status, o.delivery_address, o.phone, 
                     o.dispute_status, o.dispute_category, o.dispute_description, o.dispute_reported_at,
                     o.feedback_rating, o.feedback_comment, o.created_at,
                     u.username, u.email
              FROM orders o
              JOIN users u ON o.user_id = u.id
              WHERE o.dispute_status IS NOT NULL AND o.dispute_status != ''
              ORDER BY o.dispute_reported_at DESC";
              
    $stmt = $db->query($query);
    $orders = $stmt->fetchAll();
    
    if (empty($orders)) {
        sendJsonResponse(true, "No disputed orders found.", []);
    }
    
    // Fetch order items for each disputed order
    $orderIds = array_map(function($order) {
        return (int)$order['id'];
    }, $orders);
    
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    
    $itemsQuery = "SELECT oi.order_id, oi.menu_item_id, oi.quantity, oi.price, m.name AS item_name 
                   FROM order_items oi
                   JOIN menu_items m ON oi.menu_item_id = m.id
                   WHERE oi.order_id IN ($placeholders)";
                   
    $itemsStmt = $db->prepare($itemsQuery);
    $itemsStmt->execute($orderIds);
    $allItems = $itemsStmt->fetchAll();
    
    // Group items by order_id
    $groupedItems = [];
    foreach ($allItems as $item) {
        $orderId = (int)$item['order_id'];
        if (!isset($groupedItems[$orderId])) {
            $groupedItems[$orderId] = [];
        }
        
        $groupedItems[$orderId][] = [
            'menu_item_id' => (int)$item['menu_item_id'],
            'item_name' => $item['item_name'],
            'quantity' => (int)$item['quantity'],
            'price' => (float)$item['price']
        ];
    }
    
    // Format response payload
    $formattedOrders = [];
    foreach ($orders as $order) {
        $id = (int)$order['id'];
        $formattedOrders[] = [
            'order_id' => $id,
            'order_number' => $order['order_number'],
            'total_price' => (float)$order['total_price'],
            'status' => $order['status'],
            'delivery_address' => $order['delivery_address'],
            'phone' => $order['phone'],
            'dispute_status' => $order['dispute_status'],
            'dispute_category' => $order['dispute_category'],
            'dispute_description' => $order['dispute_description'],
            'dispute_reported_at' => $order['dispute_reported_at'],
            'feedback_rating' => $order['feedback_rating'] !== null ? (int)$order['feedback_rating'] : null,
            'feedback_comment' => $order['feedback_comment'],
            'created_at' => $order['created_at'],
            'username' => $order['username'],
            'email' => $order['email'],
            'items' => isset($groupedItems[$id]) ? $groupedItems[$id] : []
        ];
    }
    
    sendJsonResponse(true, "Disputed orders retrieved successfully.", $formattedOrders);
    
} catch (Exception $e) {
    logCustomError("get-disputed-orders.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve disputed orders: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
