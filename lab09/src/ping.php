<?php
require __DIR__.'/header.php'; theme_head('NETTOOLS — IP MAP');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>IP MAP</h2>
    <a class="pill" href="/">Home</a>
  </div>
  <form method="POST" class="row" autocomplete="off">
    <input type="text" name="ip" placeholder="IP or host (e.g., 8.8.8.8)" required>
    <button type="submit">Ping</button>
  </form>
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $ip = $_POST['ip'];
      echo "<pre>";
      // INTENTIONALLY VULNERABLE: direct concatenation
      system("ping -c 2 " . $ip);
      echo "</pre>";
  }
  ?>
</div></div>
<?php theme_foot(); ?>
