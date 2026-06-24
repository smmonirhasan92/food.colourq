-- Food Delivery & Real-Time Notification System Schema

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    role VARCHAR(20) NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 1.5 Delivery Men Table
CREATE TABLE IF NOT EXISTS delivery_men (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status VARCHAR(20) DEFAULT 'available', -- 'available', 'busy', 'offline'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- 2. Menu Items Table
CREATE TABLE IF NOT EXISTS menu_items (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2) NULL DEFAULT NULL,
    category VARCHAR(50) NOT NULL, -- 'appetizer', 'main', 'dessert', 'drink'
    image_url VARCHAR(255),
    is_available TINYINT DEFAULT 1,
    is_deleted TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    total_price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending', -- 'pending', 'preparing', 'ready', 'delivered', 'cancelled'
    delivery_address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    is_notified TINYINT DEFAULT 0,
    notification_sent_at TIMESTAMP NULL,
    confirmed_at TIMESTAMP NULL,
    prepared_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    dispute_status VARCHAR(20) NULL,
    dispute_category VARCHAR(50) NULL,
    dispute_description TEXT NULL,
    dispute_reported_at TIMESTAMP NULL,
    feedback_rating INTEGER NULL,
    feedback_comment TEXT NULL,
    delivery_man_id INTEGER NULL,
    order_type VARCHAR(20) DEFAULT 'online', -- 'online' or 'pos'
    discount_percent DECIMAL(5, 2) DEFAULT 0.00,
    discount_amount DECIMAL(10, 2) DEFAULT 0.00,
    mfs_sender_number VARCHAR(20) NULL,
    mfs_transaction_id VARCHAR(50) NULL,
    payment_method VARCHAR(30) DEFAULT 'cod',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- 4. Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    order_id INTEGER NOT NULL,
    menu_item_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items (id) ON DELETE CASCADE
);

-- 5. Notifications Log Table
CREATE TABLE IF NOT EXISTS notifications_log (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    order_id INTEGER,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE SET NULL
);

-- 6. Admin Settings Table
CREATE TABLE IF NOT EXISTS admin_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optimized Indexes for Performance
CREATE INDEX idx_users_username ON users (username);
CREATE INDEX idx_users_role ON users (role);

CREATE INDEX idx_menu_items_category ON menu_items (category);
CREATE INDEX idx_menu_items_availability ON menu_items (is_available);

CREATE INDEX idx_orders_user_id ON orders (user_id);
CREATE INDEX idx_orders_order_number ON orders (order_number);
CREATE INDEX idx_orders_status ON orders (status);
CREATE INDEX idx_orders_is_notified ON orders (is_notified);
CREATE INDEX idx_orders_created_at ON orders (created_at);

CREATE INDEX idx_order_items_order_id ON order_items (order_id);
CREATE INDEX idx_order_items_menu_item_id ON order_items (menu_item_id);

CREATE INDEX idx_notifications_user_unread ON notifications_log (user_id, is_read);
