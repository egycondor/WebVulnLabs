DROP DATABASE IF EXISTS sqli_login;
CREATE DATABASE sqli_login;
USE sqli_login;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(100) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user'
);

INSERT INTO users (username, password, role) VALUES
('alice', 'alice123', 'user'),
('bob', 'bob123', 'user'),
('admin', 'SuperSecure!', 'admin');
