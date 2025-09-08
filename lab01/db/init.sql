DROP DATABASE IF EXISTS brutelab;
CREATE DATABASE brutelab;
USE brutelab;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  -- MD5 on purpose (weak!): echo -n 'password' | md5sum
  password_md5 CHAR(32),
  full_name VARCHAR(100)
);

INSERT INTO users (username, password_md5, full_name) VALUES
('admin',  '5f4dcc3b5aa765d61d8327deb882cf99', 'Admin User'),      -- password
('ahmed',  'e10adc3949ba59abbe56e057f20f883e', 'Ahmed Nosir'),    -- 123456
('guest',  '084e0343a0486ff05530df6c705c8bb4', 'Guest Account');  -- guest
