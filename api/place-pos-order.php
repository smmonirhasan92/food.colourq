<?php
/**
 * Place POS Order API
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

// Map incoming payload variables with POS specific fallbacks
$username = !empty($input['customer_name']) ? trim($input['customer_name']) : 'POS Customer';
$phone = !empty($input['customer_phone']) ? trim($input['customer_phone']) : 'POS-Counter';
$email = !empty($input['customer_email']) ? trim($input['customer_email']) : '';
$items = isset($input['items']) ? $input['items'] : null;
$discountPercent = isset($input['discount_percent']) ? (float)$input['discount_percent'] : 0.00;
$paymentMethod = !empty($input['payment_method']) ? trim($input['payment_method']) : 'cod';
$mfsSender = !empty($input['mfs_sender_number']) ? trim($input['mfs_sender_number']) : null;
$mfsTxnId = !empty($input['mfs_transaction_id']) ? trim($input['mfs_transaction_id']) : null;
$orderStatus = !empty($input['status']) ? trim($input['status']) : 'delivered'; // Default completed for POS

if (empty($items) || !is_array($items)) {
    sendJsonResponse(false, "Cart items list must be a non-empty array.", null, 400);
}

if ($discountPercent < 0 || $discountPercent > 100) {
    sendJsonResponse(false, "Discount percentage must be between 0 and 100.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // 1. Identify or Create Customer User
    $userId = null;
    
    if ($phone !== 'POS-Counter') {
        // Search by phone first
        $userStmt = $db->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
        $userStmt->execute([$phone]);
        $user = $userStmt->fetch();
        if ($user) {
            $userId = (int)$user['id'];
        }
    }
    
    if (!$userId && !empty($email)) {
        // Search by email if phone search missed
        $userStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $userStmt->execute([$email]);
        $user = $userStmt->fetch();
        if ($user) {
            $userId = (int)$user['id'];
        }
    }
    
    if (!$userId) {
        // Create new guest customer account for this phone
        $guestEmail = !empty($email) ? $email : 'pos_' . time() . '_' . rand(100, 999) . '@pos.com';
        $guestUsername = ($username !== 'POS Customer') ? $username : 'pos_customer_' . rand(100, 999);
        
        // Ensure username is unique
        $checkUser = $db->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $checkUser->execute([$guestUsername]);
        if ($checkUser->fetch()) {
            $guestUsername = $guestUsername . rand(10, 99);
        }
        
        $randomPass = bin2hex(random_bytes(8));
        $hashedPass = hashPassword($randomPass);
        
        $insertUser = $db->prepare("
            INSERT INTO users (username, password, email, phone, role) 
            VALUES (?, ?, ?, ?, 'customer')
        ");
        $insertUser->execute([$guestUsername, $hashedPass, $guestEmail, $phone]);
        $userId = (int)$db->lastInsertId();
        
        // If we created a new user, update username for notification
        $username = $guestUsername;
    }
    
    // 2. Validate Items and Calculate Totals
    $grossTotal = 0.0;
    $validatedItems = [];
    
    foreach ($items as $item) {
        $itemId = null;
        if (isset($item['menu_item_id'])) {
            $itemId = (int)$item['menu_item_id'];
        } elseif (isset($item['id'])) {
            $itemId = (int)$item['id'];
        }
        
        $qty = null;
        if (isset($item['quantity'])) {
            $qty = (int)$item['quantity'];
        } elseif (isset($item['qty'])) {
            $qty = (int)$item['qty'];
        }
        
        if ($itemId === null || $qty === null) {
            sendJsonResponse(false, "Each item must specify menu_item_id (or id) and quantity (or qty).", null, 400);
        }
        
        if ($qty <= 0) {
            sendJsonResponse(false, "Item quantities must be positive integers.", null, 400);
        }
        
        // Fetch price and check availability
        $menuStmt = $db->prepare("SELECT id, name, price, discount_price, is_available FROM menu_items WHERE id = ? LIMIT 1");
        $menuStmt->execute([$itemId]);
        $menuItem = $menuStmt->fetch();
        
        if (!$menuItem) {
            sendJsonResponse(false, "Menu item with ID {$itemId} does not exist.", null, 404);
        }
        
        if ((int)$menuItem['is_available'] !== 1) {
            sendJsonResponse(false, "Menu item '{$menuItem['name']}' is not available.", null, 400);
        }
        
        $price = $menuItem['discount_price'] !== null && (float)$menuItem['discount_price'] > 0 ? (float)$menuItem['discount_price'] : (float)$menuItem['price'];
        $grossTotal += ($price * $qty);
        
        $validatedItems[] = [
            'id' => $itemId,
            'price' => $price,
            'quantity' => $qty
        ];
    }
    
    // Calculate discount details
    $discountAmount = $grossTotal * ($discountPercent / 100);
    $netTotal = $grossTotal - $discountAmount;
    
    // 3. Generate Unique POS Order Number (Format: POS-YYYYMMDD-XXXX)
    $orderNumber = 'POS-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
    
    $checkOrder = $db->prepare("SELECT id FROM orders WHERE order_number = ? LIMIT 1");
    $checkOrder->execute([$orderNumber]);
    if ($checkOrder->fetch()) {
        $orderNumber = 'POS-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
    }
    
    // Start Transaction
    $db->beginTransaction();
    
    // 4. Insert into orders table
    $insertOrder = $db->prepare("
        INSERT INTO orders (
            user_id, order_number, total_price, status, delivery_address, 
            phone, is_notified, order_type, discount_percent, discount_amount, 
            mfs_sender_number, mfs_transaction_id
        ) VALUES (?, ?, ?, ?, 'POS Counter', ?, 1, 'pos', ?, ?, ?, ?)
    ");
    $insertOrder->execute([
        $userId, $orderNumber, $netTotal, $orderStatus, $phone, 
        $discountPercent, $discountAmount, $mfsSender, $mfsTxnId
    ]);
    $orderId = (int)$db->lastInsertId();
    
    // 5. Insert into order_items table
    $insertItem = $db->prepare("
        INSERT INTO order_items (order_id, menu_item_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");
    foreach ($validatedItems as $vItem) {
        $insertItem->execute([$orderId, $vItem['id'], $vItem['quantity'], $vItem['price']]);
    }
    
    // Commit transaction
    $db->commit();
    
    // Return success response
    sendJsonResponse(true, "POS order placed successfully.", [
        'order_id' => $orderId,
        'order_number' => $orderNumber,
        'gross_total' => $grossTotal,
        'discount_amount' => $discountAmount,
        'net_total' => $netTotal,
        'status' => $orderStatus
    ], 201);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    logCustomError("place-pos-order.php error: " . $e->getMessage());
    sendJsonResponse(false, "Failed to place POS order: " . $e->getMessage(), null, 500);
}
