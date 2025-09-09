<?php
require_once __DIR__.'/config.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=$_POST['username']??''; $p=$_POST['password']??'';
  $user=get_user_by_username($u);
  if($user && $user['password_md5']===weak_hash($p)){
    setcookie('user', $user['username'], 0, '/');
    // VULN: role is mirrored to a client-controlled cookie and later trusted
    setcookie('role', $user['role'], 0, '/');
    header("Location: /home.php"); exit;
  }
  $msg="Invalid credentials.";
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title>
<style>
body{margin:0;background:#0b1222;color:#e5e7eb;font-family:system-ui}
.wrap{display:grid;min-height:100dvh;place-items:center}
.card{width:100%;max-width:420px;background:#0f172a;border:1px solid #1f2937;border-radius:16px;padding:24px;margin:30px}
label{display:block;margin:8px 0 6px}input{width:100%;padding:11px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px}
button{margin-top:12px;width:100%;padding:12px;border:0;border-radius:12px;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:700}
.msg{background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca;padding:10px;border-radius:12px;margin-bottom:10px}
</style></head>
<body><div class="wrap"><div class="card">
<h2>Login</h2>
<?php if($msg): ?><p class="msg"><?=htmlspecialchars($msg)?></p><?php endif; ?>
<form method="POST">
  <label>Username</label><input name="username" placeholder="alice">
  <label>Password</label><input name="password" type="password" placeholder="alice123">
  <button type="submit">Sign in</button>
</form>
<p style="margin-top:10px;color:#94a3b8">Users: admin/admin123, alice/alice123, bob/bob123</p>
</div></div></body></html>
