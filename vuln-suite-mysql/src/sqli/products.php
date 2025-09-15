<?php
require __DIR__.'/../header.php'; require __DIR__.'/../db.php'; theme_head('SQLi Products');
$q = $_GET['q'] ?? '';
$sql = "SELECT id,name,price,stock FROM products WHERE name LIKE '%$q%'"; // VULN
echo '<div class="wrap"><div class="card"><h2>Products Search</h2>';
echo '<form method="GET"><label>q</label><input name="q" value="'.htmlspecialchars($q).'"><button>Search</button></form>';
echo '<pre>SQL: '.htmlspecialchars($sql).'</pre>';
try{
  $res = db()->query($sql);
  if($res){
    echo '<ul>';
    while($row = $res->fetch_assoc()){
      echo '<li>#'.$row['id'].' '.htmlspecialchars($row['name']).' - $'.$row['price'].' (stock '.$row['stock'].')</li>';
    }
    echo '</ul>';
  } else {
    echo '<div class="pill">DB Error: '.htmlspecialchars(db()->error).'</div>';
  }
}catch(Exception $e){
  echo '<div class="pill">Error: '.htmlspecialchars($e->getMessage()).'</div>';
}
echo '<p class="muted">Try: %\' ORDER BY 4-- - , or UNION SELECT @@hostname,database(),user(),version()-- -</p>';
echo '</div></div>'; theme_foot();
