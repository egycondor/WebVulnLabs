<?php
require_once __DIR__.'/config.php';
$sess = get_session();
if(!$sess){ header("Location: /login.php"); exit; }
if(!$sess['mfa_ok']){ header("Location: /verify.php"); exit; } // no bypass

?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>MFA Lab – Home</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{margin:0;background:#0b1222;color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
.wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
.card{width:100%;max-width:720px;background:#0f172a;border:1px solid #1f2937;border-radius:16px;padding:24px}
a{color:#a5f3fc}
</style>
</head>
<body>
<div class="wrap"><div class="card">
  <h2>Welcome, <?=htmlspecialchars($sess['username'])?></h2>
  <p>2FA passed ✅</p>
  <p>FLAG-MFA-OTP-BF</p>
  <p><a href="/logout.php">Logout</a></p>
</div></div>
</body></html>
