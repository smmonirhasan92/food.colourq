<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Crispy Chicken</title>
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
        
        <!-- Sidebar Drawer Navigation -->
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
                <a href="manage-orders.php" class="sidebar-link active">
                    <i class="fa-solid fa-receipt"></i> Live Orders
                </a>
                <a href="manage-menu.php" class="sidebar-link">
                    <i class="fa-solid fa-pizza-slice"></i> Culinary Menu
                </a>
                <a href="manage-riders.php" class="sidebar-link">
                    <i class="fa-solid fa-motorcycle"></i> Delivery Riders
                </a>
                <a href="reports.php" class="sidebar-link">
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

        <!-- Main Content Panel -->
        <main class="admin-main">
            
            <div class="admin-topbar">
                <div class="page-title">
                    <h1 style="color: var(--text-primary);">Real-Time Order Desk</h1>
                    <p style="color: var(--text-secondary);">Track live customer tickets, update kitchen statuses, and trigger synthesized alarms.</p>
                </div>

                <div class="realtime-indicator realtime-active">
                    <span class="indicator-dot"></span>
                    <span class="indicator-label">Order Streams Active</span>
                </div>
            </div>

            <!-- Orders Table Container -->
            <section class="glass-panel" style="padding: 2rem; overflow: hidden; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--text-primary);">Active Queue</h3>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: var(--text-muted); cursor: pointer; margin-right: 0.5rem; user-select: none;">
                            <input type="checkbox" id="sound-alert-toggle" checked style="accent-color: var(--primary);"> Sound Alerts
                        </label>
                        <input type="text" class="form-input btn-sm" placeholder="Search orders..." style="width: 220px;" id="order-search-box">
                        <button class="btn btn-secondary btn-sm" onclick="triggerAutoRefreshSim()">
                            <i class="fa-solid fa-rotate"></i> Sync Live
                        </button>
                    </div>
                </div>

                <!-- Responsive Stackable Live Table -->
                <div class="table-responsive">
                    <table class="premium-table stack-mobile">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Order Items</th>
                                <th>Price</th>
                                <th>Received Time</th>
                                <th>Tracking Status</th>
                                <th>Actions Control</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-tbody">
                            
                            <!-- Row 1 -->
                            <tr id="order-row-1">
                                <td data-label="Order ID" style="font-weight: 700; color: var(--primary);">#FC-8726-A</td>
                                <td data-label="Customer Name">
                                    <div style="font-weight: 600; color: var(--text-primary);">Alice Smith</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">alice@example.com</div>
                                </td>
                                <td data-label="Order Items">
                                    <div style="font-size: 0.9rem;">1x Avocado Superfood Bowl</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Glazed dressings, seed toppings</div>
                                </td>
                                <td data-label="Price" style="font-weight: 700; color: var(--text-primary);">Tk. 250</td>
                                <td data-label="Received Time" style="color: var(--text-muted);">12:02 PM (3 Mins ago)</td>
                                <td data-label="Tracking Status">
                                    <span class="status-badge status-pending" id="badge-1">Pending</span>
                                </td>
                                <td data-label="Actions Control">
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <button class="btn btn-primary btn-sm" onclick="advanceOrderStatus(1)" title="Advance order state">
                                            Accept <i class="fa-solid fa-check"></i>
                                        </button>
                                        <button class="btn btn-glass btn-sm" onclick="cancelOrder(1)" title="Cancel Order ticket">
                                            Cancel
                                        </button>
                                        <button class="btn btn-secondary btn-sm" onclick="ringStatusAlarm('pending')" title="Test Sound notification">
                                            <i class="fa-solid fa-volume-high"></i> Ring
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr id="order-row-2">
                                <td data-label="Order ID" style="font-weight: 700; color: var(--primary);">#FC-8725-B</td>
                                <td data-label="Customer Name">
                                    <div style="font-weight: 600; color: var(--text-primary);">Robert Johnson</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">robert@example.com</div>
                                </td>
                                <td data-label="Order Items">
                                    <div style="font-size: 0.9rem;">1x Pan-Seared Salmon</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Warm dill butter mash</div>
                                </td>
                                <td data-label="Price" style="font-weight: 700; color: var(--text-primary);">Tk. 950</td>
                                <td data-label="Received Time" style="color: var(--text-muted);">11:54 AM (11 Mins ago)</td>
                                <td data-label="Tracking Status">
                                    <span class="status-badge status-preparing" id="badge-2">Preparing</span>
                                </td>
                                <td data-label="Actions Control">
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <button class="btn btn-success btn-sm" onclick="advanceOrderStatus(2)" style="background: #10b981; border-color: #10b981; color: white;">
                                            Ready <i class="fa-solid fa-pizza-slice"></i>
                                        </button>
                                        <button class="btn btn-glass btn-sm" onclick="cancelOrder(2)">
                                            Cancel
                                        </button>
                                        <button class="btn btn-secondary btn-sm" onclick="ringStatusAlarm('preparing')">
                                            <i class="fa-solid fa-volume-high"></i> Ring
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 3 -->
                            <tr id="order-row-3">
                                <td data-label="Order ID" style="font-weight: 700; color: var(--primary);">#FC-8724-C</td>
                                <td data-label="Customer Name">
                                    <div style="font-weight: 600; color: var(--text-primary);">Emily Davis</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">emily@example.com</div>
                                </td>
                                <td data-label="Order Items">
                                    <div style="font-size: 0.9rem;">1x Truffle Margherita</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Extra basil, thin crust</div>
                                </td>
                                <td data-label="Price" style="font-weight: 700; color: var(--text-primary);">Tk. 650</td>
                                <td data-label="Received Time" style="color: var(--text-muted);">11:42 AM (23 Mins ago)</td>
                                <td data-label="Tracking Status">
                                    <span class="status-badge status-delivering" id="badge-3">Delivering</span>
                                </td>
                                <td data-label="Actions Control">
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <button class="btn btn-success btn-sm" onclick="advanceOrderStatus(3)">
                                            Complete <i class="fa-solid fa-circle-check"></i>
                                        </button>
                                        <button class="btn btn-glass btn-sm" onclick="cancelOrder(3)">
                                            Cancel
                                        </button>
                                        <button class="btn btn-secondary btn-sm" onclick="ringStatusAlarm('delivering')">
                                            <i class="fa-solid fa-volume-high"></i> Ring
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Quality Dispute & Refund Center -->
            <section class="glass-panel" style="padding: 2rem; margin-top: 3rem; overflow: hidden; background-color: var(--bg-dark-surface); border: 1px solid rgba(239, 68, 68, 0.2); box-shadow: 0 8px 32px rgba(239, 68, 68, 0.05); margin-bottom: 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-triangle-exclamation" style="color: var(--danger);"></i> Quality Dispute Resolution Desk
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem;">Resolve customer issues for spoiled or cold food. Approve refunds or dispatch complimentary replacements instantly.</p>
                    </div>
                    <div class="realtime-indicator" style="background: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.3);">
                        <span class="indicator-dot" style="background: var(--danger); box-shadow: 0 0 8px var(--danger);"></span>
                        <span class="indicator-label" style="color: var(--danger); font-weight: 600;">Dispute Stream Active</span>
                    </div>
                </div>

                <!-- Disputes Table / Cards -->
                <div class="table-responsive">
                    <table class="premium-table stack-mobile">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Details</th>
                                <th>Issue Category</th>
                                <th>Customer Statement</th>
                                <th>Disputed Items</th>
                                <th>Resolution Status</th>
                                <th>Action Control</th>
                            </tr>
                        </thead>
                        <tbody id="disputes-table-tbody">
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-circle-check" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 1rem; color: var(--success);"></i>
                                    <p style="font-weight: 500;">No active quality disputes reported. Great job kitchen!</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Delivery Team Status Panel -->
            <section class="glass-panel" style="padding: 2rem; margin-top: 3rem; overflow: hidden; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color); margin-bottom: 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-truck-ramp-box" style="color: var(--primary);"></i> Delivery Team Performance & Status
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem;">Monitor riders' active status and tracking metrics (completed orders counter).</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="premium-table stack-mobile">
                        <thead>
                            <tr>
                                <th>Rider ID</th>
                                <th>Rider Name</th>
                                <th>Contact Phone</th>
                                <th>Active Status</th>
                                <th>Completed Deliveries</th>
                            </tr>
                        </thead>
                        <tbody id="delivery-team-tbody">
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-spinner fa-spin" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                    <p>Loading delivery team data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/animations.js"></script>

    <script>
        const orderSteps = {
            1: 'pending',
            2: 'preparing',
            3: 'delivering',
            4: 'completed',
            5: 'cancelled'
        };

        let lastCheckedOrdersCount = 0;
        let isLiveConnected = false;
        let activeOrdersCache = [];
        let alarmInterval = null;
        let pendingAlertOrder = null;
        let knownOrders = new Set();
        let isFirstLoad = true;

        function getNextStatus(current) {
            if (current === 'pending') return 'preparing';
            if (current === 'preparing') return 'ready';
            if (current === 'ready') return 'delivering';
            if (current === 'delivering') return 'completed';
            return 'completed';
        }

        function getStatusColorClass(status) {
            return `status-badge status-${status.toLowerCase()}`;
        }

        /**
         * Dynamic action buttons based on status state
         */
        function getActionButtonsHtml(orderId, orderNum, currentStatus) {
            const status = currentStatus.toLowerCase();
            let buttonsHtml = '';
            if (status === 'pending') {
                buttonsHtml = `
                    <button class="btn btn-primary btn-sm" onclick="updateOrderStatus('${orderNum}', 'preparing', ${orderId})" title="Accept order into kitchen">
                        Accept <i class="fa-solid fa-check"></i>
                    </button>
                    <button class="btn btn-glass btn-sm" onclick="updateOrderStatus('${orderNum}', 'cancelled', ${orderId})">
                        Cancel
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="ringStatusAlarm('${currentStatus}')">
                        <i class="fa-solid fa-volume-high"></i> Ring
                    </button>
                `;
            } else if (status === 'preparing') {
                buttonsHtml = `
                    <button class="btn btn-success btn-sm" onclick="updateOrderStatus('${orderNum}', 'ready', ${orderId})" title="Mark order as ready in kitchen" style="background: #10b981; border-color: #10b981; color: white;">
                        Ready <i class="fa-solid fa-pizza-slice"></i>
                    </button>
                    <button class="btn btn-glass btn-sm" onclick="updateOrderStatus('${orderNum}', 'cancelled', ${orderId})">
                        Cancel
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="ringStatusAlarm('${currentStatus}')">
                        <i class="fa-solid fa-volume-high"></i> Ring
                    </button>
                `;
            } else if (status === 'ready') {
                buttonsHtml = `
                    <button class="btn btn-primary btn-sm" onclick="openAssignRiderModal('${orderNum}', ${orderId})" title="Send order out for delivery">
                        Deliver <i class="fa-solid fa-truck"></i>
                    </button>
                    <button class="btn btn-glass btn-sm" onclick="updateOrderStatus('${orderNum}', 'cancelled', ${orderId})">
                        Cancel
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="ringStatusAlarm('${currentStatus}')">
                        <i class="fa-solid fa-volume-high"></i> Ring
                    </button>
                `;
            } else if (status === 'delivering') {
                buttonsHtml = `
                    <button class="btn btn-success btn-sm" onclick="updateOrderStatus('${orderNum}', 'delivered', ${orderId})" title="Mark order as completed">
                        Complete <i class="fa-solid fa-circle-check"></i>
                    </button>
                    <button class="btn btn-glass btn-sm" onclick="updateOrderStatus('${orderNum}', 'cancelled', ${orderId})">
                        Cancel
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="ringStatusAlarm('${currentStatus}')">
                        <i class="fa-solid fa-volume-high"></i> Ring
                    </button>
                `;
            } else if (status === 'delivered' || status === 'completed') {
                buttonsHtml = `
                    <span style="color: var(--success); font-weight: 600; font-size: 0.9rem;">
                        <i class="fa-solid fa-circle-check"></i> Fully Processed
                    </span>
                `;
            } else if (status === 'cancelled') {
                buttonsHtml = `
                    <span style="color: var(--text-muted); font-weight: 600; font-size: 0.9rem;">
                        <i class="fa-solid fa-ban"></i> Void/Cancelled
                    </span>
                `;
            }

            // Always add the thermal print button for non-cancelled orders
            if (status !== 'cancelled') {
                buttonsHtml += `
                    <button class="btn btn-secondary btn-sm" onclick="printThermalInvoice('${orderNum}')" title="Print thermal receipt">
                        <i class="fa-solid fa-print"></i> Print
                    </button>
                `;
            }

            return buttonsHtml;
        }

        /**
         * Main function to transition an order status (either live API or fallback simulator)
         */
        async function updateOrderStatus(orderNumber, newStatus, numericId = null) {
            try {
                const payload = {
                    order_number: orderNumber,
                    status: newStatus
                };

                const response = await fetch('../api/update-order-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        if (window.NotificationSystem) {
                            window.NotificationSystem.toast('success', 'Order State Updated', `Order #${orderNumber} has transitioned to: ${newStatus.toUpperCase()}`);
                        }
                        fetchPendingOrdersDesk();
                        return;
                    }
                }
                throw new Error('API failed or offline');

            } catch (error) {
                console.log('[AdminDesk] updateOrderStatus API error. Triggering premium simulator fallback:', error);
                
                const row = document.getElementById(`order-row-${numericId}`);
                if (!row) return;

                const badge = document.getElementById(`badge-${numericId}`);
                if (badge) {
                    badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    badge.className = getStatusColorClass(newStatus);
                }

                const actionTd = row.querySelector('td[data-label="Actions Control"]');
                if (actionTd) {
                    const actionContainer = actionTd.querySelector('div') || actionTd;
                    actionContainer.innerHTML = getActionButtonsHtml(numericId, orderNumber, newStatus);
                }

                if (window.NotificationSystem) {
                    const toastType = newStatus === 'delivered' ? 'success' : (newStatus === 'cancelled' ? 'error' : 'info');
                    window.NotificationSystem.toast(
                        toastType, 
                        'State Advanced (Simulated)', 
                        `Order #${orderNumber} has updated to ${newStatus.toUpperCase()}`
                    );
                }
            }
        }

        function advanceOrderStatus(rowId) {
            const badge = document.getElementById(`badge-${rowId}`);
            if (!badge) return;
            const current = badge.textContent.trim().toLowerCase();
            const next = getNextStatus(current);
            const row = document.getElementById(`order-row-${rowId}`);
            const orderNum = row ? row.cells[0].textContent.trim() : `FC-MOCK-${rowId}`;
            updateOrderStatus(orderNum, next, rowId);
        }

        function cancelOrder(rowId) {
            const row = document.getElementById(`order-row-${rowId}`);
            const orderNum = row ? row.cells[0].textContent.trim() : `FC-MOCK-${rowId}`;
            updateOrderStatus(orderNum, 'cancelled', rowId);
        }

        /**
         * Audio alerts playback trigger based on status
         */
        function ringStatusAlarm(statusLabel) {
            const status = statusLabel.toLowerCase();
            let chimeType = 'info';
            if (status === 'delivered' || status === 'completed') chimeType = 'success';
            if (status === 'cancelled') chimeType = 'error';
            if (status === 'preparing') chimeType = 'warning';

            if (window.NotificationSystem && window.NotificationSystem.synth) {
                window.NotificationSystem.synth.playChime(chimeType);
            }
        }

        /**
         * Dynamically render orders into admin table
         */
        function renderOrdersTable(ordersList) {
            const tbody = document.getElementById('orders-table-tbody');
            if (!tbody || !ordersList) return;

            if (ordersList.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fa-solid fa-receipt" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                            <p style="font-weight: 500;">No pending or active orders found.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = ordersList.map((order, idx) => {
                const numericId = order.order_id || order.id || (idx + 1);
                
                let itemsHtml = '';
                if (Array.isArray(order.items)) {
                    itemsHtml = order.items.map(item => `<div>${item.quantity}x ${item.item_name || item.name || 'Gourmet Selection'}</div>`).join('');
                } else if (order.items_summary) {
                    itemsHtml = `<div style="font-size: 0.9rem;">${order.items_summary}</div>`;
                } else {
                    itemsHtml = `<div style="font-size: 0.9rem;">${order.items || 'Gourmet Culinary Selection'}</div>`;
                }

                let receivedTime = 'Just now';
                if (order.created_at) {
                    const cleanDateStr = order.created_at.replace(/-/g, '/');
                    const parsedDate = new Date(cleanDateStr);
                    receivedTime = isNaN(parsedDate.getTime()) ? order.created_at : parsedDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }

                const customerName = (order.customer && order.customer.username) ? order.customer.username : (order.username || order.customer_name || 'Client');
                const customerEmail = (order.customer && order.customer.email) ? order.customer.email : (order.email || order.customer_email || '');

                return `
                    <tr id="order-row-${numericId}">
                        <td data-label="Order ID" style="font-weight: 700; color: var(--primary);">${order.order_number}</td>
                        <td data-label="Customer Name">
                            <div style="font-weight: 600; color: var(--text-primary);">${customerName}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">${customerEmail}</div>
                        </td>
                        <td data-label="Order Items">
                            ${itemsHtml}
                        </td>
                        <td data-label="Price" style="font-weight: 700; color: var(--text-primary);">Tk. ${parseFloat(order.total_price).toFixed(0)}</td>
                        <td data-label="Received Time" style="color: var(--text-muted);">${receivedTime}</td>
                        <td data-label="Tracking Status">
                            <span class="${getStatusColorClass(order.status)}" id="badge-${numericId}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span>
                            ${order.delivery_man_name ? `<div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;"><i class="fa-solid fa-motorcycle"></i> ${order.delivery_man_name}</div>` : ''}
                        </td>
                        <td data-label="Actions Control">
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                ${getActionButtonsHtml(numericId, order.order_number, order.status)}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        /**
         * Fetch active list from get-pending-orders.php API
         */
        async function fetchPendingOrdersDesk() {
            try {
                const response = await fetch('../api/get-pending-orders.php');
                if (!response.ok) throw new Error('API Offline');
                const result = await response.json();
                
                if (result.success && result.data) {
                    isLiveConnected = true;
                    activeOrdersCache = result.data;
                    renderOrdersTable(result.data);

                    const indicator = document.querySelector('.realtime-indicator');
                    if (indicator) {
                        indicator.className = 'realtime-indicator realtime-active';
                        const label = indicator.querySelector('.indicator-label');
                        if (label) label.textContent = 'Live Orders Stream Active';
                    }

                    // Loop check for any newly arrived pending orders
                    let hasNewPending = false;
                    let latestPendingOrder = null;

                    result.data.forEach(order => {
                        if (order.status.toLowerCase() === 'pending') {
                            if (!knownOrders.has(order.order_number)) {
                                if (!isFirstLoad) {
                                    hasNewPending = true;
                                    latestPendingOrder = order;
                                }
                                knownOrders.add(order.order_number);
                            }
                        }
                    });

                    // On first load, populate all current order numbers to avoid alert burst
                    if (isFirstLoad) {
                        result.data.forEach(order => {
                            knownOrders.add(order.order_number);
                        });
                        isFirstLoad = false;
                    }

                    if (hasNewPending && latestPendingOrder) {
                        triggerNewOrderAlert(latestPendingOrder);
                    } else if (lastCheckedOrdersCount > 0 && result.data.length > lastCheckedOrdersCount) {
                        // General notification toast
                        if (window.NotificationSystem) {
                            window.NotificationSystem.toast('success', 'New Ticket Placed', 'A new customer storefront order has arrived.');
                        }
                    }

                    lastCheckedOrdersCount = result.data.length;
                }
            } catch (error) {
                console.log('[Admin desk] API polling offline. Operating in simulation mode.');
                isLiveConnected = false;
                
                const indicator = document.querySelector('.realtime-indicator');
                if (indicator) {
                    indicator.className = 'realtime-indicator realtime-offline';
                    const label = indicator.querySelector('.indicator-label');
                    if (label) label.textContent = 'Offline Fallback Simulator';
                }
            }
        }

        function triggerAutoRefreshSim() {
            if (isLiveConnected) {
                fetchPendingOrdersDesk();
            } else {
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('success', 'Database Sync Complete', 'Simulated refresh updated storefront order queues.');
                }
            }
        }

        async function fetchDisputesDesk() {
            try {
                const response = await fetch('../api/get-disputed-orders.php');
                if (!response.ok) throw new Error('Disputes API offline');
                const result = await response.json();
                
                if (result.success && result.data) {
                    renderDisputesTable(result.data);
                }
            } catch (error) {
                console.warn('[AdminDesk] Disputes fetch offline:', error);
            }
        }

        function renderDisputesTable(disputesList) {
            const tbody = document.getElementById('disputes-table-tbody');
            if (!tbody) return;

            if (disputesList.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fa-solid fa-circle-check" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 1rem; color: var(--success);"></i>
                            <p style="font-weight: 500;">No active quality disputes reported. Great job kitchen!</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = disputesList.map(order => {
                let itemsHtml = order.items.map(item => `<div>${item.quantity}x ${item.item_name}</div>`).join('');
                
                let statusBadge = '';
                if (order.dispute_status === 'pending') {
                    statusBadge = `<span class="status-badge status-pending" style="color: var(--warning); border-color: var(--warning);"><i class="fa-solid fa-clock-rotate-left"></i> Review Pending</span>`;
                } else if (order.dispute_status === 'refunded') {
                    statusBadge = `<span class="status-badge status-cancelled" style="color: var(--danger); border-color: var(--danger);"><i class="fa-solid fa-circle-dollar-to-slot"></i> Refunded</span>`;
                } else if (order.dispute_status === 'replaced') {
                    statusBadge = `<span class="status-badge status-completed" style="color: var(--success); border-color: var(--success);"><i class="fa-solid fa-truck-ramp-box"></i> Replaced</span>`;
                }

                let actionsHtml = '';
                if (order.dispute_status === 'pending') {
                    actionsHtml = `
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <button class="btn btn-primary btn-sm" onclick="resolveCustomerDispute('${order.order_number}', 'replace')" style="background: var(--success); border-color: var(--success); color: white;" title="Dispatch fresh items free of charge">
                                Replace <i class="fa-solid fa-rotate"></i>
                            </button>
                            <button class="btn btn-glass btn-sm" onclick="resolveCustomerDispute('${order.order_number}', 'refund')" style="border-color: rgba(239, 68, 68, 0.4); color: var(--danger);" title="Refund complete payment">
                                Refund <i class="fa-solid fa-circle-dollar-to-slot"></i>
                            </button>
                        </div>
                    `;
                } else {
                    actionsHtml = `<span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;"><i class="fa-solid fa-lock"></i> Case Resolved</span>`;
                }

                let ratingHtml = '';
                if (order.feedback_rating !== null) {
                    ratingHtml = `
                        <div style="margin-top: 0.5rem; border-top: 1px dashed var(--border-color); padding-top: 0.5rem;">
                            <span style="color: #ffc107; font-weight: 600;">Rating: ${'★'.repeat(order.feedback_rating)}${'☆'.repeat(5-order.feedback_rating)}</span>
                            ${order.feedback_comment ? `<div style="font-style: italic; font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.15rem;">"${order.feedback_comment}"</div>` : ''}
                        </div>
                    `;
                }

                return `
                    <tr>
                        <td data-label="Order ID" style="font-weight: 700; color: var(--primary);">${order.order_number}</td>
                        <td data-label="Customer Details">
                            <div style="font-weight: 600; color: var(--text-primary);">${order.username}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">${order.email}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">${order.phone}</div>
                        </td>
                        <td data-label="Issue Category">
                            <span style="font-weight: 600; color: var(--danger); font-size: 0.85rem; background: rgba(239, 68, 68, 0.05); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); border: 1px solid rgba(239, 68, 68, 0.15);">
                                ${order.dispute_category}
                            </span>
                        </td>
                        <td data-label="Customer Statement" style="max-width: 250px;">
                            <div style="font-size: 0.85rem; line-height: 1.4; color: var(--text-secondary); word-break: break-word; font-style: italic;">
                                "${order.dispute_description}"
                            </div>
                            ${ratingHtml}
                        </td>
                        <td data-label="Disputed Items">
                            <div style="font-size: 0.85rem; font-weight: 500;">
                                ${itemsHtml}
                            </div>
                        </td>
                        <td data-label="Resolution Status">
                            ${statusBadge}
                        </td>
                        <td data-label="Action Control">
                            ${actionsHtml}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function resolveCustomerDispute(orderNum, resolution) {
            if (!confirm(`Are you sure you want to resolve dispute for #${orderNum} via ${resolution.toUpperCase()}?`)) {
                return;
            }

            try {
                const response = await fetch('../api/resolve-dispute.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_number: orderNum,
                        resolution: resolution
                    })
                });

                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Dispute Resolved', `Order #${orderNum} dispute closed via: ${resolution.toUpperCase()}`);
                    }
                    // Sync dashboards
                    fetchDisputesDesk();
                    fetchPendingOrdersDesk(); // Sync active queue if replacement order dispatched!
                } else {
                    throw new Error(result.message || 'Resolve failed');
                }
            } catch (err) {
                console.error(err);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Action Failed', err.message || 'Failed to process dispute resolution');
                }
            }
        }

        // Setup filter box searching
        document.addEventListener('DOMContentLoaded', () => {
            const searchBox = document.getElementById('order-search-box');
            if (searchBox) {
                searchBox.addEventListener('input', (e) => {
                    const term = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#orders-table-tbody tr');
                    
                    rows.forEach(row => {
                        const name = row.cells[1].textContent.toLowerCase();
                        const id = row.cells[0].textContent.toLowerCase();
                        
                        if (name.includes(term) || id.includes(term)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            fetchPendingOrdersDesk();
            fetchDisputesDesk();
            fetchDeliveryMen();
            setInterval(() => {
                fetchPendingOrdersDesk();
                fetchDisputesDesk();
                fetchDeliveryMen();
            }, 15000);
        });
    </script>

    <!-- POS New Order Alert Overlay -->
    <div id="pos-alert-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: var(--bg-dark-surface, #1e293b); border: 2px solid var(--primary); padding: 3rem; border-radius: 24px; text-align: center; width: 480px; max-width: 90%; box-shadow: 0 25px 50px rgba(0,0,0,0.5); position: relative;">
            <div style="width: 90px; height: 90px; background: rgba(234, 103, 33, 0.1); border: 2px solid var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--primary); font-size: 2.5rem;">
                <i class="fa-solid fa-bell fa-shake"></i>
            </div>
            <h2 style="font-family: var(--font-heading); font-size: 1.85rem; color: #ffffff; margin-bottom: 0.5rem;">New Customer Order!</h2>
            <p style="color: var(--text-muted, #94a3b8); margin-bottom: 1.5rem; font-size: 1rem;">A new storefront request is waiting for your kitchen approval.</p>
            
            <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 16px; margin-bottom: 2rem; text-align: left;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    <span style="color: var(--text-secondary);">Order ID:</span>
                    <span id="alert-order-number" style="font-weight: 700; color: var(--primary);">#FC-XXXX</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    <span style="color: var(--text-secondary);">Customer:</span>
                    <span id="alert-customer-name" style="font-weight: 600; color: #ffffff;">John Doe</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                    <span style="color: var(--text-secondary);">Total Bill:</span>
                    <span id="alert-total-price" style="font-weight: 700; color: #ffffff;">Tk. 0</span>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button class="btn btn-primary btn-lg" id="btn-accept-alert" style="flex: 1;" onclick="acceptOrderFromAlert()">
                    Accept Order <i class="fa-solid fa-check" style="margin-left: 0.25rem;"></i>
                </button>
                <button class="btn btn-glass btn-lg" id="btn-dismiss-alert" style="flex: 1; border-color: rgba(255, 255, 255, 0.1); color: #ffffff;" onclick="dismissOrderAlert()">
                    Dismiss
                </button>
            </div>
        </div>
    </div>

    <!-- Assign Rider Modal Overlay -->
    <div id="assign-rider-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); z-index: 10000; align-items: center; justify-content: center; transition: all 0.3s ease;">
        <div class="glass-panel" style="background: var(--bg-dark-surface, #1e293b); border: 2px solid var(--primary); padding: 3rem; border-radius: 24px; text-align: center; width: 450px; max-width: 90%; box-shadow: 0 25px 50px rgba(0,0,0,0.5); position: relative; transform: scale(0.9); transition: transform 0.3s ease;">
            <div style="width: 70px; height: 70px; background: rgba(234, 103, 33, 0.1); border: 2px solid var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--primary); font-size: 2rem;">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
            <h3 style="font-family: var(--font-heading); font-size: 1.5rem; color: #ffffff; margin-bottom: 0.5rem;">Assign Delivery Rider</h3>
            <p id="assign-rider-order-label" style="color: var(--text-muted, #94a3b8); margin-bottom: 1.5rem; font-size: 0.95rem;">Choose a rider to dispatch for Order #FC-XXXX</p>
            
            <div style="text-align: left; margin-bottom: 2rem;">
                <label style="color: var(--text-muted, #94a3b8); font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Select Available Rider</label>
                <select id="rider-select" class="form-input" style="width: 100%; padding: 0.75rem; background: var(--bg-dark, #0f172a); border: 1px solid var(--border-color, #334155); color: #ffffff; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; outline: none; cursor: pointer;">
                    <!-- Dynamic select options -->
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button class="btn btn-primary btn-lg" style="flex: 1;" onclick="confirmAssignRider()">
                    Dispatch <i class="fa-solid fa-paper-plane" style="margin-left: 0.25rem;"></i>
                </button>
                <button class="btn btn-glass btn-lg" style="flex: 1; border-color: rgba(255, 255, 255, 0.1); color: #ffffff;" onclick="closeAssignRiderModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Thermal Receipt container for print media -->
    <div id="thermal-receipt"></div>

    <script>
        function triggerNewOrderAlert(order) {
            pendingAlertOrder = order;
            
            // Set alert details
            document.getElementById('alert-order-number').textContent = order.order_number;
            document.getElementById('alert-customer-name').textContent = (order.customer && order.customer.username) ? order.customer.username : (order.username || order.customer_name || 'Client');
            document.getElementById('alert-total-price').textContent = `Tk. ${parseFloat(order.total_price).toFixed(0)}`;
            
            // Show modal
            document.getElementById('pos-alert-overlay').style.display = 'flex';
            
            // Start looping sound chime
            if (alarmInterval) clearInterval(alarmInterval);
            
            // Play immediately
            if (window.NotificationSystem && window.NotificationSystem.synth) {
                window.NotificationSystem.synth.playChime('warning');
            }
            
            alarmInterval = setInterval(() => {
                const soundToggle = document.getElementById('sound-alert-toggle');
                const soundEnabled = soundToggle ? soundToggle.checked : true;
                if (soundEnabled && window.NotificationSystem && window.NotificationSystem.synth) {
                    window.NotificationSystem.synth.playChime('warning');
                }
            }, 2500);
        }

        function dismissOrderAlert() {
            // Silence alarm
            if (alarmInterval) {
                clearInterval(alarmInterval);
                alarmInterval = null;
            }
            document.getElementById('pos-alert-overlay').style.display = 'none';
        }

        function acceptOrderFromAlert() {
            if (pendingAlertOrder) {
                // If it is a mock order (from simulation)
                if (typeof pendingAlertOrder.order_id === 'undefined' && pendingAlertOrder.id === 'undefined') {
                    // Try to transition mock row if it matches id
                    const mockRowId = pendingAlertOrder.order_number.replace('FC-MOCK-', '');
                    advanceOrderStatus(mockRowId);
                } else {
                    updateOrderStatus(pendingAlertOrder.order_number, 'preparing', pendingAlertOrder.order_id || pendingAlertOrder.id);
                }
            }
            dismissOrderAlert();
        }

        function printThermalInvoice(orderNum) {
            // Find order in activeOrdersCache
            let order = activeOrdersCache.find(o => o.order_number === orderNum);
            
            // Fallback mock check if not connected to live DB
            if (!order) {
                let row = null;
                const rows = document.querySelectorAll('#orders-table-tbody tr');
                rows.forEach(r => {
                    if (r.cells[0].textContent.trim() === orderNum) {
                        row = r;
                    }
                });
                if (row) {
                    order = {
                        order_number: orderNum,
                        username: row.cells[1].querySelector('div').textContent.trim(),
                        email: row.cells[1].querySelectorAll('div')[1].textContent.trim(),
                        items: row.cells[2].innerText || row.cells[2].textContent.trim(),
                        total_price: parseFloat(row.cells[3].textContent.replace('Tk. ', '').trim()) || 250,
                        created_at: new Date().toISOString()
                    };
                }
            }

            if (!order) {
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Error', 'Order details not found.');
                }
                return;
            }

            // Generate thermal layout
            let itemsHtml = '';
            let subtotal = 0;
            if (Array.isArray(order.items)) {
                itemsHtml = order.items.map(item => {
                    const itemCost = parseFloat(item.price) * parseInt(item.quantity);
                    subtotal += itemCost;
                    return `
                        <tr>
                            <td>${item.quantity}x ${item.item_name || item.name}</td>
                            <td class="text-right">Tk. ${itemCost.toFixed(0)}</td>
                        </tr>
                    `;
                }).join('');
            } else {
                subtotal = parseFloat(order.total_price) - 60 - ((parseFloat(order.total_price) - 60) * 0.05);
                itemsHtml = `
                    <tr>
                        <td>${order.items_summary || order.items || 'Gourmet Selection'}</td>
                        <td class="text-right">Tk. ${subtotal.toFixed(0)}</td>
                    </tr>
                `;
            }

            const tax = subtotal * 0.05; // 5% tax
            const deliveryFee = 60.00;
            const total = parseFloat(order.total_price) || (subtotal + tax + deliveryFee);

            const receiptHtml = `
                <div class="receipt-wrapper">
                    <div class="receipt-header">
                        <div class="receipt-logo">Crispy Chicken</div>
                        <div class="receipt-sub">Premium Delivery Service</div>
                        <div class="receipt-sub">Web: food.colourq.com.bd</div>
                    </div>
                    <div class="receipt-divider"></div>
                    <div class="receipt-title">Order Ticket</div>
                    <div class="receipt-divider"></div>
                    
                    <table class="receipt-info-table">
                        <tr>
                            <td><strong>Order No:</strong></td>
                            <td style="text-align: right;">${order.order_number}</td>
                        </tr>
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td style="text-align: right;">${new Date().toLocaleDateString()}</td>
                        </tr>
                        <tr>
                            <td><strong>Customer:</strong></td>
                            <td style="text-align: right;">${order.username || order.customer_name || 'Client'}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td style="text-align: right;">${order.phone || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td style="text-align: right; word-break: break-word; max-width: 150px;">${order.delivery_address || 'N/A'}</td>
                        </tr>
                        ${order.delivery_man_name ? `
                        <tr>
                            <td><strong>Rider:</strong></td>
                            <td style="text-align: right;">${order.delivery_man_name} (${order.delivery_man_phone || 'N/A'})</td>
                        </tr>
                        ` : ''}
                    </table>
                    
                    <div class="receipt-divider"></div>
                    
                    <table class="receipt-items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>
                    
                    <div class="receipt-divider"></div>
                    
                    <table class="receipt-totals-table">
                        <tr>
                            <td>Subtotal:</td>
                            <td style="text-align: right;">Tk. ${subtotal.toFixed(0)}</td>
                        </tr>
                        <tr>
                            <td>VAT (5%):</td>
                            <td style="text-align: right;">Tk. ${tax.toFixed(0)}</td>
                        </tr>
                        <tr>
                            <td>Delivery Fee:</td>
                            <td style="text-align: right;">Tk. ${deliveryFee.toFixed(0)}</td>
                        </tr>
                        <tr class="total-row">
                            <td>TOTAL:</td>
                            <td style="text-align: right;">Tk. ${total.toFixed(0)}</td>
                        </tr>
                    </table>
                    
                    <div class="receipt-divider"></div>
                    <div class="receipt-footer">
                        Thank You For Ordering!<br>
                        Enjoy Your Meal!
                    </div>
                </div>
            `;

            // Inject into #thermal-receipt
            let container = document.getElementById('thermal-receipt');
            if (!container) {
                container = document.createElement('div');
                container.id = 'thermal-receipt';
                document.body.appendChild(container);
            }
            container.innerHTML = receiptHtml;

            // Trigger Print
            window.print();
        }

        // Delivery Man Assignment System
        let assignOrderNum = null;
        let assignOrderId = null;
        let deliveryMenCache = [];

        async function fetchDeliveryMen() {
            try {
                const response = await fetch('../api/get-delivery-men.php');
                if (!response.ok) throw new Error('API offline');
                const result = await response.json();
                if (result.success && result.data) {
                    deliveryMenCache = result.data;
                    renderDeliveryTeam(result.data);
                    populateRiderSelect(result.data);
                }
            } catch (error) {
                console.warn('[AdminDesk] Failed to load delivery team. Falling back to mock riders.');
                const mockTeam = [
                    { id: 1, name: 'Rahat Khan', phone: '01712345678', status: 'available', delivery_count: 5 },
                    { id: 2, name: 'Sumon Mia', phone: '01812345678', status: 'available', delivery_count: 8 },
                    { id: 3, name: 'Kamal Hossain', phone: '01912345678', status: 'available', delivery_count: 3 }
                ];
                deliveryMenCache = mockTeam;
                renderDeliveryTeam(mockTeam);
                populateRiderSelect(mockTeam);
            }
        }

        function renderDeliveryTeam(team) {
            const tbody = document.getElementById('delivery-team-tbody');
            if (!tbody) return;
            
            if (team.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No riders registered in database.
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = team.map(rider => `
                <tr>
                    <td data-label="Rider ID" style="font-weight: 700; color: var(--primary);">#RD-${rider.id}</td>
                    <td data-label="Rider Name" style="font-weight: 600; color: var(--text-primary);">${rider.name}</td>
                    <td data-label="Contact Phone" style="color: var(--text-muted); font-family: monospace;">${rider.phone}</td>
                    <td data-label="Active Status">
                        <span class="status-badge ${rider.status === 'available' ? 'status-success' : 'status-preparing'}">${rider.status.toUpperCase()}</span>
                    </td>
                    <td data-label="Completed Deliveries" style="font-weight: 700; color: var(--text-primary);">${rider.delivery_count} Orders</td>
                </tr>
            `).join('');
        }

        function populateRiderSelect(team) {
            const select = document.getElementById('rider-select');
            if (!select) return;
            select.innerHTML = team.map(rider => 
                `<option value="${rider.id}">${rider.name} (${rider.phone}) - ${rider.status.toUpperCase()}</option>`
            ).join('');
        }

        function openAssignRiderModal(orderNum, orderId) {
            assignOrderNum = orderNum;
            assignOrderId = orderId;
            document.getElementById('assign-rider-order-label').textContent = `Choose a rider to dispatch for Order #${orderNum}`;
            const overlay = document.getElementById('assign-rider-overlay');
            overlay.style.display = 'flex';
            setTimeout(() => {
                overlay.querySelector('.glass-panel').style.transform = 'scale(1)';
            }, 10);
        }

        function closeAssignRiderModal() {
            const overlay = document.getElementById('assign-rider-overlay');
            overlay.querySelector('.glass-panel').style.transform = 'scale(0.9)';
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 150);
            assignOrderNum = null;
            assignOrderId = null;
        }

        async function confirmAssignRider() {
            const select = document.getElementById('rider-select');
            if (!select || !assignOrderNum) return;
            const riderId = select.value;
            
            try {
                const payload = {
                    order_number: assignOrderNum,
                    status: 'delivering',
                    delivery_man_id: parseInt(riderId)
                };

                const response = await fetch('../api/update-order-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        if (window.NotificationSystem) {
                            window.NotificationSystem.toast('success', 'Order Dispatched', `Order #${assignOrderNum} dispatched with Rider ID: ${riderId}`);
                        }
                        closeAssignRiderModal();
                        fetchPendingOrdersDesk();
                        fetchDeliveryMen();
                        return;
                    }
                }
                throw new Error('API offline');
            } catch (error) {
                console.log('[AdminDesk] confirmAssignRider API fallback:', error);
                
                // Update caching for fallback simulator
                let rider = deliveryMenCache.find(r => r.id == riderId);
                if (rider) {
                    rider.delivery_count = parseInt(rider.delivery_count) + 1;
                    renderDeliveryTeam(deliveryMenCache);
                }

                // Call client-side simulator update
                const badge = document.getElementById(`badge-${assignOrderId}`);
                if (badge) {
                    badge.textContent = 'Delivering';
                    badge.className = 'status-badge status-delivering';
                }

                // Add rider name beneath badge
                const td = badge.parentNode;
                const existingRiderInfo = td.querySelector('.rider-info-sub');
                if (existingRiderInfo) existingRiderInfo.remove();
                
                const riderDiv = document.createElement('div');
                riderDiv.className = 'rider-info-sub';
                riderDiv.style.fontSize = '0.75rem';
                riderDiv.style.color = 'var(--text-muted)';
                riderDiv.style.marginTop = '0.25rem';
                riderDiv.innerHTML = `<i class="fa-solid fa-motorcycle"></i> ${rider ? rider.name : 'Rider'}`;
                td.appendChild(riderDiv);

                const row = document.getElementById(`order-row-${assignOrderId}`);
                const actionTd = row ? row.querySelector('td[data-label="Actions Control"]') : null;
                if (actionTd) {
                    const actionContainer = actionTd.querySelector('div') || actionTd;
                    actionContainer.innerHTML = getActionButtonsHtml(assignOrderId, assignOrderNum, 'delivering');
                }

                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('success', 'State Advanced (Simulated)', `Order #${assignOrderNum} has updated to DELIVERING`);
                }

                closeAssignRiderModal();
            }
        }
    </script>
</body>
</html>
