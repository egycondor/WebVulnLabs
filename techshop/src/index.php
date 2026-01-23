<?php
require_once 'config.php';

$db = getDB();
$products = [];

// Get all products
$result = $db->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC");
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Premium Electronics Store</title>
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
                <?php if ($user): ?>
                    <a href="cart.php">Cart</a>
                    <a href="orders.php">My Orders</a>
                    <a href="profile.php"><?php echo h($user['username']); ?> (<?php echo number_format($user['balance'], 2); ?>)</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container">
        <section class="hero">
            <h2>Welcome to TechShop</h2>
            <p>Your one-stop shop for premium electronics and accessories</p>
        </section>

        <section class="products-grid">
            <h2>Featured Products</h2>
            <div class="grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo h($product['image_url']); ?>" alt="<?php echo h($product['name']); ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo h($product['name']); ?></h3>
                        <p class="product-description"><?php echo h($product['description']); ?></p>
                        <div class="product-footer">
                            <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php if ($user): ?>
                                <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-primary">Add to Cart</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary">Login to Buy</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 TechShop. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
