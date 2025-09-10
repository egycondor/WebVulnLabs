<?php require __DIR__.'/header.php'; theme_head('Command Injection Lab'); ?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Command Injection Lab</h1>
    <span class="pill">Two tools</span>
  </div>
  <p class="muted">Practice command injection against two endpoints.</p>
  <div class="nav">
    <a href="/ping.php">IP Map (Ping)</a>
    <a href="/records_viewer.php">Medical Records Viewer</a>
  </div>
</div></div>
<?php theme_foot(); ?>
