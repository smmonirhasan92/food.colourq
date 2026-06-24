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
$delivery_charge = 50;

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
    $cost_price = isset($input['cost_price']) ? (float)$input['cost_price'] : null;
    $discount_price = isset($input['discount_price']) && $input['discount_price'] !== '' ? (float)$input['discount_price'] : null;
    $delivery_charge = isset($input['delivery_charge']) ? (int)$input['delivery_charge'] : 50;
    $category = isset($input['category']) ? trim($input['category']) : null;
    $description = isset($input['description']) ? trim($input['description']) : null;
    $imageUrl = isset($input['image_url']) ? trim($input['image_url']) : null;
} else {
    // 2. Handle multipart/form-data / standard POST Input (Form Upload)
    $_POST = sanitizeInput($_POST);
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : null;
    $cost_price = isset($_POST['cost_price']) ? (float)$_POST['cost_price'] : null;
    $discount_price = isset($_POST['discount_price']) && $_POST['discount_price'] !== '' ? (float)$_POST['discount_price'] : null;
    $delivery_charge = isset($_POST['delivery_charge']) ? (int)$_POST['delivery_charge'] : 50;
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

if ($discount_price !== null) {
    if ($discount_price <= 0) {
        sendJsonResponse(false, "Discount price must be greater than zero.", null, 400);
    }
    if ($discount_price >= $price) {
        sendJsonResponse(false, "Discount price must be less than the original price.", null, 400);
    }
}

try {
    $db = Database::getConnection();

    // Fetch valid categories from the database dynamically
    $categoriesStmt = $db->query("SELECT name, slug FROM menu_categories");
    $dbCategories = $categoriesStmt->fetchAll();
    
    $validSlugs = array_column($dbCategories, 'slug');
    
    // Support mapping display names to standard slugs
    $categoryMapping = [
        'Hot Starters' => 'appetizer',
        'Gourmet Mains' => 'main',
        'Organic Bowls' => 'main',
        'Desserts' => 'dessert',
        'Drinks' => 'drink',
        'Starter' => 'appetizer',
        'Best Seller' => 'main',
        'Dessert' => 'dessert',
        'Drink' => 'drink'
    ];
    
    foreach ($dbCategories as $dbCat) {
        $categoryMapping[$dbCat['name']] = $dbCat['slug'];
    }

    $mappedCategory = 'appetizer';
    if (in_array($category, $validSlugs)) {
        $mappedCategory = $category;
    } elseif (isset($categoryMapping[$category])) {
        $mappedCategory = $categoryMapping[$category];
    } else {
        if (!empty($validSlugs)) {
            $mappedCategory = $validSlugs[0];
        }
    }

    // Default cost_price to 50% of price if not provided
    if ($cost_price === null || $cost_price < 0) {
        $cost_price = $price * 0.5;
    }

    // Parse variations list from request
    $variations = [];
    if (strpos($contentType, 'application/json') !== false) {
        $variations = isset($input['variations']) ? $input['variations'] : [];
    } else {
        $variationsJson = isset($_POST['variations']) ? $_POST['variations'] : '';
        if (!empty($variationsJson)) {
            $variations = json_decode($variationsJson, true);
        }
    }

    $db->beginTransaction();

    $stmt = $db->prepare("
        INSERT INTO menu_items (name, description, price, cost_price, discount_price, delivery_charge, category, image_url, is_available) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
    ");
    
    $stmt->execute([$name, $description, $price, $cost_price, $discount_price, $delivery_charge, $mappedCategory, $imageUrl]);
    $itemId = (int)$db->lastInsertId();

    $savedVariations = [];
    if (is_array($variations) && !empty($variations)) {
        foreach ($variations as $var) {
            $varName = isset($var['name']) ? trim($var['name']) : '';
            $varPrice = isset($var['price']) ? (float)$var['price'] : 0.00;
            if ($varName !== '') {
                $stmtVar = $db->prepare("
                    INSERT INTO menu_item_variations (menu_item_id, name, price, is_available) 
                    VALUES (?, ?, ?, 1)
                ");
                $stmtVar->execute([$itemId, $varName, $varPrice]);
                $varId = (int)$db->lastInsertId();
                $savedVariations[] = [
                    'id' => $varId,
                    'name' => $varName,
                    'price' => $varPrice
                ];
            }
        }
    }

    $db->commit();
    
    sendJsonResponse(true, "Menu item added successfully.", [
        'id' => $itemId,
        'name' => $name,
        'price' => $price,
        'cost_price' => $cost_price,
        'discount_price' => $discount_price,
        'category' => $mappedCategory,
        'image_url' => $imageUrl,
        'is_available' => 1,
        'variations' => $savedVariations
    ], 201);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    logCustomError("add-menu-item.php error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    sendJsonResponse(false, "Failed to add menu item: " . (APP_ENV === 'development' ? $e->getMessage() : "Internal Server Error."), null, 500);
}
