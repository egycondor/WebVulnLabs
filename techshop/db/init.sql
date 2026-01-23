CREATE DATABASE IF NOT EXISTS sessionlab;
USE sessionlab;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    balance DECIMAL(10,2) DEFAULT 1000.00,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image_url VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Coupons table (for workflow bypass)
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_percent DECIMAL(5,2) NOT NULL,
    min_purchase DECIMAL(10,2) DEFAULT 0.00,
    max_discount DECIMAL(10,2) DEFAULT NULL,
    valid_from DATE NOT NULL,
    valid_until DATE NOT NULL,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insert sample users
INSERT INTO users (username, email, password, balance, role) VALUES
('admin', 'admin@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 10000.00, 'admin'),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 500.00, 'customer'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 750.00, 'customer');

-- Insert sample products
INSERT INTO products (name, description, price, stock, image_url, category) VALUES
('Wireless Headphones', 'Premium noise-cancelling wireless headphones with 30-hour battery life', 199.99, 50, 'https://via.placeholder.com/300x300?text=Headphones', 'Electronics'),
('Smart Watch', 'Feature-rich smartwatch with health tracking and notifications', 299.99, 30, 'https://via.placeholder.com/300x300?text=SmartWatch', 'Electronics'),
('Laptop Stand', 'Ergonomic aluminum laptop stand for better posture', 49.99, 100, 'https://via.placeholder.com/300x300?text=LaptopStand', 'Accessories'),
('Mechanical Keyboard', 'RGB mechanical keyboard with Cherry MX switches', 129.99, 75, 'https://via.placeholder.com/300x300?text=Keyboard', 'Electronics'),
('USB-C Hub', '7-in-1 USB-C hub with HDMI, USB 3.0, and card reader', 79.99, 60, 'https://via.placeholder.com/300x300?text=USBHub', 'Accessories'),
('Wireless Mouse', 'Ergonomic wireless mouse with precision tracking', 39.99, 120, 'https://via.placeholder.com/300x300?text=Mouse', 'Accessories'),
('Monitor 27"', '4K UHD 27-inch monitor with HDR support', 449.99, 25, 'https://via.placeholder.com/300x300?text=Monitor', 'Electronics'),
('Webcam HD', '1080p HD webcam with auto-focus and noise cancellation', 89.99, 80, 'https://via.placeholder.com/300x300?text=Webcam', 'Electronics');

-- Insert sample coupons
INSERT INTO coupons (code, discount_percent, min_purchase, max_discount, valid_from, valid_until, usage_limit, is_active) VALUES
('WELCOME10', 10.00, 50.00, 20.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 100, TRUE),
('SUMMER25', 25.00, 100.00, 50.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 50, TRUE),
('VIP50', 50.00, 200.00, 100.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 10, TRUE);
