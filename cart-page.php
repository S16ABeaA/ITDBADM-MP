<?php 
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html"); 

if ($_SESSION['user_id'] == null) {
  header("Location: login-signup.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
   <link href="./css/cart-page.css" rel="stylesheet">
</head>

<body>
  <div class="content-section">
    <div class="cart-header">
      <h1 class="cart-title">Your Shopping Cart</h1>
      <button class="continue-shopping" onclick="location.href = './homepage.php'">
        <i class="fas fa-arrow-left"></i> Continue Shopping
      </button>
    </div>

    <div class="cart-main-container">
      <div class="cart-order-container">

        <?php
        $hasItems = false;
        
        // Check if product cart exists and has items
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
          $hasItems = true;
          
          // Get product details for all items in cart
          $cartItems = array();
          
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
              
              // Now get the product name from the appropriate category table
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
              
              $cartItems[$cartKey] = array(
                'productID' => $productID,
                'branchID' => $branchID,
                'ProductName' => $productName,
                'ImageID' => $product['ImageID'],
                'Price' => $product['Price']
              );
            }
            $stmt->close();
          }
          
          // Display PRODUCT cart items
          echo '<div class="cart-section">
                  <h3 class="section-title">Products</h3>
                  <div class="products-section">';
          
          foreach ($_SESSION['cart'] as $cartKey => $cartItem) {
            if (isset($cartItems[$cartKey])) {
              $product = $cartItems[$cartKey];
        ?>

          <div class="order product-order" data-product="<?php echo $product['productID']; ?>" data-branch="<?php echo $product['branchID']; ?>" data-cartkey="<?php echo htmlspecialchars($cartKey); ?>" data-price="<?php echo $cartItem['price']; ?>">

            <div class="order-image-container">
              <img class="order-image" src="./images/<?php echo htmlspecialchars($product['ImageID']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
            </div>

            <div class="order-info">
              <div class="product-name"><?php echo htmlspecialchars($product['ProductName']); ?></div>
              <div class="product-price">₱<?php echo number_format($cartItem['price'], 2); ?></div>
              <div class="product-branch">Branch ID: <?php echo $product['branchID']; ?></div>
            </div>

            <div class="product-quantity">
              <button class="add-subtract-btn" data-action="decrease">-</button>
              <div class="quantity"><?php echo $cartItem['quantity']; ?></div>
              <button class="add-subtract-btn" data-action="increase">+</button>
            </div>

            <div class="product-total-cost">
              ₱<?php echo number_format($cartItem['quantity'] * $cartItem['price'], 2); ?>
            </div>

            <button class="remove-from-cart-btn" data-cartkey="<?php echo htmlspecialchars($cartKey); ?>" data-type="product">
              <i class="fa-solid fa-trash"></i>
            </button>

          </div>

        <?php 
            }
          }
          echo '</div></div>'; // Close products section
        } 
        
        // Check if service cart exists and has items
        if (isset($_SESSION['service_cart']) && !empty($_SESSION['service_cart'])) {
          $hasItems = true;
          
          // Display SERVICE cart items
          echo '<div class="cart-section">
                  <h3 class="section-title">Services</h3>
                  <div class="services-section">';
          
          foreach ($_SESSION['service_cart'] as $cartKey => $serviceItem) {
            // Get service details
            $serviceID = $serviceItem['serviceID'];
            $serviceSQL = "SELECT Type FROM services WHERE ServiceID = ?";
            $serviceStmt = $conn->prepare($serviceSQL);
            $serviceStmt->bind_param("i", $serviceID);
            $serviceStmt->execute();
            $serviceResult = $serviceStmt->get_result();
            $serviceData = $serviceResult->fetch_assoc();
            $serviceStmt->close();
            
            $serviceType = $serviceData['Type'] ?? 'Unknown Service';
            $isFromStore = $serviceItem['isFromStore'] ? 'Yes' : 'No';
            $surchargeText = $serviceItem['isFromStore'] ? '' : ' (+5% surcharge)';
        ?>

          <div class="order service-order" data-cartkey="<?php echo htmlspecialchars($cartKey); ?>" data-price="<?php echo $serviceItem['finalPrice']; ?>">

            <div class="order-image-container">
              <div class="service-icon">
                <i class="fas fa-tools"></i>
              </div>
            </div>

            <div class="order-info">
              <div class="product-name"><?php echo htmlspecialchars($serviceType); ?> Service</div>
              <div class="product-price">₱<?php echo number_format($serviceItem['finalPrice'], 2); ?></div>
              <div class="service-details">
                Ball from our store: <?php echo $isFromStore; ?><?php echo $surchargeText; ?>
              </div>
            </div>

            <div class="product-quantity service-quantity">
              <div class="quantity">1</div>
            </div>

            <div class="product-total-cost">
              ₱<?php echo number_format($serviceItem['finalPrice'], 2); ?>
            </div>

            <button class="remove-from-cart-btn" data-cartkey="<?php echo htmlspecialchars($cartKey); ?>" data-type="service">
              <i class="fa-solid fa-trash"></i>
            </button>

          </div>

        <?php 
          }
          echo '</div></div>'; // Close services section
        }
        
        // Show empty cart message if no items
        if (!$hasItems) { 
        ?>
          <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added any items to your cart yet.</p>
            <button onclick="location.href='./homepage.php'" class="shop-now-btn">Shop Now</button>
          </div>
        <?php } ?>

      </div>

      <?php 
      if ($hasItems) {
        // Calculate totals for both products and services
        $itemCount = 0;
        $subtotal = 0;
        
        // Product totals
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
          foreach ($_SESSION['cart'] as $cartItem) {
            $itemCount += $cartItem['quantity'];
            $subtotal += $cartItem['quantity'] * $cartItem['price'];
          }
        }
        
        // Service totals
        if (isset($_SESSION['service_cart']) && !empty($_SESSION['service_cart'])) {
          foreach ($_SESSION['service_cart'] as $serviceItem) {
            $itemCount += 1; // Each service counts as 1 item
            $subtotal += $serviceItem['finalPrice'];
          }
        }
        
        $total = $subtotal;
      ?>
      <div class="cart-summary">
        <div class="summary-container">
          <h2 class="summary-title">Order Summary</h2>
          
          <div class="summary-row">
            <span>Subtotal (<?php echo $itemCount; ?> items)</span>
            <span>₱<?php echo number_format($subtotal, 2); ?></span>
          </div>
          <div class="summary-row summary-total">
            <span>Total</span>
            <span>₱<?php echo number_format($total, 2); ?></span>
          </div>
          <button class="checkout-btn" onclick="location.href='checkout-page.php'">Proceed to Checkout</button>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      // Quantity adjustment functionality for PRODUCTS only
      $('.cart-order-container').on('click', '.add-subtract-btn', function() {
        const $order = $(this).closest('.order');
        const $quantity = $order.find('.quantity');
        const cartKey = $order.data('cartkey');
        const action = $(this).data('action');
        const price = parseFloat($order.data('price'));
        
        let currentQuantity = parseInt($quantity.text());
        
        if (action === 'increase') {
          currentQuantity += 1;
        } else if (action === 'decrease' && currentQuantity > 1) {
          currentQuantity -= 1;
        } else {
          return;
        }
        
        // Update quantity in session via AJAX
        $.post('update_cart_quantity.php', {
          cartKey: cartKey,
          quantity: currentQuantity
        }, function(response) {
          if (response.success) {
            $quantity.text(currentQuantity);
            const itemTotal = (price * currentQuantity).toFixed(2);
            $order.find('.product-total-cost').text('₱' + itemTotal);
            updateCartSummary();
          } else {
            alert('Error updating quantity: ' + response.message);
            $quantity.text(response.currentQuantity || currentQuantity);
          }
        }, 'json').fail(function() {
          alert('Connection error while updating quantity');
          $quantity.text(currentQuantity - (action === 'increase' ? 1 : -1));
        });
      });
      
      // Remove item functionality for both PRODUCTS and SERVICES
      $('.cart-order-container').on('click', '.remove-from-cart-btn', function() {
        const cartKey = $(this).data('cartkey');
        const itemType = $(this).data('type'); // 'product' or 'service'
        const $order = $(this).closest('.order');
        
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
          return;
        }
        
        const endpoint = itemType === 'service' ? 'remove_service_from_cart.php' : 'remove_from_cart.php';
        
        // Remove from session via AJAX
        $.post(endpoint, {
          cartKey: cartKey
        }, function(response) {
          if (response.success) {
            $order.fadeOut(300, function() {
              $(this).remove();
              updateCartSummary();
              
              // Check if both product and service sections are empty
              const hasProducts = $('.products-section .order').length > 0;
              const hasServices = $('.services-section .order').length > 0;
              
              if (!hasProducts && !hasServices) {
                showEmptyCart();
              } else {
                // Hide empty sections
                if (!hasProducts) {
                  $('.products-section').closest('.cart-section').hide();
                }
                if (!hasServices) {
                  $('.services-section').closest('.cart-section').hide();
                }
              }
            });
          } else {
            alert('Error removing item: ' + response.message);
          }
        }, 'json').fail(function() {
          alert('Connection error while removing item');
        });
      });
      
      function updateCartSummary() {
        let itemCount = 0;
        let subtotal = 0;
        
        // Calculate products
        $('.product-order').each(function() {
          const quantity = parseInt($(this).find('.quantity').text());
          const price = parseFloat($(this).data('price'));
          itemCount += quantity;
          subtotal += price * quantity;
        });
        
        // Calculate services
        $('.service-order').each(function() {
          const price = parseFloat($(this).data('price'));
          itemCount += 1;
          subtotal += price;
        });
        
        const total = subtotal;
        
        // Update summary display
        $('.summary-row:first span:first').text('Subtotal (' + itemCount + ' items)');
        $('.summary-row:first span:last').text('₱' + subtotal.toFixed(2));
        $('.summary-total span:last').text('₱' + total.toFixed(2));
      }
      
      function showEmptyCart() {
        $('.cart-order-container')
          .html('<div class="empty-cart"><i class="fas fa-shopping-cart"></i><h2>Your cart is empty</h2><p>Looks like you haven\'t added any items to your cart yet.</p><button onclick="location.href=\'./homepage.php\'" class="shop-now-btn">Shop Now</button></div>');
        
        $('.cart-summary').hide();
      }
    });
  </script>
</body>
</html>

<?php include("footer.html");?>