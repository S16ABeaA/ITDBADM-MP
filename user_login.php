<?php
require_once 'dependencies/config.php';
require_once 'dependencies/session.php';

$max_attempts = 3;
$lockout_time = 300; // 5 minutes in seconds

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login-email"])) {
    $email = trim($_POST["login-email"]);
    $password = trim($_POST["login-password"]);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (empty($email) || empty($password)) {
        header("Location: login-signup.php?error=empty");
        exit();
    }

    // check if user exists and get UserID
    $user_check = $conn->prepare("SELECT UserID FROM users WHERE Email = ?");
    $user_check->bind_param("s", $email);
    $user_check->execute();
    $user_result = $user_check->get_result();
    
    if ($user_result->num_rows === 0) {
        $user_check->close();
        header("Location: login-signup.php?error=invalid");
        exit();
    }
    
    $user_data = $user_result->fetch_assoc();
    $user_id = $user_data['UserID'];
    $user_check->close();

    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $login_successful = false;
    if ($user) {
        $stored = $user['Password'];
        $is_hashed = preg_match('/^\$2y\$|^\$2a\$|^\$2b\$/', $stored);
        $login_successful = password_verify($password, $stored);
    }

    // Log the login attempt using stored procedure
    $status = $login_successful ? 'success' : 'failed';
    $log_stmt = $conn->prepare("CALL logging_login_attempt(?, ?, ?)");
    $log_stmt->bind_param("iss", $user_id, $ip_address, $status);
    $log_stmt->execute();
    $log_stmt->close();

    if ($login_successful) {
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_name'] = $user['FirstName'];
        $_SESSION['user_email'] = $user['Email'];
        $_SESSION['user_role'] = $user['Role'];
        
        // Get appropriate database connection for the user's role
        $conn = getDBConnection($user['Role']);
        
        // Redirect based on role and branch selection
        if (isset($_SESSION['selected_branch_id']) && !empty($_SESSION['selected_branch_id'])) {
            if (strtolower($user['Role']) === 'staff') {
                header("Location: staffUI/staff-homepage.php");
            } else if (strtolower($user['Role']) === 'admin') {
                header("Location: adminUI/admin-homepage.php");
            } else {
                header("Location: homepage.php");
            }
        } else {
            if (strtolower($user['Role']) === 'staff') {
                header("Location: staffUI/staff-homepage.php");
            } else if (strtolower($user['Role']) === 'admin') {
                header("Location: adminUI/admin-homepage.php");
            } else {
                header("Location: select-branch.php");
            }
        }
        exit();
    } else {
        header("Location: login-signup.php?error=invalid");
        exit();
    }
}
?>