<?php include("header.html");?>
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
            <button class="add-subtract-btn">-</button>
            <div class="quantity">
              1
            </div>
            <button class="add-subtract-btn">+</button>
          </div>

          <div class="product-total-cost">
            ₱3889.99
          </div>
          <button class="remove-from-cart-btn">
            <i class="fa-solid fa-trash"></i>
          </button>
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
            <button class="add-subtract-btn">-</button>
            <div class="quantity">
              2
            </div>
            <button class="add-subtract-btn">+</button>
          </div>

          <div class="product-total-cost">
            ₱16259.98
          </div>
          <button class="remove-from-cart-btn">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>

      <div class="cart-summary">
        <div class="summary-container">
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
          <button class="checkout-btn" onclick="location.href='checkout-page.php'">Proceed to Checkout</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      // Quantity adjustment functionality
      $('.add-subtract-btn').on('click', function() {
        const $order = $(this).closest('.order');
        const $quantity = $order.find('.quantity');
        const $priceElement = $order.find('.product-price');
        const $totalElement = $order.find('.product-total-cost');
        
        let currentQuantity = parseInt($quantity.text());
        const unitPrice = parseFloat($priceElement.text().replace('₱', ''));
        
        if ($(this).text() === '+') {
          currentQuantity += 1;
        } else if ($(this).text() === '-' && currentQuantity > 1) {
          currentQuantity -= 1;
        }
        
        $quantity.text(currentQuantity);
        
        // Update total for this item
        const newTotal = (unitPrice * currentQuantity).toFixed(2);
        $totalElement.text('₱' + newTotal);
        
        // Update cart summary
        updateCartSummary();
      });
      
      // Remove item functionality
      $('.remove-from-cart-btn').on('click', function() {
        $(this).closest('.order').fadeOut(300, function() {
          $(this).remove();
          updateCartSummary();
          
          // Show empty cart message if no items left
          if ($('.order').length === 0) {
            showEmptyCart();
          }
        });
      });
      
      function updateCartSummary() {
        let itemCount = 0;
        let subtotal = 0;
        
        $('.order').each(function() {
          const quantity = parseInt($(this).find('.quantity').text());
          const price = parseFloat($(this).find('.product-price').text().replace('₱', ''));
          
          itemCount += quantity;
          subtotal += price * quantity;
        });
        
        const shipping = 59.99;
        const tax = subtotal * 0.08; // 8% tax
        const total = subtotal + shipping + tax;
        
        // Update summary display
        $('.summary-row:first span:first').text(`Subtotal (${itemCount} items)`);
        $('.summary-row:first span:last').text('₱' + subtotal.toFixed(2));
        $('.summary-row:eq(1) span:last').text('₱' + shipping.toFixed(2));
        $('.summary-row:eq(2) span:last').text('₱' + tax.toFixed(2));
        $('.summary-total span:last').text('₱' + total.toFixed(2));
      }
      
      function showEmptyCart() {
        $('.cart-order-container')
          .html(`
            <div class="empty-cart">
              <i class="fas fa-shopping-cart"></i>
              <h2>Your cart is empty</h2>
              <p>Looks like you haven't added any items to your cart yet.</p>
              <button onclick="location.href='./homepage.php'" class="shop-now-btn">Shop Now</button>
            </div>
          `)
          .addClass('empty'); // Add the empty class
        
        $('.cart-summary').hide();
      }
      $('.remove-from-cart-btn').on('click', function() {
        $(this).closest('.order').fadeOut(300, function() {
          $(this).remove();
          updateCartSummary();
          
          // Show empty cart message if no items left
          if ($('.order').length === 0) {
            showEmptyCart();
          } else {
            // Remove empty class if there are items
            $('.cart-order-container').removeClass('empty');
          }
        });
      });
    });
  </script>
</body>
</html>


<?php include("footer.html");?>