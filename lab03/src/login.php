<?php
require_once __DIR__.'/config.php';
$msg = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  $user = find_user($u);
  if($user && $user['password_md5'] === weak_hash($p)){
    setcookie('user',$u,0,'/');
    header("Location: /home.php"); exit;

  } else { $msg = "Invalid credentials."; }
}
$just = isset($_GET['just']) ? $_GET['just'] : '';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* same vibe as register */
:root{--bg:#0f172a;--acc:#22d3ee}
body{margin:0;background:linear-gradient(180deg,#0b1222,#0a0f1e);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
.wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
.card{width:100%;max-width:520px;background:linear-gradient(180deg,#0f172a,#0b1222);border:1px solid #1f2937;border-radius:20px;padding:28px}
h1{margin:0 0 6px} .muted{color:#94a3b8;margin:0 0 16px}
.field{margin-bottom:14px} label{display:block;margin:0 0 6px;color:#cbd5e1;font-size:14px}
input{width:100%;padding:12px 14px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px}
.btn{width:100%;padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:700;cursor:pointer}
.msg{margin:0 0 10px;padding:10px 12px;border-radius:12px;background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca}
a{color:#a5f3fc}
</style></head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Login</h1>
      <p class="muted">Use your account or the seeded users: <code>student / Student123!</code>, <code>guest / guest</code></p>
      <?php if($just==='registered'): ?><p class="msg" style="background:#0e1a12;border-color:#14532d;color:#bbf7d0">Registration done — please log in.</p><?php endif; ?>
      <?php if($msg): ?><p class="msg"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

      <form method="POST">
        <div class="field"><label>Username</label><input name="username" required></div>
        <div class="field"><label>Password</label><input name="password" type="password" required></div>
        <button class="btn" type="submit">Sign in</button>
      </form>

      <p class="muted" style="margin-top:12px">Forgot? <a href="/reset_request.php">Reset password</a></p>
    </div>
  </div>
</body>
</html>
