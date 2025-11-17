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
    $currencyCode = $_POST['currency'];
    
    // Get user and branch info from session
    $customerID = $_SESSION['user_id'] ?? 0;
    $branchID = $_SESSION['selected_branch_id'] ?? 0;
    
    if ($customerID === 0 || $branchID === 0) {
        throw new Exception('User information missing');
    }
    
    // Calculate total from cart (in PHP - base currency)
    $subtotalPHP = 0;
    foreach ($_SESSION['cart'] as $cartItem) {
        $subtotalPHP += $cartItem['quantity'] * $cartItem['price'];
    }
    
    // Total is just the subtotal (no shipping)
    $totalPHP = $subtotalPHP;
    
    // Get currency ID and rate from database
    $currencySQL = "SELECT CurrencyID, Currency_Rate, Symbol FROM currency WHERE Currency_Name = ?";
    $currencyStmt = $conn->prepare($currencySQL);
    $currencyStmt->bind_param("s", $currencyCode);
    $currencyStmt->execute();
    $currencyResult = $currencyStmt->get_result();
    
    if ($currencyResult->num_rows === 0) {
        throw new Exception('Invalid currency selected');
    }
    
    $currencyData = $currencyResult->fetch_assoc();
    $currencyID = $currencyData['CurrencyID'];
    $currencyRate = $currencyData['Currency_Rate'];
    $currencySymbol = $currencyData['Symbol'];
    
    $currencyStmt->close();
    
    // Convert total to selected currency for order record
    $totalInSelectedCurrency = $totalPHP * $currencyRate;
    
    // Call the stored procedure
    $stmt = $conn->prepare("CALL ProcessOrder(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissid", $customerID, $branchID, $paymentMethod, $deliveryMethod, $currencyID, $totalInSelectedCurrency);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create order: ' . $stmt->error);
    }
    
    // Get the result from stored procedure
    $result = $stmt->get_result();
    $orderData = $result->fetch_assoc();
    $orderID = $orderData['OrderID'];
    
    $stmt->close();
    $conn->next_result();
    
    // Insert order details with prices in selected currency
    foreach ($_SESSION['cart'] as $cartItem) {
        $priceInSelectedCurrency = $cartItem['price'] * $currencyRate;
        
        $orderDetailSQL = "INSERT INTO orderdetails (OrderID, ProductID, Quantity, price) 
                           VALUES (?, ?, ?, ?)";
        $orderDetailStmt = $conn->prepare($orderDetailSQL);
        $orderDetailStmt->bind_param("iiid", 
            $orderID, 
            $cartItem['productID'], 
            $cartItem['quantity'], 
            $priceInSelectedCurrency
        );
        
        if (!$orderDetailStmt->execute()) {
            throw new Exception('Failed to add order details: ' . $orderDetailStmt->error);
        }
        $orderDetailStmt->close();
    }
    
    // Clear cart session
    unset($_SESSION['cart']);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully', 
        'orderID' => $orderID,
        'currency' => $currencyCode,
        'currencySymbol' => $currencySymbol,
        'currencyRate' => $currencyRate,
        'total' => $totalInSelectedCurrency
    ]);
    
} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>