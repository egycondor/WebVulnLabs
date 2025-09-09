<?php
require_once __DIR__.'/config.php';
$msg=''; $link='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=$_POST['username'] ?? '';
  // Always "send" something…
  $msg = "If the account exists, a reset link will be shown below.";

  // VULN: we actually disclose the live reset link
  $u_enc = urlencode($u);
  $link = "/reset_do.php?u=$u_enc";
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Reset</title>
<style>body{font-family:system-ui;background:#0b1222;color:#e5e7eb}
.main{max-width:600px;margin:40px auto;padding:24px;border:1px solid #1f2937;border-radius:16px;background:#0f172a}
</style></head>
<body><div class="main">
<h2>Password Reset</h2>
<?php if($msg): ?><p><?=htmlspecialchars($msg)?></p><?php endif; ?>
<?php if($link): ?><p>Reset link: <a href="<?=htmlspecialchars($link)?>"><?=htmlspecialchars($link)?></a></p><?php endif; ?>
<form method="POST">
  <p><label>Username <input name="username" placeholder="alice"></label></p>
  <p><button type="submit">Send reset</button></p>
</form>
<p><a href="/home.php">Home</a></p>
</div></body></html>
