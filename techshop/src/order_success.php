<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$order_id = intval($_GET['order_id'] ?? 0);
if ($order_id === 0) {
    header('Location: orders.php');
    exit;
}

$db = getDB();
$user = getCurrentUser();

$stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: orders.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - TechShop</title>
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
                <a href="orders.php">My Orders</a>
                <a href="profile.php"><?php echo h($user['username']); ?></a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="success-message">
            <h2>Order Placed Successfully!</h2>
            <p>Thank you for your purchase. Your order has been received and is being processed.</p>
            <div class="order-details">
                <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
            </div>
            <div class="success-actions">
                <a href="orders.php" class="btn btn-primary">View All Orders</a>
                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </div>
    </main>
</body>
</html>
