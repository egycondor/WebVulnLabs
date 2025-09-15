<?php
require __DIR__.'/../header.php'; theme_head('XXE Parser');
$out='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $xml = $_POST['xml'] ?? '';
  libxml_use_internal_errors(true);
  $dom = new DOMDocument();
  if($dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD)){
    $out = $dom->saveXML();
  } else {
    foreach(libxml_get_errors() as $e){ $out .= $e->message; }
  }
}
?>
<div class="wrap"><div class="card">
  <h2>Upload XML</h2>
  <form method="POST"><textarea name="xml" rows="10"><root><name>alice</name></root></textarea><button>Parse</button></form>
  <?php if($out): ?><pre><?php echo htmlspecialchars($out); ?></pre><?php endif; ?>
  <p class="muted">Example: &lt;!DOCTYPE x [&lt;!ENTITY xxe SYSTEM "file:///etc/passwd"&gt;]&gt;&lt;root&gt;&amp;xxe;&lt;/root&gt;</p>
</div></div>
<?php theme_foot(); ?>
