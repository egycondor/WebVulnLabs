DROP DATABASE IF EXISTS sqli_post;
CREATE DATABASE sqli_post;
USE sqli_post;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(100) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user'
);

CREATE TABLE notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT NOT NULL,
  title VARCHAR(100),
  body TEXT,
  secret_flag VARCHAR(255) DEFAULT NULL
);

INSERT INTO users (username, password, role) VALUES
('alice', 'alice123', 'user'),
('bob', 'bob123', 'user'),
('admin', 'Summer2025!', 'admin');

INSERT INTO notes (owner_id, title, body, secret_flag) VALUES
(1, 'Welcome', 'Hello Alice!', NULL),
(1, 'Profile', 'Remember to update your profile.', NULL),
(2, 'Blue Team', 'Bob likes detections.', NULL),
(3, 'Admin Runbook', 'Only admins should see this.', 'FLAG{POST_LOGIN_SQLI_OWNED}');
