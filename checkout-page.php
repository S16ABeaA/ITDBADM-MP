<?php include("header.html");?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link href="./css/checkout-page.css" rel="stylesheet">

</head>

<body>
  <div class="content-section">
    <a href="./homepage.php" class="continue-shopping">
      <i class="fas fa-arrow-left"></i> Continue Shopping
    </a>
    
    <h1 class="cart-title">CHECKOUT</h1>
    
    <div class="checkout-container">
      <!-- User Information -->
      <div class="checkout-form">
        <div class="form-section">
          <div class="section-title">
            <i class="fas fa-user"></i>
            User Information
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
            </div>
          </div>
        </div>

        <!-- Address Information -->
        <div class="form-section">
          <div class="section-title">
            <i class="fas fa-map-marker-alt"></i>
            Address Information
          </div>
          
          <div class="details-form">
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
          </div>
        </div>

        <!-- Payment Method -->
        <div class="form-section">
          <div class="section-title">
            <i class="fas fa-credit-card"></i>
            Payment Method
          </div>

            <div class="currency-selection">
              <div class="form-group">
                <label for="currency">Select Currency</label>
                <select id="currency" name="currency">
                  <option value="PHP" selected>Philippine Peso (₱)</option>
                  <option value="USD">US Dollar ($)</option>
                  <option value="KOR">Korean Won (₩)</option>
                </select>
              </div>
            </div>
          
          <div class="payment-methods">
            <label class="payment-method">
              <input type="radio" name="payment" value="credit-card" checked>
              <i class="fas fa-credit-card payment-icon"></i>
              <span>Credit Card</span>
            </label>
            
            <label class="payment-method">
              <input type="radio" name="payment" value="paypal">
              <i class="fab fa-cc-paypal payment-icon"></i>
              <span>PayPal</span>
            </label>
            
            <label class="payment-method">
              <input type="radio" name="payment" value="cash">
              <i class="fas fa-money-bill-wave payment-icon"></i>
              <span>Cash on Delivery</span>
            </label>
          </div>
        </div>

        <div class="form-section">
          <div class="section-title">
            <i class="fas fa-credit-card"></i>
            Delivery Method
          </div>
          
          <div class="delivery-methods">
            <label class="delivery-method">
              <input type="radio" name="delivery" value="delivery" checked>
              <i class="fa-solid fa-truck delivery-icon"></i>
              <span>Delivery</span>
            </label>
            
            <label class="delivery-method">
              <input type="radio" name="delivery" value="pickup">
              <i class="fas fa-store payment-icon"></i>
              <span>Pickup</span>
            </label>
          </div>
        </div>

      </div>

      <!-- Order Items at Bottom -->
      <div class="order-items-section">
        <div class="order-items-header">
          <h2 class="order-items-title">Order Items</h2>
        </div>
        
        <div class="order">
          <div class="order-image-container">
            <img class="order-image" src="./images/bowlingbag1.png" alt="Bowling Bag">
          </div>
          <div class="order-info">
            <div class="product-name">
              Premium Bowling Bag
            </div>
            <div class="product-price">
              ₱3889.99
            </div>
            <div class="product-info">
              Size: Large
            </div>
          </div>
          <div class="product-quantity">
            1
          </div>
          <div class="product-total-cost">
            ₱3889.99
          </div>
        </div>

        <div class="order">
          <div class="order-image-container">
            <img class="order-image" src="./images/bowlingball1.png" alt="Bowling Ball">
          </div>
          <div class="order-info">
            <div class="product-name">
              Professional Bowling Ball
            </div>
            <div class="product-price">
              ₱8129.99
            </div>
            <div class="product-info">
              Weight: 15 lbs
            </div>
          </div>
          <div class="product-quantity">
            2
          </div>
          <div class="product-total-cost">
            ₱16259.98
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="order-summary-section">
        <h2 class="summary-title">Order Summary</h2>
        <div class="summary-row">
          <span>Subtotal (3 items)</span>
          <span>₱20149.97</span>
        </div>
        <div class="summary-row">
          <span>Shipping</span>
          <span>₱59.99</span>
        </div>
        <div class="summary-row">
          <span>Tax</span>
          <span>₱128.00</span>
        </div>
        <div class="summary-row summary-total">
          <span>Total</span>
          <span>₱20337.96</span>
        </div>
        <button class="checkout-btn">Place Order</button>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      // Payment method selection
      $('.payment-method').on('click', function() {
        $('.payment-method').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input').prop('checked', true);
      });

      $('.delivery-method').on('click', function() {
        $('.delivery-method').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input').prop('checked', true);
      });

      // Initialize first payment method as selected
      $('.payment-method:first').addClass('selected');
      $('.delivery-method:first').addClass('selected');


    });
  </script>
</body>
</html>

<?php include("footer.html");?>