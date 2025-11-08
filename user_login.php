<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

$max_attempts = 3;
$lockout_time = 30; // 30 secs for testing

if (isset($_SESSION['login_attempts']) && isset($_SESSION['last_attempt_time'])) {
    if ($_SESSION['login_attempts'] >= $max_attempts && 
        (time() - $_SESSION['last_attempt_time']) < $lockout_time) {
        header("Location: login-signup.php?error=locked");
        exit();
    } else if ((time() - $_SESSION['last_attempt_time']) >= $lockout_time) {
        $_SESSION['login_attempts'] = 0;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login-email"])) {
    $email = trim($_POST["login-email"]);
    $password = trim($_POST["login-password"]);

    if (empty($email) || empty($password)) {
        header("Location: login-signup.php?error=invalid");
        exit();
    }

    $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;
    $_SESSION['last_attempt_time'] = time();

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
            if (hash_equals((string)$stored, (string)$password)) {
                $login_successful = true;

                // Rehash to bcrypt
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
                if ($update) {
                    $update->bind_param("si", $newHash, $user['UserID']);
                    $update->execute();
                    $update->close();
                }
            }
        }
    }

    if ($login_successful) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_name'] = $user['FirstName'];
        $_SESSION['user_email'] = $user['Email'];

        // Instead of redirecting immediately, output HTML with JS alert
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Login Successful</title>
            <script>
                alert("Login Successful!\\nUser ID: '.$_SESSION['user_id'].'\\nName: '.$_SESSION['user_name'].'\\nEmail: '.$_SESSION['user_email'].'");
                window.location.href = "homepage.php"; // redirect after alert
            </script>
        </head>
        <body></body>
        </html>';
        exit();
    }


    $stmt->close();
    header("Location: login-signup.php?error=invalid");
    exit();
}
?>