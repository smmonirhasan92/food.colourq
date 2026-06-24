<?php
/**
 * Database Reset Tool for Admins
 * Food ColourQ - Securely clear all products, orders and notifications for a clean restart.
 */
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_reset'])) {
    try {
        $db = Database::getConnection();
        $db->beginTransaction();

        // 1. Delete order items
        $db->exec("DELETE FROM order_items");
        
        // 2. Delete orders
        $db->exec("DELETE FROM orders");
        
        // 3. Delete menu variations
        $db->exec("DELETE FROM menu_item_variations");
        
        // 4. Delete menu items
        $db->exec("DELETE FROM menu_items");
        
        // 5. Delete notifications
        $db->exec("DELETE FROM notifications_log");

        // Reset SQLite auto-increment counters if applicable
        if (DB_TYPE === 'sqlite') {
            $db->exec("DELETE FROM sqlite_sequence WHERE name IN ('order_items', 'orders', 'menu_item_variations', 'menu_items', 'notifications_log')");
        } else {
            // MySQL truncate/reset auto-increment is handled by DELETE, but we can't run TRUNCATE easily with foreign keys enabled
            // So we stick to DELETE, which is 100% safe.
        }

        $db->commit();
        $message = "Database reset successfully! All orders, products, and variations have been cleared. You can now upload fresh products.";
    } catch (Exception $e) {
        if ($db && $db->inTransaction()) {
            $db->rollBack();
        }
        $error = "Reset failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Database - Crispy Chicken</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Outfit', sans-serif;
        }
        .reset-container {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
            margin: 1rem;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body>

    <div class="glass-panel reset-container" style="background-color: var(--bg-dark-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); text-align: center;">
        <div style="font-size: 3.5rem; color: #ef4444; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        
        <h2 style="font-family: var(--font-heading); font-size: 1.75rem; margin-bottom: 1rem; color: var(--text-primary);">Reset Store Database</h2>
        
        <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
            This action will <strong>permanently delete</strong> all products, variations, orders, and customer order histories. 
            Your admin login credentials and categories will be preserved.
        </p>

        <?php if ($message): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.9rem; text-align: left; display: flex; gap: 0.75rem; align-items: start;">
                <i class="fa-solid fa-circle-check" style="margin-top: 0.2rem; font-size: 1.1rem;"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
            <a href="manage-menu.php" class="btn btn-primary" style="display: block; width: 100%; text-decoration: none; text-align: center;">Go to Menu Catalog</a>
        <?php elseif ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.9rem; text-align: left; display: flex; gap: 0.75rem; align-items: start;">
                <i class="fa-solid fa-circle-xmark" style="margin-top: 0.2rem; font-size: 1.1rem;"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!$message): ?>
            <form method="POST">
                <input type="hidden" name="confirm_reset" value="1">
                <button type="submit" class="btn btn-danger" style="width: 100%; padding: 0.85rem; border-radius: var(--radius-md); font-size: 1rem; margin-bottom: 1rem;" onclick="return confirm('Are you absolutely sure? This action is irreversible!')">
                    Yes, Clear Everything <i class="fa-solid fa-trash"></i>
                </button>
                <a href="manage-menu.php" class="btn btn-glass" style="display: block; width: 100%; padding: 0.85rem; border-radius: var(--radius-md); text-decoration: none; text-align: center; color: var(--text-secondary);">
                    Cancel & Go Back
                </a>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
