<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the delete address flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delete_address'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and validate address ID
$addressID = isset($_POST['addressID']) ? trim($_POST['addressID']) : null;

if (empty($addressID) || !is_numeric($addressID)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Address ID is required and must be numeric.'
    ]);
    exit;
}

// Check if address is used by any users before deleting
$checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE AddressID = ?");
if ($checkStmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare check failed).']);
    exit;
}
$checkStmt->bind_param('i', $addressID);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$checkRow = $checkResult->fetch_assoc();
$checkStmt->close();

if ($checkRow['count'] > 0) {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'message' => 'Cannot delete this address because it is assigned to one or more users.'
    ]);
    exit;
}

// Delete address using prepared statement
$stmt = $conn->prepare("DELETE FROM address WHERE AddressID = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
    exit;
}

$stmt->bind_param('i', $addressID);
$exec = $stmt->execute();

if ($exec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete address: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected > 0) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Address has been deleted successfully.',
        'addressId' => $addressID
    ]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Address not found.']);
    exit;
}
?>
