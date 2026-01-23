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

// Get cart items
$stmt = $db->prepare("
    SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Only redirect if cart is empty AND it's a GET request (not POST)
// This allows direct POST requests for workflow bypass testing
if (empty($cart_items) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 10.00;
$total = $subtotal + $shipping;

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Calculate order total from submitted data
    // Note: In production, always recalculate from server-side data
    $calculated_subtotal = 0;
    $order_items = [];
    
    if (isset($_POST['items']) && is_array($_POST['items']) && !empty($_POST['items'])) {
        // Process order items from form submission
        foreach ($_POST['items'] as $item_data) {
            $product_id = intval($item_data['product_id'] ?? 0);
            $quantity = intval($item_data['quantity'] ?? 0);
            $unit_price = floatval($item_data['unit_price'] ?? 0);
            
            if ($product_id > 0 && $quantity > 0) {
                $order_items[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price
                ];
                $calculated_subtotal += $unit_price * $quantity;
            }
        }
        
        // Use calculated subtotal if items were provided
        if ($calculated_subtotal > 0) {
            $subtotal = $calculated_subtotal;
        }
    } else {
        // Fallback to cart items
        foreach ($cart_items as $item) {
            $order_items[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price']
            ];
        }
    }
    
    // Apply discount if provided via form (for promotional codes entered by user)
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    if ($discount_amount > 0) {
        $subtotal = max(0, $subtotal - $discount_amount);
    }
    
    // Get shipping cost (can be overridden for special promotions)
    $shipping = floatval($_POST['shipping'] ?? 10.00);
    $total = $subtotal + $shipping;
    
    // Allow total override for special cases (admin promotions, etc.)
    if (isset($_POST['final_total']) && floatval($_POST['final_total']) > 0) {
        $total = floatval($_POST['final_total']);
    }
    
    $shipping_address = $_POST['shipping_address'] ?? 'Not provided';
    $coupon_code = $_POST['coupon_code'] ?? '';
    
    // Apply coupon code if provided
    $coupon_discount = 0;
    if (!empty($coupon_code)) {
        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND valid_from <= CURDATE() AND valid_until >= CURDATE()");
        $stmt->bind_param("s", $coupon_code);
        $stmt->execute();
        $coupon = $stmt->get_result()->fetch_assoc();
        
        if ($coupon) {
            if ($coupon['usage_limit'] === NULL || $coupon['used_count'] < $coupon['usage_limit']) {
                if ($subtotal >= $coupon['min_purchase']) {
                    $coupon_discount = ($subtotal * $coupon['discount_percent']) / 100;
                    if ($coupon['max_discount'] !== NULL && $coupon_discount > $coupon['max_discount']) {
                        $coupon_discount = $coupon['max_discount'];
                    }
                    $total = $subtotal + $shipping - $coupon_discount;
                }
            }
        }
    }
    
    // Refresh user balance
    $user = getCurrentUser();
    
    // Validate sufficient balance
    if ($user['balance'] >= $total) {
        // Create order
        $db->begin_transaction();
        try {
            $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status, payment_status, shipping_address) VALUES (?, ?, 'pending', 'paid', ?)");
            $stmt->bind_param("ids", $_SESSION['user_id'], $total, $shipping_address);
            $stmt->execute();
            $order_id = $db->insert_id;
            
            // Process order items
            foreach ($order_items as $item) {
                if ($item['product_id'] > 0 && $item['quantity'] > 0) {
                    // Verify product exists
                    $stmt = $db->prepare("SELECT id, stock FROM products WHERE id = ?");
                    $stmt->bind_param("i", $item['product_id']);
                    $stmt->execute();
                    $product = $stmt->get_result()->fetch_assoc();
                    
                    if ($product) {
                        // Record order item with submitted price and quantity
                        $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['unit_price']);
                        $stmt->execute();
                        
                        // Update inventory
                        $stmt = $db->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");
                        $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                        $stmt->execute();
                    }
                }
            }
            
            // Deduct balance
            $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->bind_param("di", $total, $_SESSION['user_id']);
            $stmt->execute();
            
            // Clear cart
            $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            
            $db->commit();
            $success = "Order placed successfully! Order ID: #{$order_id}";
            header("Location: order_success.php?order_id={$order_id}");
            exit;
        } catch (Exception $e) {
            $db->rollback();
            $error = "Order failed: " . $e->getMessage();
        }
    } else {
        $error = "Insufficient account balance. You have $" . number_format($user['balance'], 2) . 
                 " but need $" . number_format($total, 2) . 
                 ". Please add funds to your account or remove items from your cart.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TechShop</title>
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
                <a href="cart.php">Cart</a>
                <a href="orders.php">My Orders</a>
                <a href="profile.php"><?php echo h($user['username']); ?></a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <h2>Checkout</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo h($error); ?></div>
        <?php endif; ?>
        
        <div class="checkout-container">
            <div class="checkout-form">
                <h3>Shipping Information</h3>
                <form method="POST" id="checkout-form">
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address</label>
                        <textarea id="shipping_address" name="shipping_address" rows="4" required>123 Main St, City, State, ZIP</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="coupon_code">Promo Code (Optional)</label>
                        <input type="text" id="coupon_code" name="coupon_code" placeholder="Enter promo code">
                        <small style="color: #666; font-size: 0.9rem;">Have a promo code? Enter it here for instant savings!</small>
                    </div>
                    
                    <!-- Order processing data -->
                    <input type="hidden" name="discount_amount" value="0">
                    <input type="hidden" name="shipping" value="<?php echo $shipping; ?>">
                    <input type="hidden" name="final_total" value="<?php echo $total; ?>">
                    
                    <!-- Cart items -->
                    <?php foreach ($cart_items as $item): ?>
                        <input type="hidden" name="items[][product_id]" value="<?php echo $item['product_id']; ?>">
                        <input type="hidden" name="items[][quantity]" value="<?php echo $item['quantity']; ?>">
                        <input type="hidden" name="items[][unit_price]" value="<?php echo $item['price']; ?>">
                    <?php endforeach; ?>
                    
                    <div class="order-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="display_subtotal">$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span id="display_shipping">$<?php echo number_format($shipping, 2); ?></span>
                        </div>
                        <?php if (!empty($coupon_code)): ?>
                        <div class="summary-row" style="color: #28a745;">
                            <span>Discount Applied:</span>
                            <span>-</span>
                        </div>
                        <?php endif; ?>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span id="display_total">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="summary-row" style="border-top: 1px solid #eee; padding-top: 10px; margin-top: 10px;">
                            <span>Account Balance:</span>
                            <span style="font-weight: 600;">$<?php echo number_format($user['balance'], 2); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Place Order</button>
                </form>
            </div>
            
            <div class="checkout-items">
                <h3>Order Items</h3>
                <?php foreach ($cart_items as $item): ?>
                    <div class="checkout-item">
                        <strong><?php echo h($item['name']); ?></strong>
                        <span><?php echo $item['quantity']; ?> x $<?php echo number_format($item['price'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>
