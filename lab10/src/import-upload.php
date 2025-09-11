<?php
require __DIR__.'/header.php'; theme_head('Upload Import');
$upload_dir = __DIR__ . '/uploads';

// Ensure upload dir exists and is writable
if (!is_dir($upload_dir)) { @mkdir($upload_dir, 0777, true); }
@chmod($upload_dir, 0777);

?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>Upload Import</h2>
    <a class="pill" href="/">Home</a>
  </div>
  <form method="POST" enctype="multipart/form-data">
    <label>Select XML file</label>
    <input type="file" name="xmlfile" accept=".xml, text/xml, application/xml" required>
    <div style="margin-top:10px"><button type="submit">Upload & Process</button></div>
  </form>

  <?php if($_SERVER['REQUEST_METHOD']==='POST'): ?>
    <div style="margin-top:14px"></div>
    <?php
      if (!isset($_FILES['xmlfile']) || $_FILES['xmlfile']['error'] !== UPLOAD_ERR_OK) {
        echo '<div class="msg bad"><b>Upload error:</b> '.htmlspecialchars((string)($_FILES['xmlfile']['error'] ?? 'unknown')).'</div>';
      } else {
        $name = basename($_FILES['xmlfile']['name']);
        $dest = $upload_dir . '/' . $name;
        if (!move_uploaded_file($_FILES['xmlfile']['tmp_name'], $dest)) {
          echo '<div class="msg bad"><b>Failed to save the uploaded file.</b></div>';
        } else {
          $raw = file_get_contents($dest);
          libxml_use_internal_errors(true);
          // Vulnerable parse: DTDs + entity substitution enabled (XXE / SSRF)
          $xml = simplexml_load_string($raw, "SimpleXMLElement",
            LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NOENT);
          if(!$xml){
            echo '<div class="msg bad"><b>XML Errors:</b><pre>';
            foreach (libxml_get_errors() as $err){ echo htmlspecialchars($err->message); }
            echo '</pre></div>';
          } else {
            echo '<div class="msg ok">Parsing complete.</div>';
            echo '<h3>Parsed Output</h3><pre>'.htmlspecialchars(print_r($xml, true)).'</pre>';
            echo '<p class="muted">Stored as: uploads/'.htmlspecialchars($name).'</p>';
          }
        }
      }
    ?>
  <?php endif; ?>
</div></div>
<?php theme_foot(); ?>