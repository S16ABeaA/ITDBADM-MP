<?php 
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html");
?>
<!DOCTYPE html>
<html>
<head>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee+Shade&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/homepage.css?v=1.1">
  <title>Homepage | AnimoBowl</title>
</head>
<body>
  <div class="content-section">
    <div class="welcome">Welcome To AnimoBowl</div>
    <?php if (!isset($_SESSION['user_id'])): ?>
      <button class="get-started-button" onclick="location.href='login-signup.php'">Sign up now</button>
    <?php endif; ?>

    <!-- Bowling Balls -->
    <div class="bowling-ball-section">
      <h2>Bowling Ball</h2>
      <?php
        $sql = "
            SELECT DISTINCT
                p.ProductID,
                p.ImageID,
                b.Name,
                p.Price,
                i.Quantity
            FROM bowlingball b
            JOIN product p ON b.ProductID = p.ProductID
            JOIN product_variant pv ON p.ProductID = pv.ProductID 
            JOIN inventory i ON pv.VariantID = i.VariantID
            ORDER BY p.ProductID ASC
            LIMIT 5;
        ";

        if ($result = $conn->query($sql)) {
            echo '<div class="hp-product-row">';
            while ($row = $result->fetch_assoc()) {
                $id = (int)$row['ProductID'];
                $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.png');
                $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
                $price = htmlspecialchars($row['Price']);
                $quantity = (int)$row['Quantity'];
                $soldOut = ($quantity <= 0);

                echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
                echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $name . '">';
                if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
                echo '  <h5 class="hp-product-name">' . $name . '</h5>';
                echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
                echo '</div>';
            }
            echo '</div>';
        }
      ?>
      <div class="display-view-btn">
        <button class="view-more-btn" onclick="location.href='view-all-products.php'">View More</button>
      </div>
    </div>

    <!-- Bowling Shoes -->
    <div class="bowling-shoes-section">
      <h2>Bowling Shoes</h2>
      <?php
        $sql = "
          SELECT DISTINCT
              p.ProductID,
              p.ImageID,
              bs.Name,
              p.Price,
              i.Quantity
          FROM bowlingshoes bs
            JOIN product p ON bs.ProductID = p.ProductID
            JOIN product_variant pv ON p.ProductID = pv.ProductID 
            JOIN inventory i ON pv.VariantID = i.VariantID
            ORDER BY p.ProductID ASC
            LIMIT 5;
        ";

        if ($result = $conn->query($sql)) {
            echo '<div class="hp-product-row">';
            while ($row = $result->fetch_assoc()) {
                $id = (int)$row['ProductID'];
                $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.png');
                $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
                $price = htmlspecialchars($row['Price']);
                $quantity = (int)$row['Quantity'];
                $soldOut = ($quantity <= 0);

                echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
                echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $name . '">';
                if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
                echo '  <h5 class="hp-product-name">' . $name . '</h5>';
                echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
                echo '</div>';
            }
            echo '</div>';
        }
      ?>
      <div class="display-view-btn">
        <button class="view-more-btn" onclick="location.href='view-all-products.php'">View More</button>
      </div>
    </div>

    <!-- Bowling Bags -->
    <div class="bowling-bag-section">
      <h2>Bowling Bag</h2>
      <?php
        $sql = "
          SELECT DISTINCT
              p.ProductID,
              p.ImageID,
              bb.Name,
              p.Price,
              i.Quantity
          FROM bowlingbag bb
            JOIN product p ON bb.ProductID = p.ProductID
            JOIN product_variant pv ON p.ProductID = pv.ProductID 
            JOIN inventory i ON pv.VariantID = i.VariantID
            ORDER BY p.ProductID ASC
            LIMIT 5;
        ";

        if ($result = $conn->query($sql)) {
            echo '<div class="hp-product-row">';
            while ($row = $result->fetch_assoc()) {
                $id = (int)$row['ProductID'];
                $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.jpg');
                $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
                $price = htmlspecialchars($row['Price']);
                $quantity = (int)$row['Quantity'];
                $soldOut = ($quantity <= 0);

                echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
                echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $name . '">';
                if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
                echo '  <h5 class="hp-product-name">' . $name . '</h5>';
                echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
                echo '</div>';
            }
            echo '</div>';
        }
      ?>
      <div class="display-view-btn">
        <button class="view-more-btn" onclick="location.href='view-all-products.php'">View More</button>
      </div>
    </div>

    <!-- Bowling Accessories -->
    <div class="bowling-accessories-section">
      <h2>Bowling Accessories</h2>
      <?php
        $sql = "
          SELECT DISTINCT
              p.ProductID,
              p.ImageID,
              ba.Name,
              p.Price,
              i.Quantity
          FROM bowlingaccessories ba
            JOIN product p ON ba.ProductID = p.ProductID
            JOIN product_variant pv ON p.ProductID = pv.ProductID 
            JOIN inventory i ON pv.VariantID = i.VariantID
            ORDER BY p.ProductID ASC
            LIMIT 5;
        ";

        if ($result = $conn->query($sql)) {
            echo '<div class="hp-product-row">';
            while ($row = $result->fetch_assoc()) {
                $id = (int)$row['ProductID'];
                $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.png');
                $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
                $price = htmlspecialchars($row['Price']);
                $quantity = (int)$row['Quantity'];
                $soldOut = ($quantity <= 0);

                echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
                echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $name . '">';
                if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
                echo '  <h5 class="hp-product-name">' . $name . '</h5>';
                echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
                echo '</div>';
            }
            echo '</div>';
        }
      ?>
      <div class="display-view-btn">
        <button class="view-more-btn" onclick="location.href='view-all-products.php'">View More</button>
      </div>
    </div>

    <!-- Cleaning Supplies -->
    <div class="cleaning-supplies-section">
      <h2>Cleaning Supplies</h2>
      <?php
        $sql = "
          SELECT DISTINCT
              p.ProductID,
              p.ImageID,
              c.Name,
              p.Price,
              i.Quantity
          FROM cleaningsupplies c
            JOIN product p ON c.ProductID = p.ProductID
            JOIN product_variant pv ON p.ProductID = pv.ProductID 
            JOIN inventory i ON pv.VariantID = i.VariantID
            ORDER BY p.ProductID ASC
            LIMIT 5;
        ";

        if ($result = $conn->query($sql)) {
            echo '<div class="hp-product-row">';
            while ($row = $result->fetch_assoc()) {
                $id = (int)$row['ProductID'];
                $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.png');
                $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
                $price = htmlspecialchars($row['Price']);
                $quantity = (int)$row['Quantity'];
                $soldOut = ($quantity <= 0);

                echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
                echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $name . '">';
                if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
                echo '  <h5 class="hp-product-name">' . $name . '</h5>';
                echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
                echo '</div>';
            }
            echo '</div>';
        }
      ?>
      <div class="display-view-btn">
        <button class="view-more-btn" onclick="location.href='view-all-products.php'">View More</button>
      </div>
    </div>

  </div>
</body>
</html>
<?php include("footer.html"); ?>