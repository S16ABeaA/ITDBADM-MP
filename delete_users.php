<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the delete user flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delete_user'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and validate user ID
$userID = isset($_POST['userID']) ? trim($_POST['userID']) : null;

if (empty($userID) || !is_numeric($userID)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'User ID is required and must be numeric.'
    ]);
    exit;
}

// Delete user using prepared statement
$stmt = $conn->prepare("CALL DeleteUser(?)");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
    exit;
}

$stmt->bind_param('i', $userID);
$exec = $stmt->execute();

if ($exec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete user: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected > 0) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'User has been deleted successfully.',
        'userId' => $userID
    ]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}
?>
