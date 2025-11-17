<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the service update flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['insertedit_service'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and normalize form data
$serviceID = isset($_POST['serviceID']) ? trim($_POST['serviceID']) : null;
$serviceType = isset($_POST['serviceType']) ? trim($_POST['serviceType']) : '';
$servicePrice = isset($_POST['servicePrice']) ? trim($_POST['servicePrice']) : '';
$serviceAvailability = isset($_POST['serviceAvailability']) ? trim($_POST['serviceAvailability']) : '';

// Validate required fields
if (empty($serviceID) || empty($serviceType) || empty($servicePrice) || empty($serviceAvailability)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'All required fields must be provided.'
    ]);
    exit;
}

// Validate price is numeric
if (!is_numeric($servicePrice)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Price must be a valid number.'
    ]);
    exit;
}

// Update service using prepared statements
$stmt = $conn->prepare("UPDATE services SET Type = ?, Price = ?, Availability = ? WHERE ServiceID = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
    exit;
}

$stmt->bind_param('sdsi', $serviceType, $servicePrice, $serviceAvailability, $serviceID);
$exec = $stmt->execute();

if ($exec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update service: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected >= 0) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Service has been updated successfully.',
        'serviceId' => $serviceID,
        'data' => [
            'serviceId' => $serviceID,
            'serviceType' => $serviceType,
            'price' => floatval($servicePrice),
            'availability' => $serviceAvailability
        ]
    ]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No rows updated.']);
    exit;
}
?>
