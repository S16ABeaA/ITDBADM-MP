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
        'KRW' => 3
    ];
    $currencyID = $currencyMap[$currency] ?? 1;
    
    // Call the stored procedure
    $stmt = $conn->prepare("CALL ProcessOrder(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissid", $customerID, $branchID, $paymentMethod, $deliveryMethod, $currencyID, $total);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create order: ' . $stmt->error);
    }
    
    // Get the result from stored procedure
    $result = $stmt->get_result();
    $orderData = $result->fetch_assoc();
    $orderID = $orderData['OrderID'];
    
    $stmt->close();
    $conn->next_result();
    
    // Insert order details
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
    
    // Clear cart session
    unset($_SESSION['cart']);
    
    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'orderID' => $orderID]);
    
} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>