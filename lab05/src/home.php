<?php
require_once __DIR__.'/config.php';
$user = $_COOKIE['user'] ?? null;
$role = $_COOKIE['role'] ?? 'user';
if(!$user){ header("Location: /login.php"); exit; }
$me = get_user_by_username($user);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Home</title>
<style>
body{margin:0;background:#0b1222;color:#e5e7eb;font-family:system-ui}
.main{max-width:900px;margin:40px auto;padding:24px;border:1px solid #1f2937;border-radius:16px;background:#0f172a}
a{color:#a5f3fc}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.panel{border:1px solid #263046;background:#0b1324;border-radius:14px;padding:16px}
.pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#0b1324;border:1px solid #243046}
</style></head>
<body>
<div class="main">
  <h2>Welcome, <?=htmlspecialchars($me['full_name'])?></h2>
  <p>Username: <b><?=htmlspecialchars($me['username'])?></b> · Role (cookie): <span class="pill"><?=htmlspecialchars($role)?></span></p>

  <div class="grid">
    <div class="panel">
      <h3>Profile</h3>
      <p><a href="/profile.php?id=<?= (int)$me['id'] ?>">View / Edit your profile</a></p>
      <p><a href="/download.php?file=invoice_<?= (int)$me['id'] ?>.pdf">Download your invoice</a></p>
    </div>
    <div class="panel">
      <h3>Support</h3>
      <p><a href="/reset_request.php">Password reset</a></p>
      <p><a href="/logout.php">Logout</a></p>
    </div>
  </div>

  <div class="panel" style="margin-top:16px">
    <h3>Admin Area</h3>
    <p><a href="/admin.php">Admin dashboard</a> · <a href="/add_user.php">Add user</a></p>
    <p style="color:#94a3b8">(* training note: admin pages trust the <code>role</code> cookie)</p>
  </div>
</div>
</body></html>
