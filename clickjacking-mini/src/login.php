<?php
require __DIR__.'/header.php'; require __DIR__.'/utils.php'; start_lab_session();
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  if($u==='alice' && $p==='alice123'){
    $_SESSION['user']=['name'=>'alice','email'=>'alice@example.com','deleted'=>false];
    $_SESSION['csrf']=bin2hex(random_bytes(16));
    header('Location: /auth/account.php'); exit;
  } else { $err='Invalid credentials'; }
}
theme_head('Login');
?>
<div class="wrap"><div class="card">
  <h2>Login</h2>
  <?php if($err): ?><div class="pill"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <form method="POST">
    <label>Username</label><input name="username" value="alice">
    <label>Password</label><input name="password" type="password" value="alice123">
    <button>Sign in</button>
  </form>
</div></div>
<?php theme_foot(); ?>
