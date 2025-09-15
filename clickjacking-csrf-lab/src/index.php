<?php
require __DIR__.'/header.php'; theme_head('Clickjacking + CSRF Token Lab');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Clickjacking Lab</h1>
    <span class="pill">Delete account is CSRF-token protected</span>
  </div>
  <p class="muted">Your task: craft HTML on a decoy site that frames the account page and tricks a user into clicking the hidden Delete button.</p>
  <div class="nav">
    <a href="/login.php">Login</a>
    <a href="/account.php">Account</a>
    <a href="/readme.html">Readme</a>
  </div>
</div></div>
<?php theme_foot(); ?>
