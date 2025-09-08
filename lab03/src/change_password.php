<?php
require_once __DIR__.'/config.php';
$u = $_COOKIE['user'] ?? null;
if(!$u){ header("Location: /login.php"); exit; }
$msgs=[]; $ok=false;

if($_SERVER['REQUEST_METHOD']==='POST'){
  $cur = $_POST['current'] ?? '';
  $new = $_POST['new'] ?? '';
  $conf = $_POST['confirm'] ?? '';
  $user = find_user($u);

  if(!$user || $user['password_md5']!==weak_hash($cur)){
    $msgs[]="Current password incorrect.";
  } else if($new!==$conf){
    $msgs[]="New password and confirmation do not match.";
  } else {
    // VERBOSE feedback tells exactly what's missing
    $fb = rule_feedback($new);
    if(!flawed_policy_accepts($new)){
      $msgs = array_merge($msgs, $fb);
    } else {
      // VULN: allows reuse of the current password (no history checks)
      set_password($u,$new);
      $ok=true;
    }
  }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Change Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root{--bg:#0f172a;--acc:#22d3ee}
body{margin:0;background:linear-gradient(180deg,#0b1222,#0a0f1e);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
.wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
.card{width:100%;max-width:620px;background:linear-gradient(180deg,#0f172a,#0b1222);border:1px solid #1f2937;border-radius:20px;padding:28px}
h1{margin:0 0 6px} .muted{color:#94a3b8;margin:0 0 16px}
.field{margin-bottom:14px} label{display:block;margin:0 0 6px;color:#cbd5e1;font-size:14px}
input{width:100%;padding:12px 14px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px}
.btn{padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:700;cursor:pointer}
.msg{margin:0 0 10px;padding:10px 12px;border-radius:12px;background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca}
.ok{background:#0e1a12;border-color:#14532d;color:#bbf7d0}
a{color:#a5f3fc}
</style></head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Change Password</h1>
      <p class="muted">Logged in as <b><?= htmlspecialchars($u) ?></b>. Server accepts OR-based complexity and allows reuse.</p>

      <?php if($ok): ?><p class="msg ok">Password updated.</p><?php endif; ?>
      <?php foreach($msgs as $m): ?><p class="msg"><?= htmlspecialchars($m) ?></p><?php endforeach; ?>

      <form method="POST">
        <div class="field"><label>Current password</label><input name="current" type="password" required></div>
        <div class="field"><label>New password</label><input name="new" type="password" required></div>
        <div class="field"><label>Confirm</label><input name="confirm" type="password" required></div>
        <button class="btn" type="submit">Update</button>
      </form>

      <p class="muted" style="margin-top:12px"><a href="/login.php">Back</a></p>
    </div>
  </div>
</body>
</html>
