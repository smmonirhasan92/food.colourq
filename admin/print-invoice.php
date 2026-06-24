<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$orderNumber = isset($_GET['order_number']) ? trim($_GET['order_number']) : '';

if (empty($orderNumber)) {
    die("Error: Order number is required.");
}

try {
    $db = Database::getConnection();
    
    // Fetch order details
    $orderStmt = $db->prepare("
        SELECT o.id, o.order_number, o.total_price, o.discount_percent, o.discount_amount, 
               o.payment_method, o.created_at, o.phone as customer_phone, o.order_type, 
               u.username as customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.order_number = ? 
        LIMIT 1
    ");
    $orderStmt->execute([$orderNumber]);
    $order = $orderStmt->fetch();
    
    if (!$order) {
        die("Error: Order #{$orderNumber} not found.");
    }
    
    // Fetch order items
    $itemsStmt = $db->prepare("
        SELECT oi.price, oi.quantity, m.name as item_name
        FROM order_items oi
        JOIN menu_items m ON oi.menu_item_id = m.id
        WHERE oi.order_id = ?
    ");
    $itemsStmt->execute([$order['id']]);
    $items = $itemsStmt->fetchAll();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo htmlspecialchars($order['order_number']); ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
            width: 80mm;
            box-sizing: border-box;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .brand-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .meta-table, .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 4px;
        }
        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .total-section {
            width: 100%;
            margin-top: 8px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 13px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 15px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            margin-top: 5px;
        }
        @media print {
            body {
                width: 80mm;
                padding: 5px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="text-center">
        <div class="brand-title">CRISPY CHICKEN</div>
        <div style="font-size: 11px;">Food Court, Nodi Bangla Center Point</div>
        <div style="font-size: 11px;">Bhairab Town</div>
        <div style="font-size: 11px;">Phone: 01681-560308</div>
    </div>

    <div class="separator"></div>

    <!-- Metadata -->
    <table class="meta-table">
        <tr>
            <td><strong>Invoice:</strong> <?php echo htmlspecialchars($order['order_number']); ?></td>
            <td class="text-right"><strong>Date:</strong> <?php echo date('d-M-Y H:i', strtotime($order['created_at'])); ?></td>
        </tr>
        <tr>
            <td><strong>Register:</strong> <?php echo strtoupper($order['order_type']); ?> Counter</td>
            <td class="text-right"><strong>Cashier:</strong> Admin</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo htmlspecialchars($order['customer_phone']); ?>)</td>
        </tr>
    </table>

    <div class="separator"></div>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Item Description</th>
                <th class="text-center" style="width: 20%;">Qty</th>
                <th class="text-right" style="width: 30%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grossTotal = 0.0;
            foreach ($items as $item): 
                $sub = (float)$item['price'] * (int)$item['quantity'];
                $grossTotal += $sub;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                    <td class="text-right">Tk. <?php echo number_format($sub, 0); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="separator"></div>

    <!-- Totals -->
    <div class="total-section">
        <div class="total-row">
            <span>Gross Subtotal:</span>
            <span>Tk. <?php echo number_format($grossTotal, 0); ?></span>
        </div>
        <div class="total-row">
            <span>Discount (<?php echo number_format($order['discount_percent'], 0); ?>%):</span>
            <span style="color: #000;">- Tk. <?php echo number_format($order['discount_amount'], 0); ?></span>
        </div>
        <div class="total-row grand-total">
            <span>NET TOTAL:</span>
            <span>Tk. <?php echo number_format($order['total_price'], 0); ?></span>
        </div>
        <div class="total-row" style="margin-top: 6px; font-size: 11px;">
            <span>Payment Method:</span>
            <span>
                <?php 
                $methodMap = [
                    'cod' => 'Cash Payment',
                    'bkash' => 'bKash MFS',
                    'nagad' => 'Nagad MFS',
                    'rocket' => 'Rocket MFS',
                    'card' => 'Card Terminal'
                ];
                echo isset($methodMap[$order['payment_method']]) ? $methodMap[$order['payment_method']] : strtoupper($order['payment_method']);
                ?>
            </span>
        </div>
    </div>

    <div class="separator"></div>

    <!-- Footer -->
    <div class="text-center" style="font-size: 11px; margin-top: 15px;">
        <div>Thank you for dining with us!</div>
        <div style="font-weight: bold; margin-top: 5px;">Powered By :- Crispy Chicken</div>
    </div>

</body>
</html>
