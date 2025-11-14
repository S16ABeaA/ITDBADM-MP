<?php 
  require_once 'dependencies/session.php';
  require_once 'dependencies/config.php';
  include("header.html")
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
        <div class="name">User 1</div>
        <div class="email">user.one@example.com</div>
        <button id="logoutBtn" class="btn-logout">Logout</button>
      </div>
    </div>

    <div class="tabs">
      <div class="tab active" data-tab="orders">Orders</div>
      <div class="tab" data-tab="account">Account Details</div>
      <div class="tab" data-tab="payment">Payment Methods</div>
    </div>

    <!-- Orders Tab -->
    <div class="tab-content active" id="orders">
      <div class="orders-section">
        <div class="section-title">
          <i class="fas fa-shopping-bag"></i>
          Recent Orders
        </div>
        
        <div class="order">
          <div class="order-image">
            <img src="./images/bowlingball1.png" alt="Ball">
          </div>
          <div class="order-info">
            <div class="order-title">Ball - &#8369; 1129.99</div>
            <div class="order-details">Ordered: Oct 12, 2023 - Status: Delivered</div>
          </div>
          <button>View Details</button>
        </div>
        
        <div class="order">
          <div class="order-image">
            <img src="./images/bowlingbag1.png" alt="Bag">
          </div>
          <div class="order-info">
            <div class="order-title">Bowling Bag - &#8369; 1199.99</div>
            <div class="order-details">Ordered: Oct 5, 2023 - Status: Shipped</div>
          </div>
          <button>View Details</button>
        </div>
        
        <div class="order">
          <div class="order-image">
            <img src="./images/cleaningsupplies.png" alt="Cleaning Supplies">
          </div>
          <div class="order-info">
            <div class="order-title">Cleaning Supplies - &#8369; 334.99</div>
            <div class="order-details">Ordered: Sep 28, 2023 - Status: Delivered</div>
          </div>
          <button>View Details</button>
        </div>
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
              <div class="order-id">Order #: <span id="modalOrderId">ORD-123456</span></div>
              <div class="order-date">Order Date: <span id="modalOrderDate">Oct 12, 2023</span></div>
              <div class="order-status">Status: <span id="modalOrderStatus" class="status-delivered">Delivered</span></div>
            </div>
            
            <div class="order-items">
              <h4>Items Ordered</h4>
              <div class="order-item">
                <div class="item-image">
                  <img src="./images/bowlingball1.png" alt="Ball">
                </div>
                <div class="item-details">
                  <div class="item-name">Professional Bowling Ball</div>
                  <div class="item-price">₱1129.99</div>
                  <div class="item-quantity">Quantity: 1</div>
                </div>
                <div class="item-total">₱1129.99</div>
              </div>
              
              <div class="order-item">
                <div class="item-image">
                  <img src="./images/bowlingbag1.png" alt="Bag">
                </div>
                <div class="item-details">
                  <div class="item-name">Premium Bowling Bag</div>
                  <div class="item-price">₱1199.99</div>
                  <div class="item-quantity">Quantity: 1</div>
                </div>
                <div class="item-total">₱1199.99</div>
              </div>
            </div>
            
            <div class="order-totals">
              <div class="total-row">
                <span>Subtotal:</span>
                <span>₱2329.98</span>
              </div>
              <div class="total-row">
                <span>Shipping:</span>
                <span>₱59.99</span>
              </div>
              <div class="total-row">
                <span>Tax:</span>
                <span>₱128.00</span>
              </div>
              <div class="total-row grand-total">
                <span>Total:</span>
                <span>₱2517.97</span>
              </div>
            </div>
            
            <div class="shipping-info">
              <h4>Shipping Information</h4>
              <div class="shipping-details">
                <p><strong>User One</strong></p>
                <p>123 Main Street</p>
                <p>Manila, MNL 1000</p>
                <p>Phone: 1234 567 8910</p>
                <p>Email: user.one@example.com</p>
              </div>
            </div>
            
            <div class="tracking-info">
              <h4>Tracking Information</h4>
              <div class="tracking-details">
                <div class="tracking-number">
                  <strong>Tracking Number:</strong> TRK-789456123
                </div>
                <div class="tracking-carrier">
                  <strong>Carrier:</strong> LBC Express
                </div>
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
        
        <div class="details-form">
          <div class="form-row">
            <div class="form-group">
              <label for="firstName">First Name</label>
              <input type="text" id="firstName" value="User">
            </div>
            <div class="form-group">
              <label for="lastName">Last Name</label>
              <input type="text" id="lastName" value="One">
            </div>
          </div>
          
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" value="user.one@example.com">
          </div>
          
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" value="1234 567 8910">
          
          <div class="form-group">
            <label for="birthdate">Date of Birth</label>
            <input type="date" id="birthdate" value="2005-05-15">
          </div>
          
          <div class="form-group">
            <label for="address">Address</label>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="city">City</label>
              <input type="text" id="city" value="Manila">
            </div>
            <div class="form-group">
              <label for="state">State</label>
              <input type="text" id="state" value="MNL">
            </div>
            <div class="form-group">
              <label for="zip">ZIP Code</label>
              <input type="text" id="zip" value="1000">
            </div>
          </div>
          
          <div class="form-actions">
            <button class="btn btn-change-password" id="changePasswordBtn">Change Password</button>
            <button class="btn btn-secondary">Cancel</button>
            <button class="btn btn-primary">Save Changes</button>
          </div>

         <div class="modal" id="changePasswordModal">
            <div class="modal-content">
              <div class="modal-header">
                <h3>Change Password</h3>
                <span class="close">&times;</span>
              </div>
              <form id="changePasswordForm">
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
                  <button type="submit" class="btn btn-primary" id="savePasswordChange">Change Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>

     <!-- Payment Methods Tab -->
    <div class="tab-content" id="payment">
      <div class="payment-methods-section">
        <div class="section-title">
          <i class="fas fa-credit-card"></i>
          Payment Methods
        </div>
        
        <div class="payment-methods">
          <div class="payment-card default">
            <div class="payment-type">
              <i class="fab fa-cc-visa payment-icon"></i>
              <span>Visa</span>
            </div>
            <div class="card-number">**** **** **** 4242</div>
            <div class="card-details">
              <span>Expires: 05/2025</span>
              <span class="default-badge">Default</span>
            </div>
            <div class="card-actions">
              <span class="remove-card">Remove</span>
            </div>
          </div>
          
          <div class="payment-card">
            <div class="payment-type">
              <i class="fab fa-cc-mastercard payment-icon"></i>
              <span>Mastercard</span>
            </div>
            <div class="card-number">**** **** **** 5555</div>
            <div class="card-details">
              <span>Expires: 11/2024</span>
              <span class="set-default">Set as Default</span>
            </div>
            <div class="card-actions">
              <span class="remove-card">Remove</span>
            </div>
          </div>
          
          <div class="payment-card">
            <div class="payment-type">
              <i class="fab fa-paypal payment-icon"></i>
              <span>PayPal</span>
            </div>
            <div class="card-number">user.one@example.com</div>
            <div class="card-details">
              <span>Connected</span>
              <span class="set-default">Set as Default</span>
            </div>
            <div class="card-actions">
              <span class="remove-card">Remove</span>
            </div>
          </div>
        </div>
        
        <div class="add-payment-method">
          <div class="add-payment-title">Add New Payment Method</div>
          
          <div class="payment-type-selector">
            <div class="payment-type-option selected">
              <i class="fas fa-credit-card payment-type-icon"></i>
              <div>Credit Card</div>
            </div>
            <div class="payment-type-option">
              <i class="fab fa-paypal payment-type-icon"></i>
              <div>PayPal</div>
            </div>
            <div class="payment-type-option">
              <i class="fab fa-apple payment-type-icon"></i>
              <div>Apple Pay</div>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="cardNumber">Card Number</label>
              <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="cardName">Name on Card</label>
              <input type="text" id="cardName" placeholder="User">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="expiryDate">Expiry Date</label>
              <input type="text" id="expiryDate" placeholder="MM/YY">
            </div>
            <div class="form-group">
              <label for="cvv">CVV</label>
              <input type="text" id="cvv" placeholder="123">
            </div>
          </div>
          
          <div class="form-actions">
            <button class="btn btn-primary">Add Payment Method</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
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
      event.preventDefault(); // Prevent default form submission
      
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
      
      console.log('Password change requested');
      console.log('Current:', currentPassword);
      console.log('New:', newPassword);
      
      
      alert('Password changed successfully!');
      closePasswordModal();
    });



        // Payment method type selection
        document.querySelectorAll('.payment-type-option').forEach(option => {
          option.addEventListener('click', () => {
            document.querySelectorAll('.payment-type-option').forEach(o => {
              o.classList.remove('selected');
            });
            option.classList.add('selected');
          });
        });

        // Set default payment method
    function setupPaymentMethodListeners() {
      // Remove existing listeners first
      document.querySelectorAll('.set-default').forEach(link => {
        link.replaceWith(link.cloneNode(true));
      });
      
      // Add new listeners to all "Set as Default" links
      document.querySelectorAll('.set-default').forEach(link => {
        link.addEventListener('click', function() {
          // Reset ALL payment cards
          document.querySelectorAll('.payment-card').forEach(card => {
            card.classList.remove('default');
            const statusElement = card.querySelector('.card-details span:last-child');
            if (statusElement) {
              statusElement.textContent = 'Set as Default';
              statusElement.className = 'set-default';
            }
          });
          
          // Set new default
          const clickedCard = this.closest('.payment-card');
          clickedCard.classList.add('default');
          
          // Update status
          const statusElement = clickedCard.querySelector('.card-details span:last-child');
          if (statusElement) {
            statusElement.textContent = 'Default';
            statusElement.className = 'default-badge';
          }
          
          // Re-setup listeners for the new state
          setupPaymentMethodListeners();
        });
      });
    }

    // Initial setup
    setupPaymentMethodListeners();

        // Remove payment method
        document.querySelectorAll('.remove-card').forEach(link => {
          link.addEventListener('click', () => {
            if (confirm('Are you sure you want to remove this payment method?')) {
              link.closest('.payment-card').remove();
            }
          });
        });


    // Order Details Modal functionality
function setupOrderDetailsModal() {
  const orderDetailsModal = document.getElementById('orderDetailsModal');
  const closeOrderModal = document.getElementById('closeOrderModal');
  const closeBtn = orderDetailsModal.querySelector('.close');
  
  // Function to open modal with order data
  function openOrderDetailsModal(orderData) {
    // Populate modal with order data
    document.getElementById('modalOrderId').textContent = orderData.id;
    document.getElementById('modalOrderDate').textContent = orderData.date;
    document.getElementById('modalOrderStatus').textContent = orderData.status;
    document.getElementById('modalOrderStatus').className = `status-${orderData.status.toLowerCase()}`;
    
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
  
  // Update existing order button listeners to use the modal
  document.querySelectorAll('.order button').forEach((button, index) => {
    button.addEventListener('click', () => {
      // Sample order data - in real app, this would come from your data source
      const sampleOrders = [
        {
          id: 'ORD-789456',
          date: 'Oct 12, 2023',
          status: 'Delivered',
          items: [
            { name: 'Professional Bowling Ball', price: 1129.99, quantity: 1, image: './images/bowlingball1.png' }
          ],
          subtotal: 1129.99,
          shipping: 59.99,
          tax: 67.20,
          total: 1257.18
        },
        {
          id: 'ORD-123456',
          date: 'Oct 5, 2023',
          status: 'Shipped',
          items: [
            { name: 'Premium Bowling Bag', price: 1199.99, quantity: 1, image: './images/bowlingbag1.png' }
          ],
          subtotal: 1199.99,
          shipping: 59.99,
          tax: 71.40,
          total: 1331.38
        },
        {
          id: 'ORD-456123',
          date: 'Sep 28, 2023',
          status: 'Delivered',
          items: [
            { name: 'Cleaning Supplies', price: 334.99, quantity: 1, image: './images/cleaningsupplies.png' }
          ],
          subtotal: 334.99,
          shipping: 59.99,
          tax: 19.80,
          total: 414.78
        }
      ];
      
      openOrderDetailsModal(sampleOrders[index]);
    });
  });
}

// Initialize the modal when page loads
document.addEventListener('DOMContentLoaded', setupOrderDetailsModal);
  </script>
</body>
</html>

<?php include("footer.html")?>