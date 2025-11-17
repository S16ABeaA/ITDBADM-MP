<?php
require_once 'dependencies/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user-email"])) {
    $firstName = trim($_POST["firstname"]);
    $lastName = trim($_POST["lastname"]);
    $email = trim($_POST["user-email"]);
    $contact = trim($_POST["user-cell-no"]);
    $password = trim($_POST["signup-password"]);
    $confirmPassword = trim($_POST["confirm-password-input"]);

    if ($password !== $confirmPassword) { // passwords do not match
        header("Location: login-signup.php?show=signup&error=nomatch");
        exit();
    }

    $check = $conn->prepare("SELECT Email FROM users WHERE Email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        header("Location: login-signup.php?show=signup&error=exists");
        exit();
    }

    // Start transaction for atomic operations
    $conn->begin_transaction();

    try {
        // Insert default address
        $defaultCity = "City";
        $defaultStreet = "Street"; 
        $defaultZipCode = "1234";
        
        $addressStmt = $conn->prepare("INSERT INTO address (City, Street, zip_code) VALUES (?, ?, ?)");
        $addressStmt->bind_param("sss", $defaultCity, $defaultStreet, $defaultZipCode);
        
        if (!$addressStmt->execute()) {
            throw new Exception("Failed to create address");
        }
        
        // Get the newly created AddressID
        $addressID = $conn->insert_id;
        $addressStmt->close();

        // Insert user with the address reference
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userStmt = $conn->prepare("INSERT INTO users (FirstName, LastName, Email, MobileNumber, Password, AddressID) VALUES (?, ?, ?, ?, ?, ?)");
        $userStmt->bind_param("sssssi", $firstName, $lastName, $email, $contact, $hashedPassword, $addressID);

        if (!$userStmt->execute()) {
            throw new Exception("Failed to create user");
        }

        // Commit transaction if both operations succeed
        $conn->commit();
        
        header("Location: login-signup.php?show=login&success=registered");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction if any operation fails
        $conn->rollback();
        header("Location: login-signup.php?show=signup&error=server");
        exit();
    }
}
?>