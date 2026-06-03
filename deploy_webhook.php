<?php
/**
 * Automated Git Deployment Webhook (CI/CD)
 * Food ColourQ - Premium Zero-Hassle cPanel Auto-Deployment
 */

// Secure webhook secret token to prevent unauthorized triggers (optional, but highly recommended)
// You can set this as a secret in GitHub Webhooks settings.
define('WEBHOOK_SECRET', 'colourq_food_secret_token');

// Log file configuration
$logFile = __DIR__ . '/database/deploy_log.txt';

function logMessage($msg) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $msg\n", FILE_APPEND);
}

// Verify request signature if secret is provided in headers
$headers = getallheaders();
$signature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';

if (!empty(WEBHOOK_SECRET) && !empty($signature)) {
    $payload = file_get_contents('php://input');
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, WEBHOOK_SECRET);
    
    if (!hash_equals($expectedSignature, $signature)) {
        logMessage("ERROR: Invalid signature verification failed.");
        header('HTTP/1.1 403 Forbidden');
        echo "Invalid Signature";
        exit;
    }
}

logMessage("SUCCESS: Webhook triggered by GitHub push event.");

// Execute Git Pull on the cPanel server automatically
$output = [];
$return_var = 0;

// Path to git binary is usually just 'git' on standard Linux hosts. 
// Run git pull origin main and capture output
exec("git pull origin main 2>&1", $output, $return_var);

$logOutput = implode("\n", $output);
logMessage("GIT PULL OUTCOME (Status: $return_var):\n$logOutput");

header('Content-Type: application/json');
echo json_encode([
    'success' => $return_var === 0,
    'status_code' => $return_var,
    'output' => $output,
    'message' => $return_var === 0 ? "Automated Git Pull successfully completed!" : "Git Pull failed. Please verify repository permissions."
]);
exit;
