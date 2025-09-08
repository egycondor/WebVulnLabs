<?php
require_once __DIR__.'/config.php';

$u = $_GET['u'] ?? '';
$code = $_GET['code'] ?? '';
$expected = substr(md5($u.date('Ymd')),0,6);

if(!$u || $code!==$expected || !find_user($u)){
  http_response_code(400);
  echo "<p>Invalid reset link.</p>";
  exit;
}

// VULN: predictable temporary password revealed to user
$temp = strtolower($u)."2025";
set_password($u, $temp);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Reset Done</title>
<style>body{font-family:system-ui,Segoe UI,Roboto;background:#0b1222;color:#e5e7eb;padding:30px}</style>
</head>
<body>
  <h2>Password Reset</h2>
  <p>Your temporary password is: <code><?= htmlspecialchars($temp) ?></code></p>
  <p><a href="/login.php">Back to login</a></p>
</body>
</html>

