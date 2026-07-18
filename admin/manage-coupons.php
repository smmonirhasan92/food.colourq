<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$db = Database::getConnection();
$coupons = $db->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
    
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="brand-logo">Crispy Chicken<span class="brand-dot"></span></a>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="sidebar-link"><i class="fa-solid fa-chart-pie"></i> Dashboard Stats</a>
                <a href="pos.php" class="sidebar-link"><i class="fa-solid fa-cash-register"></i> POS Counter</a>
                <a href="manage-orders.php" class="sidebar-link"><i class="fa-solid fa-receipt"></i> Live Orders</a>
                <a href="manage-menu.php" class="sidebar-link"><i class="fa-solid fa-pizza-slice"></i> Culinary Menu</a>
                <a href="manage-riders.php" class="sidebar-link"><i class="fa-solid fa-motorcycle"></i> Delivery Riders</a>
                <a href="manage-coupons.php" class="sidebar-link active"><i class="fa-solid fa-ticket"></i> Manage Coupons</a>
                <a href="reports.php" class="sidebar-link"><i class="fa-solid fa-chart-line"></i> Business Reports</a>
            </nav>
            <div class="sidebar-footer" style="padding: 1rem 1.5rem;">
                <a href="logout.php" class="sidebar-link" style="color: #ef4444;"><i class="fa-solid fa-power-off"></i> Logout</a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <h1 class="page-title">Manage Coupons</h1>
            </header>

            <div class="admin-content" style="padding: 2rem;">
                <div class="admin-card" style="margin-bottom: 2rem;">
                    <h3>Create New Coupon</h3>
                    <form id="create-coupon-form" style="display: flex; gap: 1rem; margin-top: 1rem; align-items: flex-end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Coupon Code</label>
                            <input type="text" id="coupon-code" class="form-input" required placeholder="e.g. EID50">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Discount Type</label>
                            <select id="coupon-type" class="form-input form-select" required>
                                <option value="fixed">Fixed (Tk)</option>
                                <option value="percent">Percentage (%)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Discount Value</label>
                            <input type="number" id="coupon-value" class="form-input" min="1" required placeholder="e.g. 50">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Coupon</button>
                    </form>
                </div>

                <div class="admin-card">
                    <table class="data-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid #e2e8f0;">
                                <th style="padding: 1rem;">Code</th>
                                <th style="padding: 1rem;">Type</th>
                                <th style="padding: 1rem;">Value</th>
                                <th style="padding: 1rem;">Status</th>
                                <th style="padding: 1rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coupons as $c): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1rem; font-weight: bold;"><?php echo htmlspecialchars($c['code']); ?></td>
                                <td style="padding: 1rem;"><?php echo ucfirst(htmlspecialchars($c['discount_type'])); ?></td>
                                <td style="padding: 1rem;"><?php echo number_format($c['discount_value'], 2); ?></td>
                                <td style="padding: 1rem;">
                                    <?php if ($c['is_active']): ?>
                                        <span class="badge" style="background: #10b981; color: white; padding: 0.25rem 0.5rem; border-radius: 99px; font-size: 0.8rem;">Active</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: #94a3b8; color: white; padding: 0.25rem 0.5rem; border-radius: 99px; font-size: 0.8rem;">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; display: flex; gap: 0.5rem;">
                                    <button onclick="toggleCoupon(<?php echo $c['id']; ?>, <?php echo $c['is_active'] ? 0 : 1; ?>)" class="btn btn-sm <?php echo $c['is_active'] ? 'btn-secondary' : 'btn-primary'; ?>">
                                        <?php echo $c['is_active'] ? 'Disable' : 'Enable'; ?>
                                    </button>
                                    <button onclick="deleteCoupon(<?php echo $c['id']; ?>)" class="btn btn-sm" style="background: #ef4444; color: white;"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($coupons)): ?>
                            <tr>
                                <td colspan="5" style="padding: 2rem; text-align: center; color: #64748b;">No coupons found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('create-coupon-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const code = document.getElementById('coupon-code').value;
            const type = document.getElementById('coupon-type').value;
            const value = document.getElementById('coupon-value').value;

            try {
                const response = await fetch('../api/manage-coupons.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'create', code, type, value })
                });
                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert("Error creating coupon");
            }
        });

        async function toggleCoupon(id, status) {
            try {
                const response = await fetch('../api/manage-coupons.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'toggle', id, status })
                });
                const result = await response.json();
                if (result.success) {
                    location.reload();
                }
            } catch (err) {
                alert("Error updating coupon");
            }
        }

        async function deleteCoupon(id) {
            if (!confirm("Are you sure you want to delete this coupon?")) return;
            try {
                const response = await fetch('../api/manage-coupons.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', id })
                });
                const result = await response.json();
                if (result.success) {
                    location.reload();
                }
            } catch (err) {
                alert("Error deleting coupon");
            }
        }
    </script>
</body>
</html>
