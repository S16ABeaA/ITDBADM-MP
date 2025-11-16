<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the address insert flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['insertedit_address'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and normalize form data
$city = isset($_POST['addressCity']) ? trim($_POST['addressCity']) : '';
$street = isset($_POST['addressStreet']) ? trim($_POST['addressStreet']) : '';
$zipCode = isset($_POST['addressZipCode']) ? trim($_POST['addressZipCode']) : '';

// Validate required fields
if (empty($city) || empty($street) || empty($zipCode)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'City, Street, and Zip Code are required.'
    ]);
    exit;
}

// Insert new address (using prepared statement)
$insertStmt = $conn->prepare("CALL AddAddress(?, ?, ?)");
if ($insertStmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare insert failed).']);
    exit;
}
$insertStmt->bind_param('sss', $city, $street, $zipCode);
$insertExec = $insertStmt->execute();

if ($insertExec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to insert address: ' . $insertStmt->error]);
    $insertStmt->close();
    exit;
}

$newAddressId = $insertStmt->insert_id;
$insertStmt->close();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Address has been added successfully.',
    'addressId' => $newAddressId,
    'data' => [
        'addressId' => $newAddressId,
        'city' => $city,
        'street' => $street,
        'zipCode' => $zipCode
    ]
]);
exit;
?>
