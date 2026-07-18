<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

handleCORS();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed", null, 405);
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input || empty($input['code'])) {
    sendJsonResponse(false, "Coupon code is required.", null, 400);
}

$code = strtoupper(trim($input['code']));

try {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT discount_type, discount_value FROM coupons WHERE code = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();
    
    if ($coupon) {
        sendJsonResponse(true, "Coupon applied!", [
            'type' => $coupon['discount_type'],
            'value' => (float)$coupon['discount_value']
        ]);
    } else {
        sendJsonResponse(false, "Invalid or expired coupon.", null, 404);
    }
} catch (Exception $e) {
    sendJsonResponse(false, "Error validating coupon: " . $e->getMessage(), null, 500);
}
