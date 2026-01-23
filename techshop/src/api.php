<?php
require_once 'config.php';

header('Content-Type: application/json');

// API endpoints for AJAX requests
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'check_balance':
        $user = getCurrentUser();
        echo json_encode([
            'success' => true,
            'balance' => floatval($user['balance']),
            'username' => $user['username']
        ]);
        break;
    
    case 'apply_coupon':
        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            echo json_encode(['error' => 'Coupon code required']);
            break;
        }
        
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $coupon = $stmt->get_result()->fetch_assoc();
        
        if ($coupon) {
            echo json_encode([
                'success' => true,
                'coupon' => [
                    'code' => $coupon['code'],
                    'discount_percent' => floatval($coupon['discount_percent']),
                    'min_purchase' => floatval($coupon['min_purchase']),
                    'max_discount' => $coupon['max_discount'] ? floatval($coupon['max_discount']) : null
                ]
            ]);
        } else {
            echo json_encode(['error' => 'Invalid coupon code']);
        }
        break;
    
    case 'get_product':
        $product_id = intval($_GET['product_id'] ?? 0);
        if ($product_id === 0) {
            echo json_encode(['error' => 'Product ID required']);
            break;
        }
        
        $db = getDB();
        $stmt = $db->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if ($product) {
            echo json_encode([
                'success' => true,
                'product' => [
                    'id' => intval($product['id']),
                    'name' => $product['name'],
                    'price' => floatval($product['price']),
                    'stock' => intval($product['stock'])
                ]
            ]);
        } else {
            echo json_encode(['error' => 'Product not found']);
        }
        break;
    
    case 'transfer_balance':
        // User-to-user balance transfer feature
        $to_user = $_GET['to_user'] ?? '';
        $amount = floatval($_GET['amount'] ?? 0);
        
        if (empty($to_user) || $amount <= 0) {
            echo json_encode(['error' => 'Invalid parameters']);
            break;
        }
        
        $db = getDB();
        $current_user = getCurrentUser();
        
        if ($current_user['balance'] < $amount) {
            echo json_encode(['error' => 'Insufficient balance']);
            break;
        }
        
        // Get recipient user
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $to_user);
        $stmt->execute();
        $recipient = $stmt->get_result()->fetch_assoc();
        
        if (!$recipient) {
            echo json_encode(['error' => 'Recipient not found']);
            break;
        }
        
        // Transfer balance
        $db->begin_transaction();
        try {
            $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $current_user['id']);
            $stmt->execute();
            
            $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $recipient['id']);
            $stmt->execute();
            
            $db->commit();
            echo json_encode([
                'success' => true,
                'message' => "Transferred $" . number_format($amount, 2) . " to {$to_user}"
            ]);
        } catch (Exception $e) {
            $db->rollback();
            echo json_encode(['error' => 'Transfer failed']);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
