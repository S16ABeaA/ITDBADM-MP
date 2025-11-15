<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

header('Content-Type: application/json');

if (!isset($_POST['cartKey'], $_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$cartKey = $_POST['cartKey'];
$quantity = (int)$_POST['quantity'];

// Validate quantity
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

try {
    // Check if cart exists and item exists
    if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$cartKey])) {
        throw new Exception('Item not found in cart');
    }

    // Check stock availability
    $productID = $_SESSION['cart'][$cartKey]['productID'];
    $stockCheck = $conn->prepare("SELECT quantity FROM product WHERE ProductID = ?");
    $stockCheck->bind_param("i", $productID);
    $stockCheck->execute();
    $stockCheck->bind_result($availableQty);
    $stockCheck->fetch();
    $stockCheck->close();

    if ($availableQty < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }

    // Update quantity in session
    $_SESSION['cart'][$cartKey]['quantity'] = $quantity;

    echo json_encode(['success' => true, 'message' => 'Quantity updated']);

} catch (Exception $e) {
    error_log("Update cart quantity error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>