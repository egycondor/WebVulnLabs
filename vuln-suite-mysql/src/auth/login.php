<?php
require __DIR__.'/../header.php'; require __DIR__.'/../db.php'; start_session();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  // VULN: SQL injection in login
  $sql = "SELECT id,username,role FROM users WHERE username='$u' AND password='$p'";
  $res = db()->query($sql);
  if($res && $res->num_rows){
    $_SESSION['user'] = $res->fetch_assoc();
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
    header('Location: /auth/account.php'); exit;
  } else { $msg = '<div class="msg bad">Invalid credentials</div>'; }
}
theme_head('Login');
?>
<div class="wrap"><div class="card">
  <h2>Login</h2>
  <?php echo $msg; ?>
  <form method="POST">
    <label>Username</label><input name="username" value="alice">
    <label>Password</label><input name="password" type="password" value="alice123">
    <button>Sign in</button>
  </form>
  <p class="pill">Tip: Try SQLi like <code>' OR '1'='1 -- -</code></p>
</div></div>
<?php theme_foot(); ?>
