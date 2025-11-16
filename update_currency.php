<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the currency update flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['insertedit_currency'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and normalize form data
$currencyID = isset($_POST['currencyID']) ? trim($_POST['currencyID']) : null;
$currencyCode = isset($_POST['currencyCode']) ? trim($_POST['currencyCode']) : '';
$exchangeRate = isset($_POST['exchangeRate']) ? trim($_POST['exchangeRate']) : '';

// Validate required fields
if (empty($currencyID) || empty($currencyCode) || empty($exchangeRate)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Currency ID, Code, and Exchange Rate are required.'
    ]);
    exit;
}

// Validate exchange rate is numeric
if (!is_numeric($exchangeRate)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Exchange Rate must be a valid number.'
    ]);
    exit;
}

// Update currency using prepared statements
$stmt = $conn->prepare("UPDATE currency SET Currency_Name = ?, Currency_Rate = ? WHERE CurrencyID = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
    exit;
}

$stmt->bind_param('sds', $currencyCode, $exchangeRate, $currencyID);
$exec = $stmt->execute();

if ($exec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update currency: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected >= 0) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Currency has been updated successfully.',
        'currencyId' => $currencyID,
        'data' => [
            'currencyId' => $currencyID,
            'currencyCode' => $currencyCode,
            'exchangeRate' => floatval($exchangeRate)
        ]
    ]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No rows updated.']);
    exit;
}
?>