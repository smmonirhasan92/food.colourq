<?php
/**
 * GET Order Status API
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

$orderNumber = isset($_GET['order_number']) ? trim($_GET['order_number']) : null;

if (!$orderNumber) {
    sendJsonResponse(false, "Missing required parameter 'order_number'.", null, 400);
}

// Sanitize input
$orderNumber = sanitizeInput($orderNumber);

try {
    $db = Database::getConnection();
    
    // Fetch order header
    $stmt = $db->prepare("
        SELECT o.id, o.order_number, o.total_price, o.status, o.delivery_address, o.phone, 
               o.created_at, o.confirmed_at, o.prepared_at, o.delivered_at,
               o.dispute_status, o.dispute_category, o.dispute_description, o.dispute_reported_at,
               o.feedback_rating, o.feedback_comment,
               u.username, u.email,
               d.name AS delivery_man_name, d.phone AS delivery_man_phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN delivery_men d ON o.delivery_man_id = d.id
        WHERE o.order_number = ?
        LIMIT 1
    ");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    
    if (!$order) {
        sendJsonResponse(false, "Order '{$orderNumber}' not found.", null, 404);
    }
    
    $orderId = (int)$order['id'];
    
    // Fetch order line items
    $itemsStmt = $db->prepare("
        SELECT oi.menu_item_id, oi.quantity, oi.price, oi.variation_name, m.name AS item_name, m.image_url 
        FROM order_items oi
        JOIN menu_items m ON oi.menu_item_id = m.id
        WHERE oi.order_id = ?
    ");
    $itemsStmt->execute([$orderId]);
    $items = $itemsStmt->fetchAll();
    
    // Format items array
    $formattedItems = [];
    foreach ($items as $item) {
        $formattedItems[] = [
            'menu_item_id' => (int)$item['menu_item_id'],
            'item_name' => $item['item_name'],
            'variation_name' => $item['variation_name'],
            'quantity' => (int)$item['quantity'],
            'price' => (float)$item['price'],
            'image_url' => $item['image_url']
        ];
    }
    
    // Structure order details response
    $payload = [
        'order_id' => $orderId,
        'order_number' => $order['order_number'],
        'total_price' => (float)$order['total_price'],
        'status' => $order['status'],
        'delivery_address' => $order['delivery_address'],
        'phone' => $order['phone'],
        'created_at' => $order['created_at'],
        'confirmed_at' => $order['confirmed_at'],
        'prepared_at' => $order['prepared_at'],
        'delivered_at' => $order['delivered_at'],
        'dispute_status' => $order['dispute_status'],
        'dispute_category' => $order['dispute_category'],
        'dispute_description' => $order['dispute_description'],
        'dispute_reported_at' => $order['dispute_reported_at'],
        'feedback_rating' => $order['feedback_rating'] !== null ? (int)$order['feedback_rating'] : null,
        'feedback_comment' => $order['feedback_comment'],
        'delivery_man_name' => $order['delivery_man_name'],
        'delivery_man_phone' => $order['delivery_man_phone'],
        'customer' => [
            'username' => preg_replace('/ \d+$/', '', $order['username']),
            'email' => $order['email']
        ],
        'items' => $formattedItems
    ];
    
    sendJsonResponse(true, "Order status retrieved successfully.", $payload);
    
} catch (Exception $e) {
    logCustomError("get-order-status.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to retrieve order status: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
