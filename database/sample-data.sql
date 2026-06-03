-- Food Delivery & Real-Time Notification System Sample Data

-- 1. Seed Users (1 Admin, 4 Customers)
-- Password for all seed users is 'password' (hashed using PASSWORD_DEFAULT / bcrypt)
INSERT INTO users (username, password, email, phone, role, created_at, updated_at) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@fooddelivery.com', '+15550100', 'admin', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('john_doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'john@example.com', '+15550101', 'customer', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('jane_smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jane@example.com', '+15550102', 'customer', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('alex_jones', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alex@example.com', '+15550103', 'customer', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('sarah_connor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sarah@example.com', '+15550104', 'customer', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 2. Seed 13 Premium Menu Items
INSERT INTO menu_items (name, description, price, category, image_url, is_available, created_at) VALUES
-- Appetizers
('Truffle Garlic Sourdough', 'Toasted sourdough bread baked with roasted garlic, fragrant black truffle butter, and melted fresh mozzarella cheese.', 420.00, 'appetizer', 'images/truffle_garlic_bread.jpg', 1, CURRENT_TIMESTAMP),
('Crispy Calamari', 'Tender calamari rings coated in seasoned flour, fried golden-brown, served with spicy garlic aioli.', 580.00, 'appetizer', 'images/crispy_calamari.jpg', 1, CURRENT_TIMESTAMP),
('Stuffed Cremini Mushrooms', 'Fresh cremini mushrooms stuffed with sweet Italian sausage, aromatic herbs, cream cheese, and parmesan.', 520.00, 'appetizer', 'images/stuffed_mushrooms.jpg', 1, CURRENT_TIMESTAMP),
('Tomato Bruschetta', 'Roma tomatoes mixed with fresh garlic, basil, and extra virgin olive oil, served on grilled baguette slices.', 350.00, 'appetizer', 'images/tomato_bruschetta.jpg', 1, CURRENT_TIMESTAMP),

-- Mains
('Filet Mignon', 'An 8-ounce premium tenderloin beef steak grilled to perfection, served with truffle mashed potatoes and red wine reduction sauce.', 2450.00, 'main', 'images/filet_mignon.jpg', 1, CURRENT_TIMESTAMP),
('Pan-Seared Salmon', 'Crispy-skin Atlantic salmon fillet served with lemon herb quinoa, tender asparagus, and citrus butter sauce.', 1850.00, 'main', 'images/seared_salmon.jpg', 1, CURRENT_TIMESTAMP),
('Wild Mushroom Risotto', 'Creamy Arborio rice slowly cooked in rich broth with mixed wild mushrooms and a drizzle of white truffle oil.', 1250.00, 'main', 'images/mushroom_risotto.jpg', 1, CURRENT_TIMESTAMP),
('Butter Chicken with Naan', 'Tender tandoori chicken cooked in a rich, velvety spiced tomato-butter sauce, served with hot garlic naan.', 850.00, 'main', 'images/butter_chicken.jpg', 1, CURRENT_TIMESTAMP),
('Classic Wagyu Burger', 'A juicy half-pound Wagyu beef patty with caramelized onions, aged white cheddar, butter lettuce, and truffle mayo on a brioche bun.', 980.00, 'main', 'images/wagyu_burger.jpg', 1, CURRENT_TIMESTAMP),

-- Desserts
('New York Cheesecake', 'Rich and creamy cheesecake baked on a buttery graham cracker crust, finished with fresh strawberry compote.', 450.00, 'dessert', 'images/cheesecake.jpg', 1, CURRENT_TIMESTAMP),

-- Drinks
('Fresh Mango Smoothie', 'A delicious blend of sweet Alphonso mangoes, organic honey, Greek yogurt, and a touch of fresh mint.', 320.00, 'drink', 'images/mango_smoothie.jpg', 1, CURRENT_TIMESTAMP),
('Iced Caramel Macchiato', 'Bold espresso poured over cold milk, vanilla syrup, and ice, finished with a sweet caramel drizzle.', 380.00, 'drink', 'images/iced_macchiato.jpg', 1, CURRENT_TIMESTAMP),
('Sparkling Virgin Mojito', 'A refreshing mix of fresh lime juice, mint leaves, cane sugar, topped with sparkling club soda and crushed ice.', 280.00, 'drink', 'images/virgin_mojito.jpg', 1, CURRENT_TIMESTAMP);

-- 3. Seed Admin Settings
INSERT INTO admin_settings (setting_key, setting_value, updated_at) VALUES
('restaurant_status', 'open', CURRENT_TIMESTAMP),
('estimated_delivery_time', '30-45 mins', CURRENT_TIMESTAMP),
('delivery_fee', '60.00', CURRENT_TIMESTAMP),
('minimum_order_value', '250.00', CURRENT_TIMESTAMP);
