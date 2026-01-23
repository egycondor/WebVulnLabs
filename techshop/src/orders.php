<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$user = getCurrentUser();

$stmt = $db->prepare("
    SELECT o.*, 
           (SELECT SUM(oi.quantity * oi.unit_price) FROM order_items oi WHERE oi.order_id = o.id) as calculated_total
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - TechShop</title>
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
        <h2>My Orders</h2>
        
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <p>You have no orders yet. <a href="products.php">Start shopping</a></p>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <h3>Order #<?php echo $order['id']; ?></h3>
                                <p class="order-date">Placed on <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div class="order-status">
                                <span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                                <span class="badge badge-<?php echo $order['payment_status']; ?>"><?php echo ucfirst($order['payment_status']); ?></span>
                            </div>
                        </div>
                        <div class="order-body">
                            <?php
                            $stmt = $db->prepare("
                                SELECT oi.*, p.name, p.image_url
                                FROM order_items oi
                                JOIN products p ON oi.product_id = p.id
                                WHERE oi.order_id = ?
                            ");
                            $stmt->bind_param("i", $order['id']);
                            $stmt->execute();
                            $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            ?>
                            <div class="order-items">
                                <?php foreach ($items as $item): ?>
                                    <div class="order-item">
                                        <img src="<?php echo h($item['image_url']); ?>" alt="<?php echo h($item['name']); ?>" class="order-item-image">
                                        <div class="order-item-info">
                                            <h4><?php echo h($item['name']); ?></h4>
                                            <p>Quantity: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['unit_price'], 2); ?></p>
                                        </div>
                                        <div class="order-item-total">
                                            $<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="order-footer">
                                <div class="order-total">
                                    <strong>Total: $<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </div>
                                <div class="order-address">
                                    <strong>Shipping to:</strong><br>
                                    <?php echo nl2br(h($order['shipping_address'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
