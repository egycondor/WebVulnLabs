<?php
require_once __DIR__.'/config.php';
$sess = get_session();
if(!$sess){ header("Location: /login.php"); exit; }
$msg='';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $otp = $_POST['otp'] ?? '';
  // Intentionally weak: compare plaintext, no attempt cap, no delay
  if($otp === $sess['otp_code']){
    mark_mfa_ok($sess['sid']);
    header("Location: /home.php"); exit;
  } else {
    $msg = "Invalid OTP."; // constant failure marker for fuzzing
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"><title>MFA Lab – Verify</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{margin:0;background:#0b1222;color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
.wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
.card{width:100%;max-width:420px;background:#0f172a;border:1px solid #1f2937;border-radius:16px;padding:24px}
h2{margin:0 0 10px} .muted{color:#94a3b8;margin:0 0 14px}
label{display:block;margin:8px 0 6px;color:#cbd5e1} input{width:100%;padding:11px 12px;border:1px solid #253046;background:#0b1324;color:#e5e7eb;border-radius:12px}
button{width:100%;padding:12px;border-radius:12px;border:0;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:700;margin-top:12px}
.msg{background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca;padding:10px;border-radius:12px;margin-bottom:10px}
</style>
</head>
<body>
<div class="wrap"><div class="card">
  <h2>Two-Factor Verification</h2>
  <p class="muted">A 4-digit code was “sent to your device”.</p>
  <?php if($msg): ?><p class="msg"><?=htmlspecialchars($msg)?></p><?php endif; ?>
  <form method="POST">
    <label>One-Time Password</label>
    <input name="otp" maxlength="4" placeholder="0000" autocomplete="one-time-code">
    <button type="submit">Verify</button>
  </form>
</div></div>
</body></html>

