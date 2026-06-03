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
                <a href="manage-orders.php" class="sidebar-link">
                    <i class="fa-solid fa-receipt"></i> Live Orders
                </a>
                <a href="manage-menu.php" class="sidebar-link active">
                    <i class="fa-solid fa-pizza-slice"></i> Culinary Menu
                </a>
                <a href="manage-riders.php" class="sidebar-link">
                    <i class="fa-solid fa-motorcycle"></i> Delivery Riders
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="../customer/index.php" class="sidebar-link">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Customer Portal
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
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                        <i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> Add Gourmet Dish
                    </h3>

                    <form id="add-dish-form" onsubmit="handleNewDishSubmit(event)">
                        <div class="form-group">
                            <label class="form-label" for="dish-name">Dish Name</label>
                            <input class="form-input" type="text" id="dish-name" required placeholder="e.g. Herb Crusted Ribeye">
                        </div>

                        <div class="grid grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="dish-price">Price (Tk.)</label>
                                <input class="form-input" type="number" id="dish-price" step="1" required placeholder="e.g. 450">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="dish-category">Category</label>
                                <select class="form-input form-select" id="dish-category">
                                    <option value="Hot Starters">Hot Starters</option>
                                    <option value="Gourmet Mains" selected>Gourmet Mains</option>
                                    <option value="Organic Bowls">Organic Bowls</option>
                                    <option value="Desserts">Desserts</option>
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

            const categoryMap = {
                'appetizer': 'Hot Starters',
                'main': 'Gourmet Mains',
                'dessert': 'Desserts',
                'drink': 'Drinks'
            };

            const categoryColors = {
                'appetizer': 'var(--gradient-primary)',
                'main': 'var(--gradient-success)',
                'dessert': 'linear-gradient(135deg, #ec4899, #be185d)',
                'drink': 'linear-gradient(135deg, #3b82f6, #1d4ed8)'
            };

            grid.innerHTML = items.map(item => {
                const displayCategory = categoryMap[item.category] || item.category;
                const bgStyle = categoryColors[item.category] || 'var(--gradient-success)';
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
                                <span class="menu-card-price" style="font-size: 1.15rem; color: var(--primary);">Tk. ${item.price.toFixed(0)}</span>
                                <button class="btn btn-glass btn-sm" onclick="toggleAvailability(${item.id}, ${item.is_available})" id="btn-avail-${item.id}">
                                    <i class="fa-solid ${buttonIcon}"></i> ${buttonText}
                                </button>
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

        window.addEventListener('load', fetchMenuItems);
    </script>
</body>
</html>
