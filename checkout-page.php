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
$stmt->close();
$conn->next_result();

// Get all currency rates using the stored procedure
$currency_stmt = $conn->prepare("CALL GetCurrencyRate()");
$currency_stmt->execute();
$currency_result = $currency_stmt->get_result();
$currencies = array();

while ($currency = $currency_result->fetch_assoc()) {
    $currencies[$currency['Currency_Name']] = array(
        'rate' => $currency['Currency_Rate'],
        'symbol' => $currency['Symbol'],
        'id' => $currency['CurrencyID']
    );
}
$currency_stmt->close();
$conn->next_result();

// Set default currency
$default_currency = 'PHP';
$current_currency = $default_currency;
$current_symbol = $currencies[$default_currency]['symbol'];
$current_rate = $currencies[$default_currency]['rate'];

//debugging
echo "<script>console.log('User ID: " . json_encode($user) . "');</script>";
echo "<script>console.log('Currencies: " . json_encode($currencies) . "');</script>";
?>

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
                <input type="text" id="firstName" value="<?php echo htmlspecialchars($user['FirstName']); ?>" readonly>
              </div>
              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" value="<?php echo htmlspecialchars($user['LastName']); ?>" readonly>
              </div>
            </div>
            
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" value="<?php echo htmlspecialchars($user['Email']); ?>" readonly>
            </div>
            
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" value="<?php echo htmlspecialchars($user['MobileNumber']); ?>" readonly>
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
              <input type="text" id="address" value="<?php echo htmlspecialchars($user['Street']); ?>" readonly>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" value="<?php echo htmlspecialchars($user['City']); ?>" readonly>
              </div>
              <div class="form-group">
                <label for="street">Street</label>
                <input type="text" id="street" value="<?php echo htmlspecialchars($user['Street']); ?>" readonly>
              </div>
              <div class="form-group">
                <label for="zip">ZIP Code</label>
                <input type="text" id="zip" value="<?php echo htmlspecialchars($user['zip_code']); ?>" readonly>
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
                      <?php foreach ($currencies as $code => $currency_data): ?>
                          <option value="<?php echo $code; ?>" 
                                  data-rate="<?php echo $currency_data['rate']; ?>"
                                  data-symbol="<?php echo htmlspecialchars($currency_data['symbol']); ?>"
                                  <?php echo $code === $default_currency ? 'selected' : ''; ?>>
                              <?php echo $code . ' (' . htmlspecialchars($currency_data['symbol']) . ')'; ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              </div>
          </div>
          
          <div class="payment-methods">
            <label class="payment-method">
              <input type="radio" name="payment" value="Cash" checked>
              <i class="fas fa-money-bill-wave payment-icon"></i>
              <span>Cash</span>
            </label>
            
            <label class="payment-method">
              <input type="radio" name="payment" value="Credit Card">
              <i class="fas fa-credit-card payment-icon"></i>
              <span>Credit Card</span>
            </label>
            
            <label class="payment-method">
              <input type="radio" name="payment" value="Online">
              <i class="fas fa-wallet payment-icon"></i>
              <span>Online</span>
            </label>
          </div>
        </div>

        <div class="form-section">
          <div class="section-title">
            <i class="fas fa-truck"></i>
            Delivery Method
          </div>
          
          <div class="delivery-methods">
            <label class="delivery-method">
              <input type="radio" name="delivery" value="Delivery" checked>
              <i class="fa-solid fa-truck delivery-icon"></i>
              <span>Delivery</span>
            </label>
            
            <label class="delivery-method">
              <input type="radio" name="delivery" value="Pickup">
              <i class="fas fa-store delivery-icon"></i>
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
        
        <?php
        // Check if cart exists and has items
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
          
          // Get product details for all items in cart
          $cartItems = array();
          $itemCount = 0;
          $subtotal = 0;
          
          foreach ($_SESSION['cart'] as $cartKey => $cartItem) {
            $productID = $cartItem['productID'];
            $branchID = $cartItem['branchID'];
            
            // Get product basic info
            $sql = "SELECT p.ImageID, p.Price FROM product p WHERE p.ProductID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $productID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
              $product = $result->fetch_assoc();
              
              $productName = "Product Name Not Found";
              
              // Check each category table for the product name
              $tables = array(
                'bowlingball' => 'SELECT Name FROM bowlingball WHERE ProductID = ? AND BranchID = ?',
                'bowlingbag' => 'SELECT Name FROM bowlingbag WHERE ProductID = ? AND BranchID = ?',
                'bowlingshoes' => 'SELECT name AS Name FROM bowlingshoes WHERE ProductID = ? AND BranchID = ?',
                'bowlingaccessories' => 'SELECT Name FROM bowlingaccessories WHERE ProductID = ? AND BranchID = ?',
                'cleaningsupplies' => 'SELECT Name FROM cleaningsupplies WHERE ProductID = ? AND BranchID = ?'
              );
              
              foreach ($tables as $tableName => $sqlCheck) {
                $stmt2 = $conn->prepare($sqlCheck);
                $stmt2->bind_param("ii", $productID, $branchID);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                
                if ($result2->num_rows > 0) {
                  $nameData = $result2->fetch_assoc();
                  $productName = $nameData['Name'];
                  $stmt2->close();
                  break;
                }
                $stmt2->close();
              }
              
              $itemCount += $cartItem['quantity'];
              $itemTotal = $cartItem['quantity'] * $cartItem['price'];
              $subtotal += $itemTotal;
        ?>

        <div class="order">
          <div class="order-image-container">
            <img class="order-image" src="./images/<?php echo htmlspecialchars($product['ImageID']); ?>" alt="<?php echo htmlspecialchars($productName); ?>">
          </div>
          <div class="order-info">
            <div class="product-name">
              <?php echo htmlspecialchars($productName); ?>
            </div>
            <div class="product-price">
                <span class="currency-symbol"><?php echo $current_symbol; ?></span><span class="price-amount" data-base-price="<?php echo $cartItem['price']; ?>"><?php echo number_format($cartItem['price'], 2); ?></span>
            </div>
            <div class="product-info">
              Branch ID: <?php echo $branchID; ?>
            </div>
          </div>
          <div class="product-quantity">
            <?php echo $cartItem['quantity']; ?>
          </div>
          <div class="product-total-cost">
              <span class="currency-symbol"><?php echo $current_symbol; ?></span><span class="total-amount" data-base-total="<?php echo $itemTotal; ?>"><?php echo number_format($itemTotal, 2); ?></span>
          </div>
        </div>

        <?php 
            }
            $stmt->close();
          }
          
          // Calculate totals
          $total = $subtotal;
          
        } else {
          // Empty cart message
          echo '<div class="empty-cart-message">Your cart is empty. <a href="./homepage.php">Continue shopping</a>.</div>';
          $itemCount = 0;
          $subtotal = 0;
          $total = 0;
        }
        $conn->close();
        ?>
      </div>

      <!-- Order Summary -->
      <div class="order-summary-section">
          <h2 class="summary-title">Order Summary</h2>
          <div class="summary-row">
              <span>Subtotal (<span id="itemCount"><?php echo $itemCount; ?></span> items)</span>
              <span><span class="currency-symbol"><?php echo $current_symbol; ?></span><span id="subtotal"><?php echo number_format($subtotal, 2); ?></span></span>
          </div>
          <div class="summary-row summary-total">
              <span>Total</span>
              <span><span class="currency-symbol"><?php echo $current_symbol; ?></span><span id="total"><?php echo number_format($total, 2); ?></span></span>
          </div>
          <button class="checkout-btn" id="placeOrderBtn" <?php echo (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) ? 'disabled' : ''; ?>>Place Order</button>
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function() {
      // Store original PHP amounts in base currency (PHP)
      const baseAmounts = {
          subtotal: <?php echo $subtotal; ?>,
          total: <?php echo $total; ?>
      };

      // Store individual product prices
      const productPrices = [];
      $('.price-amount').each(function() {
          productPrices.push({
              element: $(this),
              basePrice: parseFloat($(this).data('base-price'))
          });
      });

      // Store individual product totals
      const productTotals = [];
      $('.total-amount').each(function() {
          productTotals.push({
              element: $(this),
              baseTotal: parseFloat($(this).data('base-total'))
          });
      });

      // Currency conversion function
      function convertCurrency(amount, fromRate, toRate) {
          // Convert from PHP to target currency
          // If converting from PHP (rate 1) to USD (rate 0.018), amount * 0.018
          return amount * toRate;
      }

      // Update all prices when currency changes
      function updatePrices(selectedCurrency, currencyRate, currencySymbol) {
          // Update currency symbol everywhere
          $('.currency-symbol').text(currencySymbol);
          
          // Update summary amounts
          $('#subtotal').text(convertCurrency(baseAmounts.subtotal, 1, currencyRate).toFixed(2));
          $('#total').text(convertCurrency(baseAmounts.total, 1, currencyRate).toFixed(2));
          
          // Update individual product prices
          productPrices.forEach(product => {
              product.element.text(convertCurrency(product.basePrice, 1, currencyRate).toFixed(2));
          });
          
          // Update individual product totals
          productTotals.forEach(product => {
              product.element.text(convertCurrency(product.baseTotal, 1, currencyRate).toFixed(2));
          });
      }

      // Currency selection change handler
      $('#currency').on('change', function() {
          const selectedOption = $(this).find('option:selected');
          const selectedCurrency = selectedOption.val();
          const currencyRate = parseFloat(selectedOption.data('rate'));
          const currencySymbol = selectedOption.data('symbol');
          
          updatePrices(selectedCurrency, currencyRate, currencySymbol);
          
          // Update session or hidden field for form submission
          sessionStorage.setItem('selectedCurrency', selectedCurrency);
          sessionStorage.setItem('currencyRate', currencyRate);
          sessionStorage.setItem('currencySymbol', currencySymbol);
      });

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

      // Place Order functionality
      $('#placeOrderBtn').on('click', function() {
          if ($(this).is(':disabled')) {
              return;
          }

          const paymentMethod = $('input[name="payment"]:checked').val();
          const deliveryMethod = $('input[name="delivery"]:checked').val();
          const selectedCurrency = $('#currency').val();
          const currencyRate = $('#currency option:selected').data('rate');
          const currencySymbol = $('#currency option:selected').data('symbol');

          // Show loading state
          $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

          // Submit order via AJAX
          $.post('process_order.php', {
              paymentMethod: paymentMethod,
              deliveryMethod: deliveryMethod,
              currency: selectedCurrency,
              currencyRate: currencyRate,
              currencySymbol: currencySymbol,
              subtotal: baseAmounts.subtotal,
              total: baseAmounts.total
          }, function(response) {
              if (response.success) {
                  alert('Order placed successfully! Order ID: ' + response.orderID);
                  window.location.href = 'order_confirmation.php?order_id=' + response.orderID;
              } else {
                  alert('Error placing order: ' + response.message);
                  $('#placeOrderBtn').html('Place Order').prop('disabled', false);
              }
          }, 'json').fail(function() {
              alert('Connection error while placing order');
              $('#placeOrderBtn').html('Place Order').prop('disabled', false);
          });
      });

      $('#currency').val('<?php echo $default_currency; ?>');
  });
  </script>
</body>
</html>

<?php include("footer.html");?>