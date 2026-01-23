<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechShop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>TechShop</h1>
            </div>
            <div class="nav-menu">
                <a href="index.php">Home</a>
                <a href="register.php">Register</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="auth-form">
            <h2>Login to Your Account</h2>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo h($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="auth-link">Don't have an account? <a href="register.php">Register here</a></p>
            <div class="demo-accounts">
                <h3>Demo Accounts:</h3>
                <p>Username: <strong>admin</strong> / Password: <strong>password</strong></p>
                <p>Username: <strong>john_doe</strong> / Password: <strong>password</strong></p>
            </div>
        </div>
    </main>
</body>
</html>
