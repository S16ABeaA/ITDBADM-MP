<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

header('Content-Type: application/json');

// Check if cart exists and has items (both products and services)
$hasProducts = isset($_SESSION['cart']) && !empty($_SESSION['cart']);
$hasServices = isset($_SESSION['service_cart']) && !empty($_SESSION['service_cart']);

if (!$hasProducts && !$hasServices) {
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
    
    if ($customerID === 0) {
        throw new Exception('User information missing');
    }
    
    // Calculate total from both carts (in PHP - base currency)
    $subtotalPHP = 0;
    
    // Calculate product totals
    if ($hasProducts) {
        foreach ($_SESSION['cart'] as $cartItem) {
            $subtotalPHP += $cartItem['quantity'] * $cartItem['price'];
        }
    }
    
    // Calculate service totals
    if ($hasServices) {
        foreach ($_SESSION['service_cart'] as $serviceItem) {
            $subtotalPHP += $serviceItem['finalPrice'];
        }
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
    
    // Call the stored procedure to create the order
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
    
    // Insert PRODUCT order details with prices in selected currency
    if ($hasProducts) {
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
                throw new Exception('Failed to add product order details: ' . $orderDetailStmt->error);
            }
            $orderDetailStmt->close();
        }
    }
    
    // Insert SERVICE details with prices in selected currency
    if ($hasServices) {
        foreach ($_SESSION['service_cart'] as $serviceItem) {
            $priceInSelectedCurrency = $serviceItem['finalPrice'] * $currencyRate;
            
            $serviceDetailSQL = "INSERT INTO servicedetails (OrderID, ServiceID, isFromStore, price) 
                                 VALUES (?, ?, ?, ?)";
            $serviceDetailStmt = $conn->prepare($serviceDetailSQL);
            $serviceDetailStmt->bind_param("iiid", 
                $orderID, 
                $serviceItem['serviceID'], 
                $serviceItem['isFromStore'], 
                $priceInSelectedCurrency
            );
            
            if (!$serviceDetailStmt->execute()) {
                throw new Exception('Failed to add service details: ' . $serviceDetailStmt->error);
            }
            $serviceDetailStmt->close();
        }
    }
    
    // Clear both cart sessions
    unset($_SESSION['cart']);
    unset($_SESSION['service_cart']);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully', 
        'orderID' => $orderID,
        'currency' => $currencyCode,
        'currencySymbol' => $currencySymbol,
        'currencyRate' => $currencyRate,
        'total' => $totalInSelectedCurrency,
        'itemsProcessed' => [
            'products' => $hasProducts ? count($_SESSION['cart'] ?? []) : 0,
            'services' => $hasServices ? count($_SESSION['service_cart'] ?? []) : 0
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>