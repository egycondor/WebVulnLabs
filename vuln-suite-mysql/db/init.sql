DROP DATABASE IF EXISTS vwapp;
CREATE DATABASE vwapp;
USE vwapp;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255), -- intentionally plaintext for training
  email VARCHAR(120),
  role ENUM('user','admin') DEFAULT 'user'
);

INSERT INTO users (username,password,email,role) VALUES
('alice','alice123','alice@example.com','user'),
('bob','bob123','bob@example.com','user'),
('admin','admin','admin@example.com','admin');

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120),
  price DECIMAL(10,2),
  stock INT DEFAULT 0,
  description TEXT
);

INSERT INTO products (name,price,stock,description) VALUES
('Ceramic Mug',9.99,50,'Sturdy mug for hot beverages'),
('Graphic Tee',19.50,35,'Cotton T-shirt with logo'),
('Zip Hoodie',39.00,20,'Warm and comfy hoodie');

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  total DECIMAL(10,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  qty INT,
  price DECIMAL(10,2)
);

INSERT INTO orders(user_id,total) VALUES (1,50.00),(2,99.00),(3,0.00);
INSERT INTO order_items(order_id,product_id,qty,price) VALUES
(1,1,2,9.99),(1,2,1,19.50),
(2,3,2,39.00),
(3,1,1,0.00);

CREATE TABLE notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  body TEXT
);
INSERT INTO notes(user_id,body) VALUES
(1,'Welcome Alice'),
(2,'Bob private note'),
(3,'Admin secret: FLAG{IDOR_DEMO}');

