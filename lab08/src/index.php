<?php
require __DIR__.'/header.php'; theme_head('Reflective XSS Lab');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Reflective XSS Lab</h1>
    <span class="pill">Two levels: Low & High</span>
  </div>
  <p class="muted">Practice reflected cross-site scripting on two endpoints with different defenses.</p>
  <div class="nav">
    <a href="/xss-low.php">Low level</a>
    <a href="/xss-high.php">High level</a>
  </div>
</div></div>
<?php theme_foot(); ?>
