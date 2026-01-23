<?php
require_once 'config.php';

$db = getDB();
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM products WHERE stock > 0";
$params = [];
$types = "";

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $search_term = "%{$search}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - TechShop</title>
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
                    <a href="profile.php"><?php echo h($user['username']); ?></a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container">
        <h2>All Products</h2>
        
        <div class="filters">
            <form method="GET" class="filter-form">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo h($search); ?>">
                <select name="category">
                    <option value="">All Categories</option>
                    <option value="Electronics" <?php echo $category === 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                    <option value="Accessories" <?php echo $category === 'Accessories' ? 'selected' : ''; ?>>Accessories</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="products-grid">
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
                            <span class="stock">Stock: <?php echo $product['stock']; ?></span>
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
        </div>
    </main>
</body>
</html>
