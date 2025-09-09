<?php
require_once __DIR__.'/config.php';
$user = $_COOKIE['user'] ?? null;
$role = $_COOKIE['role'] ?? 'user';
if(!$user){ header("Location: /login.php"); exit; }

// VULN: trusts role from cookie; change role=admin to escalate
if($role !== 'admin'){
  http_response_code(403);
  echo "<p style='color:#fecaca;background:#1a0d10;padding:10px;border:1px solid #7f1d1d;border-radius:10px'>Forbidden: admin role required.</p>";
  exit;
}

$res = $conn->query("SELECT id,username,full_name,role FROM users ORDER BY id");
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin</title></head>
<body style="font-family:system-ui;background:#0b1222;color:#e5e7eb">
<div style="max-width:900px;margin:40px auto;padding:24px;border:1px solid #1f2937;border-radius:16px;background:#0f172a">
<h2>Admin Dashboard</h2>
<table border="1" cellpadding="6" style="border-collapse:collapse">
<tr><th>ID</th><th>Username</th><th>Name</th><th>Role</th></tr>
<?php while($row=$res->fetch_assoc()): ?>
<tr>
  <td><?= (int)$row['id'] ?></td>
  <td><?= htmlspecialchars($row['username']) ?></td>
  <td><?= htmlspecialchars($row['full_name']) ?></td>
  <td><?= htmlspecialchars($row['role']) ?></td>
</tr>
<?php endwhile; ?>
</table>
<p><a href="/add_user.php">Add user</a> · <a href="/home.php">Home</a></p>
</div></body></html>
