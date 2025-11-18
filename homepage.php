<?php 
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html");

// Get appropriate database connection
$role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'customer';
$conn = getDBConnection($role);

// Check if branch is selected
$branchSelected = isset($_SESSION['selected_branch_id']) && !empty($_SESSION['selected_branch_id']);
$branchId = $branchSelected ? $_SESSION['selected_branch_id'] : null;
?>

<!DOCTYPE html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css2?family=Bungee+Shade&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/homepage.css">
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <title>Homepage | AnimoBowl</title>
</head>
<body>
  <div class="content-section">
    <div class="welcome">Welcome To AnimoBowl</div>

    <?php if (!$branchSelected): ?>
      <div class="branch-selection-prompt">
        <p>Please select a branch to view products:</p>
        <button class="select-branch-btn" onclick="location.href='select-branch.php'">Select Branch</button>
      </div>
    <?php else: ?>

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
            b.weight,
            p.Price,
            p.quantity
          FROM bowlingball b
          JOIN product p ON b.ProductID = p.ProductID
          WHERE p.BranchID = ?
          ORDER BY p.ProductID ASC
          LIMIT 5;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $branchId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
          echo '<div class="hp-product-row">';
          while ($row = $result->fetch_assoc()) {
            $id = (int)$row['ProductID'];
            $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.png');
            $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
            $weight = isset($row['weight']) ? (int)$row['weight'] : '';
            $price = htmlspecialchars($row['Price']);
            $quantity = (int)$row['quantity'];
            $soldOut = ($quantity <= 0);

            $displayName = $weight ? ($name . ' - ' . $weight . 'lbs') : $name;

            echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
            echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $displayName . '">';
            if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
            echo '  <h5 class="hp-product-name">' . $displayName . '</h5>';
            echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
            echo '</div>';
          }
          echo '</div>';
        }
        $stmt->close();
        ?>
        <div class="display-view-btn">
          <button class="view-more-btn" onclick="location.href='view-all-products.php?type=bowlingball'">View More</button>
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
            p.quantity,
            bs.sex,
            bs.size
          FROM bowlingshoes bs
          JOIN product p ON bs.ProductID = p.ProductID
          WHERE p.BranchID = ?
          ORDER BY p.ProductID ASC
          LIMIT 5;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $branchId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
          echo '<div class="hp-product-row">';
          while ($row = $result->fetch_assoc()) {
            $id = (int)$row['ProductID'];
            $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.png');
            $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
            $price = htmlspecialchars($row['Price']);
            $quantity = (int)$row['quantity'];
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
        $stmt->close();
        ?>
        <div class="display-view-btn">
          <button class="view-more-btn" onclick="location.href='view-all-products.php?type=bowlingshoes'">View More</button>
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
            p.quantity,
            bb.color
          FROM bowlingbag bb
          JOIN product p ON bb.ProductID = p.ProductID
          WHERE p.BranchID = ?
          ORDER BY p.ProductID ASC
          LIMIT 5;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $branchId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
          echo '<div class="hp-product-row">';
          while ($row = $result->fetch_assoc()) {
            $id = (int)$row['ProductID'];
            $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.jpg');
            $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
            $price = htmlspecialchars($row['Price']);
            $quantity = (int)$row['quantity'];
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
        $stmt->close();
        ?>
        <div class="display-view-btn">
          <button class="view-more-btn" onclick="location.href='view-all-products.php?type=bowlingbag'">View More</button>
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
              p.Quantity,
              ba.Handedness,
              ba.type
          FROM bowlingaccessories ba
          JOIN product p ON ba.ProductID = p.ProductID
          WHERE p.BranchID = ?
          ORDER BY p.ProductID ASC
          LIMIT 5;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $branchId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
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
        $stmt->close();
        ?>
        <div class="display-view-btn">
          <button class="view-more-btn" onclick="location.href='view-all-products.php?type=bowlingaccessories'">View More</button>
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
              p.quantity
          FROM cleaningsupplies c
          JOIN product p ON c.ProductID = p.ProductID
          WHERE p.BranchID = ?
          ORDER BY p.ProductID ASC
          LIMIT 5;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $branchId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
          echo '<div class="hp-product-row">';
          while ($row = $result->fetch_assoc()) {
            $id = (int)$row['ProductID'];
            $img = htmlspecialchars($row['ImageID'] ?: 'images/placeholder.png');
            $name = htmlspecialchars($row['Name'] ?? 'Unnamed');
            $price = htmlspecialchars($row['Price']);
            $quantity = (int)$row['quantity'];
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
        $stmt->close();
        ?>
        <div class="display-view-btn">
          <button class="view-more-btn" onclick="location.href='view-all-products.php?type=cleaningsupplies'">View More</button>
        </div>
      </div>

    <?php endif;?>

  </div>
</body>
</html>

<?php include("footer.html"); ?>