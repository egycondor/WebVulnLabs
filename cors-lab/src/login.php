<?php
require __DIR__.'/header.php';
require __DIR__.'/utils.php';
start_lab_session();

$err = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  if($u==='alice' && $p==='alice123'){
    $_SESSION['user'] = ['name'=>'alice','email'=>'alice@example.com','balance'=>'$5000'];
    header('Location: /account.php'); exit;
  } else { $err = 'Invalid credentials.'; }
}
theme_head('Login');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>Login</h2>
    <a class="pill" href="/">Home</a>
  </div>
  <?php if($err): ?><div class="msg bad"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <form method="POST">
    <label>Username</label><input name="username" value="alice">
    <label>Password</label><input name="password" type="password" value="alice123">
    <div style="margin-top:10px"><button type="submit">Sign in</button></div>
  </form>
</div></div>
<?php theme_foot(); ?>
