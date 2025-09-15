<?php
require __DIR__.'/../header.php'; require __DIR__.'/../db.php'; start_session();
if(empty($_SESSION['user'])){ header('Location: /auth/login.php'); exit; }
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['do']??'')==='delete'){
  $token = $_POST['csrf'] ?? '';
  if(!hash_equals($_SESSION['csrf'] ?? '', $token)){
    $msg = '<div class="msg bad">Invalid CSRF</div>';
  } else {
    // Simulate delete by flag in session
    $_SESSION['user']['deleted'] = true;
    $msg = '<div class="msg ok">Account deleted. Lab solved.</div>';
  }
}
theme_head('Account');
?>
<div class="wrap"><div class="card">
  <h2>Account</h2>
  <?php echo $msg; ?>
  <p class="muted">Logged in as <b><?php echo htmlspecialchars($_SESSION['user']['username']); ?></b></p>
  <?php if(empty($_SESSION['user']['deleted'])): ?>
  <form method="POST">
    <input type="hidden" name="do" value="delete">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
    <button style="background:#ef4444;color:#fff;border-radius:12px;padding:12px 16px">Delete account</button>
  </form>
  <?php else: ?><div class="pill">Status: DELETED</div><?php endif; ?>
</div></div>
<?php theme_foot(); ?>
