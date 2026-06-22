<?php
/**
 * Check Customer API for POS
 * Food Delivery & Real-Time Notification System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Enable CORS
handleCORS();

// Only allow GET or POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed.", null, 405);
}

// Get phone number from request parameters
$phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : '';

if (empty($phone)) {
    sendJsonResponse(false, "Phone number is required.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // 1. Search for customer in users table by phone
    $userStmt = $db->prepare("SELECT id, username, email FROM users WHERE phone = ? ORDER BY id DESC LIMIT 1");
    $userStmt->execute([$phone]);
    $user = $userStmt->fetch();
    
    // 2. Count total orders and get last delivery address by phone in orders table
    $orderStmt = $db->prepare("
        SELECT COUNT(id) as order_count, delivery_address 
        FROM orders 
        WHERE phone = ? 
        GROUP BY phone 
        LIMIT 1
    ");
    $orderStmt->execute([$phone]);
    $orderInfo = $orderStmt->fetch();
    
    $exists = false;
    $customerName = '';
    $customerEmail = '';
    $deliveryAddress = '';
    $orderCount = 0;
    
    if ($user) {
        $exists = true;
        $customerName = $user['username'];
        $customerEmail = $user['email'];
    }
    
    if ($orderInfo) {
        $exists = true;
        $orderCount = (int)$orderInfo['order_count'];
        $deliveryAddress = $orderInfo['delivery_address'];
        
        // If user wasn't found in users table but orders exist, try to use name from last order
        if (empty($customerName)) {
            $lastOrderStmt = $db->prepare("SELECT user_id FROM orders WHERE phone = ? ORDER BY id DESC LIMIT 1");
            $lastOrderStmt->execute([$phone]);
            $lastOrder = $lastOrderStmt->fetch();
            if ($lastOrder) {
                $uStmt = $db->prepare("SELECT username, email FROM users WHERE id = ?");
                $uStmt->execute([$lastOrder['user_id']]);
                $u = $uStmt->fetch();
                if ($u) {
                    $customerName = $u['username'];
                    $customerEmail = $u['email'];
                }
            }
        }
    }
    
    sendJsonResponse(true, "Customer lookup successful.", [
        'exists' => $exists,
        'customer_name' => $customerName,
        'customer_email' => $customerEmail,
        'delivery_address' => $deliveryAddress,
        'order_count' => $orderCount
    ]);
    
} catch (Exception $e) {
    logCustomError("check-customer.php error: " . $e->getMessage());
    sendJsonResponse(false, "Internal server error occurred.", null, 500);
}
