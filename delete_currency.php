<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the delete currency flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delete_currency'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and validate currency ID
$currencyID = isset($_POST['currencyID']) ? trim($_POST['currencyID']) : null;

if (empty($currencyID) || !is_numeric($currencyID)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Currency ID is required and must be numeric.'
    ]);
    exit;
}

// Delete currency using prepared statement
$stmt = $conn->prepare("DELETE FROM currency WHERE CurrencyID = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
    exit;
}

$stmt->bind_param('i', $currencyID);
$exec = $stmt->execute();

if ($exec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete currency: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected > 0) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Currency has been deleted successfully.',
        'currencyId' => $currencyID
    ]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Currency not found.']);
    exit;
}
?>
