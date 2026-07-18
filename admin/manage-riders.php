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
    <title>Manage Riders - Food ColourQ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>
</head>
<body>

    <!-- Mobile Header Panel -->
    <div class="admin-mobile-header hide-desktop">
        <a href="dashboard.php" class="brand-logo" style="font-size: 1.35rem;">
            Food ColourQ <span class="brand-dot"></span>
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
                <a href="pos.php" class="sidebar-link">
                    <i class="fa-solid fa-cash-register"></i> POS Counter
                </a>
                <a href="manage-orders.php" class="sidebar-link">
                    <i class="fa-solid fa-receipt"></i> Live Orders
                </a>
                <a href="manage-menu.php" class="sidebar-link">
                    <i class="fa-solid fa-pizza-slice"></i> Culinary Menu
                </a>
                <a href="manage-riders.php" class="sidebar-link active">
                    <i class="fa-solid fa-motorcycle"></i> Delivery Riders
                </a>
                <a href="reports.php" class="sidebar-link">
                    <i class="fa-solid fa-chart-line"></i> Business Reports
                </a>
                <a href="manage-coupons.php" class="sidebar-link">
                    <i class="fa-solid fa-ticket"></i> Manage Coupons
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
                    <h1 style="color: var(--text-primary);">Delivery Riders Directory</h1>
                    <p style="color: var(--text-secondary);">Register new riders, track completed deliveries, and monitor payout balances (Tk. 60 per delivery).</p>
                </div>

                <div class="realtime-indicator realtime-active">
                    <span class="indicator-dot"></span>
                    <span class="indicator-label">Rider Metrics Active</span>
                </div>
            </div>

            <!-- Main Layout (Add Rider & Directory Table) -->
            <div class="grid grid-cols-2" style="grid-template-columns: 1fr 2fr; align-items: start; gap: 2rem;">
                
                <!-- Register Rider Form Panel -->
                <section class="glass-panel" style="padding: 2rem; position: sticky; top: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                        <i class="fa-solid fa-user-plus" style="color: var(--primary);"></i> Register Rider
                    </h3>

                    <form id="add-rider-form" onsubmit="handleNewRiderSubmit(event)">
                        <div class="form-group">
                            <label class="form-label" for="rider-name">Full Name</label>
                            <input class="form-input" type="text" id="rider-name" required placeholder="e.g. Rahat Khan">
                        </div>

                        <div class="form-group" style="margin-bottom: 2rem;">
                            <label class="form-label" for="rider-phone">Contact Phone</label>
                            <input class="form-input" type="tel" id="rider-phone" required placeholder="e.g. 01712345678">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Register Rider <i class="fa-solid fa-motorcycle" style="margin-left: 0.25rem;"></i>
                        </button>
                    </form>
                </section>

                <!-- Riders Directory Table -->
                <section class="glass-panel" style="padding: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--text-primary);">Rider Accounts</h3>
                        <span id="riders-count" style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Showing 0 Riders</span>
                    </div>

                    <div class="table-responsive">
                        <table class="premium-table stack-mobile">
                            <thead>
                                <tr>
                                    <th>Rider ID</th>
                                    <th>Rider Name</th>
                                    <th>Contact Phone</th>
                                    <th>Active Status</th>
                                    <th>Completed Orders</th>
                                    <th>Earnings (পাওনা)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="riders-table-tbody">
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                        <i class="fa-solid fa-spinner fa-spin" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                        <p>Loading rider accounts...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/animations.js"></script>

    <script>
        let ridersCache = [];

        async function fetchRiders() {
            try {
                const response = await fetch('../api/get-delivery-men.php');
                if (!response.ok) throw new Error('API Offline');
                const result = await response.json();
                
                if (result.success && result.data) {
                    ridersCache = result.data;
                    renderRidersTable(result.data);
                } else {
                    throw new Error(result.message || 'Failed to fetch riders');
                }
            } catch (error) {
                console.warn('[RidersDesk] Falling back to default mock team:', error);
                const mockTeam = [
                    { id: 1, name: 'Rahat Khan', phone: '01712345678', status: 'available', delivery_count: 5 },
                    { id: 2, name: 'Sumon Mia', phone: '01812345678', status: 'available', delivery_count: 8 },
                    { id: 3, name: 'Kamal Hossain', phone: '01912345678', status: 'available', delivery_count: 3 }
                ];
                ridersCache = mockTeam;
                renderRidersTable(mockTeam);
            }
        }

        function renderRidersTable(riders) {
            const tbody = document.getElementById('riders-table-tbody');
            const countSpan = document.getElementById('riders-count');
            if (!tbody) return;

            if (countSpan) {
                countSpan.textContent = `Showing ${riders.length} Riders`;
            }

            if (riders.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fa-solid fa-motorcycle" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                            <p style="font-weight: 500;">No delivery riders registered yet.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = riders.map(rider => {
                const count = parseInt(rider.delivery_count || 0);
                const earnings = count * 60; // Tk. 60 per delivery
                
                let statusBadge = '';
                if (rider.status === 'available') {
                    statusBadge = `<span class="status-badge status-success">AVAILABLE</span>`;
                } else if (rider.status === 'busy') {
                    statusBadge = `<span class="status-badge status-preparing">BUSY</span>`;
                } else {
                    statusBadge = `<span class="status-badge status-pending">OFFLINE</span>`;
                }

                return `
                    <tr id="rider-row-${rider.id}">
                        <td data-label="Rider ID" style="font-weight: 700; color: var(--primary);">#RD-${rider.id}</td>
                        <td data-label="Rider Name" style="font-weight: 600; color: var(--text-primary);">${rider.name}</td>
                        <td data-label="Contact Phone" style="color: var(--text-muted); font-family: monospace;">${rider.phone}</td>
                        <td data-label="Active Status">${statusBadge}</td>
                        <td data-label="Completed Orders" style="font-weight: 600; color: var(--text-primary);">${count} Orders</td>
                        <td data-label="Earnings (পাওনা)" style="font-weight: 700; color: #10b981;">Tk. ${earnings}</td>
                        <td data-label="Actions">
                            <button class="btn btn-glass btn-sm" onclick="deleteRider(${rider.id})" style="border-color: rgba(239, 68, 68, 0.3); color: var(--danger);" title="Delete Rider">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function handleNewRiderSubmit(e) {
            e.preventDefault();
            const nameInput = document.getElementById('rider-name');
            const phoneInput = document.getElementById('rider-phone');
            const submitBtn = e.target.querySelector('button[type="submit"]');

            const payload = {
                name: nameInput.value,
                phone: phoneInput.value
            };

            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Registering...';

                const response = await fetch('../api/add-delivery-man.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Rider Registered', `${payload.name} has been added as a delivery partner.`);
                    }
                    nameInput.value = '';
                    phoneInput.value = '';
                    fetchRiders();
                } else {
                    throw new Error(result.message || 'Failed to add rider');
                }
            } catch (error) {
                console.error(error);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Registration Failed', error.message || 'Error occurred while saving rider.');
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Register Rider <i class="fa-solid fa-motorcycle" style="margin-left: 0.25rem;"></i>';
            }
        }

        async function deleteRider(riderId) {
            if (!confirm('Are you sure you want to delete this delivery rider?')) {
                return;
            }

            try {
                const response = await fetch('../api/delete-delivery-man.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: riderId })
                });

                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Rider Deleted', 'The delivery rider profile has been removed.');
                    }
                    fetchRiders();
                } else {
                    throw new Error(result.message || 'Failed to delete rider');
                }
            } catch (error) {
                console.error(error);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Deletion Failed', error.message || 'Error occurred while deleting rider.');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchRiders();
        });
    </script>
</body>
</html>
