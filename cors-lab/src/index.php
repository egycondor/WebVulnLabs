<?php
require __DIR__.'/header.php'; theme_head('CORS Lab');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>CORS Misconfiguration</h1>
    <span class="pill">Origin Reflection</span>
  </div>
  <p class="muted">Demo of Access-Control-Allow-Origin reflection with credentials.</p>
  <div class="nav">
    <a href="/login.php">Login</a>
    <a href="/account.php">Account API</a>
  </div>
</div></div>
<?php theme_foot(); ?>
