<?php
error_reporting(0);
$mysqli = @new mysqli('db','sqli','sqli','sqli_post');
if($mysqli->connect_error){ die('DB error'); }

$u = $_POST['username'] ?? '';
$p = $_POST['password'] ?? '';

// Basic safe login (prepared) – so the SQLi is NOT here.
$stmt = $mysqli->prepare("SELECT id, username, role FROM users WHERE username=? AND password=? LIMIT 1");
$stmt->bind_param("ss", $u, $p);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()){
  setcookie('user', $row['username'], 0, '/');
  setcookie('uid', $row['id'], 0, '/');
  setcookie('role', $row['role'], 0, '/');
  header("Location: /dashboard.php"); exit;
}
header("Location: /index.php"); exit;
