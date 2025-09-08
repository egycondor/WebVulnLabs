<?php
$u = $_COOKIE['user'] ?? null;
if (!$u) { header("Location: /login.php"); exit; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{--bg:#0f172a;--acc:#22d3ee}
    body{margin:0;background:linear-gradient(180deg,#0b1222,#0a0f1e);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
    .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:820px;background:linear-gradient(180deg,#0f172a,#0b1222);border:1px solid #1f2937;border-radius:20px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,.35)}
    .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
    .pill{padding:6px 10px;border-radius:999px;background:#0b1324;border:1px solid #243046;font-size:12px}
    .grid{display:grid;gap:14px;grid-template-columns:1fr 1fr}
    .panel{border:1px solid #263046;background:#0b1324;border-radius:16px;padding:16px}
    a{color:#a5f3fc;text-decoration:none} a:hover{text-decoration:underline}
    .btn{display:inline-block;margin-top:8px;padding:10px 12px;border-radius:10px;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:600}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="top">
        <h2>Welcome, <?= htmlspecialchars($u) ?></h2>
        <span class="pill">Authenticated</span>
      </div>

      <div class="grid">
        <div class="panel">
          <h3>Account</h3>
          <p>Manage your credentials and security settings.</p>
          <a class="btn" href="/change_password.php">Change Password</a>
        </div>
        <div class="panel">
          <h3>Support</h3>
          <p>Lost access? Request a reset link (training flow).</p>
          <a class="btn" href="/reset_request.php">Reset Password</a>
        </div>
      </div>

      <div class="panel" style="margin-top:14px">
        <h3>Navigation</h3>
        <p>
          <a href="/policy.php">Password Policy (documented)</a> ·
          <a href="/login.php">Login</a> ·
          <a href="/logout.php">Logout</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
