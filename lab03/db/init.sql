DROP DATABASE IF EXISTS pwlab;
CREATE DATABASE pwlab;
USE pwlab;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password_md5 CHAR(32),
  pwd_changed_at DATETIME
);

-- Demo users
INSERT INTO users (username, password_md5, pwd_changed_at) VALUES
('student', MD5('Student123!'), NOW()),
('guest', MD5('guest'), NOW());
