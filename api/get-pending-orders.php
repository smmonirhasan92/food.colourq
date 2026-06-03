<?php
/**
 * Get Pending Orders API
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
    
    // Fetch all active/pending orders (excluding completed 'delivered' and 'cancelled')
    $query = "SELECT o.id, o.order_number, o.total_price, o.status, o.delivery_address, o.phone, 
                     o.created_at, o.confirmed_at, o.prepared_at, 
                     u.username, u.email, u.role
              FROM orders o
              JOIN users u ON o.user_id = u.id
              WHERE o.status NOT IN ('" . STATUS_DELIVERED . "', '" . STATUS_CANCELLED . "')
              ORDER BY o.created_at DESC";
              
    $stmt = $db->query($query);
    $orders = $stmt->fetchAll();
    
    if (empty($orders)) {
        sendJsonResponse(true, "No pending orders found.", []);
    }
    
    // Solve N+1 query problem by batch-fetching all order items in one database hit
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
    
    // Map items back into the orders array
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
            'created_at' => $order['created_at'],
            'confirmed_at' => $order['confirmed_at'],
            'prepared_at' => $order['prepared_at'],
            // Dynamic dual customer mapping to support direct and nested payload styles
            'username' => $order['username'],
            'email' => $order['email'],
            'role' => $order['role'],
            'customer' => [
                'username' => $order['username'],
                'email' => $order['email'],
                'role' => $order['role']
            ],
            'items' => isset($groupedItems[$id]) ? $groupedItems[$id] : []
        ];
    }
    
    sendJsonResponse(true, "Pending orders retrieved successfully.", $formattedOrders);
    
} catch (Exception $e) {
    logCustomError("get-pending-orders.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve pending orders: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
