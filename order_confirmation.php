<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html"); 

// Get order ID from URL
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID.");
}

$orderID = (int)$_GET['order_id'];

// Simple test query first
$testSQL = "SELECT OrderID, Status, Total FROM orders WHERE OrderID = ?";
$testStmt = $conn->prepare($testSQL);
$testStmt->bind_param("i", $orderID);
$testStmt->execute();
$testResult = $testStmt->get_result();

if ($testResult->num_rows === 0) {
    die("Order not found in database.");
}

$order = $testResult->fetch_assoc();
$testStmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation - AnimoBowl</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
  <link href="./css/order_confirmation.css" rel="stylesheet">
</head>

<body>
  <div class="content-section">
    <div class="confirmation-container">
      <!-- Success Header -->
      <div class="success-header">
        <div class="success-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="success-title">Order Confirmed!</h1>
        <p class="success-message">Thank you for your purchase. Your order has been successfully placed.</p>
        <div class="order-number">Order #<?php echo $orderID; ?></div>
      </div>

      <!-- Order Details -->
      <div class="order-details">
        <div class="detail-item">
          <span class="detail-label">Order ID:</span>
          <span class="detail-value">#<?php echo $orderID; ?></span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Status:</span>
          <span class="detail-value status-<?php echo strtolower($order['Status']); ?>">
            <?php echo $order['Status']; ?>
          </span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Total Amount:</span>
          <span class="detail-value">₱<?php echo number_format($order['Total'], 2); ?></span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="action-buttons">
        <button class="btn-primary" onclick="window.print()">
          <i class="fas fa-print"></i> Print Receipt
        </button>
        <button class="btn-secondary" onclick="location.href='homepage.php'">
          <i class="fas fa-shopping-bag"></i> Continue Shopping
        </button>
      </div>

      <!-- Help Section -->
      <div class="help-section">
        <h3>Need Help?</h3>
        <p>If you have any questions about your order, please contact our customer support.</p>
        <div class="contact-info">
          <div class="contact-item">
            <i class="fas fa-phone"></i>
            <span>+63 2 1234 5678</span>
          </div>
          <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <span>support@animobowl.com</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Auto-redirect to homepage after 30 seconds
    setTimeout(() => {
      window.location.href = 'homepage.php';
    }, 30000);
  </script>
</body>
</html>

<?php include("footer.html"); ?>