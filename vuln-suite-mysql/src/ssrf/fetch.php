<?php
require __DIR__.'/../header.php'; theme_head('SSRF Fetcher');
$url = $_GET['url'] ?? '';
$body = ''; $err='';
if($url){
  $ctx = stream_context_create(['http'=>['timeout'=>4]]);
  $body = @file_get_contents($url,false,$ctx); // VULN
  if($body===false){ $err='Fetch failed'; }
}
?>
<div class="wrap"><div class="card">
  <h2>Fetcher</h2>
  <form method="GET"><label>url</label><input name="url" value="<?php echo htmlspecialchars($url); ?>"><button>Fetch</button></form>
  <?php if($err): ?><div class="msg bad"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <?php if($body!=='' && $body!==False): ?><pre><?php echo htmlspecialchars($body); ?></pre><?php endif; ?>
</div></div>
<?php theme_foot(); ?>
