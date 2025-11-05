<?php 
require_once 'dependencies/config.php'; 
session_start();

$max_attempts = 3;
$lockout_time = 30; // 30secs for testing

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
        $login_successful = password_verify($password, $user['Password']);
    }

    if ($login_successful) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_name'] = $user['FirstName'];
        $_SESSION['user_email'] = $user['Email'];
        
        header("Location: homepage.php");
        exit();
    } else {
        header("Location: login-signup.php?error=invalid");
        exit();
    }
    $stmt->close();
}
?>