DROP DATABASE IF EXISTS privlab;
CREATE DATABASE privlab;
USE privlab;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password_md5 CHAR(32),
  full_name VARCHAR(100),
  role VARCHAR(20) DEFAULT 'user'
);

CREATE TABLE invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  filename VARCHAR(100)
);

INSERT INTO users (username, password_md5, full_name, role) VALUES
('admin',  MD5('admin123'),   'Admin User', 'admin'),
('alice',  MD5('alice123'),   'Alice Smith','user'),
('bob',    MD5('bob123'),     'Bob Jones',  'user');

INSERT INTO invoices (user_id, filename) VALUES
(2, 'invoice_2.pdf'),
(3, 'invoice_3.pdf');
