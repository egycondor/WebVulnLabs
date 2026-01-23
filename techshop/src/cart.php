<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$user = getCurrentUser();

// Add to cart
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);
    $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1");
    $stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
    $stmt->execute();
    header('Location: cart.php');
    exit;
}

// Update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $item_id, $_SESSION['user_id']);
        $stmt->execute();
    } else {
        $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $item_id, $_SESSION['user_id']);
        $stmt->execute();
    }
    header('Location: cart.php');
    exit;
}

// Remove item
if (isset($_GET['remove'])) {
    $item_id = intval($_GET['remove']);
    $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $item_id, $_SESSION['user_id']);
    $stmt->execute();
    header('Location: cart.php');
    exit;
}

// Get cart items
$stmt = $db->prepare("
    SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.stock, p.image_url
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - TechShop</title>
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
        <h2>Shopping Cart</h2>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Your cart is empty. <a href="products.php">Continue shopping</a></p>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo h($item['image_url']); ?>" alt="<?php echo h($item['name']); ?>" class="cart-item-image">
                        <div class="cart-item-info">
                            <h3><?php echo h($item['name']); ?></h3>
                            <p class="cart-item-price">$<?php echo number_format($item['price'], 2); ?> each</p>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <label>Quantity: 
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" style="width: 60px;">
                                </label>
                                <button type="submit" name="update" class="btn btn-small">Update</button>
                            </form>
                            <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-small btn-danger">Remove</a>
                        </div>
                        <div class="cart-item-total">
                            <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>$10.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">$<?php echo number_format($total + 10, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
