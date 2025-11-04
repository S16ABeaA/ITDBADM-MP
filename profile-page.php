<?php include("header.html")?>
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
            <input type="text" id="address" value="123 Main Street">
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

    // Order view buttons
    document.querySelectorAll('.order button').forEach(button => {
      button.addEventListener('click', () => {
        alert('Order details would be displayed here');
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
  
  // Enhanced validation
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
  
  // Here you would typically make an API call to change the password
  console.log('Password change requested');
  console.log('Current:', currentPassword);
  console.log('New:', newPassword);
  
  // Simulate successful password change
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
  </script>
</body>
</html>

<?php include("footer.html")?>