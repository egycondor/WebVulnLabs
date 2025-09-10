<?php
error_reporting(0);
$mysqli = @new mysqli('db','shop','shop','shop');
if($mysqli->connect_error){ die('DB error'); }

$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "SELECT name, price, description FROM products WHERE name LIKE '%$q%'";

if ($sort !== '') {
  $sql .= " ORDER BY $sort";
}

$res = $mysqli->query($sql);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Acme Shop — Products</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{--bg:#0b1222;--panel:#0f172a;--border:#1f2937;--muted:#94a3b8}
    *{box-sizing:border-box} body{margin:0;background:var(--bg);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
    .container{max-width:980px;margin:30px auto;padding:24px;background:#0f172a;border:1px solid #1f2937;border-radius:18px}
    h1{margin:.2em 0 .5em} .muted{color:var(--muted)}
    input{padding:10px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:10px}
    button{padding:10px 14px;border-radius:10px;border:0;background:linear-gradient(90deg,#06b6d4,#22d3ee);color:#031321;font-weight:700}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{border:1px solid #263046;padding:10px} th{background:#0b1324}
    .msg{padding:10px;border-radius:12px;margin:10px 0;background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca}
    code{background:#0b1324;border:1px solid #243046;border-radius:8px;padding:2px 6px}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
  </style>
</head>
<body>
<div class="container">
  <h1>Products</h1>
  <p class="muted">Search by name. Try exploring with SQLi:
    <code>' ORDER BY 1 -- </code>,
    <code>' UNION SELECT @@hostname,database(),version() -- -</code>,
    <code>' UNION SELECT user(), current_user(), database() -- -</code>
  </p>

  <form method="GET">
    <div class="row">
      <div><input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="e.g., Mug"></div>
      <div><input name="sort" value="<?= htmlspecialchars($sort) ?>" placeholder="ORDER BY expr (e.g., 1 or price)"></div>
    </div>
    <div style="margin-top:8px"><button type="submit">Search</button>
      <a href="/products.php">Reset</a></div>
  </form>

  <?php if(!$res): ?>
    <div class="msg"><b>SQL Error:</b> <?= htmlspecialchars($mysqli->error) ?></div>
    <pre class="msg" style="white-space:pre-wrap"><?= htmlspecialchars($sql) ?></pre>
  <?php else: ?>
    <table>
      <thead><tr><th>Name</th><th>Price</th><th>Description</th></tr></thead>
      <tbody>
      <?php while($row = $res->fetch_row()): ?>
        <tr>
          <td><?= htmlspecialchars($row[0]) ?></td>
          <td><?= htmlspecialchars($row[1]) ?></td>
          <td><?= htmlspecialchars($row[2]) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body>
</html>
