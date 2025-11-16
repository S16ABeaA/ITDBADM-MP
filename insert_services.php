<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the service insert flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['insertedit_service'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and normalize form data
$serviceType = isset($_POST['serviceType']) ? trim($_POST['serviceType']) : '';
$servicePrice = isset($_POST['servicePrice']) ? trim($_POST['servicePrice']) : '';
$serviceAvailability = isset($_POST['serviceAvailability']) ? trim($_POST['serviceAvailability']) : '';
$serviceDescription = isset($_POST['serviceDescription']) ? trim($_POST['serviceDescription']) : '';

// Validate required fields
if (empty($serviceType) || empty($servicePrice) || empty($serviceAvailability)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Type, Price, and Availability are required.'
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

// Insert new service (using prepared statement)
$insertStmt = $conn->prepare("INSERT INTO services (Type, Price, Availability) VALUES (?, ?, ?)");
if ($insertStmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare insert failed).']);
    exit;
}
$insertStmt->bind_param('sds', $serviceType, $servicePrice, $serviceAvailability);
$insertExec = $insertStmt->execute();

if ($insertExec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to insert service: ' . $insertStmt->error]);
    $insertStmt->close();
    exit;
}

$newServiceId = $insertStmt->insert_id;
$insertStmt->close();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Service has been added successfully.',
    'serviceId' => $newServiceId,
    'data' => [
        'serviceId' => $newServiceId,
        'serviceType' => $serviceType,
        'price' => floatval($servicePrice),
        'availability' => $serviceAvailability,
        'description' => $serviceDescription
    ]
]);
exit;
?>
