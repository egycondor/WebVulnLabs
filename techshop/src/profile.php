<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$user = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_balance'])) {
    $amount = floatval($_POST['amount']);
    if ($amount > 0 && $amount <= 10000) {
        $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param("di", $amount, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $success = "Balance added successfully!";
            $user = getCurrentUser(); // Refresh user data
        } else {
            $error = "Failed to add balance";
        }
    } else {
        $error = "Invalid amount";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - TechShop</title>
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
                <a href="products.php">Products</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">My Orders</a>
                <a href="profile.php"><?php echo h($user['username']); ?></a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <h2>My Profile</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo h($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
        <?php endif; ?>
        
        <div class="profile-container">
            <div class="profile-card">
                <h3>Account Information</h3>
                <div class="profile-info">
                    <p><strong>Username:</strong> <?php echo h($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo h($user['email']); ?></p>
                    <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></p>
                    <p><strong>Account Balance:</strong> $<?php echo number_format($user['balance'], 2); ?></p>
                </div>
                
                <h3>Add Balance</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="amount">Amount ($)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" max="10000" required>
                    </div>
                    <button type="submit" name="add_balance" class="btn btn-primary">Add Balance</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
