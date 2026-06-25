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
    <title>POS Billing Desk - Crispy Chicken</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>
    <style>
        .pos-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 1.5rem;
            align-items: start;
        }
        @media (max-width: 1200px) {
            .pos-layout {
                grid-template-columns: minmax(0, 1fr) 320px;
                gap: 1rem;
            }
        }
        @media (max-width: 992px) {
            .pos-layout {
                grid-template-columns: 1fr;
            }
            .pos-cart-section {
                position: relative;
                top: 0;
            }
        }
        .pos-menu-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .pos-cart-section {
            position: sticky;
            top: 2rem;
        }
        .pos-cat-tabs {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .pos-cat-tab {
            padding: 0.5rem 1.25rem;
            border-radius: var(--radius-md);
            background: var(--bg-dark-surface);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            cursor: pointer;
            font-weight: 600;
            white-space: nowrap;
            transition: var(--transition-fast);
        }
        .pos-cat-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .pos-menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.25rem;
        }
        .pos-item-card {
            background: var(--bg-dark-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            overflow: hidden;
            cursor: pointer;
            transition: var(--transition-fast);
        }
        .pos-item-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            box-shadow: var(--shadow-sm);
        }
        .pos-item-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        .pos-item-body {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .pos-item-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pos-item-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.05rem;
        }
        .pos-cart-list {
            max-height: 250px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
        }
        .pos-cart-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.02);
            padding: 0.6rem 0.75rem;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,0.03);
        }
        .pos-cart-info {
            flex: 1;
        }
        .pos-cart-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-primary);
        }
        .pos-cart-price {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .pos-cart-qty-ctrl {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0 1rem;
        }
        .pos-qty-btn {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            background: var(--bg-dark-surface);
            color: var(--text-primary);
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pos-qty-val {
            font-weight: 700;
            font-size: 0.9rem;
            width: 20px;
            text-align: center;
        }
        .pos-cart-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .pos-cart-subtotal {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text-primary);
            min-width: 60px;
            text-align: right;
        }
        .pos-remove-btn {
            color: var(--danger);
            cursor: pointer;
            font-size: 0.95rem;
            background: none;
            border: none;
        }
        .pos-bill-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .pos-bill-total {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary);
            border-top: 1px dashed var(--border-color);
            padding-top: 0.75rem;
            margin-top: 0.75rem;
        }
        .pos-customer-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.25rem;
        }
        .pos-mfs-details {
            border: 1px solid rgba(234, 103, 33, 0.2);
            background: rgba(234, 103, 33, 0.03);
            border-radius: var(--radius-sm);
            padding: 0.85rem;
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        /* Custom modal for print preview */
        .print-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .print-modal.active {
            display: flex;
        }
        .print-modal-content {
            background: white;
            width: 90%;
            max-width: 450px;
            height: 85vh;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .print-modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-iframe {
            flex: 1;
            width: 100%;
            border: none;
        }
    </style>
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
                <a href="pos.php" class="sidebar-link active">
                    <i class="fa-solid fa-cash-register"></i> POS Counter
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
            
            <div class="admin-topbar">
                <div class="page-title">
                    <h1 style="color: var(--text-primary);">POS Billing Register</h1>
                    <p style="color: var(--text-secondary);">Direct counter order taking, automated repeat customer discounts, and receipt invoice generations.</p>
                </div>

                <div class="realtime-indicator realtime-active">
                    <span class="indicator-dot"></span>
                    <span class="indicator-label">Register Online</span>
                </div>
            </div>

            <!-- POS Register Workspace Layout -->
            <div class="pos-layout">
                
                <!-- Left: Catalog & Menu Search -->
                <div class="pos-menu-section">
                    
                    <!-- Search and filters -->
                    <div class="glass-panel" style="padding: 1.25rem; display: flex; gap: 1rem; align-items: center; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                        <div style="flex: 1; position: relative;">
                            <input class="form-input" type="text" id="pos-search" placeholder="Search culinary dishes..." style="padding-left: 2.5rem;" onkeyup="filterMenuItems()">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; top: 1.05rem; color: var(--text-muted);"></i>
                        </div>
                    </div>

                    <!-- Category Tabs — Dynamically populated from DB via JS -->
                    <div class="pos-cat-tabs" id="pos-category-tabs">
                        <div class="pos-cat-tab active" data-slug="all" onclick="selectCategory('all')">All Dishes</div>
                        <!-- Additional tabs loaded from api/get-categories.php -->
                    </div>

                    <!-- Menu Grid -->
                    <div class="pos-menu-grid" id="pos-grid">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Right: Billing Cart & Customer Details -->
                <div class="pos-cart-section">
                    <div class="glass-panel" style="padding: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; color: var(--text-primary);">
                            <span><i class="fa-solid fa-shopping-basket" style="color: var(--primary);"></i> Checkout Cart</span>
                            <button class="btn btn-glass btn-sm" onclick="clearCart()" style="padding: 0.35rem 0.6rem; font-size: 0.75rem; border-color: rgba(239, 68, 68, 0.2); color: var(--danger); width: auto;">Clear All</button>
                        </h3>

                        <!-- Cart Items List -->
                        <div class="pos-cart-list" id="pos-cart-container">
                            <!-- Populated dynamically -->
                            <div style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
                                <i class="fa-solid fa-cart-shopping" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 0.75rem;"></i>
                                <p style="font-size: 0.9rem; font-weight: 500;">Cart is empty. Select menu items.</p>
                            </div>
                        </div>

                        <!-- Customer & Loyalty Info -->
                        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
                            <h4 style="font-weight: 700; font-size: 0.95rem; margin-bottom: 1rem; color: var(--text-primary);"><i class="fa-solid fa-user-tag" style="color: var(--primary);"></i> Customer Registry</h4>
                            
                            <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 0.75rem;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="cust-phone">Phone Number</label>
                                    <input class="form-input" type="text" id="cust-phone" placeholder="e.g. 01712345678" onkeyup="checkCustomerLoyalty(this.value)">
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="cust-name">Customer Name</label>
                                    <input class="form-input" type="text" id="cust-name" placeholder="Guest Customer">
                                </div>
                            </div>
                            <div id="loyalty-indicator-container"></div>
                        </div>

                        <!-- Payment & Discounts -->
                        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
                            <h4 style="font-weight: 700; font-size: 0.95rem; margin-bottom: 1rem; color: var(--text-primary);"><i class="fa-solid fa-credit-card" style="color: var(--primary);"></i> Payment & Billing</h4>
                            
                            <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 0.5rem;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-input form-select" id="pos-payment-method" onchange="toggleMfsInputs(this.value)">
                                        <option value="cod" selected>Cash Payment</option>
                                        <option value="bkash">bKash (MFS)</option>
                                        <option value="nagad">Nagad (MFS)</option>
                                        <option value="rocket">Rocket (MFS)</option>
                                        <option value="card">Card Terminal</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="pos-discount">Discount (%)</label>
                                    <input class="form-input" type="number" id="pos-discount" min="0" max="100" value="0" oninput="calculateBillingTotals()">
                                </div>
                            </div>

                            <!-- Dynamic MFS Fields -->
                            <div id="mfs-fields" class="pos-mfs-details" style="display: none;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="mfs-phone">Sender Mobile No.</label>
                                    <input class="form-input" type="text" id="mfs-phone" placeholder="Sender Account Number">
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="mfs-txn">Transaction ID (TxnID)</label>
                                    <input class="form-input" type="text" id="mfs-txn" placeholder="e.g. TRN8X92K1">
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Calculation Board -->
                        <div style="margin-bottom: 2rem;">
                            <div class="pos-bill-row">
                                <span style="color: var(--text-muted);">Gross Total:</span>
                                <span style="font-weight: 600;" id="bill-gross">Tk. 0</span>
                            </div>
                            <div class="pos-bill-row">
                                <span style="color: var(--text-muted);">Discount Amount:</span>
                                <span style="font-weight: 600; color: var(--danger);" id="bill-discount">Tk. 0</span>
                            </div>
                            <div class="pos-bill-row pos-bill-total">
                                <span>Net Pay:</span>
                                <span id="bill-net">Tk. 0</span>
                            </div>
                        </div>

                        <button class="btn btn-primary" style="width: 100%; padding: 1.1rem; font-size: 1.1rem; font-weight: 700;" onclick="submitPosOrder()">
                            <i class="fa-solid fa-receipt"></i> Place POS Order & Print Receipt
                        </button>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Printable Invoice Iframe Modal popup -->
    <div class="print-modal" id="print-modal-popup">
        <div class="print-modal-content">
            <div class="print-modal-header">
                <h4 style="font-weight:700; color:#1e293b; margin:0;"><i class="fa-solid fa-print"></i> Thermal Receipt Preview</h4>
                <button type="button" onclick="closePrintModal()" style="border:none; background:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
            </div>
            <iframe id="print-preview-frame" class="print-iframe"></iframe>
            <div style="padding:1rem; border-top:1px solid #e2e8f0; display:flex; gap:0.5rem; background:#f8fafc;">
                <button class="btn btn-primary" onclick="printReceiptIframe()" style="flex:1; width:auto; border-radius:6px; padding:0.75rem;">Print Receipt</button>
                <button class="btn btn-glass" onclick="closePrintModal()" style="width:auto; border-radius:6px; padding:0.75rem; border-color:#cbd5e1; color:#475569;">Close</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/animations.js"></script>

    <script>
        let menuItems = [];
        let posCart = [];
        let selectedCategoryFilter = 'all';
        let debounceTimer;

        // Decode HTML entities stored in DB
        function decodeHtmlEntities(str) {
            if (!str) return '';
            const txt = document.createElement('textarea');
            txt.innerHTML = str;
            let decoded = txt.value;
            for (let i = 0; i < 4; i++) {
                txt.innerHTML = decoded;
                if (txt.value === decoded) break;
                decoded = txt.value;
            }
            return decoded;
        }

        async function loadPOSCategories() {
            try {
                const res = await fetch('../api/get-categories.php');
                const result = await res.json();
                if (result.success && result.data) {
                    const tabsContainer = document.getElementById('pos-category-tabs');
                    if (!tabsContainer) return;
                    // Keep the 'All Dishes' tab, append the rest
                    let html = `<div class="pos-cat-tab active" data-slug="all" onclick="selectCategory('all')">All Dishes</div>`;
                    result.data.forEach(cat => {
                        html += `<div class="pos-cat-tab" data-slug="${cat.slug}" onclick="selectCategory('${cat.slug}')">${cat.name}</div>`;
                    });
                    tabsContainer.innerHTML = html;
                }
            } catch (e) {
                console.warn('POS categories fallback:', e);
            }
        }

        async function fetchPOSCatalog() {
            try {
                const response = await fetch('../api/get-all-menu-items.php');
                const result = await response.json();
                if (result.success && result.data) {
                    menuItems = result.data.filter(item => item.is_available === 1);
                    renderPOSGrid(menuItems);
                }
            } catch (error) {
                console.error("Error loading POS items:", error);
            }
        }

        function renderPOSGrid(items) {
            const grid = document.getElementById('pos-grid');
            if (!grid) return;

            let filtered = items;
            if (selectedCategoryFilter !== 'all') {
                filtered = items.filter(item => item.category === selectedCategoryFilter);
            }

            if (filtered.length === 0) {
                grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-muted);">No available items in this category.</div>`;
                return;
            }

            // Map image names
            const premiumImageMap = {
                'truffle_garlic_bread.jpg': 'https://images.unsplash.com/photo-1619535860434-ba1d8fa12536?auto=format&fit=crop&q=80&w=600',
                'crispy_calamari.jpg': 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&q=80&w=600',
                'stuffed_mushrooms.jpg': 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=600',
                'tomato_bruschetta.jpg': 'https://images.unsplash.com/photo-1572656631137-7935297eff55?auto=format&fit=crop&q=80&w=600',
                'filet_mignon.jpg': 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=600',
                'seared_salmon.jpg': 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&q=80&w=600',
                'mushroom_risotto.jpg': 'https://images.unsplash.com/photo-1476124369491-e77d540d907f?auto=format&fit=crop&q=80&w=600',
                'butter_chicken.jpg': 'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?auto=format&fit=crop&q=80&w=600',
                'wagyu_burger.jpg': 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&q=80&w=600',
                'cheesecake.jpg': 'https://images.unsplash.com/photo-1524351199679-46cddf530c04?auto=format&fit=crop&q=80&w=600',
                'mango_smoothie.jpg': 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&q=80&w=600',
                'iced_macchiato.jpg': 'https://images.unsplash.com/photo-1557925923-cd4648e21187?auto=format&fit=crop&q=80&w=600',
                'virgin_mojito.jpg': 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&q=80&w=600'
            };

            grid.innerHTML = filtered.map(item => {
                // Resolve image path
                let img = item.image_url || '';
                if (img.startsWith('../images/')) {
                    // Correct as-is from admin/ context
                } else if (img.startsWith('images/')) {
                    img = '../' + img;
                } else if (img && !img.startsWith('http') && !img.startsWith('/')) {
                    img = '../images/' + img;
                }
                if (!img) img = '../assets/img/placeholder.jpg';

                const safeName = decodeHtmlEntities(item.name);

                const priceHtml = item.discount_price !== null && item.discount_price > 0 ?
                    `Tk. ${item.discount_price.toFixed(0)} <del style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.35rem;">Tk. ${item.price.toFixed(0)}</del>` :
                    `Tk. ${item.price.toFixed(0)}`;

                let variationsDropdown = '';
                if (item.variations && item.variations.length > 0) {
                    variationsDropdown = `
                        <select class="form-input form-select pos-var-select" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; margin-top: 0.5rem; height: auto; background: var(--bg-dark); color: white; border-color: rgba(255,255,255,0.08);" onclick="event.stopPropagation()">
                            ${item.variations.map(v => `<option value="${v.id}">${decodeHtmlEntities(v.name)} (Tk. ${parseFloat(v.price).toFixed(0)})</option>`).join('')}
                        </select>
                    `;
                }

                return `
                    <div class="pos-item-card" onclick="handlePOSCardClick(event, ${item.id})">
                        <img src="${img}" alt="${safeName}" class="pos-item-img" onerror="this.onerror=null; this.src='../assets/img/placeholder.jpg';">
                        <div class="pos-item-body">
                            <h4 class="pos-item-title">${safeName}</h4>
                            <span class="pos-item-price">${priceHtml}</span>
                            ${variationsDropdown}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function selectCategory(category) {
            selectedCategoryFilter = category;
            const tabs = document.querySelectorAll('.pos-cat-tab');
            tabs.forEach(tab => {
                tab.classList.toggle('active', tab.dataset.slug === category);
            });
            renderPOSGrid(menuItems);
        }

        function filterMenuItems() {
            const query = document.getElementById('pos-search').value.toLowerCase();
            const filtered = menuItems.filter(item => item.name.toLowerCase().includes(query));
            renderPOSGrid(filtered);
        }

        function handlePOSCardClick(event, id) {
            const select = event.currentTarget.querySelector('.pos-var-select');
            let variationId = null;
            if (select) {
                variationId = parseInt(select.value) || null;
            }
            addToPOSCart(id, variationId);
        }

        function addToPOSCart(id, variationId = null) {
            const item = menuItems.find(item => item.id === id);
            if (!item) return;

            let price = item.discount_price !== null && item.discount_price > 0 ? item.discount_price : item.price;
            let variationName = null;

            if (variationId !== null && item.variations) {
                const variation = item.variations.find(v => parseInt(v.id) === variationId);
                if (variation) {
                    price = parseFloat(variation.price);
                    variationName = variation.name;
                }
            }

            const cartKey = variationId ? `${id}-${variationId}` : `${id}`;

            const existing = posCart.find(cartItem => cartItem.cartKey === cartKey);
            if (existing) {
                existing.quantity++;
            } else {
                posCart.push({
                    cartKey: cartKey,
                    id: item.id,
                    name: item.name,
                    price: price,
                    quantity: 1,
                    variation_id: variationId,
                    variation_name: variationName
                });
            }
            renderPOSCart();
        }

        function changeQuantity(cartKey, delta) {
            const item = posCart.find(cartItem => cartItem.cartKey === cartKey);
            if (!item) return;

            item.quantity += delta;
            if (item.quantity <= 0) {
                posCart = posCart.filter(cartItem => cartItem.cartKey !== cartKey);
            }
            renderPOSCart();
        }

        function removeCartItem(cartKey) {
            posCart = posCart.filter(cartItem => cartItem.cartKey !== cartKey);
            renderPOSCart();
        }

        function clearCart() {
            posCart = [];
            renderPOSCart();
        }

        function renderPOSCart() {
            const container = document.getElementById('pos-cart-container');
            if (!container) return;

            if (posCart.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
                        <i class="fa-solid fa-cart-shopping" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 0.75rem;"></i>
                        <p style="font-size: 0.9rem; font-weight: 500;">Cart is empty. Select menu items.</p>
                    </div>
                `;
                calculateBillingTotals();
                return;
            }

            container.innerHTML = posCart.map(item => {
                const subtotal = item.price * item.quantity;
                const displayName = item.variation_name ? `${item.name} (${item.variation_name})` : item.name;
                return `
                    <div class="pos-cart-row">
                        <div class="pos-cart-info">
                            <div class="pos-cart-title">${displayName}</div>
                            <div class="pos-cart-price">Tk. ${item.price.toFixed(0)}</div>
                        </div>
                        <div class="pos-cart-qty-ctrl">
                            <button type="button" class="pos-qty-btn" onclick="changeQuantity('${item.cartKey}', -1)">-</button>
                            <span class="pos-qty-val">${item.quantity}</span>
                            <button type="button" class="pos-qty-btn" onclick="changeQuantity('${item.cartKey}', 1)">+</button>
                        </div>
                        <div class="pos-cart-subtotal">Tk. ${subtotal.toFixed(0)}</div>
                        <button type="button" class="pos-remove-btn" onclick="removeCartItem('${item.cartKey}')">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                `;
            }).join('');

            calculateBillingTotals();
        }

        function calculateBillingTotals() {
            let gross = 0;
            posCart.forEach(item => {
                gross += (item.price * item.quantity);
            });

            const discountPercent = parseFloat(document.getElementById('pos-discount').value) || 0;
            const discountAmt = gross * (discountPercent / 100);
            const net = gross - discountAmt;

            document.getElementById('bill-gross').textContent = `Tk. ${gross.toLocaleString()}`;
            document.getElementById('bill-discount').textContent = `Tk. ${discountAmt.toLocaleString()}`;
            document.getElementById('bill-net').textContent = `Tk. ${net.toLocaleString()}`;
        }

        function toggleMfsInputs(paymentVal) {
            const fields = document.getElementById('mfs-fields');
            if (!fields) return;

            if (['bkash', 'nagad', 'rocket'].includes(paymentVal)) {
                fields.style.display = 'flex';
            } else {
                fields.style.display = 'none';
            }
        }

        function checkCustomerLoyalty(phoneVal) {
            clearTimeout(debounceTimer);
            const indicator = document.getElementById('loyalty-indicator-container');
            if (!indicator) return;

            if (phoneVal.length < 5) {
                indicator.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`../api/check-customer.php?phone=${encodeURIComponent(phoneVal)}`);
                    const result = await response.json();
                    
                    if (result.success && result.data && result.data.exists) {
                        const cust = result.data;
                        document.getElementById('cust-name').value = cust.customer_name || '';
                        
                        let badgeHtml = '';
                        if (cust.order_count > 0) {
                            badgeHtml = `
                                <div class="pos-customer-badge">
                                    <i class="fa-solid fa-crown"></i> Loyal Customer (${cust.order_count} Past Orders)
                                </div>
                            `;
                            // Auto-apply a 5% loyalty discount suggestion as reference
                            const discountInput = document.getElementById('pos-discount');
                            if (discountInput && parseInt(discountInput.value) === 0) {
                                discountInput.value = 5;
                                calculateBillingTotals();
                                if (window.NotificationSystem) {
                                    window.NotificationSystem.toast('info', 'Discount Suggestion', 'Applied 5% loyal customer discount recommendation.');
                                }
                            }
                        }
                        
                        indicator.innerHTML = badgeHtml;
                    } else {
                        indicator.innerHTML = '';
                    }
                } catch (err) {
                    console.error(err);
                }
            }, 500);
        }

        async function submitPosOrder() {
            if (posCart.length === 0) {
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('warning', 'Billing Error', 'Cart is empty. Please select menu items.');
                }
                return;
            }

            const phone = document.getElementById('cust-phone').value.trim() || 'POS-Counter';
            const name = document.getElementById('cust-name').value.trim() || 'POS Customer';
            const paymentMethod = document.getElementById('pos-payment-method').value;
            const discountPercent = parseFloat(document.getElementById('pos-discount').value) || 0;
            
            const mfsPhone = document.getElementById('mfs-phone').value.trim();
            const mfsTxn = document.getElementById('mfs-txn').value.trim();

            if (['bkash', 'nagad', 'rocket'].includes(paymentMethod) && (!mfsPhone || !mfsTxn)) {
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('warning', 'Billing Error', 'Please enter MFS Sender Mobile No. and Transaction ID.');
                }
                return;
            }

            const payload = {
                customer_phone: phone,
                customer_name: name,
                items: posCart,
                discount_percent: discountPercent,
                payment_method: paymentMethod,
                mfs_sender_number: mfsPhone || null,
                mfs_transaction_id: mfsTxn || null,
                status: 'delivered' // Auto-deliver POS orders
            };

            try {
                const response = await fetch('../api/place-pos-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                
                if (result.success && result.data) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'POS Order Saved', `Order #${result.data.order_number} recorded successfully.`);
                    }
                    
                    // Trigger receipt print preview
                    openPrintModal(result.data.order_number);
                    
                    // Reset POS Counter state
                    clearCart();
                    document.getElementById('cust-phone').value = '';
                    document.getElementById('cust-name').value = '';
                    document.getElementById('pos-discount').value = 0;
                    document.getElementById('pos-payment-method').value = 'cod';
                    document.getElementById('mfs-phone').value = '';
                    document.getElementById('mfs-txn').value = '';
                    toggleMfsInputs('cod');
                    document.getElementById('loyalty-indicator-container').innerHTML = '';
                    
                } else {
                    throw new Error(result.message || 'API error');
                }
            } catch (err) {
                console.error(err);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'POS Placement Failed', err.message || 'Error occurred while saving transaction.');
                }
            }
        }

        function openPrintModal(orderNumber) {
            const modal = document.getElementById('print-modal-popup');
            const iframe = document.getElementById('print-preview-frame');
            if (modal && iframe) {
                iframe.src = `print-invoice.php?order_number=${encodeURIComponent(orderNumber)}`;
                modal.classList.add('active');
            }
        }

        function closePrintModal() {
            const modal = document.getElementById('print-modal-popup');
            if (modal) modal.classList.remove('active');
        }

        function printReceiptIframe() {
            const iframe = document.getElementById('print-preview-frame');
            if (iframe) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        // Initialize Catalog on load
        window.addEventListener('load', async () => {
            await loadPOSCategories();
            fetchPOSCatalog();
        });
    </script>
</body>
</html>
