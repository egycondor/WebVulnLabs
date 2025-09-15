<?php
require __DIR__.'/header.php'; theme_head('Clickjacking Lab (HTTPS)');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Clickjacking Lab (HTTPS)</h1>
    <span class="pill">Cookie: SameSite=None; Secure · Frameable page</span>
  </div>
  <p class="muted">Flow: Login → Account (Delete button) → Trick user via a framed page on another origin.</p>
  <ul>
    <li><a href="/login.php">Login</a></li>
    <li><a href="/auth/account.php">Account</a></li>
    <li>Serve <code>src/attacker/decoy.html</code> from another origin (e.g., <code>attacker.example.test:8000</code>).</li>
  </ul>
</div></div>
<?php theme_foot(); ?>