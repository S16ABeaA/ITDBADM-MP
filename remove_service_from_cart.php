<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1); 

require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

header('Content-Type: application/json');

// Check if required data is present
if (!isset($_POST['cartKey'])) {
    echo json_encode(['success' => false, 'message' => 'Missing cart key']);
    exit;
}

$cartKey = $_POST['cartKey'];

try {
    // Check if service cart exists and contains the item
    if (isset($_SESSION['service_cart']) && isset($_SESSION['service_cart'][$cartKey])) {
        // Remove the service from cart
        unset($_SESSION['service_cart'][$cartKey]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Service removed from cart',
            'remainingCount' => count($_SESSION['service_cart'])
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Service not found in cart']);
    }

} catch (Exception $e) {
    error_log("Remove service from cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

ob_end_flush();
?>