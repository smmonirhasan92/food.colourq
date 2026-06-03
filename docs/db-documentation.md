# Database Documentation & Dictionary

This document details the database schema and layout for the **Food Delivery & Real-Time Notification System**.

## Tables & Fields

### 1. `users`
Stores all account details for customers and administrators.
* `id` (INTEGER, Primary Key, Auto Increment)
* `username` (VARCHAR(50), Unique, Not Null) - User's display name and login identifier.
* `password` (VARCHAR(255), Not Null) - Securely hashed bcrypt password string.
* `email` (VARCHAR(100), Unique, Not Null) - Contact email address.
* `role` (VARCHAR(20), Default: 'customer') - Account role: `customer` or `admin`.
* `created_at` (TIMESTAMP, Default: Current Timestamp) - Account creation time.
* `updated_at` (TIMESTAMP, Default: Current Timestamp) - Account update time.

### 2. `menu_items`
Contains all premium food and beverage items offered by the restaurant.
* `id` (INTEGER, Primary Key, Auto Increment)
* `name` (VARCHAR(100), Not Null) - Food item name.
* `description` (TEXT) - Detailed food description.
* `price` (DECIMAL(10,2), Not Null) - Base price.
* `category` (VARCHAR(50), Not Null) - Category: `appetizer`, `main`, `dessert`, or `drink`.
* `image_url` (VARCHAR(255)) - Relative file path or absolute URL of the item image.
* `is_available` (TINYINT, Default: 1) - Availability flag (1 for Yes, 0 for No).
* `created_at` (TIMESTAMP, Default: Current Timestamp) - Item insertion time.

### 3. `orders`
Stores order headers including status tracking and total prices.
* `id` (INTEGER, Primary Key, Auto Increment)
* `user_id` (INTEGER, Not Null, Foreign Key to `users.id` with CASCADE delete) - Placing customer.
* `total_price` (DECIMAL(10,2), Not Null) - Calculated total order cost.
* `status` (VARCHAR(20), Default: 'pending') - Current state: `pending`, `preparing`, `ready`, `delivered`, `cancelled`.
* `delivery_address` (TEXT, Not Null) - Explicit drop-off location.
* `created_at` (TIMESTAMP, Default: Current Timestamp) - Timestamp when order was placed.
* `updated_at` (TIMESTAMP, Default: Current Timestamp) - Timestamp of last status change.

### 4. `order_items`
Stores the individual food items, quantities, and prices for each order (many-to-many relationship table).
* `id` (INTEGER, Primary Key, Auto Increment)
* `order_id` (INTEGER, Not Null, Foreign Key to `orders.id` with CASCADE delete)
* `menu_item_id` (INTEGER, Not Null, Foreign Key to `menu_items.id` with CASCADE delete)
* `quantity` (INTEGER, Not Null) - Quantity purchased.
* `price` (DECIMAL(10,2), Not Null) - Purchase price at the time of order (protects against future menu price modifications).

### 5. `notifications_log`
Tracks notifications sent to users regarding their order statuses.
* `id` (INTEGER, Primary Key, Auto Increment)
* `user_id` (INTEGER, Not Null, Foreign Key to `users.id` with CASCADE delete)
* `order_id` (INTEGER, Foreign Key to `orders.id` with SET NULL if order is deleted)
* `message` (VARCHAR(255), Not Null) - Body of the notification message.
* `is_read` (TINYINT, Default: 0) - Seen flag (0 for Unread, 1 for Read).
* `created_at` (TIMESTAMP, Default: Current Timestamp) - Log entry time.

### 6. `admin_settings`
Global system variables, configurations, and restaurant metrics.
* `setting_key` (VARCHAR(50), Primary Key) - Key identifier.
* `setting_value` (TEXT) - Configuration value.
* `updated_at` (TIMESTAMP, Default: Current Timestamp) - Last modification time.

---

## Optimized Indexes for High Performance

The schema incorporates strategic indexes to maintain ultra-fast API endpoints (target execution time: <150ms).

| Index Name | Table | Columns | Purpose |
| :--- | :--- | :--- | :--- |
| `idx_users_username` | `users` | `username` | Enhances authentication query lookup speed. |
| `idx_users_role` | `users` | `role` | Optimizes role-based filtering/analytics. |
| `idx_menu_items_category` | `menu_items` | `category` | Accelerates category-based menu retrieval. |
| `idx_menu_items_availability` | `menu_items` | `is_available` | Filters out sold-out items instantly. |
| `idx_orders_user_id` | `orders` | `user_id` | Speeds up user-centric order history retrieval. |
| `idx_orders_status` | `orders` | `status` | Enhances real-time pending order list polling for kitchen/drivers. |
| `idx_orders_created_at` | `orders` | `created_at` | Speeds up dashboard reporting and order sorting. |
| `idx_order_items_order_id` | `order_items` | `order_id` | Accelerates order details and bill breakdown queries. |
| `idx_order_items_menu_item_id` | `order_items` | `menu_item_id` | Helps in generating item popularities / sales metrics. |
| `idx_notifications_user_unread` | `notifications_log` | `user_id, is_read` | Direct composite index for super-fast active alerts polling. |
