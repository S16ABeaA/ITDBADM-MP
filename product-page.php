<?php include("header.html")?>
<!DOCTYPE html>
<html>
<head>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
   <link rel="stylesheet" href="./css/product-page.css">
</head>

<body>
  <div class="content-section">
    <div class="product-page-container">
      <div class="image-container">
        <img class="image" src="./images/bowlingball1.png" alt="AnimoBowl Bowling Ball">
      </div>
      <div class="product-info">
        <div class="brand">AnimoBowl</div>
        <div class="product-name">Pro Strike Bowling Ball</div>
        <div class="price-message-container">
          <div class="price">&#8369; 2,000 PHP</div>
          <div class="sold-out-message">
            SOLD OUT
          </div>
        </div>
        
        <div class="size-dropdown">
          <label for="sizes">Size:</label>
          <select id="sizes" name="sizes" required class="size-pick">
            <option value="size1">10 lbs</option>
            <option value="size2">12 lbs</option>
            <option value="size3">14 lbs</option>
            <option value="size4">16 lbs</option>
          </select>
        </div>

        <div class="product-quantity">
          <button class="add-subtract-btn">-</button>
          <div class="quantity">
            1
          </div>
          <button class="add-subtract-btn">+</button>
        </div>

        <div>
          <button class="add-to-cart-btn">Add to Cart</button>
        </div>
        
        <div class="product-description">
          <h3>Product Information</h3>
          <div class="description-content">
            The AnimoBowl Pro Strike bowling ball is designed for serious bowlers who demand precision and power. With its advanced reactive coverstock and dynamic core technology, this ball delivers exceptional hook potential and pin-carrying power.
            The AnimoBowl Pro Strike bowling ball is designed for serious bowlers who demand precision and power. With its advanced reactive coverstock and dynamic core technology, this ball delivers exceptional hook potential and pin-carrying power.
            The AnimoBowl Pro Strike bowling ball is designed for serious bowlers who demand precision and power. With its advanced reactive coverstock and dynamic core technology, this ball delivers exceptional hook potential and pin-carrying power.
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      // Quantity adjustment functionality
      $('.add-subtract-btn').on('click', function() {
        const $quantity = $('.quantity');
        let currentQuantity = parseInt($quantity.text());
        
        if ($(this).text() === '+') {
          currentQuantity += 1;
        } else if ($(this).text() === '-' && currentQuantity > 1) {
          currentQuantity -= 1;
        }
        
        $quantity.text(currentQuantity);
      });
      
      // Size selection change
      $('#sizes').on('change', function() {
        console.log('Selected size:', $(this).val());
      });
    });
  </script>
</body>
</html>

<?php include("footer.html");?>