<?php
require __DIR__.'/header.php'; theme_head('Local Import – Hardened');
$raw = $_POST['xml'] ?? '';
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>Local Import (Hardened)</h2>
    <a class="pill" href="/">Home</a>
  </div>
  <form method="POST">
    <label>Paste XML</label>
    <textarea name="xml" placeholder="<root><item>Example</item></root>"><?php echo htmlspecialchars($raw); ?></textarea>
    <div style="margin-top:10px"><button type="submit">Process</button></div>
  </form>
  <?php if($_SERVER['REQUEST_METHOD']==='POST'): ?>
    <?php
      libxml_use_internal_errors(true);
      $dom = new DOMDocument();
      $dom->resolveExternals = false;
      $dom->substituteEntities = false;
      if(!$dom->loadXML($raw, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)){
        echo '<div class="msg bad"><b>XML Errors:</b><pre>';
        foreach (libxml_get_errors() as $err){ echo htmlspecialchars($err->message); }
        echo '</pre></div>';
      }else{
        $xml = simplexml_import_dom($dom);
        echo '<div class="msg ok">Parsing complete.</div>';
        echo '<h3>Parsed Output</h3><pre>'.htmlspecialchars(print_r($xml, true)).'</pre>';
      }
    ?>
  <?php endif; ?>
</div></div>
<?php theme_foot(); ?>