<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking - Food ColourQ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>
    <style>
        .grid-mobile-stack {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
        }
        .star-btn {
            font-size: 1.85rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .star-btn:hover {
            transform: scale(1.2);
            color: #ffc107;
        }
        .star-btn.active {
            color: #ffc107;
        }
        @media (max-width: 768px) {
            .grid-mobile-stack {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .no-border-mobile {
                border-right: none !important;
            }
            .no-padding-mobile {
                padding-right: 0 !important;
                padding-bottom: 2rem;
                border-bottom: 1px solid var(--border-color);
            }
        }
    </style>
</head>
<body>

    <header class="client-header">
        <div class="container header-container">
            <a href="index.php" class="brand-logo">
                Crispy Chicken<span class="brand-dot"></span>
            </a>

            <nav class="client-nav">
                <a href="index.php" class="nav-link">Home</a>
                <a href="index.php?checkout=1" class="nav-link">Checkout</a>
                <a href="order-tracking.php" class="nav-link active">Order Tracking</a>
            </nav>

            <div class="header-actions">
                <button class="cart-icon-btn">
                    <i class="fa-solid fa-shopping-bag" style="font-size: 1.15rem; color: var(--text-primary);"></i>
                    <span class="cart-badge" style="display: none;">0</span>
                </button>
                <div class="menu-toggle hide-desktop">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <main class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
        <div class="page-title" style="margin-bottom: 2.5rem; text-align: center;">
            <h1 style="color: var(--text-primary);">Real-Time Order Tracking</h1>
            <p style="color: var(--text-secondary);">Your delicious gourmet meal is being prepared. Watch the live progress updates below.</p>
        </div>

        <!-- Empty State Search Panel View (Visible when no order is specified) -->
        <div id="order-search-view" style="display: none;">
            <section class="glass-panel" id="order-search-card" style="max-width: 600px; margin: 0 auto 3rem; padding: 3rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color); text-align: center; border-radius: var(--radius-lg); opacity: 0;">
                <div style="width: 80px; height: 80px; background: rgba(234, 103, 33, 0.08); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.25rem; margin: 0 auto 1.5rem;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--text-primary); margin-bottom: 0.5rem;">Track Your Order</h2>
                <p style="color: var(--text-secondary); font-size: 1rem; margin-bottom: 2rem;">Enter your order number below to check the real-time progress of your food.</p>
 
                <form id="order-tracking-search-form" onsubmit="handleOrderSearchSubmit(event)" style="display: flex; gap: 0.75rem; margin-bottom: 2.5rem; justify-content: center; align-items: center; max-width: 480px; margin-left: auto; margin-right: auto;">
                    <input class="form-input" type="text" id="search-order-number" required placeholder="e.g. FC-872361-A" style="flex: 1; padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: rgba(255,255,255,0.02); color: var(--text-primary); font-weight: 600; text-transform: uppercase;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 1.75rem; font-weight: 600; border-radius: var(--radius-sm); white-space: nowrap;">
                        Track Order <i class="fa-solid fa-magnifying-glass" style="margin-left: 0.5rem;"></i>
                    </button>
                </form>

                <div id="recent-orders-container" style="display: none; border-top: 1px solid var(--border-color); padding-top: 2rem; text-align: left;">
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-history" style="color: var(--primary);"></i> Your Recent Orders
                    </h4>
                    <ul id="recent-orders-list" style="list-style: none; display: flex; flex-direction: column; gap: 0.85rem; padding: 0;">
                        <!-- Dynamically filled from localStorage -->
                    </ul>
                </div>
            </section>
        </div>

        <!-- Active Tracking Card View (Visible only when tracking active order) -->
        <div id="active-tracking-view" style="display: none;">
            <!-- Tracking Visual Card -->
            <section class="glass-panel order-tracker" style="margin-bottom: 3rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500;">Order ID:</span>
                        <h3 style="font-family: var(--font-heading); color: var(--primary); font-size: 1.35rem; margin-top: 0.15rem;">#FC-872361-A</h3>
                    </div>
                    <div style="text-align: right;" class="hide-mobile">
                        <span style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500;">Estimated Delivery Time:</span>
                        <h3 style="font-size: 1.25rem; color: var(--text-primary); margin-top: 0.15rem;">12:45 PM (within 25 mins)</h3>
                    </div>
                </div>

                <!-- Horizontal / Vertical Progress Steps Bar -->
                <div class="tracker-progress-wrapper">
                    <div class="tracker-progress-bar">
                        <div class="tracker-progress-bar-fill" id="tracking-bar-fill"></div>
                    </div>

                    <!-- Step 1 -->
                    <div class="tracker-step active" id="step-1">
                        <div class="tracker-step-icon">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <span class="tracker-step-label">Awaiting Approval</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="tracker-step" id="step-2">
                        <div class="tracker-step-icon">
                            <i class="fa-solid fa-cookie-bite"></i>
                        </div>
                        <span class="tracker-step-label">Preparing Food</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="tracker-step" id="step-3">
                        <div class="tracker-step-icon">
                            <i class="fa-solid fa-motorcycle"></i>
                        </div>
                        <span class="tracker-step-label">Out for Delivery</span>
                    </div>

                    <!-- Step 4 -->
                    <div class="tracker-step" id="step-4">
                        <div class="tracker-step-icon">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <span class="tracker-step-label">Order Completed</span>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 2.5rem; color: var(--text-secondary);" id="status-detailed-description">
                    Your order has been received in our kitchen and is currently awaiting approval.
                </div>
            </section>

            <!-- Quality Dispute & Feedback Section -->
            <section class="glass-panel" id="completed-order-action-card" style="display: none; padding: 2.5rem; margin-top: 3rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); opacity: 0; transform: translateY(20px); transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); margin-bottom: 3rem;">
                <div class="grid-mobile-stack">
                    
                    <!-- 1. Star Rating & Feedback Form -->
                    <div style="border-right: 1px solid var(--border-color); padding-right: 2.5rem;" class="no-border-mobile no-padding-mobile" id="feedback-form-column">
                        <h3 style="font-family: var(--font-heading); color: var(--text-primary); font-size: 1.35rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-heart-circle-check" style="color: var(--primary);"></i> How was your food?
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem;">Your feedback helps us improve our service and food quality.</p>
                        
                        <form id="feedback-form" onsubmit="submitOrderFeedback(event)">
                            <div class="form-group" style="margin-bottom: 1.5rem;">
                                <label class="form-label" style="margin-bottom: 0.5rem; display: block;">Give Rating</label>
                                <div style="display: flex; gap: 0.75rem;" class="star-rating-container">
                                    <input type="hidden" id="feedback-rating-value" value="5">
                                    <i class="fa-solid fa-star star-btn active" data-val="1" onclick="setRating(1)"></i>
                                    <i class="fa-solid fa-star star-btn active" data-val="2" onclick="setRating(2)"></i>
                                    <i class="fa-solid fa-star star-btn active" data-val="3" onclick="setRating(3)"></i>
                                    <i class="fa-solid fa-star star-btn active" data-val="4" onclick="setRating(4)"></i>
                                    <i class="fa-solid fa-star star-btn active" data-val="5" onclick="setRating(5)"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="feedback-comment">Feedback or Comments (Optional)</label>
                                <textarea class="form-input" id="feedback-comment" rows="3" placeholder="Share your thoughts on food taste, packaging, and rider behaviour..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 1rem; width: 100%; font-weight: 600;">
                                Submit Feedback <i class="fa-solid fa-paper-plane" style="margin-left: 0.25rem;"></i>
                            </button>
                        </form>
                    </div>

                    <!-- 2. Bad/Spoiled Order Dispute Claim Form -->
                    <div id="dispute-form-column">
                        <h3 style="font-family: var(--font-heading); color: var(--text-primary); font-size: 1.35rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-circle-exclamation" style="color: var(--danger);"></i> Any complaints about food?
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem;">If the food was cold, spoiled, or incorrect, you can request a refund or replacement instantly.</p>
                        
                        <form id="dispute-form" onsubmit="submitQualityDispute(event)">
                            <div class="form-group">
                                <label class="form-label" for="dispute-category">Issue Category</label>
                                <select class="form-input form-select" id="dispute-category" required>
                                    <option value="" disabled selected>Select Category...</option>
                                    <option value="Spoiled Food">Spoiled Food</option>
                                    <option value="Cold Food">Cold Food</option>
                                    <option value="Wrong Items">Wrong / Missing Items</option>
                                    <option value="Damaged Packaging">Damaged Packaging</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="dispute-desc">Detailed Description</label>
                                <textarea class="form-input" id="dispute-desc" rows="3" required placeholder="Please detail the issue. Our support team will review and resolve it immediately..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-secondary btn-sm" style="margin-top: 1rem; width: 100%; border-color: rgba(239, 68, 68, 0.4); color: var(--danger); font-weight: 600;">
                                Submit Claim / File Dispute <i class="fa-solid fa-shield-halved" style="margin-left: 0.25rem;"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </section>

            <!-- Simulated Controls Panel -->
            <section class="glass-panel" style="padding: 2.5rem; background-image: linear-gradient(rgba(234, 103, 33, 0.02), transparent); background-color: var(--bg-dark-surface); border: 1px solid var(--border-color); margin-bottom: 3rem;">
                <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-sliders" style="color: var(--primary);"></i> Simulation Control Dashboard (Testing Panel)
                </h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 2rem;">Use this simulator to test live updates of order status and progress bar fills.</p>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button class="btn btn-secondary btn-sm" onclick="setTrackingState(1)">
                    Step 1: Pending
                </button>
                <button class="btn btn-secondary btn-sm" onclick="setTrackingState(2)">
                    Step 2: Preparing
                </button>
                <button class="btn btn-secondary btn-sm" onclick="setTrackingState(3)">
                    Step 3: Delivering
                </button>
                <button class="btn btn-secondary btn-sm" onclick="setTrackingState(4)">
                    Step 4: Completed
                </button>
                <button class="btn btn-glass btn-sm" onclick="simulateCancellation()" style="border-color: rgba(234, 103, 33, 0.3); color: var(--primary);">
                    <i class="fa-solid fa-ban"></i> Cancel Simulation
                </button>
            </div>
        </section>
    </main>

    <!-- Drawer fallback -->
    <aside class="cart-drawer">
        <div class="cart-drawer-header">
            <h3 style="color: var(--text-primary);">Your Shopping Cart</h3>
            <span class="cart-drawer-close">&times;</span>
        </div>
        <div class="cart-items-list"></div>
        <div class="cart-drawer-footer">
            <button class="btn btn-primary" style="width: 100%;" onclick="window.FoodApp.closeCart()">Close Cart</button>
        </div>
    </aside>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/animations.js"></script>
    <script src="../assets/js/checkout.js"></script>

    <script>
        // Track current state
        let currentStep = 1;
        let activeOrderNumber = "FC-872361-A";
        let lastKnownStatus = null;
        let pollTimer = null;

        const descriptions = {
            1: "Your order has been received in our kitchen and is currently awaiting approval.",
            2: "Our master chefs are now preparing your delicious meal using fresh ingredients.",
            3: "Our premium delivery partner is on the way to your destination with your hot food.",
            4: "Order successfully delivered! Bon appétit from Food ColourQ!"
        };

        const notificationTitles = {
            1: "Order Placed Successfully",
            2: "Order Approved in Kitchen",
            3: "Delivery Dispatched",
            4: "Food Delivered Successfully"
        };

        const notificationMessages = {
            1: "Your order is awaiting chef approval.",
            2: "Chefs are actively preparing your meal.",
            3: "Rider is coming your way with hot food.",
            4: "Enjoy your premium delicious meal!"
        };

        function setTrackingState(step, orderNum = activeOrderNumber, triggerAlert = true) {
            currentStep = step;
            activeOrderNumber = orderNum;

            // Update DOM Order ID display
            const orderIdTitle = document.querySelector('h3[style*="FC-872361-A"]') || document.querySelector('.order-tracker h3');
            if (orderIdTitle) {
                orderIdTitle.textContent = `#${activeOrderNumber}`;
            }

            // Update Progress Bar Fills
            const totalSteps = 4;
            const percentage = ((step - 1) / (totalSteps - 1)) * 100;
            const fillBar = document.getElementById('tracking-bar-fill');
            
            if (fillBar) {
                // Reset custom error styles if any
                fillBar.style.background = 'var(--gradient-primary)';
                fillBar.style.boxShadow = '0 0 10px rgba(234, 103, 33, 0.2)';
                
                fillBar.style.width = `${percentage}%`;
                if (window.innerWidth <= 768) {
                    fillBar.style.width = '100%';
                    fillBar.style.height = `${percentage}%`;
                }
            }

            // Highlight Step circles
            for (let i = 1; i <= 4; i++) {
                const el = document.getElementById(`step-${i}`);
                if (!el) continue;

                el.classList.remove('active', 'completed');
                
                // Reset step circle style if previously cancelled
                const icon = el.querySelector('.tracker-step-icon');
                if (icon) {
                    icon.style.borderColor = '';
                    icon.style.color = '';
                    icon.style.boxShadow = '';
                }

                if (i < step) {
                    el.classList.add('completed');
                } else if (i === step) {
                    el.classList.add('active');
                }
            }

            // Update description box
            const descEl = document.getElementById('status-detailed-description');
            if (descEl) {
                descEl.textContent = descriptions[step];
            }

            // Show/Hide Completed actions (Feedback / Dispute panel)
            const completedCard = document.getElementById('completed-order-action-card');
            if (completedCard) {
                if (step === 4) {
                    completedCard.style.display = 'block';
                    setTimeout(() => {
                        completedCard.style.opacity = '1';
                        completedCard.style.transform = 'translateY(0)';
                    }, 50);
                } else {
                    completedCard.style.display = 'none';
                    completedCard.style.opacity = '0';
                    completedCard.style.transform = 'translateY(20px)';
                }
            }

            // Dispatch visual toast notice + play audio synthesised alerts sweep!
            if (triggerAlert && window.NotificationSystem) {
                const toastType = step === 4 ? 'success' : 'info';
                window.NotificationSystem.toast(toastType, notificationTitles[step], notificationMessages[step]);
            }
        }

        function simulateCancellation(orderNum = activeOrderNumber) {
            // Set error states visually
            const fillBar = document.getElementById('tracking-bar-fill');
            if (fillBar) {
                fillBar.style.background = 'var(--danger)';
                fillBar.style.boxShadow = '0 0 15px var(--danger)';
            }

            const activeStep = document.querySelector('.tracker-step.active') || document.getElementById('step-1');
            if (activeStep) {
                const icon = activeStep.querySelector('.tracker-step-icon');
                if (icon) {
                    icon.style.borderColor = 'var(--danger)';
                    icon.style.color = 'var(--danger)';
                    icon.style.boxShadow = '0 0 20px rgba(239, 68, 68, 0.4)';
                }
            }

            const descEl = document.getElementById('status-detailed-description');
            if (descEl) {
                descEl.innerHTML = `<span style="color: var(--danger); font-weight: 600;"><i class="fa-solid fa-circle-xmark"></i> Order Cancelled: The order has been cancelled by the kitchen. The refund process has been initiated.</span>`;
            }

            // Trigger harsh error synth alerts
            if (window.NotificationSystem) {
                window.NotificationSystem.toast('error', 'Order Cancelled', `Order #${orderNum} has been cancelled.`, 5000);
            }
        }

        // Live status poller function
        async function fetchLiveOrderStatus() {
            try {
                const response = await fetch(`../api/get-order-status.php?order_number=${encodeURIComponent(activeOrderNumber)}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                
                if (result.success && result.data) {
                    const order = result.data;
                    const status = order.status.toLowerCase();
                    
                    let step = 1;
                    if (status === 'preparing') step = 2;
                    else if (status === 'ready' || status === 'delivering') step = 3;
                    else if (status === 'delivered') step = 4;
                    
                    // Update connection indicator if visible
                    const indicator = document.querySelector('.realtime-indicator');
                    if (indicator) {
                        indicator.className = 'realtime-indicator realtime-active';
                        const label = indicator.querySelector('.indicator-label');
                        if (label) label.textContent = 'Live Tracking Active';
                    }

                    // Update feedback / dispute forms state if already submitted
                    if (status === 'delivered') {
                        const feedbackForm = document.getElementById('feedback-form');
                        if (feedbackForm && order.feedback_rating !== null) {
                            feedbackForm.innerHTML = `<div style="text-align: center; color: var(--success); padding: 1.5rem; font-weight: 600;"><i class="fa-solid fa-circle-check" style="font-size: 2.25rem; margin-bottom: 0.5rem; display: block; color: var(--success);"></i> Thank you for your feedback!<br><span style="font-size:0.85rem; color:var(--text-muted); font-weight: 500;">You gave a ${order.feedback_rating} star rating.</span></div>`;
                        }
                        const disputeForm = document.getElementById('dispute-form');
                        if (disputeForm && order.dispute_status) {
                            let disputeMsg = "";
                            if (order.dispute_status === 'pending') {
                                disputeMsg = `<i class="fa-solid fa-clock-rotate-left" style="font-size: 2.25rem; margin-bottom: 0.5rem; display: block; color: var(--primary);"></i> The dispute is pending review. Action will be taken shortly.`;
                            } else if (order.dispute_status === 'refunded') {
                                disputeMsg = `<i class="fa-solid fa-circle-check" style="font-size: 2.25rem; margin-bottom: 0.5rem; display: block; color: var(--success);"></i> Dispute resolved: <strong>Fully Refunded (Refunded)</strong>.`;
                            } else if (order.dispute_status === 'replaced') {
                                disputeMsg = `<i class="fa-solid fa-truck-ramp-box" style="font-size: 2.25rem; margin-bottom: 0.5rem; display: block; color: var(--success);"></i> Dispute resolved: <strong>Replacement order dispatched</strong>.`;
                            }
                            disputeForm.innerHTML = `<div style="text-align: center; color: var(--text-primary); padding: 1.5rem; font-weight: 600; border: 1.5px dashed var(--border-color); border-radius: var(--radius-md);">${disputeMsg}</div>`;
                        }
                    }

                    // Check if status changed to trigger alerts
                    if (lastKnownStatus !== status) {
                        if (status === 'cancelled') {
                            simulateCancellation(activeOrderNumber);
                        } else {
                            setTrackingState(step, activeOrderNumber, lastKnownStatus !== null);
                        }
                        lastKnownStatus = status;
                    }
                }
            } catch (error) {
                console.log('[TrackingPoller] Falling back to offline client simulator mode:', error);
                
                // Show offline connection warning on indicator (if present)
                const indicator = document.querySelector('.realtime-indicator');
                if (indicator) {
                    indicator.className = 'realtime-indicator realtime-offline';
                    const label = indicator.querySelector('.indicator-label');
                    if (label) label.textContent = 'Offline Mode (Simulation)';
                }
            }
        }

        function setRating(val) {
            document.getElementById('feedback-rating-value').value = val;
            const stars = document.querySelectorAll('.star-rating-container .star-btn');
            stars.forEach((star, idx) => {
                if (idx < val) {
                    star.classList.add('active');
                    star.style.transform = 'scale(1.25)';
                    setTimeout(() => star.style.transform = 'scale(1)', 150);
                } else {
                    star.classList.remove('active');
                }
            });
        }

        async function submitOrderFeedback(e) {
            e.preventDefault();
            const rating = parseInt(document.getElementById('feedback-rating-value').value);
            const comment = document.getElementById('feedback-comment').value.trim();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

            try {
                const response = await fetch('../api/submit-feedback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_number: activeOrderNumber,
                        rating: rating,
                        comment: comment
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Feedback Submitted', 'Your valuable feedback has been submitted successfully. Thank you!');
                    }
                    e.target.innerHTML = `<div style="text-align: center; color: var(--success); padding: 1.5rem; font-weight: 600;"><i class="fa-solid fa-circle-check" style="font-size: 2.25rem; margin-bottom: 0.5rem; display: block; color: var(--success);"></i> Thank you for your feedback!<br><span style="font-size:0.85rem; color:var(--text-muted); font-weight: 500;">You gave a ${rating} star rating.</span></div>`;
                } else {
                    throw new Error(result.message || 'Feedback failed');
                }
            } catch (err) {
                console.error(err);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Error', err.message || 'Could not submit feedback. Please try again.');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }

        async function submitQualityDispute(e) {
            e.preventDefault();
            const category = document.getElementById('dispute-category').value;
            const description = document.getElementById('dispute-desc').value.trim();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting Claim...';

            try {
                const response = await fetch('../api/report-dispute.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_number: activeOrderNumber,
                        category: category,
                        description: description
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Claim Received', 'Your dispute has been registered successfully. Our team will review it and resolve it immediately!');
                    }
                    e.target.innerHTML = `<div style="text-align: center; color: var(--text-primary); padding: 1.5rem; font-weight: 600; border: 1.5px dashed var(--border-color); border-radius: var(--radius-md);"><i class="fa-solid fa-clock-rotate-left" style="font-size: 2.25rem; margin-bottom: 0.5rem; display: block; color: var(--primary);"></i> Dispute is pending review. Action will be taken shortly.</div>`;
                } else {
                    throw new Error(result.message || 'Failed to submit dispute');
                }
            } catch (err) {
                console.error(err);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Error', err.message || 'Could not submit claim. Please try again.');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }

        function handleOrderSearchSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('search-order-number');
            if (input && input.value.trim()) {
                window.location.href = `order-tracking.php?order_number=${encodeURIComponent(input.value.trim().toUpperCase())}`;
            }
        }

        // Initialize Page
        window.addEventListener('load', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const orderNumParam = urlParams.get('order_number');
            
            const activeView = document.getElementById('active-tracking-view');
            const searchView = document.getElementById('order-search-view');
            
            if (orderNumParam && orderNumParam.trim()) {
                activeOrderNumber = orderNumParam.trim();
                
                // Show tracking view
                if (activeView) activeView.style.display = 'block';
                if (searchView) searchView.style.display = 'none';

                // Initial tracker rendering
                setTrackingState(1, activeOrderNumber, false);
                
                // Run initial check and set polling loop
                fetchLiveOrderStatus();
                pollTimer = setInterval(fetchLiveOrderStatus, 15000); // Poll every 15 seconds
            } else {
                // Show search view
                if (activeView) activeView.style.display = 'none';
                if (searchView) searchView.style.display = 'block';

                if (window.AnimationEngine && window.AnimationEngine.animateFormZoomIn) {
                    window.AnimationEngine.animateFormZoomIn('#order-search-card');
                } else {
                    const card = document.getElementById('order-search-card');
                    if (card) card.style.opacity = '1';
                }

                // Render recent orders from localStorage if any
                const recentOrders = JSON.parse(localStorage.getItem('food_coloured_recent_orders')) || [];
                const recentContainer = document.getElementById('recent-orders-container');
                const recentList = document.getElementById('recent-orders-list');
                
                if (recentOrders.length > 0 && recentContainer && recentList) {
                    recentContainer.style.display = 'block';
                    recentList.innerHTML = recentOrders.map(order => `
                        <li>
                            <a href="order-tracking.php?order_number=${order}" class="glass-panel" style="display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); text-decoration: none; color: var(--text-primary); transition: all 0.25s ease; background: rgba(255,255,255,0.01); width: 100%;">
                                <span style="font-weight: 700; color: var(--primary); font-family: var(--font-heading);"><i class="fa-solid fa-hashtag"></i> ${order}</span>
                                <span style="font-size: 0.85rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.25rem;">
                                    Click to Track <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                                </span>
                            </a>
                        </li>
                    `).join('');
                }
            }
            
            // Clean local storage if coming from successful checkout simulator submit
            if (urlParams.get('simulate_submit') === 'true' && window.CartSystem) {
                setTimeout(() => {
                    window.CartSystem.clearCart();
                }, 500);
            }
        });
    </script>
</body>
</html>
