<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the currency insert flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['insertedit_currency'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and normalize form data
$currencyCode = isset($_POST['currencyCode']) ? trim($_POST['currencyCode']) : '';
$exchangeRate = isset($_POST['exchangeRate']) ? trim($_POST['exchangeRate']) : '';

// Validate required fields
if (empty($currencyCode) || empty($exchangeRate)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Currency Code and Exchange Rate are required.'
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

// Check if currency already exists (using prepared statement)
$checkStmt = $conn->prepare("SELECT CurrencyID FROM currency WHERE Currency_Name = ?");
if ($checkStmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare check failed).']);
    exit;
}
$checkStmt->bind_param('s', $currencyCode);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$checkStmt->close();

if ($checkResult->num_rows > 0) {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'message' => 'This currency code already exists.'
    ]);
    exit;
}

// Insert new currency (using prepared statement)
$insertStmt = $conn->prepare("INSERT INTO currency (Currency_Name, Currency_Rate) VALUES (?, ?)");
if ($insertStmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare insert failed).']);
    exit;
}
$insertStmt->bind_param('sd', $currencyCode, $exchangeRate);
$insertExec = $insertStmt->execute();

if ($insertExec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to insert currency: ' . $insertStmt->error]);
    $insertStmt->close();
    exit;
}

$newCurrId = $insertStmt->insert_id;
$insertStmt->close();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Currency has been added successfully.',
    'currencyId' => $newCurrId,
    'data' => [
        'currencyId' => $newCurrId,
        'currencyCode' => $currencyCode,
        'exchangeRate' => floatval($exchangeRate)
    ]
]);
exit;
?>