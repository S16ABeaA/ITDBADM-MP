<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

header('Content-Type: application/json');

// Check if cart exists and has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

if (!isset($_POST['paymentMethod'], $_POST['deliveryMethod'], $_POST['currency'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required information']);
    exit;
}

try {
    // Get form data
    $paymentMethod = $_POST['paymentMethod'];
    $deliveryMethod = $_POST['deliveryMethod'];
    $currency = $_POST['currency'];
    
    // Get user and branch info from session
    $customerID = $_SESSION['user_id'] ?? 0;
    $branchID = $_SESSION['selected_branch_id'] ?? 0;
    
    if ($customerID === 0 || $branchID === 0) {
        throw new Exception('User information missing');
    }
    
    // Calculate total from cart
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $cartItem) {
        $subtotal += $cartItem['quantity'] * $cartItem['price'];
    }
    $shipping = 59.99;
    $tax = $subtotal * 0.08;
    $total = $subtotal + $shipping + $tax;
    
    // Get currency ID based on selection
    $currencyMap = [
        'PHP' => 1,
        'USD' => 2, 
        'KOR' => 3
    ];
    $currencyID = $currencyMap[$currency] ?? 1;
    
    // Start transaction
    $conn->begin_transaction();
    
    // Temporarily disable the OrderInsertLog trigger by renaming it (if possible)
    // Alternatively, we'll work with the trigger by ensuring proper data flow
    
    // Insert into orders table - the trigger will handle order_log automatically
    $orderSQL = "INSERT INTO orders (CustomerID, CurrencyID, BranchID, Status, Total, PaymentMode, DeliveryMethod) 
                 VALUES (?, ?, ?, 'Pending', ?, ?, ?)";
    $orderStmt = $conn->prepare($orderSQL);
    $orderStmt->bind_param("iiidss", $customerID, $currencyID, $branchID, $total, $paymentMethod, $deliveryMethod);
    
    if (!$orderStmt->execute()) {
        throw new Exception('Failed to create order: ' . $orderStmt->error);
    }
    
    $orderID = $conn->insert_id;
    $orderStmt->close();
    
    // Insert order details - triggers will handle inventory checks and updates
    foreach ($_SESSION['cart'] as $cartItem) {
        $orderDetailSQL = "INSERT INTO orderdetails (OrderID, ProductID, Quantity, price) 
                           VALUES (?, ?, ?, ?)";
        $orderDetailStmt = $conn->prepare($orderDetailSQL);
        $orderDetailStmt->bind_param("iiid", $orderID, $cartItem['productID'], $cartItem['quantity'], $cartItem['price']);
        
        if (!$orderDetailStmt->execute()) {
            throw new Exception('Failed to add order details: ' . $orderDetailStmt->error);
        }
        $orderDetailStmt->close();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Clear cart session
    unset($_SESSION['cart']);
    
    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'orderID' => $orderID]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>