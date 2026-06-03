# Food Delivery & Real-Time Notification API Endpoints

This folder contains the API endpoints for Week 3 & 4. All endpoints accept and return standard JSON.

## Core API Endpoints Map

* `GET api/get-menu.php` - Fetches the list of active food and beverage items.
* `POST api/place-order.php` - Places a new food order with multiple items.
* `GET api/get-order-status.php` - Gets the real-time status of a specific order.
* `GET api/check-new-orders.php` - Used by administration to check for newly incoming orders (long polling / real-time updates).
* `POST api/update-order-status.php` - Updates the state of an active order (kitchen / delivery status changes).
* `GET api/get-pending-orders.php` - Retrieves a list of active non-delivered orders for driver/kitchen view.

---

## Technical Features

1. **Security**: Prepared PDO statements, input sanitization via HTML and length validation, CORS verification.
2. **Real-time Engine**: Designed to support fast queries (<150ms execution) utilizing indexed columns.
3. **Flexible Architecture**: Runs dynamically on local SQLite (`database/food_delivery.db`) or production-level MySQL systems.
