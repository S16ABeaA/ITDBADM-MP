<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

header('Content-Type: application/json');

if (!isset($_POST['cartKey'])) {
    echo json_encode(['success' => false, 'message' => 'Missing cart key']);
    exit;
}

$cartKey = $_POST['cartKey'];

try {
    // Check if cart exists and item exists
    if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$cartKey])) {
        throw new Exception('Item not found in cart');
    }

    // Remove item from cart
    unset($_SESSION['cart'][$cartKey]);

    // If cart is empty, remove the cart session
    if (empty($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }

    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);

} catch (Exception $e) {
    error_log("Remove from cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>