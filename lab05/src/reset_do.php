<?php
require_once __DIR__.'/config.php';
// VULN: resets any named user without proof of ownership
$u = $_GET['u'] ?? '';
$user = get_user_by_username($u);
if(!$user){ echo "If the account exists, it has been reset."; exit; }

$new = strtolower($u)."2025"; // predictable temporary password
$uh = weak_hash($new);
$ue = $conn->real_escape_string($u);
$conn->query("UPDATE users SET password_md5='$uh' WHERE username='$ue' LIMIT 1");

echo "<p>Password for <b>".htmlspecialchars($u)."</b> reset to <code>".htmlspecialchars($new)."</code></p>";
echo '<p><a href="/login.php">Login</a></p>';
