DROP DATABASE IF EXISTS sqli_lab;
CREATE DATABASE sqli_lab;
USE sqli_lab;

CREATE TABLE public_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  bio TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO public_profiles (username, bio) VALUES
('alice', 'Coffee addict.'),
('bob',   'Likes blue team, learning red.'),
('cathy', 'Bug bounty beginner.'),
('dave',  'Purple team enjoyer.');

INSERT INTO users (username, password, role) VALUES
('alice', 'alice123', 'user'),
('bob',   'bob123',   'user'),
('cathy', 'cathy123', 'user'),
('dave',  'dave123',  'user'),
('admin', 'S3cr3t-P@ss', 'admin');
