<?php
require_once __DIR__ . '/config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $sql = "SELECT * FROM users WHERE username='" . $conn->real_escape_string($username) . "' LIMIT 1";
  $res = $conn->query($sql);
  if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    if (weak_hash($password) === $row['password_md5']) {
      // VULNERABLE: role stored directly in cookie, no integrity
      setcookie("role", $row['role'], 0, "/");
      header("Location: /home.php"); exit;
    }
  }
  $msg = "Invalid credentials.";
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Session Lab – Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root { --bg:#0f172a; --card:#111827; --muted:#94a3b8; --acc:#22d3ee; --err:#ef4444; --ok:#22c55e; }
    *{box-sizing:border-box} body{margin:0;font-family:system-ui,Segoe UI,Roboto,Ubuntu,sans-serif;background:radial-gradient(1200px 600px at 20% -10%,#1f2937,transparent),linear-gradient(180deg,#0b1222,#0a0f1e),var(--bg);color:#e5e7eb}
    .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:420px;background:linear-gradient(180deg,#0f172a,#0b1222);border:1px solid #1f2937;border-radius:20px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,.35)}
    h1{font-size:22px;margin:0 0 16px;letter-spacing:.4px}
    .muted{color:var(--muted);font-size:14px;margin:0 0 18px}
    .field{margin-bottom:14px}
    label{display:block;font-size:13px;color:#cbd5e1;margin-bottom:6px}
    input{width:100%;padding:12px 14px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px;outline:none}
    input:focus{border-color:var(--acc);box-shadow:0 0 0 3px rgba(34,211,238,.15)}
    .btn{width:100%;padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:600;cursor:pointer}
    .btn:active{transform:translateY(1px)}
    .note{margin-top:14px;font-size:13px;color:#a8b1c3}
    .msg{margin:0 0 12px;padding:10px 12px;border-radius:12px;background:#1a0d10;color:#fecaca;border:1px solid #7f1d1d}
    .foot{margin-top:18px;display:flex;justify-content:space-between;font-size:12px;color:#9aa3b7}
    a{color:#a5f3fc;text-decoration:none} a:hover{text-decoration:underline}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>🔐 Session Lab</h1>
      <p class="muted">Log in as <b>guest:guest</b>.</p>

      <?php if ($msg): ?><p class="msg"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

      <form method="POST" autocomplete="off">
        <div class="field">
          <label>Username</label>
          <input name="username" type="text" placeholder="guest">
        </div>
        <div class="field">
          <label>Password</label>
          <input name="password" type="password" placeholder="guest">
        </div>
        <button class="btn" type="submit">Sign in</button>
      </form>

      <p class="note">No registration. Use provided creds.</p>
      <div class="foot"><span>© Lab</span><a href="/home.php">Home</a></div>
    </div>
  </div>
</body>
</html>