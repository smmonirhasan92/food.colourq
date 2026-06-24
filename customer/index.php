<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Crispy Chicken</title>
    <!-- Google Fonts Outfit & Inter loaded inside style.css -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <!-- FontAwesome icons for glass icons and styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- GSAP for micro-animations fallback testing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>
</head>
<body>

    <!-- Premium Storefront Client Header -->
    <header class="client-header">
        <div class="container header-container">
            <a href="/" class="brand-logo">
                Crispy Chicken<span class="brand-dot"></span>
            </a>

            <nav class="client-nav">
                <a href="/" class="nav-link active">Home</a>
                <a href="#" class="nav-link" onclick="openCheckoutModal(); return false;">Checkout</a>
                <a href="/customer/order-tracking.php" class="nav-link">Order Tracking</a>
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

    <!-- Main Content Storefront Wrapper -->
    <main class="container" style="margin-top: 3rem; margin-bottom: 5rem;">
        
        <!-- Extraordinary Interactive Burger Assembly Section -->
        <section class="burger-assembly-container">
            <div class="burger-assembly-info">
                <span class="status-badge status-preparing" style="background: rgba(234, 103, 33, 0.08); color: var(--primary); border: 1px solid rgba(234, 103, 33, 0.15); width: fit-content; font-size: 0.9rem;">
                    <i class="fa-solid fa-crown"></i> Crispy Chicken Burger
                </span>
                <h1 style="font-size: 2.5rem; margin-top: 1rem; margin-bottom: 1rem; font-family: var(--font-heading); color: var(--text-primary);">Crispy Chicken Burger</h1>
                <p style="font-size: 1.1rem; line-height: 1.7; color: var(--text-secondary); margin-bottom: 1.5rem;">A perfect harmony of crispy and juicy flavors! Our signature masterpiece features a golden-crunch crispy chicken patty, fresh lettuce, melted cheddar cheese, and our secret special sauce. Experience premium taste in every single bite!</p>
                <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1rem;">
                    <a href="#menu-catalog-section" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-utensils"></i> Order Menu
                    </a>
                </div>
            </div>
            
            <!-- Burger Canvas Wrapper -->
            <div class="burger-canvas-container">
                <div class="glow-burst" id="burger-glow"></div>
                <div class="steam-smoke-particles" id="burger-steam">
                    <div class="steam-puff steam-puff-1"></div>
                    <div class="steam-puff steam-puff-2"></div>
                    <div class="steam-puff steam-puff-3"></div>
                </div>
                <div class="burger-canvas-wrapper" id="burger-assembly-canvas" style="display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                    <!-- Layer 1: Top Bun -->
                    <div class="burger-layer layer-top-bun" style="transform: translateY(-380px); opacity: 1; display: flex; justify-content: center; width: 100%;">
                        <img src="../images/burger_assembly/top_bun.png?v=1.0.4" alt="Top Bun" style="width: 260px; height: auto; filter: drop-shadow(0px 8px 15px rgba(0,0,0,0.15)); object-fit: contain;">
                    </div>
                    <!-- Layer 2: Tomato Slices -->
                    <div class="burger-layer layer-tomato" style="transform: translateY(-240px) translateX(-40px) rotate(-20deg); opacity: 1; display: flex; justify-content: center; width: 100%;">
                        <img src="../images/burger_assembly/tomato.png?v=1.0.4" alt="Juicy Tomato Slices" style="width: 230px; height: auto; filter: drop-shadow(0px 6px 12px rgba(0,0,0,0.12)); object-fit: contain;">
                    </div>
                    <!-- Layer 3: Melted Cheese -->
                    <div class="burger-layer layer-cheese" style="transform: scale(0.7) translateY(-140px); opacity: 1; display: flex; justify-content: center; width: 100%;">
                        <img src="../images/burger_assembly/cheese.png?v=1.0.4" alt="Melted Cheddar Cheese" style="width: 245px; height: auto; filter: drop-shadow(0px 6px 12px rgba(0,0,0,0.12)); object-fit: contain;">
                    </div>
                    <!-- Layer 4: Crispy Chicken Patty -->
                    <div class="burger-layer layer-patty" style="transform: translateY(-50px) translateX(30px) rotate(15deg); opacity: 1; display: flex; justify-content: center; width: 100%;">
                        <img src="../images/burger_assembly/chicken.png?v=1.0.4" alt="Golden Crispy Chicken Patty" style="width: 240px; height: auto; filter: drop-shadow(0px 8px 18px rgba(0,0,0,0.18)); object-fit: contain;">
                    </div>
                    <!-- Layer 5: Fresh Lettuce -->
                    <div class="burger-layer layer-lettuce" style="transform: translateY(120px) translateX(-30px) rotate(-15deg); opacity: 1; display: flex; justify-content: center; width: 100%;">
                        <img src="../images/burger_assembly/lettuce.png?v=1.0.4" alt="Fresh Crisp Lettuce" style="width: 255px; height: auto; filter: drop-shadow(0px 5px 10px rgba(0,0,0,0.1)); object-fit: contain;">
                    </div>
                    <!-- Layer 6: Bottom Bun -->
                    <div class="burger-layer layer-bottom-bun" style="transform: translateY(260px); opacity: 1; display: flex; justify-content: center; width: 100%;">
                        <img src="../images/burger_assembly/bottom_bun.png?v=1.0.4" alt="Bottom Bun" style="width: 250px; height: auto; filter: drop-shadow(0px 8px 15px rgba(0,0,0,0.15)); object-fit: contain;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Storefront Menu Section -->
        <section id="menu-catalog-section" style="scroll-margin-top: 100px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1.5rem;">
                <h2 style="font-size: 1.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); margin: 0;">
                    <i class="fa-solid fa-fire-flame-curved" style="color: var(--primary);"></i> Hot & Fresh Gourmet Menu
                </h2>
                
                <!-- Category Filter Tabs -->
                <div class="category-tabs-wrapper" style="margin-bottom: 0;">
                    <div class="category-tabs-container" id="category-tabs-container-id">
                        <button class="category-tab active" data-category="all">
                            <i class="fa-solid fa-border-all"></i> All Dishes
                        </button>
                        <button class="category-tab" data-category="appetizer">
                            <i class="fa-solid fa-bread-slice"></i> Starters
                        </button>
                        <button class="category-tab" data-category="main">
                            <i class="fa-solid fa-bowl-rice"></i> Mains
                        </button>
                        <button class="category-tab" data-category="dessert">
                            <i class="fa-solid fa-ice-cream"></i> Desserts
                        </button>
                        <button class="category-tab" data-category="drink">
                            <i class="fa-solid fa-glass-water"></i> Drinks
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Menu Loader Skeletons -->
            <div class="grid grid-cols-3 skeleton-loader-container">
                <div class="glass-panel skeleton" style="height: 380px;"></div>
                <div class="glass-panel skeleton" style="height: 380px;"></div>
                <div class="glass-panel skeleton" style="height: 380px;"></div>
            </div>

            <!-- Menu Items Grid -->
            <div class="grid grid-cols-3 storefront-menu-grid real-content" style="opacity: 0; display: none;">
                <!-- Cards dynamically loaded via JS -->
            </div>
        </section>
    </main>

    <footer style="background-color: #ffffff; border-top: 1px solid var(--border-color); padding: 3rem 0; margin-top: 5rem; text-align: center;">
        <div class="container">
            <div style="font-family: var(--font-heading); font-weight: 800; font-size: 1.5rem; color: var(--text-primary); margin-bottom: 1rem;">
                Crispy Chicken<span class="brand-dot"></span>
            </div>
            <div style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6; max-width: 500px; margin: 0 auto 1.5rem;">
                Food Court, Nodi Bangla Center Point<br>
                Bhairab Town<br>
                <i class="fa-solid fa-phone" style="color: var(--primary); margin-right: 0.25rem;"></i> Hotline: 01681-560308<br>
                <i class="fa-solid fa-wallet" style="color: #ea6721; margin-right: 0.25rem;"></i> bKash (Merchant): 01671018363
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted); border-top: 1px solid var(--border-color); padding-top: 1.5rem; font-weight: 500;">
                Powerd By :- Crispy Chicken
            </div>
        </div>
    </footer>

    <!-- Sliding Shopping Cart Drawer Overlay -->
    <aside class="cart-drawer">
        <div class="cart-drawer-header">
            <h3 style="font-size: 1.25rem; font-family: var(--font-heading); display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                <i class="fa-solid fa-shopping-bag" style="color: var(--primary);"></i> Your Shopping Cart
            </h3>
            <span class="cart-drawer-close">&times;</span>
        </div>
        
        <!-- Cart Item Entries Wrapper -->
        <div class="cart-items-list">
            <!-- Dynamically populated via checkout.js -->
        </div>

        <div class="cart-drawer-footer">
            <div class="cart-total-row">
                <span>Total Bill:</span>
                <span>Tk. 0</span>
            </div>
            <button class="btn btn-primary btn-lg" style="width: 100%; border: none;" onclick="openCheckoutModal(); if(window.FoodApp) window.FoodApp.closeCart();">
                Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </aside>

    <!-- App JavaScript files links -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/animations.js"></script>
    <script src="../assets/js/checkout.js"></script>

    <script>
        // Helper to optimize Unsplash images for storefront speed
        function optimizeUnsplashUrl(url) {
            if (!url || typeof url !== 'string') return url;
            if (url.includes('unsplash.com')) {
                // replace width to 450
                url = url.replace(/w=\d+/, 'w=450');
                // replace quality to 60
                url = url.replace(/q=\d+/, 'q=60');
            }
            return url;
        }

        let globalCategories = [];

        async function loadCategories() {
            try {
                const response = await fetch('../api/get-categories.php');
                const result = await response.json();
                if (result.success && result.data) {
                    globalCategories = result.data;
                    renderCategoryTabs(result.data);
                } else {
                    throw new Error('Failed to load categories');
                }
            } catch (error) {
                console.warn('Falling back to default categories:', error);
                globalCategories = [
                    { name: 'Starter', slug: 'appetizer' },
                    { name: 'Best Seller', slug: 'main' },
                    { name: 'Dessert', slug: 'dessert' },
                    { name: 'Drink', slug: 'drink' }
                ];
                renderCategoryTabs(globalCategories);
            }
        }

        function renderCategoryTabs(categories) {
            const container = document.getElementById('category-tabs-container-id');
            if (!container) return;

            const iconMap = {
                'all': 'fa-border-all',
                'appetizer': 'fa-bread-slice',
                'main': 'fa-bowl-rice',
                'dessert': 'fa-ice-cream',
                'drink': 'fa-glass-water'
            };

            let html = `
                <button class="category-tab active" data-category="all">
                    <i class="fa-solid fa-border-all"></i> All Dishes
                </button>
            `;

            categories.forEach(cat => {
                const icon = iconMap[cat.slug] || 'fa-tags';
                html += `
                    <button class="category-tab" data-category="${cat.slug}">
                        <i class="fa-solid ${icon}"></i> ${cat.name}
                    </button>
                `;
            });

            container.innerHTML = html;
            initCategoryFilters(); // Re-bind click handlers for dynamic tabs
        }

        // Dynamic menu catalog fetching and rendering with visual fallback
        async function loadCatalogMenu() {
            const loaders = document.querySelector('.skeleton-loader-container');
            const contentGrid = document.querySelector('.storefront-menu-grid');
            if (!contentGrid) return;

            try {
                const response = await fetch('../api/get-menu.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                
                if (result.success && result.data) {
                    const categories = result.data;
                    let htmlContent = '';
                    let totalItems = 0;

                    // Build a list of category keys in order based on dynamic categories
                    const categoryKeys = globalCategories.map(cat => cat.slug);
                    Object.keys(categories).forEach(key => {
                        if (!categoryKeys.includes(key)) {
                            categoryKeys.push(key);
                        }
                    });

                    categoryKeys.forEach(categoryKey => {
                        const items = categories[categoryKey] || [];
                        if (items.length > 0) {
                            const catObj = globalCategories.find(c => c.slug === categoryKey);
                            const badge = catObj ? catObj.name : (categoryKey.charAt(0).toUpperCase() + categoryKey.slice(1));
                            
                            items.forEach(item => {
                                totalItems++;
                                
                                // Premium image resolver to map database names to high-res mouth-watering photos
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
                                    'chocolate_lava.jpg': 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&q=80&w=600',
                                    'tiramisu.jpg': 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&q=80&w=600',
                                    'cheesecake.jpg': 'https://images.unsplash.com/photo-1524351199679-46cddf530c04?auto=format&fit=crop&q=80&w=600',
                                    'mango_smoothie.jpg': 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&q=80&w=600',
                                    'iced_macchiato.jpg': 'https://images.unsplash.com/photo-1557925923-cd4648e21187?auto=format&fit=crop&q=80&w=600',
                                    'virgin_mojito.jpg': 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&q=80&w=600'
                                };

                                let img = item.image_url || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=600';
                                if (img.startsWith('images/')) {
                                    const filename = img.substring(7);
                                    if (premiumImageMap[filename]) {
                                        img = premiumImageMap[filename];
                                    }
                                }
                                
                                const optimizedImg = optimizeUnsplashUrl(img);
                                
                                const hasDiscount = item.discount_price !== null && item.discount_price > 0;
                                const activePrice = hasDiscount ? item.discount_price : item.price;
                                const priceHtml = hasDiscount ? 
                                    `Tk. ${item.discount_price.toFixed(0)} <del style="font-size: 0.85rem; color: var(--text-muted); margin-left: 0.5rem;">Tk. ${item.price.toFixed(0)}</del>` : 
                                    `Tk. ${item.price.toFixed(0)}`;

                                htmlContent += `
                                    <div class="glass-panel glass-panel-interactive menu-card" data-category="${categoryKey}">
                                        <div class="menu-card-img-container">
                                            <span class="menu-card-badge">${badge}</span>
                                            <img src="${optimizedImg}" alt="${item.name}" class="menu-card-img" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=60&w=450'">
                                        </div>
                                        <div class="menu-card-body">
                                            <h3 class="menu-card-title">${item.name}</h3>
                                            <p class="menu-card-desc">${item.description}</p>
                                            <div class="menu-card-footer" style="flex-direction: column; align-items: stretch; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem; width: 100%;">
                                                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                    <span class="menu-card-price">${priceHtml}</span>
                                                </div>
                                                <div style="display: flex; gap: 0.5rem; width: 100%;">
                                                    <button class="btn btn-secondary btn-sm add-to-cart-btn" 
                                                            data-id="dish-${item.id}" 
                                                            data-name="${item.name}" 
                                                            data-price="${activePrice}" 
                                                            data-image="${optimizedImg}"
                                                            style="flex: 1; padding: 0.4rem 0.6rem; font-size: 0.8rem;">
                                                        <i class="fa-solid fa-plus"></i> Add
                                                    </button>
                                                    <button class="btn btn-primary btn-sm buy-now-btn" 
                                                            style="flex: 1.2; padding: 0.4rem 0.6rem; font-size: 0.8rem; background: var(--gradient-primary);"
                                                            onclick="buyNow('dish-${item.id}', '${item.name.replace(/'/g, "\\'")}', ${activePrice}, '${optimizedImg}')">
                                                        <i class="fa-solid fa-bolt"></i> Buy Now
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                    });

                    if (totalItems === 0) {
                        contentGrid.innerHTML = `
                            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: var(--text-muted);">
                                <i class="fa-solid fa-pizza-slice" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                                <h3>No items found in the menu.</h3>
                            </div>
                        `;
                    } else {
                        contentGrid.innerHTML = htmlContent;
                    }
                } else {
                    throw new Error(result.message || 'Malformed API response.');
                }
            } catch (error) {
                console.warn('[CatalogLoader] API fetch failed. Reverting to premium visual mockup catalog fallback:', error);
                
                const fallbackItems = [
                    {
                        id: 1,
                        name: "Avocado Superfood Bowl",
                        description: "Fresh organic greens, poached egg, nutritious seeds, cream cheese, and avocado mash.",
                        price: 250,
                        badge: "Best Seller",
                        category: "main",
                        image: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=600"
                    },
                    {
                        id: 2,
                        name: "Pan-Seared Organic Salmon",
                        description: "Atlantic salmon fillet, fresh vegetables, creamy butter mash, and lemon herb sauce.",
                        price: 950,
                        badge: "Best Seller",
                        category: "main",
                        image: "https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&q=80&w=600"
                    },
                    {
                        id: 3,
                        name: "Classic Pepperoni Pizza",
                        description: "Rich tomato marinara, mozzarella cheese, premium beef pepperoni, and dried oregano.",
                        price: 650,
                        badge: "Best Seller",
                        category: "main",
                        image: "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&q=80&w=600"
                    },
                    {
                        id: 4,
                        name: "Stuffed Garlic Mushrooms",
                        description: "Fresh button mushrooms stuffed with herb garlic cream cheese, baked to golden crisp perfection.",
                        price: 220,
                        badge: "Starter",
                        category: "appetizer",
                        image: "https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=600"
                    },
                    {
                        id: 5,
                        name: "Rich Chocolate Lava Cake",
                        description: "Warm molten chocolate center cake, served with a scoop of premium vanilla bean gelato.",
                        price: 180,
                        badge: "Dessert",
                        category: "dessert",
                        image: "https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&q=80&w=600"
                    },
                    {
                        id: 6,
                        name: "Tropical Mango Smoothie",
                        description: "Blended fresh ripe honey mangoes, coconut milk, and splash of organic honey.",
                        price: 120,
                        badge: "Drink",
                        category: "drink",
                        image: "https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&q=80&w=600"
                    }
                ];
 
                contentGrid.innerHTML = fallbackItems.map(item => {
                    const optimizedImg = optimizeUnsplashUrl(item.image);
                    return `
                        <div class="glass-panel glass-panel-interactive menu-card" data-category="${item.category}">
                            <div class="menu-card-img-container">
                                <span class="menu-card-badge">${item.badge}</span>
                                <img src="${optimizedImg}" alt="${item.name}" class="menu-card-img" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=60&w=450'">
                            </div>
                            <div class="menu-card-body">
                                <h3 class="menu-card-title">${item.name}</h3>
                                <p class="menu-card-desc">${item.description}</p>
                                <div class="menu-card-footer" style="flex-direction: column; align-items: stretch; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem; width: 100%;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                        <span class="menu-card-price">Tk. ${item.price.toFixed(0)}</span>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem; width: 100%;">
                                        <button class="btn btn-secondary btn-sm add-to-cart-btn" data-id="dish-${item.id}" data-name="${item.name}" data-price="${item.price}" data-image="${optimizedImg}" style="flex: 1; padding: 0.4rem 0.6rem; font-size: 0.8rem;">
                                            <i class="fa-solid fa-plus"></i> Add
                                        </button>
                                        <button class="btn btn-primary btn-sm buy-now-btn" style="flex: 1.2; padding: 0.4rem 0.6rem; font-size: 0.8rem; background: var(--gradient-primary);" onclick="buyNow('dish-${item.id}', '${item.name.replace(/'/g, "\\'")}', ${item.price}, '${optimizedImg}')">
                                            <i class="fa-solid fa-bolt"></i> Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } finally {
                // Dimiss skeletons and trigger entrance stagger
                if (loaders) loaders.style.display = 'none';
                contentGrid.style.display = 'grid';
                contentGrid.style.opacity = '1'; // Ensure the catalog container itself is visible!
                
                if (window.AnimationEngine) {
                    window.AnimationEngine.cascadeEntrance('.menu-card');
                }
            }
        }

        /**
         * Scroll-Linked Burger Layers Assembly Calculations
         */
        let isBurgerIntroPlaying = true;

        function playBurgerIntroAnimation() {
            const topBun = document.querySelector('.layer-top-bun');
            const tomato = document.querySelector('.layer-tomato');
            const cheese = document.querySelector('.layer-cheese');
            const patty = document.querySelector('.layer-patty');
            const lettuce = document.querySelector('.layer-lettuce');
            const bottomBun = document.querySelector('.layer-bottom-bun');
            const glow = document.getElementById('burger-glow');
            const steam = document.getElementById('burger-steam');

            if (window.gsap) {
                const tl = gsap.timeline({
                    onComplete: () => {
                        isBurgerIntroPlaying = false;
                        handleBurgerScroll();
                    }
                });
                tl.delay(0.6);
                tl.to(bottomBun, { y: 0, duration: 1.2, ease: "elastic.out(1, 0.75)" }, 0)
                  .to(lettuce, { y: 0, x: 0, rotation: 0, duration: 1.2, ease: "elastic.out(1, 0.75)" }, 0.1)
                  .to(patty, { y: 0, x: 0, rotation: 0, duration: 1.2, ease: "elastic.out(1, 0.75)" }, 0.2)
                  .to(cheese, { y: 0, scale: 1, duration: 1.2, ease: "elastic.out(1, 0.75)" }, 0.3)
                  .to(tomato, { y: 0, x: 0, rotation: 0, duration: 1.2, ease: "elastic.out(1, 0.75)" }, 0.4)
                  .to(topBun, { y: 0, duration: 1.2, ease: "elastic.out(1, 0.75)", onComplete: () => {
                      if (glow) glow.classList.add('active');
                      if (steam) steam.style.opacity = '1';
                  }}, 0.5);
            } else {
                setTimeout(() => {
                    const layers = [bottomBun, lettuce, patty, cheese, tomato, topBun];
                    layers.forEach(layer => {
                        if (layer) {
                            layer.style.transition = 'transform 1.2s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                            if (layer === cheese) {
                                layer.style.transform = 'scale(1) translateY(0)';
                            } else {
                                layer.style.transform = 'translateY(0) translateX(0) rotate(0)';
                            }
                        }
                    });
                    setTimeout(() => {
                        isBurgerIntroPlaying = false;
                        if (glow) glow.classList.add('active');
                        if (steam) steam.style.opacity = '1';
                    }, 1300);
                }, 600);
            }
        }

        function handleBurgerScroll() {
            if (isBurgerIntroPlaying) return;

            const topBun = document.querySelector('.layer-top-bun');
            const tomato = document.querySelector('.layer-tomato');
            const cheese = document.querySelector('.layer-cheese');
            const patty = document.querySelector('.layer-patty');
            const lettuce = document.querySelector('.layer-lettuce');
            const bottomBun = document.querySelector('.layer-bottom-bun');
            const glow = document.getElementById('burger-glow');
            const steam = document.getElementById('burger-steam');

            const scrollY = window.scrollY;
            const maxScroll = 400; // Explodes over 400px of scrolling
            let progress = 1 - (scrollY / maxScroll);
            progress = Math.min(Math.max(progress, 0), 1); // Clamp between 0 and 1

            if (progress >= 0.9) {
                if (glow) glow.classList.add('active');
                if (steam) steam.style.opacity = '1';
            } else {
                if (glow) glow.classList.remove('active');
                if (steam) steam.style.opacity = '0';
            }

            const yTopBun = -380 * (1 - progress);
            
            const yTomato = -240 * (1 - progress);
            const xTomato = -40 * (1 - progress);
            const rotTomato = -20 * (1 - progress);
            
            const yCheese = -140 * (1 - progress);
            const scaleCheese = 0.7 + 0.3 * progress;
            
            const yPatty = -50 * (1 - progress);
            const xPatty = 30 * (1 - progress);
            const rotPatty = 15 * (1 - progress);
            
            const yLettuce = 120 * (1 - progress);
            const xLettuce = -30 * (1 - progress);
            const rotLettuce = -15 * (1 - progress);
            
            const yBottomBun = 260 * (1 - progress);

            if (window.gsap) {
                gsap.to(topBun, { y: yTopBun, duration: 0.15, ease: "power1.out", overwrite: "auto" });
                gsap.to(tomato, { y: yTomato, x: xTomato, rotation: rotTomato, duration: 0.15, ease: "power1.out", overwrite: "auto" });
                gsap.to(cheese, { y: yCheese, scale: scaleCheese, duration: 0.15, ease: "power1.out", overwrite: "auto" });
                gsap.to(patty, { y: yPatty, x: xPatty, rotation: rotPatty, duration: 0.15, ease: "power1.out", overwrite: "auto" });
                gsap.to(lettuce, { y: yLettuce, x: xLettuce, rotation: rotLettuce, duration: 0.15, ease: "power1.out", overwrite: "auto" });
                gsap.to(bottomBun, { y: yBottomBun, duration: 0.15, ease: "power1.out", overwrite: "auto" });
            } else {
                if (topBun) topBun.style.transform = `translateY(${yTopBun}px)`;
                if (tomato) tomato.style.transform = `translateY(${yTomato}px) translateX(${xTomato}px) rotate(${rotTomato}deg)`;
                if (cheese) cheese.style.transform = `scale(${scaleCheese}) translateY(${yCheese}px)`;
                if (patty) patty.style.transform = `translateY(${yPatty}px) translateX(${xPatty}px) rotate(${rotPatty}deg)`;
                if (lettuce) lettuce.style.transform = `translateY(${yLettuce}px) translateX(${xLettuce}px) rotate(${rotLettuce}deg)`;
                if (bottomBun) bottomBun.style.transform = `translateY(${yBottomBun}px)`;
            }
        }

        /**
         * Dynamic Storefront Catalogue Category Tabs Filtering
         */
        function initCategoryFilters() {
            const tabs = document.querySelectorAll('.category-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    const selectedCategory = tab.dataset.category;
                    const cards = document.querySelectorAll('.storefront-menu-grid .menu-card');

                    cards.forEach((card, idx) => {
                        card.classList.add('menu-card-transitioning');
                        
                        setTimeout(() => {
                            const isMatch = (selectedCategory === 'all' || card.dataset.category === selectedCategory);
                            if (isMatch) {
                                card.classList.remove('menu-card-hidden');
                                setTimeout(() => {
                                    card.classList.remove('menu-card-transitioning');
                                }, 30 * idx); // Incremental stagger entrance
                            } else {
                                card.classList.add('menu-card-hidden');
                            }
                        }, 200);
                    });
                });
            });
        }

        // Trigger on load
        window.addEventListener('load', async () => {
            await loadCategories();
            setTimeout(loadCatalogMenu, 800); // 800ms buffer for elegant loading skeleton preview
            
            // Play intro animation, which will automatically enable scroll listener afterwards
            playBurgerIntroAnimation();
            window.addEventListener('scroll', handleBurgerScroll);

            // Check if checkout URL parameter is present
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('checkout')) {
                setTimeout(openCheckoutModal, 900);
            }
        });

        /**
         * Quick Buy Flow: clears cart, adds item, and triggers checkout modal
         */
        function buyNow(id, name, price, image) {
            if (window.CartSystem) {
                // Clear cart to ensure only this item is ordered
                window.CartSystem.clearCart();
                
                // Add item
                const itemData = {
                    id: id,
                    name: name,
                    price: parseFloat(price),
                    image: image || 'assets/img/placeholder.jpg',
                    quantity: 1
                };
                window.CartSystem.addItem(itemData);
                
                // Open modal
                openCheckoutModal();
            }
        }

        function openCheckoutModal() {
            if (window.AuthSystem) {
                window.AuthSystem.prefillCheckoutForm();
            }
            
            renderModalCheckoutSummary();
            
            if (window.AnimationEngine) {
                window.AnimationEngine.animateModalOpen('#checkout-modal-popup');
            } else {
                const modal = document.getElementById('checkout-modal-popup');
                if (modal) {
                    modal.style.display = 'flex';
                    modal.classList.add('active');
                }
            }
        }

        function closeCheckoutModal() {
            if (window.AnimationEngine) {
                window.AnimationEngine.animateModalClose('#checkout-modal-popup');
            } else {
                const modal = document.getElementById('checkout-modal-popup');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('active');
                }
            }
        }

        function renderModalCheckoutSummary() {
            const previewContainer = document.getElementById('modal-checkout-items-preview');
            const totalEl = document.getElementById('modal-checkout-total');
            const jsonInput = document.getElementById('cart-json-input');
            
            if (!previewContainer || !window.CartSystem) return;

            const items = window.CartSystem.items;
            const totals = window.CartSystem.getTotals();

            if (jsonInput) {
                jsonInput.value = JSON.stringify(items);
            }

            if (totalEl) {
                if (parseFloat(totals.discountAmount) > 0) {
                    const gross = parseFloat(totals.total) + parseFloat(totals.discountAmount);
                    totalEl.innerHTML = `Tk. ${totals.total} <del style="font-size: 0.9rem; color: var(--text-muted); margin-left: 0.5rem; font-weight: 500;">Tk. ${gross.toFixed(0)}</del>`;
                } else {
                    totalEl.textContent = `Tk. ${totals.total}`;
                }
            }

            if (items.length === 0) {
                previewContainer.innerHTML = `<p style="color: var(--text-muted); font-size: 0.9rem; padding: 1rem 0; text-align: center;">Your cart is empty.</p>`;
                return;
            }

            previewContainer.innerHTML = items.map(item => `
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; border-radius: var(--radius-md); object-fit: cover; border: 1px solid rgba(255,255,255,0.08);">
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary); max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.name}</div>
                            <div style="font-size: 0.75rem; color: var(--primary); font-weight: 600; margin-top: 0.15rem;">Tk. ${item.price.toFixed(0)} each</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary);">Qty:</span>
                            <div style="display: flex; align-items: center; gap: 0.35rem; background: rgba(234, 103, 33, 0.04); border: 1px solid rgba(234, 103, 33, 0.25); padding: 0.2rem; border-radius: 8px;">
                                <button type="button" class="qty-btn btn-modal-minus" data-id="${item.id}" style="width: 26px; height: 26px; border-radius: 6px; border: none; background: rgba(234, 103, 33, 0.12); color: var(--primary); cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; transition: all 0.2s;">-</button>
                                <span style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary); min-width: 20px; text-align: center;">${item.quantity}</span>
                                <button type="button" class="qty-btn btn-modal-plus" data-id="${item.id}" style="width: 26px; height: 26px; border-radius: 6px; border: none; background: var(--primary); color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; transition: all 0.2s;">+</button>
                            </div>
                        </div>
                        <span style="font-size: 0.95rem; font-weight: 800; color: var(--text-primary); min-width: 70px; text-align: right;">Tk. ${(item.price * item.quantity).toFixed(0)}</span>
                    </div>
                </div>
            `).join('');

            // Attach listeners to new buttons to increment/decrement dynamically
            previewContainer.querySelectorAll('.btn-modal-minus').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (window.CartSystem) {
                        window.CartSystem.removeItemQuantity(btn.dataset.id);
                    }
                });
            });

            previewContainer.querySelectorAll('.btn-modal-plus').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (window.CartSystem) {
                        window.CartSystem.addItemQuantity(btn.dataset.id);
                    }
                });
            });
        }

        // Listen for cart updates to update modal contents dynamically
        document.addEventListener('cart:updated', renderModalCheckoutSummary);

        // Toggle payment details display based on payment method selection
        document.addEventListener('DOMContentLoaded', () => {
            const paymentSelect = document.getElementById('modal-payment-method');
            const cardSection = document.getElementById('modal-card-details-section');
            const mfsSection = document.getElementById('modal-mfs-details-section');
            const qrContainer = document.getElementById('payment-qr-container');
            const instructionsText = document.getElementById('payment-instructions-text');
            const mfsSenderInput = document.getElementById('mfs-sender');
            
            if (paymentSelect) {
                const handlePaymentChange = () => {
                    const method = paymentSelect.value;
                    if (cardSection) {
                        cardSection.style.display = 'none'; // Managed via bank transfer option
                    }
                    if (mfsSection) {
                        if (['bkash', 'nagad', 'rocket', 'card'].includes(method)) {
                            mfsSection.style.display = 'block';
                            
                            // Dynamically update instructions, placeholders, and show/hide QR code
                            if (method === 'bkash') {
                                if (qrContainer) qrContainer.style.display = 'block';
                                if (instructionsText) {
                                    instructionsText.innerHTML = 'বিকাশ অ্যাপের পেমেন্ট অপশন ব্যবহার করে নিচের কিউআর কোডটি স্ক্যান করুন অথবা আমাদের মার্চেন্ট নাম্বার <strong>01671018363</strong>-এ পেমেন্ট সম্পন্ন করে নিচের ফর্মটি পূরণ করুন:';
                                }
                                if (mfsSenderInput) mfsSenderInput.placeholder = 'যেমন: 01671018363';
                            } else if (method === 'nagad') {
                                if (qrContainer) qrContainer.style.display = 'block';
                                if (instructionsText) {
                                    instructionsText.innerHTML = 'নগদ অ্যাপের পেমেন্ট অপশন ব্যবহার করে নিচের কিউআর কোডটি স্ক্যান করুন অথবা আমাদের মার্চেন্ট নাম্বার <strong>01671018363</strong>-এ পেমেন্ট সম্পন্ন করে নিচের ফর্মটি পূরণ করুন:';
                                }
                                if (mfsSenderInput) mfsSenderInput.placeholder = 'যেমন: 01671018363';
                            } else if (method === 'card') {
                                if (qrContainer) qrContainer.style.display = 'block';
                                if (instructionsText) {
                                    instructionsText.innerHTML = 'আপনার ব্যাংক অ্যাপের মাধ্যমে পেমেন্ট করতে নিচের কিউআর কোডটি স্ক্যান করুন অথবা মার্চেন্ট নাম্বার <strong>01671018363</strong>-এ ট্রান্সফার সম্পন্ন করে নিচের ফর্মটি পূরণ করুন:';
                                }
                                if (mfsSenderInput) mfsSenderInput.placeholder = 'যেমন: 01671018363';
                            } else if (method === 'rocket') {
                                if (qrContainer) qrContainer.style.display = 'none';
                                if (instructionsText) {
                                    instructionsText.innerHTML = 'আমাদের রকেট নাম্বারে পেমেন্ট সম্পন্ন করে নিচের ফর্মটি পূরণ করুন:';
                                }
                                if (mfsSenderInput) mfsSenderInput.placeholder = 'যেমন: 017XXXXXXXX';
                            }
                        } else {
                            mfsSection.style.display = 'none';
                        }
                    }
                };
                paymentSelect.addEventListener('change', handlePaymentChange);
                // Run on initial load
                handlePaymentChange();
            }
        });
    </script>

    <!-- Buy Now Premium Checkout Modal -->
    <div class="custom-modal-backdrop" id="checkout-modal-popup" style="z-index: 9999;">
        <div class="glass-panel modal-content-glass" style="max-width: 580px; width: 95%; max-height: 85vh; overflow-y: auto; padding: 2rem; border-radius: var(--radius-lg); position: relative; background-color: var(--bg-dark-surface);">
            <button class="modal-close-btn-custom" onclick="closeCheckoutModal()" style="position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; border: none; background: none;">&times;</button>
            
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 50px; height: 50px; background: rgba(234, 103, 33, 0.08); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 0.75rem;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--text-primary);">Secure Checkout</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">Complete your order details below</p>
            </div>

            <form id="checkout-payment-form" action="order-tracking.php?simulate_submit=true" method="POST">
                <!-- Hidden field to transmit local storage cart state to PHP backend -->
                <input type="hidden" id="cart-json-input" name="cart_data" value="">

                <h4 style="font-size: 1.1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; color: var(--text-primary);">
                    <i class="fa-solid fa-basket-shopping" style="color: var(--primary);"></i> Order Summary
                </h4>
                <div id="modal-checkout-items-preview" style="max-height: 150px; overflow-y: auto; margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px dashed var(--border-color);">
                    <!-- Populated via JS -->
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; color: var(--text-primary);">
                    <span>Total Bill:</span>
                    <span id="modal-checkout-total" style="color: var(--primary); font-weight: 800; font-family: var(--font-heading);">Tk. 0</span>
                </div>

                <h4 style="font-size: 1.1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; color: var(--text-primary);">
                    <i class="fa-solid fa-truck" style="color: var(--primary);"></i> Delivery Details
                </h4>

                <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="cust-name">Full Name</label>
                        <input class="form-input" type="text" id="cust-name" name="customer_name" required placeholder="e.g. John Doe" style="padding: 0.75rem 1rem;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="cust-phone">Phone Number</label>
                        <input class="form-input" type="tel" id="cust-phone" name="customer_phone" required placeholder="e.g. +8801712345678" style="padding: 0.75rem 1rem;">
                        <div id="checkout-loyalty-status" style="margin-top: 0.35rem; font-size: 0.8rem; font-weight: 600;"></div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="cust-addr">Delivery Address</label>
                    <input class="form-input" type="text" id="cust-addr" name="delivery_address" required placeholder="House No, Road No, Area / Landmark" style="padding: 0.75rem 1rem;">
                </div>

                <input type="hidden" name="delivery_notes" value="">

                <h4 style="font-size: 1.1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; color: var(--text-primary);">
                    <i class="fa-solid fa-credit-card" style="color: var(--primary);"></i> Payment Details
                </h4>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Payment Method</label>
                    <select class="form-input form-select" name="payment_method" id="modal-payment-method" style="padding: 0.75rem 1rem; background-color: #ffffff;">
                        <option value="cod" selected>Cash on Delivery (COD)</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                        <option value="rocket">Rocket</option>
                        <option value="card">Bank Transfer / Card</option>
                        <option value="crypto">Cryptocurrency Gateway</option>
                    </select>
                </div>

                <div id="modal-card-details-section" style="display: none;">
                    <!-- Managed dynamically -->
                </div>

                <div id="modal-mfs-details-section" style="display: none; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; box-shadow: inset 0 1px 1px rgba(255,255,255,0.05);">
                    <!-- QR Code Display Area (Visible for bkash, nagad, card) -->
                    <div id="payment-qr-container" style="text-align: center; margin-bottom: 1.25rem; display: none;">
                        <img src="../images/payment_qr.png" alt="Payment QR Code" style="max-width: 170px; width: 100%; height: auto; border-radius: 12px; border: 4px solid #ffffff; box-shadow: 0 8px 24px rgba(0,0,0,0.3); display: inline-block;">
                        <div style="margin-top: 0.85rem; font-size: 1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <span>Wallet Number:</span> 
                            <span style="color: var(--primary); font-size: 1.15rem; font-family: var(--font-heading); letter-spacing: 0.5px;" id="merchant-num-display">01671018363</span>
                            <button type="button" onclick="navigator.clipboard.writeText('01671018363'); alert('Merchant number copied!');" style="background: rgba(234, 103, 33, 0.1); border: 1px solid rgba(234, 103, 33, 0.3); color: var(--primary); cursor: pointer; padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                <i class="fa-solid fa-copy"></i> Copy
                            </button>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
                            Scan to Pay (bKash / Nagad / Bank Transfer)
                        </div>
                    </div>
                    
                    <div id="payment-instructions-text" style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
                        Please send the total bill amount to our Merchant Wallet: <strong style="color: var(--primary);">01671018363</strong>. Once completed, enter the verification details below:
                    </div>
                    
                    <div class="grid grid-cols-2" style="gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="mfs-sender" style="font-size: 0.75rem;">Sender Mobile No.</label>
                            <input class="form-input" type="text" id="mfs-sender" name="mfs_sender_number" placeholder="e.g. 01671018363" style="padding: 0.75rem 1rem;">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="mfs-txnid" style="font-size: 0.75rem;">Transaction ID (TxnID)</label>
                            <input class="form-input" type="text" id="mfs-txnid" name="mfs_transaction_id" placeholder="e.g. 8NX7A8D9" style="padding: 0.75rem 1rem;">
                        </div>
                    </div>
                </div>

                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; padding: 0.85rem;">
                        <i class="fa-solid fa-shield-halved"></i> Confirm Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
