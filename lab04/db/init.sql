DROP DATABASE IF EXISTS mfalab;
CREATE DATABASE mfalab;
USE mfalab;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password_md5 CHAR(32)
);

CREATE TABLE sessions (
  sid CHAR(32) PRIMARY KEY,
  username VARCHAR(50),
  otp_code CHAR(6),
  mfa_ok TINYINT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password_md5) VALUES
('victim', MD5('password'));
