<?php
/**
 * Business Reports & Analytics Desk
 * Food Delivery & Real-Time Notification System
 * Secure session protection and dynamic filter queries
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] === true) {
    // Verified session!
} else {
    header("Location: login.php");
    exit;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$dateCondition = "";

try {
    $db = Database::getConnection();

    // 1. Build date condition based on DB driver type
    $dbType = DB_TYPE;
    
    if ($dbType === 'sqlite') {
        if ($filter === 'daily') {
            $dateCondition = "AND o.created_at >= date('now', 'start of day')";
        } else if ($filter === 'weekly') {
            $dateCondition = "AND o.created_at >= date('now', '-7 days')";
        } else if ($filter === 'monthly') {
            $dateCondition = "AND o.created_at >= date('now', '-30 days')";
        } else if ($filter === 'yearly') {
            $dateCondition = "AND o.created_at >= date('now', '-365 days')";
        }
    } else {
        if ($filter === 'daily') {
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        } else if ($filter === 'weekly') {
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } else if ($filter === 'monthly') {
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        } else if ($filter === 'yearly') {
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
        }
    }

    // 2. Fetch overall financial summary
    $summaryQuery = "
        SELECT 
            COALESCE(SUM(oi.quantity * oi.price), 0) as gross_sales,
            COALESCE(SUM(oi.quantity * m.cost_price), 0) as total_cost,
            COALESCE(SUM(oi.quantity * (oi.price - m.cost_price)), 0) as gross_profit
        FROM order_items oi
        JOIN menu_items m ON oi.menu_item_id = m.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status = 'delivered' $dateCondition
    ";
    
    $summary = $db->query($summaryQuery)->fetch();

    // 3. Fetch completed delivery details to subtract delivery costs (Tk. 60 per order)
    $deliveryCostQuery = "
        SELECT COUNT(*) as completed_deliveries
        FROM orders o
        WHERE o.status = 'delivered' AND o.delivery_man_id IS NOT NULL $dateCondition
    ";
    $deliveries = $db->query($deliveryCostQuery)->fetch();
    $completedDeliveriesCount = (int)$deliveries['completed_deliveries'];
    $totalDeliveryPayout = $completedDeliveriesCount * 60; // Tk. 60 per delivery

    $netProfit = (float)$summary['gross_profit'] - $totalDeliveryPayout;

    // 4. Fetch product performance list
    $productPerformanceQuery = "
        SELECT 
            m.name,
            m.category,
            m.price,
            m.cost_price,
            SUM(oi.quantity) as qty_sold,
            SUM(oi.quantity * oi.price) as total_sales,
            SUM(oi.quantity * m.cost_price) as total_cost,
            SUM(oi.quantity * (oi.price - m.cost_price)) as net_profit
        FROM order_items oi
        JOIN menu_items m ON oi.menu_item_id = m.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status = 'delivered' $dateCondition
        GROUP BY m.id, m.name, m.category, m.price, m.cost_price
        ORDER BY qty_sold DESC
    ";
    $products = $db->query($productPerformanceQuery)->fetchAll();

    // 5. Fetch rider payouts breakdown
    $riderPayoutsQuery = "
        SELECT 
            dm.id,
            dm.name,
            dm.phone,
            dm.status,
            COUNT(o.id) as completed_orders
        FROM delivery_men dm
        LEFT JOIN orders o ON o.delivery_man_id = dm.id AND o.status = 'delivered' $dateCondition
        GROUP BY dm.id, dm.name, dm.phone, dm.status
        ORDER BY completed_orders DESC
    ";
    $riders = $db->query($riderPayoutsQuery)->fetchAll();

} catch (Exception $e) {
    logCustomError("reports.php error: " . $e->getMessage());
    $summary = ['gross_sales' => 0, 'total_cost' => 0, 'gross_profit' => 0];
    $completedDeliveriesCount = 0;
    $totalDeliveryPayout = 0;
    $netProfit = 0;
    $products = [];
    $riders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Reports - Crispy Chicken</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>
</head>
<body>

    <!-- Mobile Header Panel -->
    <div class="admin-mobile-header hide-desktop">
        <a href="dashboard.php" class="brand-logo" style="font-size: 1.35rem;">
            Crispy Chicken <span class="brand-dot"></span>
        </a>
        <div class="admin-sidebar-toggle">
            <i class="fa-solid fa-bars-staggered"></i>
        </div>
    </div>

    <div class="admin-wrapper">
        
        <!-- Sidebar Navigation Drawer -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="brand-logo">
                    Crispy Chicken<span class="brand-dot"></span>
                </a>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard.php" class="sidebar-link">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard Stats
                </a>
                <a href="manage-orders.php" class="sidebar-link">
                    <i class="fa-solid fa-receipt"></i> Live Orders
                </a>
                <a href="manage-menu.php" class="sidebar-link">
                    <i class="fa-solid fa-pizza-slice"></i> Culinary Menu
                </a>
                <a href="manage-riders.php" class="sidebar-link">
                    <i class="fa-solid fa-motorcycle"></i> Delivery Riders
                </a>
                <a href="reports.php" class="sidebar-link active">
                    <i class="fa-solid fa-chart-line"></i> Business Reports
                </a>
            </nav>

            <div class="sidebar-footer" style="display: flex; flex-direction: column; gap: 0.5rem; padding: 1rem 1.5rem;">
                <a href="../customer/index.php" class="sidebar-link" style="padding: 0.5rem 0; opacity: 0.9;">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Customer Portal
                </a>
                <a href="logout.php" class="sidebar-link" style="padding: 0.5rem 0; color: #ef4444 !important; opacity: 0.9;">
                    <i class="fa-solid fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <main class="admin-main">
            
            <div class="admin-topbar">
                <div class="page-title">
                    <h1 style="color: var(--text-primary);">Business Reports & Analytics</h1>
                    <p style="color: var(--text-secondary);">Track sales metrics, product preparation costs, rider payouts, and calculate net profits.</p>
                </div>

                <div class="realtime-indicator realtime-active">
                    <span class="indicator-dot"></span>
                    <span class="indicator-label">Report Data Synced</span>
                </div>
            </div>

            <!-- Report Filter Options -->
            <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-filter" style="color: var(--primary);"></i>
                    <span style="font-weight: 600; color: var(--text-primary);">Filter Period:</span>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="reports.php?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-glass'; ?> btn-sm" style="width: auto;">All Time</a>
                    <a href="reports.php?filter=daily" class="btn <?php echo $filter === 'daily' ? 'btn-primary' : 'btn-glass'; ?> btn-sm" style="width: auto;">Daily</a>
                    <a href="reports.php?filter=weekly" class="btn <?php echo $filter === 'weekly' ? 'btn-primary' : 'btn-glass'; ?> btn-sm" style="width: auto;">Weekly</a>
                    <a href="reports.php?filter=monthly" class="btn <?php echo $filter === 'monthly' ? 'btn-primary' : 'btn-glass'; ?> btn-sm" style="width: auto;">Monthly</a>
                    <a href="reports.php?filter=yearly" class="btn <?php echo $filter === 'yearly' ? 'btn-primary' : 'btn-glass'; ?> btn-sm" style="width: auto;">Yearly</a>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <section class="grid grid-cols-4" style="margin-bottom: 2.5rem;">
                <!-- Total Sales -->
                <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="background: rgba(234, 103, 33, 0.08); width: 50px; height: 50px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary);">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">Gross Sales</span>
                        <h2 style="font-size: 1.6rem; margin-top: 0.15rem; color: var(--text-primary);">Tk. <?php echo number_format($summary['gross_sales'], 0); ?></h2>
                    </div>
                </div>

                <!-- Total Product Cost -->
                <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="background: rgba(239, 68, 68, 0.08); width: 50px; height: 50px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--danger);">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">Product Cost</span>
                        <h2 style="font-size: 1.6rem; margin-top: 0.15rem; color: var(--text-primary);">Tk. <?php echo number_format($summary['total_cost'], 0); ?></h2>
                    </div>
                </div>

                <!-- Gross Profit -->
                <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="background: rgba(16, 185, 129, 0.08); width: 50px; height: 50px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--success);">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">Gross Profit</span>
                        <h2 style="font-size: 1.6rem; margin-top: 0.15rem; color: var(--success);">Tk. <?php echo number_format($summary['gross_profit'], 0); ?></h2>
                    </div>
                </div>

                <!-- Net Business Profit -->
                <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="background: rgba(59, 130, 246, 0.08); width: 50px; height: 50px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #3b82f6;">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">Net Profit (Riders Paid)</span>
                        <h2 style="font-size: 1.6rem; margin-top: 0.15rem; color: <?php echo $netProfit >= 0 ? '#10b981' : '#ef4444'; ?>;">Tk. <?php echo number_format($netProfit, 0); ?></h2>
                    </div>
                </div>
            </section>

            <!-- Product Performance Listing Table -->
            <section class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-utensils" style="color: var(--primary);"></i> Product Sales & Profitability Breakdown
                    </h3>
                    <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Showing <?php echo count($products); ?> Gourmet Items</span>
                </div>

                <div class="table-responsive">
                    <table class="premium-table stack-mobile">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Unit Price</th>
                                <th>Unit Cost</th>
                                <th>Qty Sold</th>
                                <th>Total Revenue</th>
                                <th>Total Cost</th>
                                <th>Net profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                        <i class="fa-solid fa-pizza-slice" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                                        <p style="font-weight: 500;">No sales recorded for the selected period.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $prod): ?>
                                    <tr>
                                        <td data-label="Item Name" style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($prod['name']); ?></td>
                                        <td data-label="Category" style="text-transform: capitalize; color: var(--text-muted);"><?php echo htmlspecialchars($prod['category']); ?></td>
                                        <td data-label="Unit Price" style="font-family: monospace;">Tk. <?php echo number_format($prod['price'], 0); ?></td>
                                        <td data-label="Unit Cost" style="font-family: monospace; color: var(--text-muted);">Tk. <?php echo number_format($prod['cost_price'], 0); ?></td>
                                        <td data-label="Qty Sold" style="font-weight: 600;"><?php echo $prod['qty_sold']; ?> pcs</td>
                                        <td data-label="Total Revenue" style="font-family: monospace; font-weight: 600;">Tk. <?php echo number_format($prod['total_sales'], 0); ?></td>
                                        <td data-label="Total Cost" style="font-family: monospace; color: var(--text-muted);">Tk. <?php echo number_format($prod['total_cost'], 0); ?></td>
                                        <td data-label="Net Profit" style="font-family: monospace; font-weight: 700; color: <?php echo $prod['net_profit'] >= 0 ? '#10b981' : '#ef4444'; ?>;">Tk. <?php echo number_format($prod['net_profit'], 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Rider Delivery Commission Dues Report -->
            <section class="glass-panel" style="padding: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-motorcycle" style="color: var(--primary);"></i> Delivery Rider Commission & Payout Status
                    </h3>
                    <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Commission rate: Tk. 60 per completed order</span>
                </div>

                <div class="table-responsive">
                    <table class="premium-table stack-mobile">
                        <thead>
                            <tr>
                                <th>Rider Name</th>
                                <th>Contact Phone</th>
                                <th>Active Status</th>
                                <th>Deliveries Completed</th>
                                <th>Commission Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riders)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                        <i class="fa-solid fa-motorcycle" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                                        <p style="font-weight: 500;">No delivery riders registered.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($riders as $rider): ?>
                                    <?php 
                                    $completedCount = (int)$rider['completed_orders'];
                                    $earnings = $completedCount * 60;
                                    ?>
                                    <tr>
                                        <td data-label="Rider Name" style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($rider['name']); ?></td>
                                        <td data-label="Contact Phone" style="font-family: monospace;"><?php echo htmlspecialchars($rider['phone']); ?></td>
                                        <td data-label="Active Status" style="text-transform: uppercase;">
                                            <?php if ($rider['status'] === 'available'): ?>
                                                <span class="status-badge status-success">AVAILABLE</span>
                                            <?php elseif ($rider['status'] === 'busy'): ?>
                                                <span class="status-badge status-preparing">BUSY</span>
                                            <?php else: ?>
                                                <span class="status-badge status-pending">OFFLINE</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Deliveries Completed" style="font-weight: 600;"><?php echo $completedCount; ?> Orders</td>
                                        <td data-label="Commission Earnings" style="font-family: monospace; font-weight: 700; color: #10b981;">Tk. <?php echo number_format($earnings, 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/animations.js"></script>

</body>
</html>
