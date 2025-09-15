<?php
require __DIR__.'/header.php'; theme_head('Clickjacking + CSRF Token Lab (HTTPS)');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Clickjacking Lab</h1>
    <span class="pill">HTTPS + SameSite=None; Secure cookie</span>
  </div>
  <p class="muted">Delete account is CSRF-token protected, but clickjacking can still trigger it.</p>
  <div class="nav">
    <a href="/login.php">Login</a>
    <a href="/account.php">Account</a>
    <a href="/readme.html">Readme</a>
  </div>
</div></div>
<?php theme_foot(); ?>
