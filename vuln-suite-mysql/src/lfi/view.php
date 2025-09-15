<?php
require __DIR__.'/../header.php'; theme_head('LFI Viewer');
$base = __DIR__.'/storage/'; @mkdir($base,0777,true); file_put_contents($base.'readme.txt',"Storage readme\n");
$f = $_GET['file'] ?? 'readme.txt';
$path = $base.$f; // VULN
echo '<div class="wrap"><div class="card"><h2>File Viewer</h2><p class="muted">Try ../../../../etc/passwd</p>';
if(is_file($path)){ echo '<pre>'.htmlspecialchars(file_get_contents($path)).'</pre>'; } else { echo '<div class="pill">Not found</div>'; }
echo '</div></div>'; theme_foot();
