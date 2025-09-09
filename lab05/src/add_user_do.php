<?php
require_once __DIR__.'/config.php';
// VULN: no authorization check at all (any logged-in user can post here)
$username = $_POST['username'] ?? '';
$full = $_POST['full_name'] ?? '';
$pass = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'user';

if($username && $pass){
  $u = $conn->real_escape_string($username);
  $f = $conn->real_escape_string($full);
  $p = weak_hash($pass);
  $r = $conn->real_escape_string($role);
  $conn->query("INSERT INTO users (username,password_md5,full_name,role) VALUES('$u','$p','$f','$r')");
  header("Location: /admin.php"); exit;
}
echo "Missing fields.";
