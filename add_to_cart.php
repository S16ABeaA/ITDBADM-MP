<?php
ob_start(); // Start output buffering
// Enable error reporting but don't display to users
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't output errors to browser
ini_set('log_errors', 1); // Log errors instead

require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

header('Content-Type: application/json');

// Check if required data is present
if (!isset($_POST['productID'], $_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$productID = (int)$_POST['productID'];
$quantity = (int)$_POST['quantity'];

// Validate quantity
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

try {
    // Check database connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Get branch ID from session
    $branchID = $_SESSION['selected_branch_id'] ?? 0;
    if ($branchID === 0) {
        throw new Exception('No branch selected');
    }

    // Check if product exists and has stock in the selected branch
    $stockCheck = $conn->prepare("SELECT p.quantity, p.Price 
                                 FROM product p 
                                 WHERE p.ProductID = ?");
    if (!$stockCheck) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stockCheck->bind_param("i", $productID);
    $stockCheck->execute();
    $stockCheck->bind_result($availableQty, $productPrice);
    $stockCheck->fetch();
    $stockCheck->close();

    if ($availableQty === null) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    if ($availableQty < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }

    // Make sure session order exists - we'll use a temporary cart session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product is already in cart
    $cartKey = $productID . '_' . $branchID;
    
    if (isset($_SESSION['cart'][$cartKey])) {
        // Update quantity if product already in cart
        $newQty = $_SESSION['cart'][$cartKey]['quantity'] + $quantity;
        
        // Check if new quantity exceeds available stock
        if ($newQty > $availableQty) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
            exit;
        }
        
        $_SESSION['cart'][$cartKey]['quantity'] = $newQty;
        echo json_encode(['success' => true, 'message' => 'Quantity updated in cart!']);
    } else {
        // Add new product to cart
        $_SESSION['cart'][$cartKey] = [
            'productID' => $productID,
            'quantity' => $quantity,
            'price' => $productPrice,
            'branchID' => $branchID
        ];
        echo json_encode(['success' => true, 'message' => 'Added to cart!']);
    }

} catch (Exception $e) {
    error_log("Add to cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
ob_end_flush(); // Send output buffer and turn off buffering
?>