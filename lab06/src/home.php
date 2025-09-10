<?php
error_reporting(0);
$user = $_COOKIE['user'] ?? null;
$role = $_COOKIE['role'] ?? null;
if(!$user){ header('Location: /index.php'); exit; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{--bg:#0b1222;--panel:#0f172a;--border:#1f2937;--muted:#94a3b8;--acc1:#06b6d4;--acc2:#22d3ee}
    *{box-sizing:border-box} body{margin:0;background:var(--bg);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
    .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:700px;background:var(--panel);border:1px solid var(--border);border-radius:18px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#0b1324;border:1px solid #243046;color:#cbd5e1;font-size:12px}
    a{color:#a5f3fc;text-decoration:none}
    .ok{background:#0f2b20;border:1px solid #16a34a;color:#bbf7d0;padding:10px;border-radius:12px;margin-top:12px}
  </style>
</head>
<body>
<div class="wrap"><div class="card">
  <h2>Welcome, <span class="pill"><?php echo htmlspecialchars($user); ?></span></h2>
  <p>Your role: <span class="pill"><?php echo htmlspecialchars($role); ?></span></p>
  <?php if($role==='admin'): ?>
    <div class="ok">FLAG: <b>FLAG{SQLI_LOGIN_BYPASS_PWNED}</b></div>
  <?php else: ?>
    <p class="muted">No flag for non-admins. Try logging in as admin… or bypassing the check.</p>
  <?php endif; ?>
  <p style="margin-top:12px"><a href="/logout.php">Logout</a></p>
</div></div>
</body>
</html>
