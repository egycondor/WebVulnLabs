<?php
require_once __DIR__ . '/config.php';
$conn->query("TRUNCATE TABLE users");
$conn->query("INSERT INTO users (username, password_md5, full_name) VALUES
('admin','5f4dcc3b5aa765d61d8327deb882cf99','Admin User'),
('ahmed','e10adc3949ba59abbe56e057f20f883e','Ahmed Nosir'),
('guest','084e0343a0486ff05530df6c705c8bb4','Guest Account')
");
echo "Reset done.";