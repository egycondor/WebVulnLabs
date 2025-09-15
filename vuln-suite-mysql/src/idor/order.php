<?php
require __DIR__.'/../header.php'; require __DIR__.'/../db.php'; theme_head('IDOR Orders');
$id = (int)($_GET['id'] ?? 1);
// VULN: no authorization check, show any order + admin note
$sql = "SELECT o.id, u.username, o.total, n.body AS note FROM orders o
        LEFT JOIN users u ON u.id=o.user_id
        LEFT JOIN notes n ON n.user_id=u.id
        WHERE o.id=$id";
$res = db()->query($sql);
echo '<div class="wrap"><div class="card"><h2>Order #'.htmlspecialchars((string)$id).'</h2>';
if($res && $res->num_rows){ echo '<pre>';
  while($r=$res->fetch_assoc()){ print_r($r); }
  echo '</pre><p class="pill">Try id=3</p>';
} else { echo '<div class="pill">No such order</div>'; }
echo '</div></div>'; theme_foot();
