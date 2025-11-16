<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the address update flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['insertedit_address'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and normalize form data
$addressID = isset($_POST['addressID']) ? trim($_POST['addressID']) : null;
$city = isset($_POST['addressCity']) ? trim($_POST['addressCity']) : '';
$street = isset($_POST['addressStreet']) ? trim($_POST['addressStreet']) : '';
$zipCode = isset($_POST['addressZipCode']) ? trim($_POST['addressZipCode']) : '';

// Validate required fields
if (empty($addressID) || empty($city) || empty($street) || empty($zipCode)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Address ID, City, Street, and Zip Code are required.'
    ]);
    exit;
}

// Update address using prepared statements
$stmt = $conn->prepare("UPDATE address SET City = ?, Street = ?, zip_code = ? WHERE AddressID = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
    exit;
}

$stmt->bind_param('sssi', $city, $street, $zipCode, $addressID);
$exec = $stmt->execute();

if ($exec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update address: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected >= 0) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Address has been updated successfully.',
        'addressId' => $addressID,
        'data' => [
            'addressId' => $addressID,
            'city' => $city,
            'street' => $street,
            'zipCode' => $zipCode
        ]
    ]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No rows updated.']);
    exit;
}
?>
