<?php
/**
 * Place Order API
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

// Map incoming payload variables with fallback keys to support both frontend formats perfectly
$username = isset($input['username']) ? $input['username'] : (isset($input['customer_name']) ? $input['customer_name'] : null);
$email = isset($input['email']) ? $input['email'] : (isset($input['customer_email']) ? $input['customer_email'] : null);
$phone = isset($input['phone']) ? $input['phone'] : (isset($input['customer_phone']) ? $input['customer_phone'] : null);
$deliveryAddress = isset($input['delivery_address']) ? $input['delivery_address'] : null;
$items = isset($input['items']) ? $input['items'] : (isset($input['cart']) ? $input['cart'] : null);

$paymentMethod = isset($input['payment_method']) ? $input['payment_method'] : 'cod';
$mfsSenderNumber = isset($input['mfs_sender_number']) ? $input['mfs_sender_number'] : null;
$mfsTransactionId = isset($input['mfs_transaction_id']) ? $input['mfs_transaction_id'] : null;

$discountPercent = isset($input['discount_percent']) ? (float)$input['discount_percent'] : 0.00;
$discountAmount = isset($input['discount_amount']) ? (float)$input['discount_amount'] : 0.00;

// Validate required fields manually using mapped variables
if (empty($username) || empty($email) || empty($phone) || empty($deliveryAddress) || empty($items)) {
    sendJsonResponse(false, "Missing required fields. Please ensure username/customer_name, email/customer_email, phone/customer_phone, delivery_address, and items/cart are provided.", null, 400);
}

// Validate Email Address
if (!validateEmail($email)) {
    sendJsonResponse(false, "Invalid email address format.", null, 400);
}

// Validate Items list
if (!is_array($items) || empty($items)) {
    sendJsonResponse(false, "Items list must be a non-empty array.", null, 400);
}

try {
    $db = Database::getConnection();
    
    // Check if user already exists by email
    $stmt = $db->prepare("SELECT id, username FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    $userId = null;
    if ($user) {
        $userId = (int)$user['id'];
        $username = $user['username']; // Use existing username for consistency
    } else {
        // Create a new guest customer account
        // Ensure username uniqueness
        $checkUser = $db->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $checkUser->execute([$username]);
        if ($checkUser->fetch()) {
            $username = $username . rand(10, 99); // Append suffix if username is taken
        }
        
        // Auto-generate guest password hash
        $randomPass = bin2hex(random_bytes(8));
        $hashedPass = hashPassword($randomPass);
        
        $insertUser = $db->prepare("
            INSERT INTO users (username, password, email, phone, role) 
            VALUES (?, ?, ?, ?, 'customer')
        ");
        $insertUser->execute([$username, $hashedPass, $email, $phone]);
        $userId = (int)$db->lastInsertId();
    }
    
    // Fetch and validate items, calculating total cost
    $totalPrice = 0.0;
    $validatedItems = [];
    
    foreach ($items as $item) {
        // Support different formats of item objects:
        // menu_item_id fallbacks: id, menu_id, menuItemId
        $itemId = null;
        if (isset($item['menu_item_id'])) {
            $itemId = (int)$item['menu_item_id'];
        } elseif (isset($item['id'])) {
            $itemId = (int)$item['id'];
        } elseif (isset($item['menu_id'])) {
            $itemId = (int)$item['menu_id'];
        } elseif (isset($item['menuItemId'])) {
            $itemId = (int)$item['menuItemId'];
        }
        
        // quantity fallbacks: count, qty, quantity
        $qty = null;
        if (isset($item['quantity'])) {
            $qty = (int)$item['quantity'];
        } elseif (isset($item['qty'])) {
            $qty = (int)$item['qty'];
        } elseif (isset($item['count'])) {
            $qty = (int)$item['count'];
        }
        
        if ($itemId === null || $qty === null) {
            sendJsonResponse(false, "Each item must specify menu_item_id (or id/menu_id) and quantity (or qty/count).", null, 400);
        }
        
        if ($qty <= 0) {
            sendJsonResponse(false, "Item quantities must be positive integers.", null, 400);
        }
        
        // Verify menu item availability and cost
        $menuStmt = $db->prepare("SELECT id, name, price, discount_price, is_available FROM menu_items WHERE id = ? LIMIT 1");
        $menuStmt->execute([$itemId]);
        $menuItem = $menuStmt->fetch();
        
        if (!$menuItem) {
            sendJsonResponse(false, "Menu item with ID {$itemId} does not exist.", null, 404);
        }
        
        if ((int)$menuItem['is_available'] !== 1) {
            sendJsonResponse(false, "Menu item '{$menuItem['name']}' is currently not available.", null, 400);
        }
        
        $price = $menuItem['discount_price'] !== null && (float)$menuItem['discount_price'] > 0 ? (float)$menuItem['discount_price'] : (float)$menuItem['price'];
        $subtotal = $price * $qty;
        $totalPrice += $subtotal;
        
        $validatedItems[] = [
            'id' => $itemId,
            'price' => $price,
            'quantity' => $qty
        ];
    }
    
    // Generate a sequential order number (format: CF-XX)
    $stmtMax = $db->query("SELECT MAX(id) FROM orders");
    $maxId = (int)$stmtMax->fetchColumn();
    $nextId = $maxId + 1;
    $orderNumber = 'CF-' . sprintf('%02d', $nextId);
    
    // Ensure uniqueness
    while (true) {
        $checkOrder = $db->prepare("SELECT id FROM orders WHERE order_number = ? LIMIT 1");
        $checkOrder->execute([$orderNumber]);
        if (!$checkOrder->fetch()) {
            break;
        }
        $nextId++;
        $orderNumber = 'CF-' . sprintf('%02d', $nextId);
    }
    
    // Calculate tax, delivery, and discount
    $tax = $totalPrice * 0.05; // 5% tax
    $deliveryFee = 60.00;
    $grossTotal = $totalPrice + $tax + $deliveryFee;
    
    // Check repeat customer loyalty status (has completed/pending/preparing orders)
    $stmtCheck = $db->prepare("SELECT COUNT(id) FROM orders WHERE phone = ? LIMIT 1");
    $stmtCheck->execute([$phone]);
    $pastOrdersCount = (int)$stmtCheck->fetchColumn();
    
    if ($pastOrdersCount > 0) {
        $discountPercent = 5.00;
        $discountAmount = $grossTotal * 0.05;
    } else {
        $discountPercent = 0.00;
        $discountAmount = 0.00;
    }
    
    $netTotal = $grossTotal - $discountAmount;

    // Start transactional processing
    $db->beginTransaction();
    
    // 1. Insert into orders table
    $insertOrder = $db->prepare("
        INSERT INTO orders (
            user_id, order_number, total_price, status, delivery_address, 
            phone, is_notified, order_type, discount_percent, discount_amount, 
            payment_method, mfs_sender_number, mfs_transaction_id
        ) VALUES (?, ?, ?, 'pending', ?, ?, 0, 'online', ?, ?, ?, ?, ?)
    ");
    $insertOrder->execute([
        $userId, $orderNumber, $netTotal, $deliveryAddress, 
        $phone, $discountPercent, $discountAmount,
        $paymentMethod, $mfsSenderNumber, $mfsTransactionId
    ]);
    $orderId = (int)$db->lastInsertId();
    
    // 2. Insert into order_items table
    $insertItem = $db->prepare("
        INSERT INTO order_items (order_id, menu_item_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");
    foreach ($validatedItems as $vItem) {
        $insertItem->execute([$orderId, $vItem['id'], $vItem['quantity'], $vItem['price']]);
    }
    
    // 3. Log an admin notification record into notifications_log
    // Query admin user id
    $adminStmt = $db->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $adminRow = $adminStmt->fetch();
    $adminId = $adminRow ? (int)$adminRow['id'] : 1; // Fallback to 1
    
    $notificationMsg = "New order {$orderNumber} placed by {$username}. Total: Tk. " . number_format($netTotal, 0) . " (" . strtoupper($paymentMethod) . ")";
    $insertNotification = $db->prepare("
        INSERT INTO notifications_log (user_id, order_id, message, is_read) 
        VALUES (?, ?, ?, 0)
    ");
    $insertNotification->execute([$adminId, $orderId, $notificationMsg]);
    
    // Commit transaction
    $db->commit();
    
    // Return success response
    sendJsonResponse(true, "Order placed successfully.", [
        'order_id' => $orderId,
        'order_number' => $orderNumber,
        'total_price' => $netTotal,
        'status' => 'pending'
    ], 201);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    logCustomError("place-order.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to place order: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
