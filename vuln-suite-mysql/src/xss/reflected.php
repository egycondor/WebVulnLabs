<?php
require __DIR__.'/../header.php'; theme_head('XSS Reflected');
$term = $_GET['term'] ?? '';
echo '<div class="wrap"><div class="card"><h2>Reflected XSS</h2>';
echo '<form><label>term</label><input name="term" value="'.htmlspecialchars($term).'"><button>Search</button></form>';
echo '<p>Unsafe reflection below:</p>';
echo '<div>'.$term.'</div>'; // VULN
echo '</div></div>'; theme_foot();
