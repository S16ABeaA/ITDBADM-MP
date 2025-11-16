<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the user insert flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['insertedit_user'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and normalize form data
$firstName = isset($_POST['userFirstName']) ? trim($_POST['userFirstName']) : '';
$lastName = isset($_POST['userLastName']) ? trim($_POST['userLastName']) : '';
$email = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : '';
$phone = isset($_POST['userPhone']) ? trim($_POST['userPhone']) : '';
$role = isset($_POST['userRole']) ? trim($_POST['userRole']) : '';
$city = isset($_POST['userCity']) ? trim($_POST['userCity']) : '';
$street = isset($_POST['userStreet']) ? trim($_POST['userStreet']) : '';
$zipCode = isset($_POST['userZipCode']) ? trim($_POST['userZipCode']) : '';
$status = isset($_POST['userStatus']) ? trim($_POST['userStatus']) : 'Active';

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($role) || empty($city) || empty($street) || empty($zipCode)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required.'
    ]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format.'
    ]);
    exit;
}

// First, insert address record
$insertAddressStmt = $conn->prepare("INSERT INTO address (City, Street, zip_code) VALUES (?, ?, ?)");
if ($insertAddressStmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare address insert failed).']);
    exit;
}
$insertAddressStmt->bind_param('sss', $city, $street, $zipCode);
$addressExec = $insertAddressStmt->execute();

if ($addressExec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to insert address: ' . $insertAddressStmt->error]);
    $insertAddressStmt->close();
    exit;
}

$addressID = $insertAddressStmt->insert_id;
$insertAddressStmt->close();

// Insert new user (using prepared statement)
// Note: Password is not required in this insert (can be set later)
$insertStmt = $conn->prepare("INSERT INTO users (FirstName, LastName, Email, MobileNumber, Role, AddressID, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($insertStmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare insert failed).']);
    exit;
}
$insertStmt->bind_param('sssssis', $firstName, $lastName, $email, $phone, $role, $addressID, '');  // Empty password for now
$insertExec = $insertStmt->execute();

if ($insertExec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to insert user: ' . $insertStmt->error]);
    $insertStmt->close();
    exit;
}

$newUserId = $insertStmt->insert_id;
$insertStmt->close();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'User has been added successfully.',
    'userId' => $newUserId,
    'data' => [
        'userId' => $newUserId,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'role' => $role,
        'city' => $city,
        'street' => $street,
        'zipCode' => $zipCode,
        'status' => $status
    ]
]);
exit;
?>
