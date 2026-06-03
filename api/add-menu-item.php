<?php
/**
 * Add Menu Item API
 * Food Delivery & Real-Time Notification System
 * Supports both JSON payloads and multipart/form-data file uploads!
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Enable CORS
handleCORS();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed. Only POST is accepted.", null, 405);
}

$name = null;
$price = null;
$category = null;
$description = null;
$imageUrl = null;

// Determine content type of request
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if (strpos($contentType, 'application/json') !== false) {
    // 1. Handle JSON Input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        sendJsonResponse(false, "Invalid JSON payload.", null, 400);
    }
    
    $input = sanitizeInput($input);
    $name = isset($input['name']) ? trim($input['name']) : null;
    $price = isset($input['price']) ? (float)$input['price'] : null;
    $category = isset($input['category']) ? trim($input['category']) : null;
    $description = isset($input['description']) ? trim($input['description']) : null;
    $imageUrl = isset($input['image_url']) ? trim($input['image_url']) : null;
} else {
    // 2. Handle multipart/form-data / standard POST Input (Form Upload)
    $_POST = sanitizeInput($_POST);
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : null;
    $category = isset($_POST['category']) ? trim($_POST['category']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $imageUrl = isset($_POST['image_url']) ? trim($_POST['image_url']) : null;
    
    // Process local file upload if present
    if (isset($_FILES['dish_image']) && $_FILES['dish_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['dish_image']['tmp_name'];
        $fileName = $_FILES['dish_image']['name'];
        $fileSize = $_FILES['dish_image']['size'];
        $fileType = $_FILES['dish_image']['type'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Allowed file extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Ensure images directory exists in root
            $uploadDir = BASE_PATH . '/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate a unique filename to prevent collisions
            $newFileName = 'dish_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Store path as a unified relative path pointing to the root images directory
                $imageUrl = '../images/' . $newFileName;
            } else {
                sendJsonResponse(false, "There was an error moving the uploaded image on the server.", null, 500);
            }
        } else {
            sendJsonResponse(false, "Invalid image format. Allowed extensions: " . implode(', ', $allowedExtensions), null, 400);
        }
    }
}

// Validate required fields
if (empty($name) || $price === null || empty($category) || empty($description)) {
    sendJsonResponse(false, "Missing required fields. Please ensure name, price, category, and description are provided.", null, 400);
}

if ($price <= 0) {
    sendJsonResponse(false, "Price must be a positive number.", null, 400);
}

// Category mapping helper to match storefront categories
$categoryMapping = [
    'Hot Starters' => 'appetizer',
    'Gourmet Mains' => 'main',
    'Organic Bowls' => 'main',
    'Desserts' => 'dessert',
    'Drinks' => 'drink',
    'appetizer' => 'appetizer',
    'main' => 'main',
    'dessert' => 'dessert',
    'drink' => 'drink'
];

$mappedCategory = isset($categoryMapping[$category]) ? $categoryMapping[$category] : 'appetizer';

try {
    $db = Database::getConnection();
    
    $stmt = $db->prepare("
        INSERT INTO menu_items (name, description, price, category, image_url, is_available) 
        VALUES (?, ?, ?, ?, ?, 1)
    ");
    
    $stmt->execute([$name, $description, $price, $mappedCategory, $imageUrl]);
    $itemId = (int)$db->lastInsertId();
    
    sendJsonResponse(true, "Menu item added successfully.", [
        'id' => $itemId,
        'name' => $name,
        'price' => $price,
        'category' => $mappedCategory,
        'image_url' => $imageUrl,
        'is_available' => 1
    ], 201);

} catch (Exception $e) {
    logCustomError("add-menu-item.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to add menu item: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
