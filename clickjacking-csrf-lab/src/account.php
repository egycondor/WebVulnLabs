<?php
require __DIR__.'/header.php';
require __DIR__.'/utils.php';
require_login();
start_lab_session();

if(empty($_SESSION['csrf'])){
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$msg = '';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['do']) && $_POST['do']==='delete'){
  $token = $_POST['csrf'] ?? '';
  if(!hash_equals($_SESSION['csrf'], $token)){
    $msg = '<div class="msg bad">Invalid CSRF token.</div>';
  } else {
    $_SESSION['user']['deleted'] = true;
    $msg = '<div class="msg ok">Your account has been deleted. Lab solved.</div>';
  }
}

theme_head('Account');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>Account</h2>
    <a class="pill" href="/">Home</a>
  </div>
  <?php echo $msg; ?>
  <p class="muted">Logged in as <b><?php echo htmlspecialchars($_SESSION['user']['name']); ?></b> (<?php echo htmlspecialchars($_SESSION['user']['email']); ?>)</p>

  <?php if(!$_SESSION['user']['deleted']): ?>
    <form method="POST" style="margin-top:16px">
      <input type="hidden" name="do" value="delete">
      <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
      <button type="submit" style="padding:16px 18px;font-size:18px;background:#ef4444;color:white;border-radius:12px">Delete account</button>
    </form>
    <p class="muted" style="margin-top:10px">This action is protected by a CSRF token.</p>
  <?php else: ?>
    <div class="msg ok">Status: <b>DELETED</b></div>
  <?php endif; ?>
</div></div>
<?php theme_foot(); ?>
