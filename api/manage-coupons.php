<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    sendJsonResponse(false, "Unauthorized access", null, 401);
}

handleCORS();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed", null, 405);
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    sendJsonResponse(false, "Invalid JSON payload.", null, 400);
}

$action = isset($input['action']) ? trim($input['action']) : '';
$db = Database::getConnection();

if ($action === 'create') {
    $code = strtoupper(trim($input['code']));
    $type = in_array(trim($input['type']), ['fixed', 'percent']) ? trim($input['type']) : 'fixed';
    $value = (float)$input['value'];
    
    if (empty($code) || $value <= 0) {
        sendJsonResponse(false, "Invalid coupon data.", null, 400);
    }
    
    $check = $db->prepare("SELECT id FROM coupons WHERE code = ?");
    $check->execute([$code]);
    if ($check->fetch()) {
        sendJsonResponse(false, "Coupon code already exists.", null, 400);
    }
    
    $stmt = $db->prepare("INSERT INTO coupons (code, discount_type, discount_value) VALUES (?, ?, ?)");
    if ($stmt->execute([$code, $type, $value])) {
        sendJsonResponse(true, "Coupon created successfully.");
    } else {
        sendJsonResponse(false, "Failed to create coupon.");
    }
} elseif ($action === 'delete') {
    $id = (int)$input['id'];
    $stmt = $db->prepare("DELETE FROM coupons WHERE id = ?");
    if ($stmt->execute([$id])) {
        sendJsonResponse(true, "Coupon deleted.");
    } else {
        sendJsonResponse(false, "Failed to delete coupon.");
    }
} elseif ($action === 'toggle') {
    $id = (int)$input['id'];
    $status = (int)$input['status'];
    $stmt = $db->prepare("UPDATE coupons SET is_active = ? WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        sendJsonResponse(true, "Coupon status updated.");
    } else {
        sendJsonResponse(false, "Failed to update status.");
    }
} else {
    sendJsonResponse(false, "Invalid action.");
}
