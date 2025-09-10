DROP DATABASE IF EXISTS shop;
CREATE DATABASE shop;
USE shop;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(8,2) NOT NULL,
  description VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(100) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user'
);

CREATE TABLE credit_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  holder VARCHAR(100),
  card_no VARCHAR(32),
  cvv VARCHAR(8),
  expiry VARCHAR(10)
);

INSERT INTO products (name, price, description) VALUES
('Red Mug', 9.99, 'Ceramic mug with matte finish'),
('Blue Hoodie', 39.90, 'Soft cotton hoodie'),
('USB-C Cable', 7.50, '1m braided cable'),
('Wireless Mouse', 22.00, '2.4G ergonomic mouse'),
('Laptop Stand', 29.00, 'Aluminum portable stand');

INSERT INTO users (username, password, role) VALUES
('alice','alice123','user'),
('bob','bob123','user'),
('admin','AdminPass!','admin');

INSERT INTO credit_cards (holder, card_no, cvv, expiry) VALUES
('Alice Smith','4111111111111111','123','12/26'),
('Bob Jones','4000056655665556','456','10/27'),
('Admin User','5555555555554444','999','01/28');
