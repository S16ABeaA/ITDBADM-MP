<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1); 

require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

header('Content-Type: application/json');

// Check if required data is present
if (!isset($_POST['serviceID'], $_POST['isFromStore'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$serviceID = (int)$_POST['serviceID'];
$isFromStore = $_POST['isFromStore'] === 'yes' ? 1 : 0; // 'yes' = 1, 'no' = 0

try {
    // Check database connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Check if service exists and get price
    $serviceCheck = $conn->prepare("SELECT ServiceID, Type, Price, Availability FROM services WHERE ServiceID = ?");
    if (!$serviceCheck) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $serviceCheck->bind_param("i", $serviceID);
    $serviceCheck->execute();
    $serviceCheck->bind_result($dbServiceID, $serviceType, $basePrice, $availability);
    $serviceCheck->fetch();
    $serviceCheck->close();

    if ($dbServiceID === null) {
        echo json_encode(['success' => false, 'message' => 'Service not found']);
        exit;
    }

    if (!$availability) {
        echo json_encode(['success' => false, 'message' => 'Service is not available']);
        exit;
    }

    // Calculate final price based on user selection
    $finalPrice = $basePrice;
    $surcharge = 0;
    
    if ($isFromStore == 0) { // If "No" (from another store)
        $surcharge = $basePrice * 0.05; // 5% surcharge
        $finalPrice = $basePrice + $surcharge;
    }

    // Initialize service cart if not exists
    if (!isset($_SESSION['service_cart'])) {
        $_SESSION['service_cart'] = [];
    }

    // Check if service is already in cart
    $cartKey = 'service_' . $serviceID . '_' . $isFromStore;
    
    if (isset($_SESSION['service_cart'][$cartKey])) {
        // Service already in cart - you can choose to update or prevent duplicates
        echo json_encode(['success' => false, 'message' => 'Service already in cart!']);
    } else {
        // Add new service to cart
        $_SESSION['service_cart'][$cartKey] = [
            'serviceID' => $serviceID,
            'serviceType' => $serviceType,
            'basePrice' => $basePrice,
            'finalPrice' => $finalPrice,
            'isFromStore' => $isFromStore,
            'surcharge' => $surcharge,
            'added_date' => date('Y-m-d H:i:s')
        ];
        echo json_encode([
            'success' => true, 
            'message' => 'Service added to cart!',
            'cartCount' => count($_SESSION['service_cart']),
            'priceDetails' => [
                'basePrice' => $basePrice,
                'surcharge' => $surcharge,
                'finalPrice' => $finalPrice,
                'isFromStore' => $isFromStore
            ]
        ]);
    }

} catch (Exception $e) {
    error_log("Add service to cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
ob_end_flush();
?>