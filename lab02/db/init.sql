DROP DATABASE IF EXISTS sessionlab;
CREATE DATABASE sessionlab;
USE sessionlab;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password_md5 CHAR(32),
  role VARCHAR(20)
);

-- guest:guest / admin:admin
INSERT INTO users (username, password_md5, role) VALUES
('guest', '084e0343a0486ff05530df6c705c8bb4', 'guest'), -- md5("guest")
('admin', '21232f297a57a5a743894a0e4a801fc3', 'admin'); -- md5("admin")