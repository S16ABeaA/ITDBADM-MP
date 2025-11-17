<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

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

    // Step 1: Check if user exists and get UserID
    $user_check = $conn->prepare("SELECT UserID FROM users WHERE Email = ?");
    $user_check->bind_param("s", $email);
    $user_check->execute();
    $user_result = $user_check->get_result();
    
    if ($user_result->num_rows === 0) {
        $record_attempt = $conn->prepare("INSERT INTO login_attempts (UserID, IPAddress, Successful) VALUES (NULL, ?, FALSE)");
        $record_attempt->bind_param("s", $ip_address);
        $record_attempt->execute();
        $record_attempt->close();
        
        $user_check->close();
        header("Location: login-signup.php?error=invalid");
        exit();
    }
    
    $user_data = $user_result->fetch_assoc();
    $user_id = $user_data['UserID'];
    $user_check->close();

    // Step 2: Check if user is locked out
    $lock_check = $conn->prepare("
        SELECT COUNT(*) as recent_failures 
        FROM login_attempts 
        WHERE UserID = ? 
        AND AttemptTime > DATE_SUB(NOW(), INTERVAL ? SECOND)
        AND Successful = FALSE
    ");
    $lock_check->bind_param("ii", $user_id, $lockout_time);
    $lock_check->execute();
    $lock_result = $lock_check->get_result();
    $lock_data = $lock_result->fetch_assoc();
    $lock_check->close();

    if ($lock_data['recent_failures'] >= $max_attempts) {
        header("Location: login-signup.php?error=locked");
        exit();
    }

    // Step 3: Verify credentials
    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $login_successful = false;
    if ($user) {
        $stored = $user['Password'];
        $is_hashed = preg_match('/^\$2y\$|^\$2a\$|^\$2b\$/', $stored);

        if ($is_hashed) {
            $login_successful = password_verify($password, $stored);
        } else {
            // Legacy password verification
            if (hash_equals((string)$stored, (string)$password)) {
                $login_successful = true;
                // Rehash password
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
                $update->bind_param("si", $newHash, $user['UserID']);
                $update->execute();
                $update->close();
            }
        }
    }

    // Step 4: Record the attempt
    $record_attempt = $conn->prepare("INSERT INTO login_attempts (UserID, IPAddress, Successful) VALUES (?, ?, ?)");
    $success_val = $login_successful ? 1 : 0;
    $record_attempt->bind_param("isi", $user_id, $ip_address, $success_val);
    $record_attempt->execute();
    $record_attempt->close();

    // Step 5: Handle login result
    if ($login_successful) {
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_name'] = $user['FirstName'];
        $_SESSION['user_email'] = $user['Email'];
        $_SESSION['user_role'] = $user['Role'];
        
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