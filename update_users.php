<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

// Only process POST requests with the user update flag
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['insertedit_user'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

// Retrieve and normalize form data
$userID = isset($_POST['userID']) ? trim($_POST['userID']) : null;
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
if (empty($userID) || empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($role) || empty($city) || empty($street) || empty($zipCode)) {
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

// Get the user's current AddressID
$getAddressStmt = $conn->prepare("SELECT AddressID FROM users WHERE UserID = ?");
if ($getAddressStmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (get address failed).']);
    exit;
}
$getAddressStmt->bind_param('i', $userID);
$getAddressStmt->execute();
$addressResult = $getAddressStmt->get_result();
$getAddressStmt->close();

if ($addressResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$userRow = $addressResult->fetch_assoc();
$addressID = $userRow['AddressID'];

// Update address first
if ($addressID) {
    $updateAddressStmt = $conn->prepare("UPDATE address SET City = ?, Street = ?, zip_code = ? WHERE AddressID = ?");
    if ($updateAddressStmt === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error (update address prepare failed).']);
        exit;
    }
    $updateAddressStmt->bind_param('sssi', $city, $street, $zipCode, $addressID);
    if (!$updateAddressStmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update address: ' . $updateAddressStmt->error]);
        $updateAddressStmt->close();
        exit;
    }
    $updateAddressStmt->close();
}

// Update user using prepared statements
$stmt = $conn->prepare("UPDATE users SET FirstName = ?, LastName = ?, Email = ?, MobileNumber = ?, Role = ? WHERE UserID = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
    exit;
}

$stmt->bind_param('ssssi', $firstName, $lastName, $email, $phone, $role, $userID);
$exec = $stmt->execute();

if ($exec === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update user: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected >= 0) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'User has been updated successfully.',
        'userId' => $userID,
        'data' => [
            'userId' => $userID,
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
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No rows updated.']);
    exit;
}
?>
