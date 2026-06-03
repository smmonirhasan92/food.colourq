/**
 * ==========================================================================
 * MAIN FRONTEND APP LOGIC - FOOD COLOURED WEEK 1 & 2
 * Handles general UI, drawer overlays, toggle buttons, and shared behaviors.
 * ==========================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize common layout elements
    initNavigationToggles();
    initCartDrawer();
    initAdminSidebar();
    initRealtimeSimulator();
    setupGlobalEvents();
});

/**
 * Handles mobile navigation toggles (hamburger menu to dropdown)
 */
function initNavigationToggles() {
    const toggleBtn = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.client-nav');

    if (toggleBtn && navMenu) {
        toggleBtn.addEventListener('click', () => {
            toggleBtn.classList.toggle('open');
            navMenu.classList.toggle('open');
        });

        // Close nav menu when clicking a link
        navMenu.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                toggleBtn.classList.remove('open');
                navMenu.classList.remove('open');
            });
        });
    }
}

/**
 * Handles Cart Drawer open/close toggling and backdrops
 */
function initCartDrawer() {
    const cartBtn = document.querySelector('.cart-icon-btn');
    const closeBtn = document.querySelector('.cart-drawer-close');
    const drawer = document.querySelector('.cart-drawer');
    
    // Create backdrop dynamically if it doesn't exist
    let backdrop = document.querySelector('.modal-backdrop');
    if (!backdrop && (cartBtn || drawer)) {
        backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop';
        document.body.appendChild(backdrop);
    }

    const openCart = () => {
        if (drawer && backdrop) {
            drawer.classList.add('open');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent body scroll
            
            // Emit custom event
            document.dispatchEvent(new CustomEvent('cart:opened'));
        }
    };

    const closeCart = () => {
        if (drawer && backdrop) {
            drawer.classList.remove('open');
            backdrop.classList.remove('active');
            document.body.style.overflow = ''; // Restore body scroll
            
            // Emit custom event
            document.dispatchEvent(new CustomEvent('cart:closed'));
        }
    };

    if (cartBtn) {
        cartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openCart();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeCart);
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeCart);
    }

    // Expose close & open functions globally for other scripts
    window.FoodApp = window.FoodApp || {};
    window.FoodApp.openCart = openCart;
    window.FoodApp.closeCart = closeCart;
}

/**
 * Handles admin sidebar collapse & toggle for smaller views
 */
function initAdminSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    const toggleBtn = document.querySelector('.admin-sidebar-toggle');

    if (sidebar && toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
                    sidebar.classList.remove('open');
                }
            }
        });
    }
}

/**
 * Simulates real-time system connection updates on dashboard & portal
 */
function initRealtimeSimulator() {
    const indicator = document.querySelector('.realtime-indicator');
    
    if (indicator) {
        // Prepare online status listeners
        window.addEventListener('online', () => {
            updateConnectionStatus(true);
        });

        window.addEventListener('offline', () => {
            updateConnectionStatus(false);
        });

        // Initial check
        updateConnectionStatus(navigator.onLine);
    }
}

function updateConnectionStatus(isOnline) {
    const indicator = document.querySelector('.realtime-indicator');
    if (!indicator) return;

    const labelSpan = indicator.querySelector('.indicator-label') || indicator;

    if (isOnline) {
        indicator.className = 'realtime-indicator realtime-active';
        if (labelSpan !== indicator) labelSpan.textContent = 'Live System Connected';
    } else {
        indicator.className = 'realtime-indicator realtime-offline';
        if (labelSpan !== indicator) labelSpan.textContent = 'Offline (Trying to reconnect)';
    }
}

/**
 * Global Event Receivers
 */
function setupGlobalEvents() {
    // Listen for custom notifications requests
    document.addEventListener('app:notify', (e) => {
        const { type, title, message } = e.detail || {};
        if (window.NotificationSystem) {
            window.NotificationSystem.toast(type || 'info', title || 'Alert', message || '');
        } else {
            console.log(`[Notification Fallback] ${title}: ${message}`);
        }
    });
}
