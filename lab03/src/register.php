<?php
require_once __DIR__.'/config.php';
$msgs = [];
$ok = false;

if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  $c = $_POST['confirm'] ?? '';

  if($p !== $c){ $msgs[]="Passwords do not match."; }
  // SERVER: intentionally weak acceptance
  if(!$msgs){
    if(flawed_policy_accepts($p)){
      // Missing: blacklist check; proper complexity; etc.
      if(find_user($u)){ $msgs[]="Username already exists."; }
      else {
        $u_esc = $conn->real_escape_string($u);
        $h = weak_hash($p);
        $conn->query("INSERT INTO users (username,password_md5,pwd_changed_at) VALUES('$u_esc','$h',NOW())");
        $ok = true;
        setcookie('user', $u, 0, '/');
        header("Location: /login.php?just=registered"); exit;
      }
    } else {
      $msgs = array_merge($msgs, rule_feedback($p)); // verbose detail
    }
  }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root{--bg:#0f172a;--acc:#22d3ee;--err:#ef4444}
body{margin:0;background:linear-gradient(180deg,#0b1222,#0a0f1e);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
.wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
.card{width:100%;max-width:520px;background:linear-gradient(180deg,#0f172a,#0b1222);border:1px solid #1f2937;border-radius:20px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,.35)}
h1{margin:0 0 6px} .muted{color:#94a3b8;margin:0 0 16px}
.field{margin-bottom:14px} label{display:block;margin:0 0 6px;color:#cbd5e1;font-size:14px}
input{width:100%;padding:12px 14px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px}
input:focus{outline:none;border-color:var(--acc);box-shadow:0 0 0 3px rgba(34,211,238,.15)}
.btn{width:100%;padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:700;cursor:pointer}
.msg{margin:0 0 10px;padding:10px 12px;border-radius:12px;background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca}
a{color:#a5f3fc}
.policy{font-size:13px;color:#9aa3b7}
</style>
<script>
// CLIENT: Strong-looking policy (UI only). Easily bypassed via proxy/curl.
function checkClientPolicy(){
  const p = document.getElementById('p').value;
  const fails = [];
  if(p.length < 12) fails.push('At least 12 chars');
  if(!/[A-Z]/.test(p)) fails.push('Add uppercase');
  if(!/[a-z]/.test(p)) fails.push('Add lowercase');
  if(!/[0-9]/.test(p)) fails.push('Add digit');
  if(!/[^A-Za-z0-9]/.test(p)) fails.push('Add special');
  const box = document.getElementById('clientFails');
  box.innerHTML = fails.length ? ('Client check: ' + fails.join(', ')) : 'Client check: ✅ looks good';
}
</script>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Register</h1>
      <p class="muted">UI shows a strong policy, but the server is much laxer. <a href="/policy.php">See documented policy</a></p>
      <?php foreach($msgs as $m): ?><p class="msg"><?= htmlspecialchars($m) ?></p><?php endforeach; ?>

      <form method="POST" oninput="checkClientPolicy()">
        <div class="field"><label>Username</label><input name="username" required></div>
        <div class="field"><label>Password</label><input id="p" name="password" type="password" required></div>
        <div class="field"><label>Confirm</label><input name="confirm" type="password" required></div>
        <p id="clientFails" class="policy">Client check: —</p>
        <button class="btn" type="submit">Create Account</button>
      </form>

      <p class="policy">Already have an account? <a href="/login.php">Login</a></p>
    </div>
  </div>
</body>
</html>
