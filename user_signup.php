<?php
require_once 'dependencies/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user-email"])) {
    $firstName = trim($_POST["firstname"]);
    $lastName = trim($_POST["lastname"]);
    $email = trim($_POST["user-email"]);
    $contact = trim($_POST["user-cell-no"]);
    $password = trim($_POST["signup-password"]);
    $confirmPassword = trim($_POST["confirm-password-input"]);

    // Validate passwords match
    if ($password !== $confirmPassword) {
        header("Location: login-signup.php?error=nomatch");
        exit();
    }

    try {
        // Call the stored procedure
        $stmt = $conn->prepare("CALL AddUser(?, ?, ?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssss", $firstName, $lastName, $contact, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            header("Location: login-signup.php?success=registered");
            exit();
        } else {
            throw new Exception("Stored procedure execution failed");
        }
        
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        
        if (strpos($errorMessage, 'Email already registered') !== false) {
            header("Location: login-signup.php?error=exists");
        } else {
            header("Location: login-signup.php?error=server");
        }
        exit();
    }
}
?>