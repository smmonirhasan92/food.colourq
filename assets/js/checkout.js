/**
 * ==========================================================================
 * SHOPPING CART & SECURE CHECKOUT MANAGEMENT - FOOD COLOURED WEEK 1 & 2
 * Handles complete client-side storage, calculations, list bindings,
 * dynamic updates, and premium interface callbacks.
 * ==========================================================================
 */

class ShoppingCartManager {
    constructor() {
        this.storageKey = 'food_coloured_cart';
        this.items = this.loadCart();
        this.standardDeliveryFee = 0; // Dynamically set from checkout form
        this.taxRate = 0; // 0% VAT
        this.couponCode = null;
        this.couponDiscountType = null;
        this.couponDiscountValue = 0;
        this.init();
    }

    init() {
        // Set up event listeners for storefront clicks & updates
        this.bindEvents();
        this.updateBadge();
        this.renderDrawerItems();
        this.updateSummaryCalculations();

        // Listen for Delivery Area selection
        const deliveryAreaSelect = document.getElementById('delivery-area');
        if (deliveryAreaSelect) {
            deliveryAreaSelect.addEventListener('change', (e) => {
                const val = e.target.value;
                if (val === 'inside_bhairab') {
                    this.standardDeliveryFee = 50;
                } else if (val === 'outside_bhairab') {
                    this.standardDeliveryFee = 100;
                } else {
                    this.standardDeliveryFee = 0;
                }
                this.updateSummaryCalculations();
            });
        }
    }

    /**
     * Retrieves cart list safely from localStorage.
     */
    loadCart() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            console.error('[CartManager] Error reading storage:', e);
            return [];
        }
    }

    /**
     * Syncs state directly back to user localStorage.
     */
    saveCart() {
        localStorage.setItem(this.storageKey, JSON.stringify(this.items));
        this.updateBadge();
        this.renderDrawerItems();
        this.updateSummaryCalculations();
        
        // Dispatch global custom event for external hooks
        document.dispatchEvent(new CustomEvent('cart:updated', {
            detail: {
                items: this.items,
                totals: this.getTotals()
            }
        }));
    }

    /**
     * Binds general event delegation to capture add-to-cart clicks.
     */
    bindEvents() {
        document.addEventListener('click', (e) => {
            const addBtn = e.target.closest('.add-to-cart-btn');
            if (addBtn) {
                e.preventDefault();
                const card = addBtn.closest('.menu-card');
                const select = card ? card.querySelector('.customer-var-select') : null;
                
                let id = addBtn.dataset.id;
                let name = addBtn.dataset.name;
                let price = parseFloat(addBtn.dataset.price);
                let variationId = null;
                let variationName = null;
                const deliveryCharge = addBtn.dataset.deliveryCharge !== undefined ? parseInt(addBtn.dataset.deliveryCharge) : 50;

                if (select) {
                    const selectedOpt = select.options[select.selectedIndex];
                    variationId = parseInt(select.value);
                    variationName = selectedOpt.text.split(' (Tk.')[0];
                    price = parseFloat(selectedOpt.dataset.price);
                    id = `${id}-${variationId}`;
                }

                const itemData = {
                    id: id,
                    name: name,
                    price: price,
                    image: addBtn.dataset.image || 'assets/img/placeholder.jpg',
                    quantity: 1,
                    variation_id: variationId,
                    variation_name: variationName,
                    delivery_charge: isNaN(deliveryCharge) ? 50 : deliveryCharge
                };
                
                this.addItem(itemData);
                
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('success', 'Added to Cart', `${itemData.name} has been added to your bag.`);
                }

                const imgEl = card ? card.querySelector('.menu-card-img') : null;

                if (imgEl && window.AnimationEngine && window.AnimationEngine.animateFlyToCart) {
                    window.AnimationEngine.animateFlyToCart(imgEl, '.cart-icon-btn');
                } else if (window.AnimationEngine) {
                    window.AnimationEngine.bounceCartIcon();
                }
            }
        });
    }

    /**
     * Appends or increments items in active cart storage lists.
     * @param {object} newItemData - Object representation of dish.
     */
    addItem(newItemData) {
        const existing = this.items.find(item => item.id === newItemData.id);
        if (existing) {
            existing.quantity += 1;
        } else {
            this.items.push(newItemData);
        }
        this.saveCart();
    }

    /**
     * Decrements quantities or completely wipes items from local systems.
     * @param {string} id - Selected item identification code.
     */
    removeItemQuantity(id) {
        const existing = this.items.find(item => item.id === id);
        if (existing) {
            existing.quantity -= 1;
            if (existing.quantity <= 0) {
                this.items = this.items.filter(item => item.id !== id);
            }
            this.saveCart();
        }
    }

    /**
     * Increments simple single-item records from active arrays.
     */
    addItemQuantity(id) {
        const existing = this.items.find(item => item.id === id);
        if (existing) {
            existing.quantity += 1;
            this.saveCart();
        }
    }

    /**
     * Wipes all current checkout listings safely.
     */
    clearCart() {
        this.items = [];
        this.saveCart();
    }

    /**
     * Aggregates total item counts to display on drawer indicators.
     */
    getItemCount() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    }

    /**
     * Computes values for items, taxes, fees, and final order bills.
     */
    getTotals() {
        const subtotal = this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const tax = 0; // VAT removed
        
        let deliveryFee = subtotal > 0 ? this.standardDeliveryFee : 0;
        
        const gross = subtotal > 0 ? (subtotal + tax + deliveryFee) : 0;

        let discountAmount = 0;
        if (this.couponCode) {
            if (this.couponDiscountType === 'percent') {
                discountAmount = gross * (this.couponDiscountValue / 100);
            } else {
                discountAmount = this.couponDiscountValue;
            }
            if (discountAmount > gross) {
                discountAmount = gross;
            }
        }
        
        const total = gross - discountAmount;

        return {
            subtotal: subtotal.toFixed(0),
            tax: tax.toFixed(0),
            deliveryFee: deliveryFee.toFixed(0),
            gross: gross.toFixed(0),
            discountAmount: discountAmount.toFixed(0),
            total: total.toFixed(0),
            count: this.items.reduce((sum, i) => sum + i.quantity, 0)
        };
    }

    /**
     * Reflects item count directly in header notifications badge.
     */
    updateBadge() {
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            const count = this.getItemCount();
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    /**
     * Iteratively draws elements inside the main floating Drawer lists.
     */
    renderDrawerItems() {
        const listContainer = document.querySelector('.cart-items-list');
        if (!listContainer) return;

        if (this.items.length === 0) {
            listContainer.innerHTML = `
                <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                    <i class="fas fa-shopping-bag" style="font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p style="font-weight: 500;">Your shopping cart is empty.</p>
                    <p style="font-size: 0.85rem; margin-top: 0.25rem;">Explore our storefront to add delicious meals!</p>
                </div>
            `;
            return;
        }

        listContainer.innerHTML = this.items.map(item => `
            <div class="cart-item" data-id="${item.id}">
                <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                <div class="cart-item-details">
                    <div>
                        <div class="cart-item-title">${item.name}${item.variation_name ? ` (${item.variation_name})` : ''}</div>
                        <div class="cart-item-price">Tk. ${(item.price * item.quantity).toFixed(0)}</div>
                    </div>
                    <div class="cart-item-qty">
                        <button class="qty-btn btn-minus" data-id="${item.id}">-</button>
                        <span style="font-weight: 600; font-size: 0.95rem;">${item.quantity}</span>
                        <button class="qty-btn btn-plus" data-id="${item.id}">+</button>
                    </div>
                </div>
            </div>
        `).join('');

        // Attach action click listeners directly
        listContainer.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', () => this.removeItemQuantity(btn.dataset.id));
        });

        listContainer.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', () => this.addItemQuantity(btn.dataset.id));
        });
    }

    /**
     * Updates calculations on both drawer totals and checkout pages.
     */
    updateSummaryCalculations() {
        const totals = this.getTotals();

        // 1. Drawer values
        const drawerTotalRow = document.querySelector('.cart-total-row span:last-child');
        if (drawerTotalRow) {
            drawerTotalRow.textContent = `Tk. ${totals.total}`;
        }

        // 2. Main checkout page calculations if they exist on DOM
        const subtotalEl = document.getElementById('checkout-subtotal');
        const taxEl = document.getElementById('checkout-tax');
        const deliveryEl = document.getElementById('checkout-delivery');
        const totalEl = document.getElementById('checkout-total');

        if (subtotalEl) subtotalEl.textContent = `Tk. ${totals.subtotal}`;
        if (taxEl) taxEl.textContent = `Tk. ${totals.tax}`;
        if (deliveryEl) deliveryEl.textContent = `Tk. ${totals.deliveryFee}`;
        if (totalEl) totalEl.textContent = `Tk. ${totals.total}`;

        // Keep a backup input variable sync for PHP checkout processes
        const inputCartData = document.getElementById('cart-json-input');
        if (inputCartData) {
            inputCartData.value = JSON.stringify(this.items);
        }
    }
}

// Instantiate Cart System globally inside user portals
document.addEventListener('DOMContentLoaded', () => {
    window.CartSystem = new ShoppingCartManager();
    window.AuthSystem = new CustomerAuthSystem();
    initCheckoutPageValidation();
});

/**
 * Validates checkouts & handles submission structures.
 */
function initCheckoutPageValidation() {
    const checkoutForm = document.getElementById('checkout-payment-form');
    if (!checkoutForm) return;

    checkoutForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (window.CartSystem && window.CartSystem.items.length === 0) {
            if (window.NotificationSystem) {
                window.NotificationSystem.toast('error', 'Checkout Blocked', 'Your shopping bag is completely empty.');
            }
            return;
        }

        const required = checkoutForm.querySelectorAll('[required]');
        let isValid = true;
        
        required.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error-pulse');
                setTimeout(() => field.classList.remove('error-pulse'), 1000);
            }
        });

        if (!isValid) {
            if (window.NotificationSystem) {
                window.NotificationSystem.toast('warning', 'Incomplete Form', 'Please complete all required shipping & payment information.');
            }
            return;
        }

        const submitBtn = checkoutForm.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'Authorize & Place Order';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Securing Culinary Order...`;
        }

        const formData = new FormData(checkoutForm);
        
        const cartItems = window.CartSystem.items.map(item => {
            const parts = typeof item.id === 'string' ? item.id.split('-') : [];
            const cleanId = parts.length > 1 ? parseInt(parts[1], 10) : parseInt(item.id, 10);
            const varId = item.variation_id ? parseInt(item.variation_id, 10) : null;
            return {
                menu_item_id: isNaN(cleanId) ? item.id : cleanId,
                variation_id: varId,
                quantity: item.quantity,
                price: item.price
            };
        });

        const phone = formData.get('customer_phone') || '';
        const cleanPhone = phone.replace(/[^0-9]/g, '') || Date.now().toString();
        const dummyEmail = `${cleanPhone}@crispybd.com`;

        const totals = window.CartSystem ? window.CartSystem.getTotals() : { discountAmount: 0, deliveryFee: 0 };
        const payload = {
            customer_name: formData.get('customer_name'),
            username: formData.get('customer_name'),
            phone: phone,
            email: dummyEmail, // Auto-generated unique email!
            delivery_address: formData.get('delivery_address'),
            delivery_notes: formData.get('delivery_notes'),
            payment_method: formData.get('payment_method'),
            mfs_sender_number: formData.get('mfs_sender_number') || null,
            mfs_transaction_id: formData.get('mfs_transaction_id') || null,
            discount_amount: parseFloat(totals.discountAmount) || 0,
            coupon_code: window.CartSystem && window.CartSystem.couponCode ? window.CartSystem.couponCode : null,
            delivery_fee: parseFloat(totals.deliveryFee) || 0,
            items: cartItems.map(item => ({
                menu_item_id: item.menu_item_id,
                variation_id: item.variation_id,
                quantity: item.quantity
            }))
        };

        try {
            console.log('[Checkout] Sending order payload:', payload);
            const response = await fetch('../api/place-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            // Handle successful API responses
            if (response.ok) {
                const data = await response.json();
                console.log('[Checkout] Server response:', data);

                if (data.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Order Authorized!', data.message || 'Your gourmet order has been successfully scheduled.');
                        // Play premium upward arpeggio sweep
                        if (window.NotificationSystem.synth) {
                            window.NotificationSystem.synth.playChime('success');
                        }
                    }

                    // Save/update address in localStorage user profile
                    const existingUser = JSON.parse(localStorage.getItem('food_coloured_user')) || {};
                    existingUser.name = formData.get('customer_name');
                    existingUser.phone = phone;
                    existingUser.address = formData.get('delivery_address');
                    localStorage.setItem('food_coloured_user', JSON.stringify(existingUser));

                    // Clear cart storage and updates
                    window.CartSystem.clearCart();
                    // Also reset coupon state
                    window.CartSystem.couponCode = null;
                    window.CartSystem.couponDiscountType = null;
                    window.CartSystem.couponDiscountValue = 0;

                    // Elegant delayed transition
                    setTimeout(() => {
                        const orderNumber = data.data && data.data.order_number ? data.data.order_number : 'FC-NEW-ORDER';
                        
                        // Safely save order number to recent orders list
                        try {
                            let recentOrders = JSON.parse(localStorage.getItem('food_coloured_recent_orders')) || [];
                            if (!recentOrders.includes(orderNumber)) {
                                recentOrders.unshift(orderNumber);
                                localStorage.setItem('food_coloured_recent_orders', JSON.stringify(recentOrders.slice(0, 5)));
                            }
                        } catch(e) { console.warn('LocalStorage restricted on mobile'); }
                        
                        window.location.href = `/customer/order-tracking.php?order_number=${orderNumber}`;
                    }, 1800);
                    return;
                } else {
                    throw new Error(data.message || 'Failed to place order.');
                }
            } else {
                let errorMsg = `API returned HTTP status ${response.status}`;
                try {
                    const errData = await response.json();
                    if (errData && errData.message) {
                        errorMsg = errData.message;
                    }
                } catch (e) { }
                throw new Error(errorMsg);
            }

        } catch (error) {
            console.error('[Checkout] Live API failure:', error);
            
            // Restore submit button
            const submitBtn = checkoutForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Confirm Order';
            }

            // Show actual error to the user
            if (window.NotificationSystem) {
                window.NotificationSystem.toast('error', 'Checkout Failed', error.message || 'An error occurred while placing your order. Please try again.');
                if (window.NotificationSystem.synth) {
                    window.NotificationSystem.synth.playChime('error');
                }
            }
        }
    });

    // Coupon application logic
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const couponInput = document.getElementById('coupon-code-input');
    const couponMessage = document.getElementById('coupon-message');

    if (applyCouponBtn && couponInput) {
        applyCouponBtn.addEventListener('click', async () => {
            const code = couponInput.value.trim().toUpperCase();
            if (!code) {
                couponMessage.innerHTML = '<span style="color: var(--danger);">Please enter a coupon code.</span>';
                return;
            }
            
            applyCouponBtn.disabled = true;
            applyCouponBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            
            try {
                const response = await fetch('../api/validate-coupon.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ code: code })
                });
                const result = await response.json();
                
                if (result.success) {
                    couponMessage.innerHTML = '<span style="color: var(--success);"><i class="fa-solid fa-check"></i> ' + result.message + '</span>';
                    if (window.CartSystem) {
                        window.CartSystem.couponCode = code;
                        window.CartSystem.couponDiscountType = result.data.type;
                        window.CartSystem.couponDiscountValue = result.data.value;
                        window.CartSystem.saveCart();
                        
                        // Force re-render of modal totals
                        if (typeof renderModalCheckoutSummary === 'function') {
                            renderModalCheckoutSummary();
                        }
                    }
                } else {
                    couponMessage.innerHTML = '<span style="color: var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> ' + result.message + '</span>';
                    if (window.CartSystem) {
                        window.CartSystem.couponCode = null;
                        window.CartSystem.couponDiscountType = null;
                        window.CartSystem.couponDiscountValue = 0;
                        window.CartSystem.saveCart();
                        if (typeof renderModalCheckoutSummary === 'function') {
                            renderModalCheckoutSummary();
                        }
                    }
                }
            } catch (err) {
                couponMessage.innerHTML = '<span style="color: var(--danger);">Error connecting to server.</span>';
            } finally {
                applyCouponBtn.disabled = false;
                applyCouponBtn.innerHTML = 'Apply';
            }
        });
    }

/**
 * ==========================================================================
 * CUSTOMER LOGIN & AUTHENTICATION SYSTEMS (PHONE-ONLY LOGINS)
 * Injects glassmorphic dynamic modal, syncs localStorage user profile data,
 * handles autofilling of checkout, and displays welcoming greeters.
 * ==========================================================================
 */
class CustomerAuthSystem {
    constructor() {
        this.modalId = 'dynamic-login-modal';
        this.init();
    }

    init() {
        this.injectStyles();
        this.injectModalHtml();
        this.updateHeaderAuthUi();
        this.prefillCheckoutForm();
    }

    injectStyles() {
        if (document.getElementById('auth-modal-styles')) return;

        const styles = `
            .custom-modal-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                z-index: 10000;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.25s ease;
            }
            .custom-modal-backdrop.active {
                display: flex;
                opacity: 1;
            }
            .modal-content-glass {
                max-width: 420px;
                width: 90%;
                padding: 2.5rem;
                background-color: var(--bg-dark-surface) !important;
                border: 1px solid var(--border-color) !important;
                position: relative;
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow-lg);
            }
            .modal-close-btn-custom {
                position: absolute;
                top: 1.25rem;
                right: 1.25rem;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: var(--text-muted);
                cursor: pointer;
                transition: color 0.2s;
            }
            .modal-close-btn-custom:hover {
                color: var(--primary);
            }
        `;

        const styleBlock = document.createElement('style');
        styleBlock.id = 'auth-modal-styles';
        styleBlock.innerHTML = styles;
        document.head.appendChild(styleBlock);
    }

    injectModalHtml() {
        if (document.getElementById(this.modalId)) return;

        const modalHtml = `
            <div class="custom-modal-backdrop" id="${this.modalId}">
                <div class="glass-panel modal-content-glass">
                    <button class="modal-close-btn-custom" onclick="window.AuthSystem.closeLoginModal()">&times;</button>
                    
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <div style="width: 60px; height: 60px; background: rgba(234, 103, 33, 0.08); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1rem;">
                            <i class="fa-solid fa-user-lock"></i>
                        </div>
                        <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--text-primary);">Customer Login</h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Login instantly with just your name and mobile number</p>
                    </div>

                    <form id="customer-login-form-custom" onsubmit="window.AuthSystem.handleLoginSubmit(event)">
                        <div class="form-group" style="margin-bottom: 1.25rem;">
                            <label class="form-label" for="login-name-field" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: var(--text-secondary); text-align: left;">Your Name</label>
                            <input class="form-input" type="text" id="login-name-field" required placeholder="e.g. John Doe" style="width: 100%; padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: rgba(255,255,255,0.02); color: var(--text-primary);">
                        </div>

                        <div class="form-group" style="margin-bottom: 2rem;">
                            <label class="form-label" for="login-phone-field" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: var(--text-secondary); text-align: left;">Mobile Number</label>
                            <input class="form-input" type="tel" id="login-phone-field" required placeholder="e.g. 01712345678" style="width: 100%; padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: rgba(255,255,255,0.02); color: var(--text-primary);">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; border-radius: var(--radius-sm); font-weight: 600;">
                            Complete Login <i class="fa-solid fa-arrow-right-to-bracket" style="margin-left: 0.5rem;"></i>
                        </button>
                    </form>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    openLoginModal(e) {
        if (e) e.preventDefault();
        if (window.AnimationEngine && window.AnimationEngine.animateModalOpen) {
            window.AnimationEngine.animateModalOpen(`#${this.modalId}`);
        } else {
            const modal = document.getElementById(this.modalId);
            if (modal) modal.classList.add('active');
        }
    }

    closeLoginModal() {
        if (window.AnimationEngine && window.AnimationEngine.animateModalClose) {
            window.AnimationEngine.animateModalClose(`#${this.modalId}`);
        } else {
            const modal = document.getElementById(this.modalId);
            if (modal) modal.classList.remove('active');
        }
    }

    handleLoginSubmit(e) {
        e.preventDefault();
        const nameField = document.getElementById('login-name-field');
        const phoneField = document.getElementById('login-phone-field');

        if (!nameField || !phoneField) return;

        const name = nameField.value.trim();
        const phone = phoneField.value.trim();

        if (!name || !phone) return;

        // Save in localStorage
        const userData = {
            name: name,
            phone: phone,
            address: ''
        };
        localStorage.setItem('food_coloured_user', JSON.stringify(userData));

        if (window.NotificationSystem) {
            window.NotificationSystem.toast('success', 'Login Successful!', `Welcome, ${name}! Your account has been linked successfully.`);
        }

        this.closeLoginModal();
        this.updateHeaderAuthUi();
        this.prefillCheckoutForm();

        // Reset form
        e.target.reset();
    }

    logout(e) {
        if (e) e.preventDefault();
        localStorage.removeItem('food_coloured_user');
        
        if (window.NotificationSystem) {
            window.NotificationSystem.toast('info', 'Logout Successful', 'You have been successfully logged out.');
        }

        this.updateHeaderAuthUi();
        
        // Clear checkout form if currently open
        const nameField = document.getElementById('cust-name');
        const phoneField = document.getElementById('cust-phone');
        if (nameField) nameField.value = '';
        if (phoneField) phoneField.value = '';
    }

    updateHeaderAuthUi() {
        const nav = document.querySelector('.client-nav');
        if (!nav) return;

        const oldTrigger = document.getElementById('login-nav-trigger');
        if (oldTrigger) oldTrigger.remove();

        const user = JSON.parse(localStorage.getItem('food_coloured_user'));
        if (user) {
            nav.insertAdjacentHTML('beforeend', `
                <a href="#" class="nav-link" id="login-nav-trigger" onclick="window.AuthSystem.logout(event)" style="color: var(--primary); font-weight: 600;">
                    <i class="fa-solid fa-user-circle"></i> ${user.name} (Logout)
                </a>
            `);
        } else {
            nav.insertAdjacentHTML('beforeend', `
                <a href="#" class="nav-link" id="login-nav-trigger" onclick="window.AuthSystem.openLoginModal(event)">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </a>
            `);
        }
    }

    prefillCheckoutForm() {
        const user = JSON.parse(localStorage.getItem('food_coloured_user'));
        if (!user) return;

        const nameField = document.getElementById('cust-name');
        const phoneField = document.getElementById('cust-phone');
        const addrField = document.getElementById('cust-addr');

        if (nameField && !nameField.value) nameField.value = user.name || '';
        if (phoneField && !phoneField.value) phoneField.value = user.phone || '';
        if (addrField && !addrField.value && user.address) addrField.value = user.address || '';

        // Dispatch a custom event to trigger loyalty check on prefill
        document.dispatchEvent(new CustomEvent('auth:prefilled'));
    }
}
