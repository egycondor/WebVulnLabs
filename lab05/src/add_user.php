<?php
require_once __DIR__.'/config.php';
$user = $_COOKIE['user'] ?? null;
if(!$user){ header("Location: /login.php"); exit; }
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Add User</title></head>
<body style="font-family:system-ui;background:#0b1222;color:#e5e7eb">
<div style="max-width:700px;margin:40px auto;padding:24px;border:1px solid #1f2937;border-radius:16px;background:#0f172a">
<h2>Add User</h2>
<!-- VULN: page is linked from admin UI, but no server-side role check here or in the action -->
<form method="POST" action="/add_user_do.php">
  <p><label>Username <input name="username"></label></p>
  <p><label>Full name <input name="full_name"></label></p>
  <p><label>Password <input name="password" type="password"></label></p>
  <p><label>Role
    <select name="role">
      <option value="user">user</option>
      <option value="admin">admin</option>
    </select>
  </label></p>
  <p><button type="submit">Create</button></p>
</form>
<p><a href="/home.php">Home</a></p>
</div></body></html>
