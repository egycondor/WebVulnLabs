<?php $role = $_COOKIE['role'] ?? 'guest'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Session Lab – Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root { --bg:#0f172a; --card:#111827; --muted:#94a3b8; --acc:#22d3ee; --ok:#22c55e; }
    body{margin:0;font-family:system-ui,Segoe UI,Roboto,Ubuntu,sans-serif;background:linear-gradient(180deg,#0b1222,#0a0f1e),var(--bg);color:#e5e7eb}
    .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:820px;background:linear-gradient(180deg,#0f172a,#0b1222);border:1px solid #1f2937;border-radius:20px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,.35)}
    .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    .pill{padding:6px 10px;border-radius:999px;background:#0b1324;border:1px solid #243046;font-size:12px}
    .ok{background:rgba(34,197,94,.15);border-color:#14532d;color:#86efac}
    .grid{display:grid;gap:14px;grid-template-columns:1fr}
    .panel{border:1px solid #263046;background:#0b1324;border-radius:16px;padding:16px}
    a{color:#a5f3fc;text-decoration:none} a:hover{text-decoration:underline}
    .btn{display:inline-block;margin-top:8px;padding:10px 12px;border-radius:10px;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:600}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="top">
        <h2>Welcome</h2>
        <span class="pill">Role: <b><?= htmlspecialchars($role) ?></b></span>
      </div>

      <div class="grid">
        <?php if ($role === 'admin'): ?>
          <div class="panel">
            <h3>Admin Panel</h3>
            <p>Secret: <code>FLAG-ADMIN-123</code></p>
          </div>
        <?php else: ?>
          <div class="panel">
            <h3>Standard User</h3>
            <p>You are logged in as a regular user.</p>
            <p style="color:#9aa3b7">Try looking at your cookies…</p>
          </div>
        <?php endif; ?>

        <div class="panel">
          <h3>Actions</h3>
          <a class="btn" href="/logout.php">Logout</a>
          <a style="margin-left:8px" href="/login.php">Back to Login</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>