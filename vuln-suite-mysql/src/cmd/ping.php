<?php
require __DIR__.'/../header.php'; theme_head('Command Injection');
$out=''; if($_SERVER['REQUEST_METHOD']==='POST'){ $ip = $_POST['ip'] ?? ''; $out = shell_exec("ping -c 2 " . $ip . " 2>&1"); } // VULN
?>
<div class="wrap"><div class="card">
  <h2>Net Ping</h2>
  <form method="POST">
    <label>IP/Host</label><input name="ip" placeholder="8.8.8.8">
    <button>Ping</button>
  </form>
  <?php if($out): ?><pre><?php echo htmlspecialchars($out); ?></pre><?php endif; ?>
</div></div>
<?php theme_foot(); ?>
