<?php
require_once __DIR__.'/config.php';
$msg = '';
$link = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  if(!find_user($u)){ $msg="If the account exists, a reset is sent."; }
  else {
    // VULN: predictable one-day code and we display the reset link directly
    $code = substr(md5('STATIC-SALT-'.$u.date('Ymd')),0,6);
    $link = "/reset_do.php?u=".urlencode($u)."&code=$code";
  }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Password Reset</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* same aesthetic */
:root{--bg:#0f172a;--acc:#22d3ee}
body{margin:0;background:linear-gradient(180deg,#0b1222,#0a0f1e);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
.wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
.card{width:100%;max-width:520px;background:linear-gradient(180deg,#0f172a,#0b1222);border:1px solid #1f2937;border-radius:20px;padding:28px}
h1{margin:0 0 6px} .muted{color:#94a3b8;margin:0 0 16px}
.field{margin-bottom:14px} label{display:block;margin:0 0 6px;color:#cbd5e1;font-size:14px}
input{width:100%;padding:12px 14px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px}
.btn{width:100%;padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:700;cursor:pointer}
.msg{margin:0 0 10px;padding:10px 12px;border-radius:12px;background:#0e1a12;border:1px solid #14532d;color:#bbf7d0}
.err{background:#1a0d10;border-color:#7f1d1d;color:#fecaca}
a{color:#a5f3fc}
</style></head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Reset Password</h1>
      <p class="muted">Predictable daily reset code. Reset link is exposed here.</p>

      <?php if($msg): ?><p class="msg err"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
      <?php if($link): ?><p class="msg">Reset link: <a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($link) ?></a></p><?php endif; ?>

      <form method="POST">
        <div class="field"><label>Username</label><input name="username" required></div>
        <button class="btn" type="submit">Send Reset</button>
      </form>

      <p class="muted" style="margin-top:12px"><a href="/login.php">Back to login</a></p>
    </div>
  </div>
</body>
</html>
