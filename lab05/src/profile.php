<?php
require_once __DIR__.'/config.php';
$current = $_COOKIE['user'] ?? null;
if(!$current){ header("Location: /login.php"); exit; }

// VULN: trusts arbitrary id= to fetch profile (IDOR)
$id = $_GET['id'] ?? 0;
$target = get_user_by_id($id);
if(!$target){ http_response_code(404); echo "Not found"; exit; }
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Profile</title></head>
<body style="font-family:system-ui;background:#0b1222;color:#e5e7eb">
<div style="max-width:700px;margin:40px auto;padding:24px;border:1px solid #1f2937;border-radius:16px;background:#0f172a">
<h2>Profile #<?= (int)$target['id'] ?></h2>
<form method="POST" action="/update_profile.php">
  <input type="hidden" name="id" value="<?= (int)$target['id'] ?>">
  <p><label>Username <input name="username" value="<?= htmlspecialchars($target['username']) ?>"></label></p>
  <p><label>Full name <input name="full_name" value="<?= htmlspecialchars($target['full_name']) ?>"></label></p>
  <!-- VULN: hidden role field can be tampered to escalate -->
  <input type="hidden" name="role" value="<?= htmlspecialchars($target['role']) ?>">
  <p><button type="submit">Save</button></p>
</form>
<p><a href="/home.php">Home</a></p>
</div></body></html>
