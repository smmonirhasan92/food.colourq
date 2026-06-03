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
    <title>Admin Dashboard - Crispy Chicken</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>
</head>
<body>

    <!-- Mobile Header Collapsed Panel -->
    <div class="admin-mobile-header hide-desktop">
        <a href="dashboard.php" class="brand-logo" style="font-size: 1.35rem;">
            Crispy Chicken <span class="brand-dot"></span>
        </a>
        <div class="admin-sidebar-toggle">
            <i class="fa-solid fa-bars-staggered"></i>
        </div>
    </div>

    <!-- Admin Wrapper Grid Layout -->
    <div class="admin-wrapper">
        
        <!-- Sidebar Navigation Drawer -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="brand-logo">
                    Crispy Chicken<span class="brand-dot"></span>
                </a>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard.php" class="sidebar-link active">
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

        <!-- Main Workspace Area -->
        <main class="admin-main">
            
            <!-- Top Action Header -->
            <div class="admin-topbar">
                <div class="page-title">
                    <h1 style="color: var(--text-primary);">Administrative Operations</h1>
                    <p style="color: var(--text-secondary);">Track metrics, configure automation toggles, and respond to order notifications.</p>
                </div>

                <!-- Real-time Active Dot Status Indicator -->
                <div class="realtime-indicator realtime-active">
                    <span class="indicator-dot"></span>
                    <span class="indicator-label">Operational Control Online</span>
                </div>
            </div>

            <!-- Dashboard Stats Summary Cards Grid -->
            <section class="grid grid-cols-4">
                <!-- Stat Card 1 -->
                <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="background: rgba(234, 103, 33, 0.08); width: 50px; height: 50px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary);">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">Total Orders</span>
                        <h2 style="font-size: 1.75rem; margin-top: 0.15rem; color: var(--text-primary);" class="stat-dial" data-val="1480">0</h2>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="background: rgba(16, 185, 129, 0.08); width: 50px; height: 50px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--success);">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">Net Sales</span>
                        <h2 style="font-size: 1.75rem; margin-top: 0.15rem; color: var(--text-primary);">Tk. <span class="stat-dial" data-val="24950">0</span></h2>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="background: rgba(59, 130, 246, 0.08); width: 50px; height: 50px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--info);">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">Avg Kitchen Prep</span>
                        <h2 style="font-size: 1.75rem; margin-top: 0.15rem; color: var(--text-primary);"><span class="stat-dial" data-val="14">0</span> mins</h2>
                    </div>
                </div>

                <!-- Stat Card 4 -->
                <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="background: rgba(245, 158, 11, 0.08); width: 50px; height: 50px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--warning);">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">Pending Actions</span>
                        <h2 style="font-size: 1.75rem; margin-top: 0.15rem; color: var(--text-primary);" class="stat-dial" data-val="3">0</h2>
                    </div>
                </div>
            </section>

            <!-- Operational Control Panels -->
            <div class="grid grid-cols-2" style="grid-template-columns: 3fr 2fr; align-items: start; gap: 2rem;">
                
                <!-- Quick Simulated Controls Widget -->
                <section class="glass-panel" style="padding: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; color: var(--text-primary);">
                        <i class="fa-solid fa-gears" style="color: var(--primary);"></i> Interactive Automation Widgets
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <!-- Configuration Item 1 -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.03); padding-bottom: 1rem;">
                            <div>
                                <h4 style="font-weight: 600; color: var(--text-primary);">Synthesizer Sound Alerts</h4>
                                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.1rem;">Enable customized alert rings fallbacks for new incoming order notifications.</p>
                            </div>
                            <label class="form-checkbox-label">
                                <input type="checkbox" checked style="display:none;" id="sound-alert-toggle">
                                <span class="form-checkbox"></span>
                            </label>
                        </div>

                        <!-- Configuration Item 2 -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.03); padding-bottom: 1rem;">
                            <div>
                                <h4 style="font-weight: 600; color: var(--text-primary);">Auto-Accept Orders</h4>
                                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.1rem;">Instantly transition new incoming storefront tickets to 'Kitchen Preparing'.</p>
                            </div>
                            <label class="form-checkbox-label">
                                <input type="checkbox" style="display:none;" id="auto-accept-toggle">
                                <span class="form-checkbox"></span>
                            </label>
                        </div>

                        <!-- Action simulations buttons -->
                        <div style="margin-top: 1rem;">
                            <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Simulate Storefront Order Actions</h4>
                            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                <button class="btn btn-primary btn-sm" onclick="simulateIncomingOrder()">
                                    <i class="fa-solid fa-plus-circle"></i> Trigger Customer Order
                                </button>
                                <button class="btn btn-secondary btn-sm" onclick="simulateKitchenWarning()">
                                    <i class="fa-solid fa-exclamation-triangle"></i> Simulate Printer Jam Warning
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Order Acceptance Monitoring Sidebar -->
                <aside class="glass-panel" style="padding: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; color: var(--text-primary);">
                        <i class="fa-solid fa-circle-nodes" style="color: var(--primary);"></i> Connected System Nodes
                    </h3>

                    <ul style="display: flex; flex-direction: column; gap: 1.25rem;">
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fa-solid fa-terminal" style="color: var(--success); font-size: 1.1rem;"></i>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary);">Kitchen Display System (KDS)</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Active IP: 192.168.1.45</div>
                                </div>
                            </div>
                            <span class="status-badge status-completed">Online</span>
                        </li>
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fa-solid fa-print" style="color: var(--success); font-size: 1.1rem;"></i>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary);">Thermal Receipt Printer</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Paper Feed Status: Normal</div>
                                </div>
                            </div>
                            <span class="status-badge status-completed">Online</span>
                        </li>
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fa-solid fa-network-wired" style="color: var(--warning); font-size: 1.1rem;"></i>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary);">SSE Event Streaming Bridge</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Ping Latency: 42ms</div>
                                </div>
                            </div>
                            <span class="status-badge status-pending">Connected</span>
                        </li>
                    </ul>
                </aside>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/animations.js"></script>

    <script>
        let lastIncomingOrderCount = 0;
        let isLiveConnected = false;

        // Trigger GSAP dials count-up simulation on load
        function animateAllCounters() {
            const counters = document.querySelectorAll('.stat-dial');
            counters.forEach(counter => {
                const target = parseInt(counter.dataset.val);
                if (window.AnimationEngine) {
                    window.AnimationEngine.animateCounter(counter, target);
                } else {
                    counter.textContent = target.toLocaleString();
                }
            });
        }

        /**
         * Dynamic polling to check for newly incoming orders
         */
        async function checkIncomingOrdersStream() {
            try {
                // Poll check-new-orders or get-pending-orders to get live operational metrics
                const response = await fetch('../api/check-new-orders.php');
                if (!response.ok) throw new Error('API Offline');
                const result = await response.json();
                
                if (result.success && result.data) {
                    isLiveConnected = true;
                    const orderData = result.data;
                    
                    // Helper to update counter with GSAP animation if value changes
                    const updateDialIfChanged = (selector, newVal) => {
                        const dial = document.querySelector(selector);
                        if (dial) {
                            const oldVal = parseFloat(dial.dataset.val) || 0;
                            if (oldVal !== newVal) {
                                dial.dataset.val = newVal;
                                if (window.AnimationEngine && window.AnimationEngine.animateCounter) {
                                    window.AnimationEngine.animateCounter(dial, newVal);
                                } else {
                                    dial.textContent = typeof newVal === 'number' ? Math.floor(newVal).toLocaleString() : newVal;
                                }
                            }
                        }
                    };

                    // Update stats dial widgets dynamically
                    if (orderData.total_orders !== undefined) {
                        updateDialIfChanged('.stat-dial[data-val="1480"]', orderData.total_orders);
                    }
                    if (orderData.net_sales !== undefined) {
                        updateDialIfChanged('.stat-dial[data-val="24950"]', orderData.net_sales);
                    }
                    if (orderData.avg_prep !== undefined) {
                        updateDialIfChanged('.stat-dial[data-val="14"]', orderData.avg_prep);
                    }
                    if (orderData.pending_actions !== undefined) {
                        updateDialIfChanged('.stat-dial[data-val="3"]', orderData.pending_actions);
                    }

                    // Trigger toast notifications + synthesized chimes for newly arrived storefront orders
                    if (orderData.new_orders && orderData.new_orders.length > 0) {
                        const soundEnabled = document.getElementById('sound-alert-toggle').checked;
                        orderData.new_orders.forEach(newOrder => {
                            if (window.NotificationSystem) {
                                window.NotificationSystem.toast(
                                    'success', 
                                    'New Order Placed', 
                                    `Order #${newOrder.order_number} by ${newOrder.username || 'Customer'} (Tk. ${parseFloat(newOrder.total_price).toFixed(0)})`
                                );
                                if (soundEnabled && window.NotificationSystem.synth) {
                                    window.NotificationSystem.synth.playChime('success');
                                }
                            }
                        });
                    }

                    // Update live indicator
                    const indicator = document.querySelector('.realtime-indicator');
                    if (indicator) {
                        indicator.className = 'realtime-indicator realtime-active';
                        const label = indicator.querySelector('.indicator-label');
                        if (label) label.textContent = 'Operational Control Online';
                    }
                }
            } catch (error) {
                console.log('[Dashboard Poller] Offline fallback mode active. Operational metrics simulated.', error);
                isLiveConnected = false;
                
                const indicator = document.querySelector('.realtime-indicator');
                if (indicator) {
                    indicator.className = 'realtime-indicator realtime-offline';
                    const label = indicator.querySelector('.indicator-label');
                    if (label) label.textContent = 'Control Offline (Simulating)';
                }
            }
        }

        function triggerNewArrivalAlert() {
            const soundEnabled = document.getElementById('sound-alert-toggle').checked;
            if (window.NotificationSystem) {
                window.NotificationSystem.toast('success', 'Incoming Order Ticket', 'A new customer storefront order has been submitted.');
                if (soundEnabled && window.NotificationSystem.synth) {
                    window.NotificationSystem.synth.playChime('success');
                }
            }
        }

        // Trigger synthesised alerts
        function simulateIncomingOrder() {
            const soundEnabled = document.getElementById('sound-alert-toggle').checked;
            
            if (window.NotificationSystem) {
                // Synthesize happy double-chime + Toast
                window.NotificationSystem.toast('success', 'Incoming Order Ticket', 'Order #FC-8921-X has been submitted by Alice Smith (Tk. 3,248)');
                
                // If synthesizer is enabled, play sound alert
                if (soundEnabled && window.NotificationSystem.synth) {
                    window.NotificationSystem.synth.playChime('success');
                }
            }
        }

        function simulateKitchenWarning() {
            const soundEnabled = document.getElementById('sound-alert-toggle').checked;
            
            if (window.NotificationSystem) {
                window.NotificationSystem.toast('warning', 'Hardware Alert', 'Kitchen receipt thermal printer is running low on paper rolls.');
                
                if (soundEnabled && window.NotificationSystem.synth) {
                    window.NotificationSystem.synth.playChime('warning');
                }
            }
        }

        // Setup dashboard triggers
        window.addEventListener('load', () => {
            animateAllCounters();
            
            // Poll for order alerts every 15 seconds
            checkIncomingOrdersStream();
            setInterval(checkIncomingOrdersStream, 15000);
        });
    </script>
</body>
</html>
