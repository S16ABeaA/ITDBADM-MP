<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login-signup.php');
    exit();
}

// If user already has a selected branch, redirect to homepage
if (isset($_SESSION['selected_branch_id']) && !empty($_SESSION['selected_branch_id'])) {
    header('Location: homepage.php');
    exit();
}

// Handle branch selection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['branch_id'])) {
    $branch_id = intval($_POST['branch_id']);
    
    // Validate branch exists
    $sql = "SELECT BranchID, Name FROM branches WHERE BranchID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $branch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $branch = $result->fetch_assoc();
        $_SESSION['selected_branch_id'] = $branch_id;
        $_SESSION['selected_branch_name'] = $branch['Name'];
        
        // Redirect to homepage after successful branch selection
        header('Location: homepage.php');
        exit();
    } else {
        $error = "Invalid branch selected";
    }
    
    $stmt->close();
}

$sql = "SELECT BranchID, Name FROM branches";
$branches = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Branch | AnimoBowl</title>
    <link rel="stylesheet" href="./css/select-branch.css">
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
</head>
<body>
    <div class="branch-selection-container">
        <div class="branch-selection-card">
            <h1>Select Your Branch</h1>
            <p>Please choose a branch to continue shopping:</p>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="select-branch.php">
                <div class="branch-options">
                    <?php if ($branches && $branches->num_rows > 0): ?>
                        <?php while ($branch = $branches->fetch_assoc()): ?>
                            <div class="branch-option">
                                <input type="radio" name="branch_id" value="<?php echo $branch['BranchID']; ?>" id="branch<?php echo $branch['BranchID']; ?>" required>
                                <label for="branch<?php echo $branch['BranchID']; ?>">
                                    <?php echo htmlspecialchars($branch['Name']); ?>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No branches available at the moment.</p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="confirm-branch-btn">Confirm Branch</button>
            </form>
        </div>
    </div>
</body>
</html>