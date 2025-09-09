<?php
require_once __DIR__.'/config.php';
$current = $_COOKIE['user'] ?? null;
if(!$current){ header("Location: /login.php"); exit; }

// VULN: no ownership check on id; accepts role from client
$id = (int)($_POST['id'] ?? 0);
$username = $conn->real_escape_string($_POST['username'] ?? '');
$full = $conn->real_escape_string($_POST['full_name'] ?? '');
$role = $conn->real_escape_string($_POST['role'] ?? 'user');

$conn->query("UPDATE users SET username='$username', full_name='$full', role='$role' WHERE id=$id LIMIT 1");

// If user changed their own role, mirror to cookie (for convenience… and vulnerability chaining)
$me = get_user_by_username($current);
if($me && $me['id']===$id){
  setcookie('user', $username, 0, '/');
  setcookie('role', $role, 0, '/');
}
header("Location: /profile.php?id=$id");
