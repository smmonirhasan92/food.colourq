<?php
/**
 * Check New Orders (Polling Endpoint) API
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
    
    // Start transaction to ensure atomic read-and-update behavior
    $db->beginTransaction();
    
    // Select unnotified orders
    // Join with users table to get placing customer details for dashboard rendering
    $query = "SELECT o.id, o.order_number, o.total_price, o.status, o.delivery_address, o.phone, o.created_at, u.username, u.email 
              FROM orders o
              JOIN users u ON o.user_id = u.id
              WHERE o.is_notified = 0
              ORDER BY o.created_at ASC";
              
    $stmt = $db->query($query);
    $newOrders = $stmt->fetchAll();
    
    if (!empty($newOrders)) {
        // Collect order IDs to flag as notified
        $orderIds = array_map(function($order) {
            return (int)$order['id'];
        }, $newOrders);
        
        // Form placeholders (e.g. "?,?,?")
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        
        // Dynamic batch update
        $updateQuery = "UPDATE orders 
                        SET is_notified = 1, notification_sent_at = CURRENT_TIMESTAMP 
                        WHERE id IN ($placeholders)";
                        
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute($orderIds);
    }
    
    $db->commit();
    
    // Typecast numeric fields in new orders
    foreach ($newOrders as &$order) {
        $order['id'] = (int)$order['id'];
        $order['total_price'] = (float)$order['total_price'];
        $order['username'] = preg_replace('/ \d+$/', '', $order['username']);
        // DB stores BD time (PHP uses date_default_timezone_set Asia/Dhaka)
        // Format as ISO 8601 with +06:00 so JS parses AM/PM correctly
        if (!empty($order['created_at'])) {
            $dt = new DateTime($order['created_at'], new DateTimeZone('Asia/Dhaka'));
            $order['created_at'] = $dt->format('c'); // "2026-06-25T14:10:00+06:00"
        }
    }
    unset($order); // break reference
    
    // 1. Calculate total_orders
    $totalOrders = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    
    // 2. Calculate net_sales (SUM of total_price where status is NOT cancelled)
    $netSales = (float)$db->query("SELECT SUM(total_price) FROM orders WHERE status != '" . STATUS_CANCELLED . "'")->fetchColumn();
    
    // 3. Calculate pending_actions (COUNT of orders where status is pending or preparing)
    $pendingActions = (int)$db->query("SELECT COUNT(*) FROM orders WHERE status IN ('" . STATUS_PENDING . "', '" . STATUS_PREPARING . "')")->fetchColumn();
    
    // 4. Calculate average preparation time dynamically, support both SQLite & MySQL
    $avgPrep = 15; // default fallback
    try {
        if (DB_TYPE === 'sqlite') {
            $avgQuery = "SELECT AVG((strftime('%s', prepared_at) - strftime('%s', confirmed_at)) / 60.0) AS avg_prep_time 
                         FROM orders 
                         WHERE prepared_at IS NOT NULL AND confirmed_at IS NOT NULL";
        } else {
            $avgQuery = "SELECT AVG(TIMESTAMPDIFF(SECOND, confirmed_at, prepared_at) / 60.0) AS avg_prep_time 
                         FROM orders 
                         WHERE prepared_at IS NOT NULL AND confirmed_at IS NOT NULL";
        }
        $avgResult = $db->query($avgQuery)->fetch();
        if ($avgResult && $avgResult['avg_prep_time'] !== null) {
            $avgPrep = max(1, round((float)$avgResult['avg_prep_time']));
        }
    } catch (Exception $e) {
        // Fallback silently to 15
        $avgPrep = 15;
    }
    
    // Construct dynamic real-time dashboard data payload
    $payload = [
        'new_orders' => $newOrders,
        'total_orders' => $totalOrders,
        'net_sales' => round($netSales, 2),
        'pending_actions' => $pendingActions,
        'avg_prep' => (int)$avgPrep
    ];
    
    sendJsonResponse(true, "Operational metrics retrieved.", $payload);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    logCustomError("check-new-orders.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to poll new orders: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
