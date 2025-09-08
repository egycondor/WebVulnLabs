<?php
require_once __DIR__ . '/config.php';

$logged_in = false;
$message = '';

if (isset($_GET['Login'])) {
    $username = isset($_GET['username']) ? $_GET['username'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';

    $sql = "SELECT id, username, password_md5, full_name
            FROM users
            WHERE username = '" . $conn->real_escape_string($username) . "'
            LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (weak_hash($password) === $row['password_md5']) {
            $logged_in = true;
            setcookie('BFSESSID', bin2hex(random_bytes(8)), 0, '/');
            $message = "Welcome, " . htmlspecialchars($row['full_name']) . "!";
        } else {
            $message = "Username and/or password incorrect.";
        }
    } else {
        $message = "Username and/or password incorrect. (user not found)";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Vulnerable Login (GET)</title>
  <style>
    body { font-family: sans-serif; max-width: 680px; margin: 40px auto; }
    .card { border: 1px solid #ccc; padding: 18px; border-radius: 10px; }
    .ok { color: #08660b; }
    .bad { color: #b00020; }
    .hint { color: #555; font-size: 0.9em; }
    code { background: #f6f6f6; padding: 2px 4px; border-radius: 4px; }
  </style>
</head>
<body>
  <h1>Brute Force Lab – Vulnerable Login</h1>
  <div class="card">
    <?php if ($logged_in): ?>
      <p class="ok"><strong><?= $message ?></strong></p>
      <p><a href="home.php">Continue to home</a></p>
    <?php else: ?>
      <?php if ($message): ?>
        <p class="bad"><strong><?= htmlspecialchars($message) ?></strong></p>
      <?php endif; ?>

      <form action="" method="GET" autocomplete="off">
        <p><label>Username: <input name="username" type="text" /></label></p>
        <p><label>Password: <input name="password" type="text" /></label></p>
        <p><button type="submit" name="Login" value="Login">Login</button></p>
      </form>

    <?php endif; ?>
  </div>
</body>
</html>