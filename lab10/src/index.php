<?php require __DIR__.'/header.php'; theme_head('Ingest Studio'); ?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Ingest Studio</h1>
    <span class="pill">XML Importers</span>
  </div>
  <p class="muted">Try different import behaviors.</p>
  <div class="nav">
    <a href="/import-basic.php">Local Import (Basic)</a>
    <a href="/import-hardened.php">Local Import (Hardened)</a>
    <a href="/import-network.php">Network Import (XXE/SSRF)</a>
    <a href="/import-upload.php">Upload Import (XXE)</a>
    <a href="/import-expect.php">Expect Import (RCE)</a>
  </div>
</div></div>
<?php theme_foot(); ?>