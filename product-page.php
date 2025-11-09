<?php 
include("header.html");
require_once 'dependencies/config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productID = (int)$_GET['id'];
    
    // debugging log - delete this later
    echo "<script>
            console.log('productID fetched: ', " . json_encode($productID) . ");
          </script>";
} else {
    die("<script>alert('Invalid product ID.'); window.location.href='homepage.php';</script>");
}

// Detect product category
$tables = [
    'bowlingball' => 'SELECT * FROM bowlingball WHERE ProductID = ?',
    'bowlingshoes' => 'SELECT * FROM bowlingshoes WHERE ProductID = ?',
    'bowlingbag' => 'SELECT * FROM bowlingbag WHERE ProductID = ?',
    'bowlingaccessories' => 'SELECT * FROM bowlingaccessories WHERE ProductID = ?',
    'cleaningsupplies' => 'SELECT * FROM cleaningsupplies WHERE ProductID = ?'
];

$productCategory = null;
$categoryData = null;

foreach ($tables as $tableName => $sqlCheck) {
    $stmt = $conn->prepare($sqlCheck);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $productCategory = $tableName;
        $categoryData = $result->fetch_assoc();
        $stmt->close();

        // debugging log - delete this later
        echo "<script>
                console.log('categoryData fetched: ', " . json_encode($categoryData) . ");
              </script>";

        break;
    }
    $stmt->close();
}

if (!$productCategory) {
    die("<script>alert('Product not found in any category.'); window.location.href='homepage.php';</script>");
}

// Fetch main product + inventory details
$sql = "
    SELECT 
        p.ProductID,
        p.Price,
        p.ImageID,
        IFNULL(SUM(i.Quantity), 0) AS Quantity
    FROM product p
    JOIN product_variant pv ON p.ProductID = pv.ProductID
    JOIN inventory i ON pv.VariantID = i.VariantID
    WHERE p.ProductID = ?
    GROUP BY p.ProductID
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();
$productResult = $stmt->get_result();

if ($productResult->num_rows === 0) {
    die("<script>alert('Product not found.'); window.location.href='homepage.php';</script>");
}

$product = $productResult->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/product-page.css">
  <title><?php echo htmlspecialchars($categoryData['Name']); ?> | AnimoBowl</title>
</head>

<body>
  <div class="content-section">
    <div class="product-page-container">
      <div class="image-container">
        <img class="image" src="./images/<?php echo htmlspecialchars($product['ImageID']); ?>" 
             alt="<?php echo htmlspecialchars($categoryData['Name']); ?>">
      </div>

      <div class="product-info">
        <div class="brand">AnimoBowl</div>
        <div class="product-name"><?php echo htmlspecialchars($categoryData['Name']); ?></div>

        <div class="price-message-container">
          <div class="price">₱<?php echo number_format($product['Price'], 2); ?></div>
          <?php if ($product['Quantity'] <= 0): ?>
            <div class="sold-out-label">SOLD OUT</div>
          <?php endif; ?>
        </div>

        <?php if ($productCategory === 'bowlingball'): ?>
          <div class="size-dropdown">
            <label for="sizes">Size:</label>
            <select id="sizes" name="sizes" required class="size-pick">
              <option value="10">10 lbs</option>
              <option value="12">12 lbs</option>
              <option value="14">14 lbs</option>
              <option value="16">16 lbs</option>
            </select>
          </div>
        <?php endif; ?>

        <?php if ($product['Quantity'] > 0): ?>
          <div class="product-quantity">
            <button class="add-subtract-btn">-</button>
            <div class="quantity">1</div>
            <button class="add-subtract-btn">+</button>
          </div>
          <div>
            <button class="add-to-cart-btn">Add to Cart</button>
          </div>
        <?php else: ?>
          <div class="sold-out-text">This product is currently sold out.</div>
        <?php endif; ?>

        <div class="product-description">
          <h3>Product Information</h3>
          <div class="description-content">
            <?php 
              foreach ($categoryData as $key => $value) {
                if ($key === 'ProductID' || $key === 'Name') continue;
                echo "<p><strong>$key:</strong> " . htmlspecialchars($value) . "</p>";
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Quantity control
    document.querySelectorAll('.add-subtract-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const qtyElem = document.querySelector('.quantity');
        let qty = parseInt(qtyElem.textContent);
        if (btn.textContent === '+' && qty < 99) qty++;
        else if (btn.textContent === '-' && qty > 1) qty--;
        qtyElem.textContent = qty;
      });
    });
  </script>
</body>
</html>

<?php include("footer.html"); ?>