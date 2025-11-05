<?php
require_once 'dependencies/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user-email"])) {
    $firstName = trim($_POST["firstname"]);
    $lastName = trim($_POST["lastname"]);
    $email = trim($_POST["user-email"]);
    $contact = trim($_POST["user-cell-no"]);
    $password = trim($_POST["signup-password"]);
    $confirmPassword = trim($_POST["confirm-password-input"]);

    // password match
    if ($password !== $confirmPassword) {
        header("Location: login-signup.php?show=signup&error=nomatch");
        exit();
    }

    // email already exists
    $check = $conn->prepare("SELECT Email FROM users WHERE Email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        header("Location: login-signup.php?show=signup&error=exists");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (FirstName, LastName, Email, MobileNumber, Password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $contact, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: login-signup.php?show=login&success=registered");
        exit();
    } else {
        header("Location: login-signup.php?show=signup&error=server");
        exit();
    }

    $stmt->close();
}
?>