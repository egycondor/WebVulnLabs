<?php
require __DIR__.'/header.php'; theme_head('Clickjacking Lab');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Clickjacking Lab</h1>
    <span class="pill">CSRF-protected delete action is frameable</span>
  </div>
  <p class="muted">Flow: Login → Account (Delete button) → Trick user via a framed page.</p>
  <p><a href="/login.php">Login</a> · <a href="/auth/account.php">Account</a> · <a href="/attacker/decoy.html">Attacker decoy</a></p>
</div></div>
<?php theme_foot(); ?>
