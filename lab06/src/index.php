<?php
error_reporting(0);
$mysqli = @new mysqli('db','sqli','sqli','sqli_login');
if($mysqli->connect_error){ die('DB error'); }

$msg = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';

  // VULNERABLE: direct string concatenation (classic login bypass)
  $sql = "SELECT id, username, role FROM users WHERE username = '$u' AND password = '$p' LIMIT 1";
  $res = $mysqli->query($sql);
  if(!$res){
    $msg = '<p class="msg bad"><b>SQL Error:</b> '.htmlspecialchars($mysqli->error).'</p><pre class="msg bad">'.htmlspecialchars($sql).'</pre>';
  } else if ($row = $res->fetch_assoc()){
    setcookie('user', $row['username'], 0, '/');
    setcookie('role', $row['role'], 0, '/');
    header('Location: /home.php'); exit;
  } else {
    $msg = '<p class="msg bad">Invalid credentials</p>';
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>SQLi – Login Bypass</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{--bg:#0b1222;--panel:#0f172a;--border:#1f2937;--muted:#94a3b8;--acc1:#06b6d4;--acc2:#22d3ee}
    *{box-sizing:border-box} body{margin:0;background:var(--bg);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
    .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:520px;background:var(--panel);border:1px solid var(--border);border-radius:18px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    h1{margin:0 0 6px} .muted{color:var(--muted);margin:0 0 16px}
    label{display:block;margin:10px 0 6px;color:#cbd5e1}
    input{width:100%;padding:12px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px}
    button{width:100%;padding:12px;border-radius:12px;border:0;background:linear-gradient(90deg,var(--acc1),var(--acc2));color:#031321;font-weight:700;margin-top:12px}
    .msg{padding:10px;border-radius:12px;margin:10px 0}
    .bad{background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca}
    .ok{background:#0f2b20;border:1px solid #16a34a;color:#bbf7d0}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#0b1324;border:1px solid #243046;color:#cbd5e1;font-size:12px}
    code{background:#0b1324;border:1px solid #243046;border-radius:8px;padding:2px 6px}
  </style>
</head>
<body>
<div class="wrap"><div class="card">
  <h1>Login</h1>
  <p class="muted">This login is <b>intentionally</b> vulnerable to SQL injection. Bypass it.</p>
  <?php if($msg) echo $msg; ?>
  <form method="POST" autocomplete="off">
    <label>Username</label>
    <input name="username" placeholder="alice">
    <label>Password</label>
    <input name="password" type="password" placeholder="alice123">
    <button type="submit">Sign in</button>
  </form>
  <p class="muted" style="margin-top:10px">Try payloads like <code>' OR '1'='1 -- -</code> or alter just one field.</p>
</div></div>
</body>
</html>
