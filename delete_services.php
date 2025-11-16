<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the delete service flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delete_service'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and validate service ID
$serviceID = isset($_POST['serviceID']) ? trim($_POST['serviceID']) : null;

if (empty($serviceID) || !is_numeric($serviceID)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Service ID is required and must be numeric.'
    ]);
    exit;
}

// Delete service using prepared statement
$stmt = $conn->prepare("DELETE FROM services WHERE ServiceID = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
    exit;
}

$stmt->bind_param('i', $serviceID);
$exec = $stmt->execute();

if ($exec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete service: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected > 0) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Service has been deleted successfully.',
        'serviceId' => $serviceID
    ]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Service not found.']);
    exit;
}
?>
