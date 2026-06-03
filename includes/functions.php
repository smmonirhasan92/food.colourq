<?php
/**
 * Core Utility Functions
 * Food Delivery & Real-Time Notification System
 */

require_once __DIR__ . '/../config/constants.php';

/**
 * Handle CORS headers and pre-flight OPTIONS requests
 */
function handleCORS(): void {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

/**
 * Sanitize input data to prevent XSS attacks
 * Supports strings and multi-dimensional arrays
 */
function sanitizeInput(mixed $data): mixed {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeInput($value);
        }
        return $data;
    }
    
    if (is_string($data)) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Format and send JSON API response, then terminate execution
 */
function sendJsonResponse(bool $success, string $message, mixed $data = null, int $statusCode = 200): void {
    // Clear any previous output buffers to avoid broken JSON
    if (ob_get_length()) {
        ob_clean();
    }
    
    // Set headers
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Hash a password using default secure hashing algorithm
 */
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify a password against a hash
 */
function verifyPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Validate an email address format
 */
function validateEmail(string $email): bool {
    $email = trim($email);
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if required fields exist in the dataset and are not empty
 * Returns an array of missing fields
 */
function checkRequiredFields(array $requiredFields, array $data): array {
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || $data[$field] === null || (is_string($data[$field]) && trim($data[$field]) === '')) {
            $missingFields[] = $field;
        }
    }
    return $missingFields;
}

/**
 * Validate order status value
 */
function isValidOrderStatus(string $status): bool {
    return in_array($status, VALID_ORDER_STATUSES, true);
}

/**
 * Validate user role value
 */
function isValidUserRole(string $role): bool {
    return in_array($role, VALID_USER_ROLES, true);
}

/**
 * Extract Bearer token from the Authorization header
 */
function getBearerToken(): ?string {
    $headers = getallheaders();
    
    // Check if Authorization header exists
    $authHeader = null;
    foreach ($headers as $key => $value) {
        if (strcasecmp($key, 'Authorization') === 0) {
            $authHeader = $value;
            break;
        }
    }
    
    if (empty($authHeader)) {
        return null;
    }
    
    // Extract the token
    if (preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
        return $matches[1];
    }
    
    return null;
}

/**
 * Polyfill for getallheaders() if not available (e.g., in some server configurations or CLI)
 */
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

/**
 * Log errors systematically with context details
 */
function logCustomError(string $message, array $context = []): void {
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
    $logMessage = "[{$timestamp}] {$message}{$contextStr}" . PHP_EOL;
    
    // Log to PHP's system error log
    error_log($message . $contextStr);
    
    // Optionally log to a custom project log file inside the workspace
    $logDir = BASE_PATH . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logDir . '/app.log', $logMessage, FILE_APPEND);
}
