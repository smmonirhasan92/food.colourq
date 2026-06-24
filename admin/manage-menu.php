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
    <title>Manage Menu - Crispy Chicken</title>
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
                <a href="pos.php" class="sidebar-link">
                    <i class="fa-solid fa-cash-register"></i> POS Counter
                </a>
                <a href="manage-orders.php" class="sidebar-link">
                    <i class="fa-solid fa-receipt"></i> Live Orders
                </a>
                <a href="manage-menu.php" class="sidebar-link active">
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
                    <h1 style="color: var(--text-primary);">Culinary Menu Master</h1>
                    <p style="color: var(--text-secondary);">Add new gourmet recipes, toggle storefront availabilities, and modify pricing.</p>
                </div>

                <div class="realtime-indicator realtime-active">
                    <span class="indicator-dot"></span>
                    <span class="indicator-label">Storefront Catalog Synced</span>
                </div>
            </div>

            <!-- Two Columns layout (Menu Creation Form & Menu Grid) -->
            <div class="grid grid-cols-2" style="grid-template-columns: 1fr 2fr; align-items: start; gap: 2rem;">
                
                <!-- Add New Item Form -->
                <section class="glass-panel" style="padding: 2rem; position: sticky; top: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color);">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; color: var(--text-primary);">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> Add Gourmet Dish
                        </span>
                        <button type="button" class="btn btn-glass btn-sm" onclick="openCategoriesModal()" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; font-weight: 600; border-color: rgba(234, 103, 33, 0.3); color: var(--primary); width: auto;">
                            <i class="fa-solid fa-tags"></i> Manage Categories
                        </button>
                    </h3>

                    <form id="add-dish-form" onsubmit="handleNewDishSubmit(event)">
                        <div class="form-group">
                            <label class="form-label" for="dish-name">Dish Name</label>
                            <input class="form-input" type="text" id="dish-name" required placeholder="e.g. Herb Crusted Ribeye">
                        </div>

                        <div class="grid grid-cols-4" style="grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" for="dish-price">Price (Tk.)</label>
                                <input class="form-input" type="number" id="dish-price" step="1" required placeholder="e.g. 450">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="dish-discount-price">Discount (Tk.)</label>
                                <input class="form-input" type="number" id="dish-discount-price" step="1" placeholder="Optional">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="dish-cost">Cost (Tk.)</label>
                                <input class="form-input" type="number" id="dish-cost" step="1" required placeholder="e.g. 200">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="dish-category">Category</label>
                                <select class="form-input form-select" id="dish-category">
                                    <option value="appetizer">Starter</option>
                                    <option value="main" selected>Best Seller</option>
                                    <option value="dessert">Dessert</option>
                                    <option value="drink">Drink</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="dish-img">Image URL (CDN / Web)</label>
                            <input class="form-input" type="url" id="dish-img" placeholder="https://images.unsplash.com/...">
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <span>Or Upload Local Image</span>
                                <span style="font-size: 0.75rem; color: var(--primary); font-weight: 600;">RECOMMENDED</span>
                            </label>
                            <div style="display: flex; gap: 1rem; align-items: center;">
                                <label class="btn btn-glass btn-sm" style="flex: 1; border: 1.5px dashed var(--border-color); cursor: pointer; padding: 0.9rem; text-align: center; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: var(--transition-fast);" id="upload-label">
                                    <i class="fa-solid fa-cloud-arrow-up" style="color: var(--primary); font-size: 1.1rem;"></i>
                                    <span id="upload-status" style="font-weight: 500;">Choose Image File</span>
                                    <input type="file" id="dish-file" accept="image/*" style="display: none;" onchange="previewLocalImage(event)">
                                </label>
                                <div id="image-preview-container" style="display: none; width: 48px; height: 48px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                                    <img id="image-preview" src="" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 2rem;">
                            <label class="form-label" for="dish-desc">Description</label>
                            <textarea class="form-input" id="dish-desc" rows="3" required placeholder="Aromatic details of raw ingredients, sauces, preparation style..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Publish to Catalog <i class="fa-solid fa-upload"></i>
                        </button>
                    </form>
                </section>
                <!-- Current Catalog Grid -->
                <section style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--text-primary);">Active Offerings</h3>
                        <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Showing 0 Recipes</span>
                    </div>

                    <div class="grid grid-cols-2" id="admin-catalog-grid">
                        <!-- Catalog items dynamically populated via fetch -->
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/animations.js"></script>

    <script>
        let activeItems = [];

        async function fetchMenuItems() {
            try {
                const response = await fetch('../api/get-all-menu-items.php');
                if (!response.ok) throw new Error('API Offline');
                const result = await response.json();
                if (result.success && result.data) {
                    activeItems = result.data;
                    renderCatalog(result.data);
                } else {
                    throw new Error(result.message || 'API error');
                }
            } catch (error) {
                console.warn('[CatalogDesk] Falling back to default mockup catalog:', error);
                const fallbackItems = [
                    {
                        id: 1,
                        name: "Avocado Superfood Bowl",
                        description: "Crispy greens, poached egg, cream cheese, avocado mash.",
                        price: 14.99,
                        category: "Organic Bowls",
                        image_url: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=600",
                        is_available: 1
                    },
                    {
                        id: 2,
                        name: "Pan-Seared Organic Salmon",
                        description: "Atlantic salmon, glazed garden greens, butter mash.",
                        price: 22.50,
                        category: "Gourmet Mains",
                        image_url: "https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&q=80&w=600",
                        is_available: 1
                    }
                ];
                activeItems = fallbackItems;
                renderCatalog(fallbackItems);
            }
        }

        function renderCatalog(items) {
            const grid = document.getElementById('admin-catalog-grid');
            const countSpan = document.querySelector('span[style*="Showing"]');
            if (!grid) return;

            if (countSpan) {
                countSpan.textContent = `Showing ${items.length} Recipes`;
            }

            if (items.length === 0) {
                grid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">No items in menu catalog.</div>`;
                return;
            }

            const categoryColors = {
                'appetizer': 'var(--gradient-primary)',
                'main': 'var(--gradient-success)',
                'dessert': 'linear-gradient(135deg, #ec4899, #be185d)',
                'drink': 'linear-gradient(135deg, #3b82f6, #1d4ed8)'
            };

            grid.innerHTML = items.map(item => {
                const catObj = categoriesCache.find(c => c.slug === item.category);
                const displayCategory = catObj ? catObj.name : item.category;
                const bgStyle = categoryColors[item.category] || 'linear-gradient(135deg, #8b5cf6, #6d28d9)';
                const opacity = item.is_available === 1 ? '1' : '0.4';
                const buttonText = item.is_available === 1 ? 'Disable' : 'Enable';
                const buttonIcon = item.is_available === 1 ? 'fa-eye-slash' : 'fa-eye';

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

                return `
                    <div class="glass-panel menu-card" id="catalog-item-${item.id}" style="opacity: ${opacity};">
                        <div class="menu-card-img-container" style="aspect-ratio: 16/9;">
                            <span class="menu-card-badge" style="background: ${bgStyle}">${displayCategory}</span>
                            <img src="${img}" alt="${item.name}" class="menu-card-img" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=600'">
                        </div>
                        <div class="menu-card-body" style="gap: 0.5rem; padding: 1.25rem;">
                            <h4 class="menu-card-title" style="font-size: 1.1rem; color: var(--text-primary);">${item.name}</h4>
                            <p class="menu-card-desc" style="font-size: 0.8rem; color: var(--text-muted);">${item.description}</p>
                            <div class="menu-card-footer" style="padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                                <span class="menu-card-price" style="font-size: 1.15rem; color: var(--primary);">
                                    ${item.discount_price && item.discount_price > 0 ? 
                                        `Tk. ${item.discount_price.toFixed(0)} <del style="font-size: 0.8rem; color: var(--text-muted); margin-left: 0.35rem;">Tk. ${item.price.toFixed(0)}</del>` : 
                                        `Tk. ${item.price.toFixed(0)}`}
                                </span>
                                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                    <button class="btn btn-glass btn-sm" onclick="openEditDishModal(${item.id})" style="padding: 0.4rem 0.6rem; border-color: rgba(59, 130, 246, 0.3); color: #3b82f6; width: auto;" title="Edit dish details">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <button class="btn btn-glass btn-sm" onclick="toggleAvailability(${item.id}, ${item.is_available})" id="btn-avail-${item.id}" style="padding: 0.4rem 0.6rem; width: auto;" title="${buttonText} dish">
                                        <i class="fa-solid ${buttonIcon}"></i> ${buttonText}
                                    </button>
                                    <button class="btn btn-glass btn-sm" onclick="deleteMenuItem(${item.id}, '${item.name.replace(/'/g, "\\'")}')" style="padding: 0.4rem 0.6rem; border-color: rgba(239, 68, 68, 0.3); color: #ef4444; width: auto;" title="Delete dish">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function previewLocalImage(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('image-preview-container');
            const previewImg = document.getElementById('image-preview');
            const statusText = document.getElementById('upload-status');
            const uploadLabel = document.getElementById('upload-label');

            if (file && previewContainer && previewImg && statusText && uploadLabel) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    previewContainer.style.display = 'block';
                    statusText.textContent = file.name;
                    statusText.style.maxWidth = '120px';
                    statusText.style.overflow = 'hidden';
                    statusText.style.textOverflow = 'ellipsis';
                    statusText.style.whiteSpace = 'nowrap';
                    uploadLabel.style.borderColor = 'var(--success)';
                }
                reader.readAsDataURL(file);
            }
        }

        async function handleNewDishSubmit(e) {
            e.preventDefault();

            const name = document.getElementById('dish-name').value;
            const price = parseFloat(document.getElementById('dish-price').value);
            const discountPriceVal = document.getElementById('dish-discount-price').value;
            const discount_price = discountPriceVal ? parseFloat(discountPriceVal) : '';
            const cost_price = parseFloat(document.getElementById('dish-cost').value);
            const category = document.getElementById('dish-category').value;
            const imgUrl = document.getElementById('dish-img').value;
            const desc = document.getElementById('dish-desc').value;
            const fileInput = document.getElementById('dish-file');

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'Publish to Catalog';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;
            }

            try {
                const formData = new FormData();
                formData.append('name', name);
                formData.append('price', price);
                formData.append('cost_price', cost_price);
                formData.append('discount_price', discount_price);
                formData.append('category', category);
                formData.append('description', desc);
                formData.append('image_url', imgUrl);
                
                if (fileInput && fileInput.files[0]) {
                    formData.append('dish_image', fileInput.files[0]);
                }

                const response = await fetch('../api/add-menu-item.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Recipe Published', `Successfully added '${name}' to catalog list.`);
                    }
                    document.getElementById('add-dish-form').reset();
                    
                    // Reset upload preview
                    const previewContainer = document.getElementById('image-preview-container');
                    const statusText = document.getElementById('upload-status');
                    const uploadLabel = document.getElementById('upload-label');
                    if (previewContainer) previewContainer.style.display = 'none';
                    if (statusText) statusText.textContent = 'Choose Image File';
                    if (uploadLabel) uploadLabel.style.borderColor = '';

                    fetchMenuItems();
                } else {
                    throw new Error(result.message || 'Failed to save');
                }
            } catch (error) {
                console.warn('[CatalogDesk] POST failed. Simulating local addition:', error);
                
                // Fallback simulator addition
                const localId = Date.now();
                const previewImg = document.getElementById('image-preview');
                const simulatedImg = (previewImg && previewImg.src && previewImg.src.startsWith('data:')) 
                    ? previewImg.src 
                    : (imgUrl || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=600');

                const mockItem = {
                    id: localId,
                    name: name,
                    price: price,
                    category: category,
                    description: desc,
                    image_url: simulatedImg,
                    is_available: 1
                };
                activeItems.push(mockItem);
                renderCatalog(activeItems);

                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('success', 'Recipe Published (Simulated)', `Successfully added '${name}' to catalog list.`);
                }
                document.getElementById('add-dish-form').reset();
                const previewContainer = document.getElementById('image-preview-container');
                const statusText = document.getElementById('upload-status');
                const uploadLabel = document.getElementById('upload-label');
                if (previewContainer) previewContainer.style.display = 'none';
                if (statusText) statusText.textContent = 'Choose Image File';
                if (uploadLabel) uploadLabel.style.borderColor = '';
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        }

        async function toggleAvailability(itemId, currentStatus) {
            const newStatus = currentStatus === 1 ? 0 : 1;

            try {
                const response = await fetch('../api/toggle-menu-item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: itemId,
                        is_available: newStatus
                    })
                });

                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        const toastType = newStatus === 1 ? 'success' : 'warning';
                        const toastMsg = newStatus === 1 ? 'Dish published with active visibility.' : 'Dish set to inactive storefront visibility.';
                        window.NotificationSystem.toast(toastType, newStatus === 1 ? 'Recipe Active' : 'Recipe Unavailable', toastMsg);
                    }
                    fetchMenuItems();
                } else {
                    throw new Error(result.message || 'Toggle failed');
                }
            } catch (error) {
                console.warn('[CatalogDesk] Toggle request failed. Simulating local toggle:', error);
                
                const btn = document.getElementById(`btn-avail-${itemId}`);
                const card = document.getElementById(`catalog-item-${itemId}`);
                if (!btn || !card) return;

                const isDisabled = btn.textContent.includes('Disable');

                if (isDisabled) {
                    btn.innerHTML = `<i class="fa-solid fa-eye"></i> Enable`;
                    card.style.opacity = '0.4';
                    const item = activeItems.find(i => i.id === itemId);
                    if (item) item.is_available = 0;

                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('warning', 'Recipe Unavailable (Simulated)', 'Dish set to inactive storefront visibility.');
                    }
                } else {
                    btn.innerHTML = `<i class="fa-solid fa-eye-slash"></i> Disable`;
                    card.style.opacity = '1';
                    const item = activeItems.find(i => i.id === itemId);
                    if (item) item.is_available = 1;

                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Recipe Active (Simulated)', 'Dish published with active visibility.');
                    }
                }
            }
        }

        async function deleteMenuItem(itemId, itemName) {
            if (!confirm(`Are you sure you want to permanently delete '${itemName}' from active listings?`)) {
                return;
            }

            try {
                const response = await fetch('../api/delete-menu-item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: itemId
                    })
                });

                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Recipe Deleted', `Successfully deleted '${itemName}' from menu.`);
                    }
                    fetchMenuItems();
                } else {
                    throw new Error(result.message || 'Delete failed');
                }
            } catch (error) {
                console.error('[CatalogDesk] Delete request failed:', error);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Delete Failed', error.message || 'Error occurred while deleting item.');
                }
            }
        }

        let categoriesCache = [];

        async function fetchCategories() {
            try {
                const response = await fetch('../api/get-categories.php');
                const result = await response.json();
                if (result.success && result.data) {
                    categoriesCache = result.data;
                    populateCategoryDropdown(result.data);
                    renderCategoriesList(result.data);
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        }

        function populateCategoryDropdown(categories) {
            const select = document.getElementById('dish-category');
            if (!select) return;
            
            select.innerHTML = categories.map(cat => {
                return `<option value="${cat.slug}">${cat.name}</option>`;
            }).join('');
        }

        function renderCategoriesList(categories) {
            const ul = document.getElementById('categories-list-ul');
            if (!ul) return;

            const systemDefaults = ['appetizer', 'main', 'dessert', 'drink'];

            if (categories.length === 0) {
                ul.innerHTML = `<li style="padding: 1rem; text-align: center; color: var(--text-muted);">No categories available.</li>`;
                return;
            }

            ul.innerHTML = categories.map(cat => {
                const isDefault = systemDefaults.includes(cat.slug);
                const deleteBtn = isDefault ? 
                    `<span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">System Default</span>` :
                    `<button type="button" class="btn btn-glass btn-sm" onclick="deleteCategory('${cat.slug}')" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border-color: rgba(239,68,68,0.3); color: var(--danger); width: auto;">
                        <i class="fa-solid fa-trash"></i> Delete
                     </button>`;

                return `
                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; border-bottom: 1px solid var(--border-color); color: var(--text-primary);">
                        <span style="font-weight: 500;">${cat.name} <span style="font-size: 0.75rem; color: var(--text-muted);">(${cat.slug})</span></span>
                        ${deleteBtn}
                    </li>
                `;
            }).join('');
        }

        function openCategoriesModal() {
            const modal = document.getElementById('categories-modal');
            if (modal) modal.classList.add('active');
        }

        function closeCategoriesModal() {
            const modal = document.getElementById('categories-modal');
            if (modal) modal.classList.remove('active');
        }

        async function handleAddCategory(e) {
            e.preventDefault();
            const input = document.getElementById('new-category-name');
            const name = input.value.trim();
            if (!name) return;

            try {
                const response = await fetch('../api/add-category.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: name })
                });
                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Category Created', `Successfully added '${name}'.`);
                    }
                    input.value = '';
                    fetchCategories();
                } else {
                    throw new Error(result.message || 'Failed to add category');
                }
            } catch (error) {
                console.error(error);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Action Failed', error.message || 'Error occurred while saving category.');
                }
            }
        }

        async function deleteCategory(slug) {
            if (!confirm('Are you sure you want to delete this category? All items under it will be moved to Starter.')) {
                return;
            }

            try {
                const response = await fetch('../api/delete-category.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ slug: slug })
                });
                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Category Deleted', 'The category has been removed.');
                    }
                    await fetchCategories();
                    fetchMenuItems();
                } else {
                    throw new Error(result.message || 'Failed to delete category');
                }
            } catch (error) {
                console.error(error);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Action Failed', error.message || 'Error occurred while deleting category.');
                }
            }
        }

        function openEditDishModal(id) {
            const item = activeItems.find(item => item.id === id);
            if (!item) return;

            document.getElementById('edit-dish-id').value = item.id;
            document.getElementById('edit-dish-name').value = item.name;
            document.getElementById('edit-dish-price').value = item.price;
            document.getElementById('edit-dish-discount-price').value = item.discount_price !== null ? item.discount_price : '';
            document.getElementById('edit-dish-cost').value = item.cost_price;
            
            // Populate category select list
            const catSelect = document.getElementById('edit-dish-category');
            if (catSelect) {
                catSelect.innerHTML = categoriesCache.map(cat => `
                    <option value="${cat.slug}" ${cat.slug === item.category ? 'selected' : ''}>${cat.name}</option>
                `).join('');
            }
            
            document.getElementById('edit-dish-img').value = item.image_url.startsWith('../images/') ? '' : item.image_url;
            document.getElementById('edit-dish-desc').value = item.description;

            // Reset image file input and preview
            document.getElementById('edit-dish-file').value = '';
            const previewContainer = document.getElementById('edit-image-preview-container');
            const previewImg = document.getElementById('edit-image-preview');
            const statusText = document.getElementById('edit-upload-status');
            const uploadLabel = document.getElementById('edit-upload-label');
            
            if (previewContainer && previewImg && statusText && uploadLabel) {
                if (item.image_url.startsWith('../images/')) {
                    previewImg.src = item.image_url;
                    previewContainer.style.display = 'block';
                    statusText.textContent = item.image_url.split('/').pop();
                    uploadLabel.style.borderColor = 'var(--success)';
                } else {
                    previewContainer.style.display = 'none';
                    statusText.textContent = 'Choose Image File';
                    uploadLabel.style.borderColor = '';
                }
            }

            const modal = document.getElementById('edit-dish-modal');
            if (modal) modal.classList.add('active');
        }

        function closeEditDishModal() {
            const modal = document.getElementById('edit-dish-modal');
            if (modal) modal.classList.remove('active');
        }

        function previewEditLocalImage(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('edit-image-preview-container');
            const previewImg = document.getElementById('edit-image-preview');
            const statusText = document.getElementById('edit-upload-status');
            const uploadLabel = document.getElementById('edit-upload-label');

            if (file && previewContainer && previewImg && statusText && uploadLabel) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    previewContainer.style.display = 'block';
                    statusText.textContent = file.name;
                    statusText.style.maxWidth = '120px';
                    statusText.style.overflow = 'hidden';
                    statusText.style.textOverflow = 'ellipsis';
                    statusText.style.whiteSpace = 'nowrap';
                    uploadLabel.style.borderColor = 'var(--success)';
                }
                reader.readAsDataURL(file);
            }
        }

        async function handleEditDishSubmit(e) {
            e.preventDefault();

            const id = document.getElementById('edit-dish-id').value;
            const name = document.getElementById('edit-dish-name').value;
            const price = parseFloat(document.getElementById('edit-dish-price').value);
            const discountPriceVal = document.getElementById('edit-dish-discount-price').value;
            const discount_price = discountPriceVal ? parseFloat(discountPriceVal) : '';
            const cost_price = parseFloat(document.getElementById('edit-dish-cost').value);
            const category = document.getElementById('edit-dish-category').value;
            const imgUrl = document.getElementById('edit-dish-img').value;
            const desc = document.getElementById('edit-dish-desc').value;
            const fileInput = document.getElementById('edit-dish-file');

            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;
            }

            try {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('name', name);
                formData.append('price', price);
                formData.append('cost_price', cost_price);
                formData.append('discount_price', discount_price);
                formData.append('category', category);
                formData.append('description', desc);
                formData.append('image_url', imgUrl);
                
                if (fileInput && fileInput.files[0]) {
                    formData.append('dish_image', fileInput.files[0]);
                }

                const response = await fetch('../api/update-menu-item.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    if (window.NotificationSystem) {
                        window.NotificationSystem.toast('success', 'Recipe Updated', `Successfully updated '${name}' in catalog.`);
                    }
                    closeEditDishModal();
                    fetchMenuItems();
                } else {
                    throw new Error(result.message || 'Failed to update');
                }
            } catch (error) {
                console.error(error);
                if (window.NotificationSystem) {
                    window.NotificationSystem.toast('error', 'Update Failed', error.message || 'Error occurred while saving updates.');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Save Updates';
                }
            }
        }

        window.addEventListener('load', async () => {
            await fetchCategories();
            await fetchMenuItems();
        });
    </script>

    <!-- Categories Modal -->
    <div id="categories-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass-panel" style="width: 100%; max-width: 480px; padding: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color); position: relative; animation: modalFadeIn 0.3s ease;">
            <button type="button" onclick="closeCategoriesModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.2rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-tags" style="color: var(--primary);"></i> Manage Categories
            </h3>
            
            <!-- Add Category Inline Form -->
            <form id="add-category-form" onsubmit="handleAddCategory(event)" style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
                <input class="form-input" type="text" id="new-category-name" required placeholder="Category name (e.g. Pizza)" style="flex: 1; margin: 0; background-color: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); color: white;">
                <button type="submit" class="btn btn-primary" style="padding: 0.85rem 1.25rem; width: auto;">
                    <i class="fa-solid fa-plus"></i> Add
                </button>
            </form>

            <!-- Categories List -->
            <div style="max-height: 250px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.5rem;">
                <ul id="categories-list-ul" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                    <!-- Dynamically populated -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Edit Dish Modal -->
    <div id="edit-dish-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass-panel" style="width: 100%; max-width: 550px; padding: 2rem; background-color: var(--bg-dark-surface); border: 1px solid var(--border-color); position: relative; animation: modalFadeIn 0.3s ease;">
            <button type="button" onclick="closeEditDishModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.2rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> Edit Gourmet Dish
            </h3>
            
            <form id="edit-dish-form" onsubmit="handleEditDishSubmit(event)">
                <input type="hidden" id="edit-dish-id">
                
                <div class="form-group">
                    <label class="form-label" for="edit-dish-name">Dish Name</label>
                    <input class="form-input" type="text" id="edit-dish-name" required placeholder="e.g. Herb Crusted Ribeye" style="background-color: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); color: white;">
                </div>

                <div class="grid grid-cols-4" style="grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="edit-dish-price">Price (Tk.)</label>
                        <input class="form-input" type="number" id="edit-dish-price" step="1" required placeholder="e.g. 450" style="background-color: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); color: white;">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit-dish-discount-price">Discount (Tk.)</label>
                        <input class="form-input" type="number" id="edit-dish-discount-price" step="1" placeholder="Optional" style="background-color: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); color: white;">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit-dish-cost">Cost (Tk.)</label>
                        <input class="form-input" type="number" id="edit-dish-cost" step="1" required placeholder="e.g. 200" style="background-color: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); color: white;">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit-dish-category">Category</label>
                        <select class="form-input form-select" id="edit-dish-category" style="background-color: rgb(15, 23, 42); border: 1px solid rgba(255, 255, 255, 0.08); color: white;">
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-dish-img">Image URL (CDN / Web)</label>
                    <input class="form-input" type="url" id="edit-dish-img" placeholder="https://images.unsplash.com/..." style="background-color: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); color: white;">
                </div>

                <div class="form-group">
                    <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <span>Or Upload Local Image</span>
                        <span style="font-size: 0.75rem; color: var(--primary); font-weight: 600;">RECOMMENDED</span>
                    </label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <label class="btn btn-glass btn-sm" style="flex: 1; border: 1.5px dashed var(--border-color); cursor: pointer; padding: 0.9rem; text-align: center; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: var(--transition-fast);" id="edit-upload-label">
                            <i class="fa-solid fa-cloud-arrow-up" style="color: var(--primary); font-size: 1.1rem;"></i>
                            <span id="edit-upload-status" style="font-weight: 500;">Choose Image File</span>
                            <input type="file" id="edit-dish-file" accept="image/*" style="display: none;" onchange="previewEditLocalImage(event)">
                        </label>
                        <div id="edit-image-preview-container" style="display: none; width: 48px; height: 48px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                            <img id="edit-image-preview" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-dish-desc">Recipe Description</label>
                    <textarea class="form-input" id="edit-dish-desc" rows="3" required placeholder="Aromatic details of raw ingredients, sauces, preparation style..." style="resize: none; background-color: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); color: white;"></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-glass" onclick="closeEditDishModal()" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 2;">Save Updates</button>
                </div>
            </form>
        </div>
    </div>

    <style>
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .modal-overlay {
        display: none !important;
    }
    .modal-overlay.active {
        display: flex !important;
    }
    </style>
</body>
</html>
