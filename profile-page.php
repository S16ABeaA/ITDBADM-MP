<?php 
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html");

$userID = $_SESSION['user_id'];

// Call stored procedure GetUserProfile
$stmt = $conn->prepare("CALL GetUserProfile(?)");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

//debugging
echo "<script>console.log('User ID: " . json_encode($user) . "');</script>";

$stmt->close();
$conn->next_result();

// Fetch user's recent orders
$ordersSQL = "SELECT o.OrderID, o.DatePurchased, o.Status, o.Total, o.PaymentMode, o.DeliveryMethod
              FROM orders o 
              WHERE o.CustomerID = ? 
              ORDER BY o.DatePurchased DESC 
              LIMIT 10";
$ordersStmt = $conn->prepare($ordersSQL);
$ordersStmt->bind_param("i", $userID);
$ordersStmt->execute();
$ordersResult = $ordersStmt->get_result();
$recentOrders = $ordersResult->fetch_all(MYSQLI_ASSOC);
$ordersStmt->close();

// Fetch order items for all orders
$orderItems = [];
if (!empty($recentOrders)) {
    $orderIDs = array_column($recentOrders, 'OrderID');
    $placeholders = str_repeat('?,', count($orderIDs) - 1) . '?';
    
    $itemsSQL = "SELECT od.OrderID, od.ProductID, od.Quantity, od.price,
                        p.ImageID,
                        COALESCE(bb.Name, bs.Name, bg.Name, ba.Name, cs.Name) as ProductName
                 FROM orderdetails od
                 JOIN product p ON od.ProductID = p.ProductID
                 LEFT JOIN bowlingball bb ON od.ProductID = bb.ProductID AND p.BranchID = bb.BranchID
                 LEFT JOIN bowlingshoes bs ON od.ProductID = bs.ProductID AND p.BranchID = bs.BranchID
                 LEFT JOIN bowlingbag bg ON od.ProductID = bg.ProductID AND p.BranchID = bg.BranchID
                 LEFT JOIN bowlingaccessories ba ON od.ProductID = ba.ProductID AND p.BranchID = ba.BranchID
                 LEFT JOIN cleaningsupplies cs ON od.ProductID = cs.ProductID AND p.BranchID = cs.BranchID
                 WHERE od.OrderID IN ($placeholders)";
    
    $itemsStmt = $conn->prepare($itemsSQL);
    $itemsStmt->bind_param(str_repeat('i', count($orderIDs)), ...$orderIDs);
    $itemsStmt->execute();
    $itemsResult = $itemsStmt->get_result();
    
    while ($item = $itemsResult->fetch_assoc()) {
        $orderItems[$item['OrderID']][] = $item;
    }
    $itemsStmt->close();
}

// Handle Save Changes POST
if (isset($_POST['saveChanges'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $city = $_POST['city'];
    $street = $_POST['street'];
    $zip = $_POST['zip'];

    $stmt = $conn->prepare("CALL ChangeUserInformation(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $userID, $firstname, $lastname, $mobile, $email, $city, $street, $zip);
    $stmt->execute();
    $stmt->close();
    $conn->next_result();

    echo "<script>alert('Profile updated successfully!'); window.location.href='profile-page.php';</script>";
    exit;
} elseif (isset($_POST['savePasswordChanges'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];

    $stmt = $conn->prepare("CALL UpdateUserPassword(?, ?)");
    $stmt->bind_param("is", $userID, $newPassword);
    $stmt->execute();
    $stmt->close();
    $conn->next_result();

    echo "<script>alert('Password changed successfully!'); window.location.href='profile-page.php';</script>";
    exit;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Page</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/profile-page.css">
</head>
<body>
  <div class="content-section">
    <div class="user-info-section">
      <div class="image-container">
        <img class="profile-picture" src="./images/default_profilepic.jpg" alt="Profile Picture">
      </div>
      
      <div class="user-info">
        <div class="name"><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></div>
        <div class="email"><?php echo htmlspecialchars($user['Email']); ?></div>
        <button id="logoutBtn" class="btn-logout">Logout</button>
      </div>
    </div>

    <div class="tabs">
      <div class="tab active" data-tab="orders">Orders</div>
      <div class="tab" data-tab="account">Account Details</div>
    </div>

    <!-- Orders Tab -->
    <div class="tab-content active" id="orders">
      <div class="orders-section">
        <div class="section-title">
          <i class="fas fa-shopping-bag"></i>
          Recent Orders
        </div>
        
        <?php if (!empty($recentOrders)): ?>
          <?php foreach ($recentOrders as $order): ?>
            <div class="order" data-order-id="<?php echo $order['OrderID']; ?>">
              <?php if (isset($orderItems[$order['OrderID']]) && !empty($orderItems[$order['OrderID']])): ?>
                <div class="order-image">
                  <img src="./images/<?php echo htmlspecialchars($orderItems[$order['OrderID']][0]['ImageID']); ?>" 
                       alt="<?php echo htmlspecialchars($orderItems[$order['OrderID']][0]['ProductName']); ?>"
                       onerror="this.src='./images/default_product.jpg'">
                </div>
                <div class="order-info">
                  <div class="order-title">
                    <?php 
                    $firstItem = $orderItems[$order['OrderID']][0];
                    echo htmlspecialchars($firstItem['ProductName']);
                    if (count($orderItems[$order['OrderID']]) > 1) {
                        echo " and " . (count($orderItems[$order['OrderID']]) - 1) . " more item(s)";
                    }
                    ?>
                    - ₱<?php echo number_format($order['Total'], 2); ?>
                  </div>
                  <div class="order-details">
                    Ordered: <?php echo date('M j, Y', strtotime($order['DatePurchased'])); ?> - 
                    Status: <span class="status-<?php echo strtolower($order['Status']); ?>"><?php echo $order['Status']; ?></span>
                  </div>
                </div>
              <?php else: ?>
                <div class="order-image">
                  <img src="./images/default_product.jpg" alt="No items">
                </div>
                <div class="order-info">
                  <div class="order-title">Order #<?php echo $order['OrderID']; ?> - ₱<?php echo number_format($order['Total'], 2); ?></div>
                  <div class="order-details">
                    Ordered: <?php echo date('M j, Y', strtotime($order['DatePurchased'])); ?> - 
                    Status: <span class="status-<?php echo strtolower($order['Status']); ?>"><?php echo $order['Status']; ?></span>
                  </div>
                </div>
              <?php endif; ?>
              <button class="view-details-btn" data-order-id="<?php echo $order['OrderID']; ?>">View Details</button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-orders">
            <i class="fas fa-shopping-cart"></i>
            <p>You haven't placed any orders yet.</p>
            <a href="homepage.php" class="btn btn-primary">Start Shopping</a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal" id="orderDetailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Order Details</h3>
          <span class="close">&times;</span>
        </div>
        <div class="modal-body">
          <div class="order-summary">
            <div class="order-header">
              <div class="order-id">Order #: <span id="modalOrderId">-</span></div>
              <div class="order-date">Order Date: <span id="modalOrderDate">-</span></div>
              <div class="order-status">Status: <span id="modalOrderStatus">-</span></div>
              <div class="order-payment">Payment: <span id="modalOrderPayment">-</span></div>
              <div class="order-delivery">Delivery: <span id="modalOrderDelivery">-</span></div>
            </div>
            
            <div class="order-items">
              <h4>Items Ordered</h4>
              <div id="modalOrderItems">
                <!-- Order items will be populated here by JavaScript -->
              </div>
            </div>
            
            <div class="order-totals">
              <div class="total-row">
                <span>Subtotal:</span>
                <span id="modalSubtotal">-</span>
              </div>
              <div class="total-row">
                <span>Shipping:</span>
                <span>₱59.99</span>
              </div>
              <div class="total-row">
                <span>Tax:</span>
                <span id="modalTax">-</span>
              </div>
              <div class="total-row grand-total">
                <span>Total:</span>
                <span id="modalTotal">-</span>
              </div>
            </div>
            
            <div class="shipping-info">
              <h4>Shipping Information</h4>
              <div class="shipping-details">
                <p><strong><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></strong></p>
                <p><?php echo htmlspecialchars($user['Street']); ?></p>
                <p><?php echo htmlspecialchars($user['City'] . ', ' . $user['zip_code']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($user['MobileNumber']); ?></p>
                <p>Email: <?php echo htmlspecialchars($user['Email']); ?></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" id="closeOrderModal">Close</button>
        </div>
      </div>
    </div>

    <!-- Account Details Tab -->
    <div class="tab-content" id="account">
      <div class="account-details-section">
        <div class="section-title">
          <i class="fas fa-user"></i>
          Account Details
        </div>

        <!-- Form to update account details -->
        <form method="POST">
          <div class="details-form">
            <div class="form-row">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['FirstName']); ?>" required>
              </div>
              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['LastName']); ?>" required>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="text" name="mobile" value="<?php echo htmlspecialchars($user['MobileNumber']); ?>" required>
            </div>

            <div class="form-group">
              <br><label for="address">Address</label>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="city">City</label>
                <input type="text" name="city" value="<?php echo htmlspecialchars($user['City']); ?>">
              </div>
              <div class="form-group">
                <label for="street">Street</label>
                <input type="text" name="street" value="<?php echo htmlspecialchars($user['Street']); ?>">
              </div>
              <div class="form-group">
                <label for="zip">ZIP Code</label>
                <input type="text" name="zip" value="<?php echo htmlspecialchars($user['zip_code']); ?>">
              </div>
            </div>

            <div class="form-actions">
              <button class="btn btn-change-password" id="changePasswordBtn" type="button">Change Password</button>
              <button class="btn btn-secondary" type="reset">Cancel</button>
              <button class="btn btn-primary" type="submit" name="saveChanges">Save Changes</button>
            </div>
          </div>
        </form>

        <div class="modal" id="changePasswordModal">
          <div class="modal-content">
            <div class="modal-header">
              <h3>Change Password</h3>
              <span class="close">&times;</span>
            </div>
            <form id="changePasswordForm" method="POST">
              <div class="modal-body">
                <div class="form-group">
                  <label for="currentPassword">Current Password</label>
                  <input type="password" id="currentPassword" name="currentPassword" placeholder="Enter current password" required>
                </div>
                <div class="form-group">
                  <label for="newPassword">New Password</label>
                  <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password" required minlength="8">
                </div>
                <div class="form-group">
                  <label for="confirmPassword">Confirm New Password</label>
                  <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelPasswordChange">Cancel</button>
                <button type="submit" class="btn btn-primary" id="savePasswordChange" name="savePasswordChanges">Change Password</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Pass PHP data to JavaScript
    const orderData = <?php echo json_encode([
        'orders' => $recentOrders,
        'orderItems' => $orderItems,
        'user' => $user
    ]); ?>;

    document.getElementById('logoutBtn').addEventListener('click', function() {
        window.location.href = 'user_logout.php';
    });

    // Tab functionality
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active class from all tabs
        document.querySelectorAll('.tab').forEach(t => {
          t.classList.remove('active');
        });
        
        // Add active class to clicked tab
        tab.classList.add('active');
        
        // Hide all tab content
        document.querySelectorAll('.tab-content').forEach(content => {
          content.classList.remove('active');
        });
        
        // Show the selected tab content
        const tabId = tab.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
      });
    });

    // Change Password Modal functionality
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const changePasswordModal = document.getElementById('changePasswordModal');
    const closeModal = document.querySelector('.close');
    const cancelPasswordChange = document.getElementById('cancelPasswordChange');
    const changePasswordForm = document.getElementById('changePasswordForm');

    // Open modal
    changePasswordBtn.addEventListener('click', () => {
      changePasswordModal.style.display = 'block';
    });

    // Close modal functions
    function closePasswordModal() {
      changePasswordModal.style.display = 'none';
      // Clear form fields and reset validation
      changePasswordForm.reset();
    }

    closeModal.addEventListener('click', closePasswordModal);
    cancelPasswordChange.addEventListener('click', closePasswordModal);

    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
      if (event.target === changePasswordModal) {
        closePasswordModal();
      }
    });

    // Handle form submission
    changePasswordForm.addEventListener('submit', (event) => {
      const currentPassword = document.getElementById('currentPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      
      if (newPassword !== confirmPassword) {
        alert('New passwords do not match');
        document.getElementById('confirmPassword').focus();
        return;
      }
      
      if (newPassword.length < 8) {
        alert('New password must be at least 8 characters long');
        document.getElementById('newPassword').focus();
        return;
      }
      
      // After validation, submit the form to PHP
      changePasswordForm.submit();
    });

    // Order Details Modal functionality
    function setupOrderDetailsModal() {
      const orderDetailsModal = document.getElementById('orderDetailsModal');
      const closeOrderModal = document.getElementById('closeOrderModal');
      const closeBtn = orderDetailsModal.querySelector('.close');

      // Function to open modal with order data
      function openOrderDetailsModal(orderId) {
        const order = orderData.orders.find(o => o.OrderID == orderId);
        const items = orderData.orderItems[orderId] || [];

        if (!order) return;

        // Populate modal with order data
        document.getElementById('modalOrderId').textContent = order.OrderID;
        document.getElementById('modalOrderDate').textContent = new Date(order.DatePurchased).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
        document.getElementById('modalOrderStatus').textContent = order.Status;
        document.getElementById('modalOrderStatus').className = `status-${order.Status.toLowerCase()}`;
        document.getElementById('modalOrderPayment').textContent = order.PaymentMode;
        document.getElementById('modalOrderDelivery').textContent = order.DeliveryMethod;

        // Calculate totals
        const subtotal = items.reduce((sum, item) => sum + (item.price * item.Quantity), 0);
        const tax = subtotal * 0.08;
        const shipping = 59.99;
        const total = subtotal + tax + shipping;

        // Populate order items
        const itemsContainer = document.getElementById('modalOrderItems');
        itemsContainer.innerHTML = '';

        if (items.length > 0) {
          items.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'order-item';
            itemElement.innerHTML = `
              <div class="item-image">
                <img src="./images/${item.ImageID}" alt="${item.ProductName}" onerror="this.src='./images/default_product.jpg'">
              </div>
              <div class="item-details">
                <div class="item-name">${item.ProductName}</div>
                <div class="item-price">₱${parseFloat(item.price).toFixed(2)}</div>
                <div class="item-quantity">Quantity: ${item.Quantity}</div>
              </div>
              <div class="item-total">₱${(item.price * item.Quantity).toFixed(2)}</div>
            `;
            itemsContainer.appendChild(itemElement);
          });
        } else {
          itemsContainer.innerHTML = '<p>No items found for this order.</p>';
        }

        // Populate totals
        document.getElementById('modalSubtotal').textContent = `₱${subtotal.toFixed(2)}`;
        document.getElementById('modalTax').textContent = `₱${tax.toFixed(2)}`;
        document.getElementById('modalTotal').textContent = `₱${order.Total}`;

        // Show modal
        orderDetailsModal.style.display = 'block';
      }

      // Function to close modal
      function closeOrderModalFunc() {
        orderDetailsModal.style.display = 'none';
      }

      // Close modal events
      closeOrderModal.addEventListener('click', closeOrderModalFunc);
      closeBtn.addEventListener('click', closeOrderModalFunc);

      // Close modal when clicking outside
      window.addEventListener('click', (event) => {
        if (event.target === orderDetailsModal) {
          closeOrderModalFunc();
        }
      });

      // Add event listeners to view details buttons
      document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', () => {
          const orderId = button.getAttribute('data-order-id');
          openOrderDetailsModal(orderId);
        });
      });
    }

    // Initialize the modal when page loads
    document.addEventListener('DOMContentLoaded', setupOrderDetailsModal);
  </script>
</body>
</html>

<?php include("footer.html")?>