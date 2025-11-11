<?php 
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html");

// ✅ Validate and capture product ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productID = (int)$_GET['id'];
    echo "<script>console.log('ProductID fetched: ' + $productID);</script>";
} else {
    die("<script>alert('Invalid product ID.'); window.location.href='homepage.php';</script>");
}

$branchID = $_SESSION["selected_branch_id"];

// ✅ Detect which table this product belongs to
$tables = [
    'bowlingball' => 'SELECT * FROM bowlingball WHERE ProductID = ? AND BranchID = ?',
    'bowlingshoes' => 'SELECT * FROM bowlingshoes WHERE ProductID = ? AND BranchID = ?',
    'bowlingbag' => 'SELECT * FROM bowlingbag WHERE ProductID = ? AND BranchID = ?',
    'bowlingaccessories' => 'SELECT * FROM bowlingaccessories WHERE ProductID = ? AND BranchID = ?',
    'cleaningsupplies' => 'SELECT * FROM cleaningsupplies WHERE ProductID = ? AND BranchID = ?'
];  

$productCategory = null;
$categoryData = null;

foreach ($tables as $tableName => $sqlCheck) {
    $stmt = $conn->prepare($sqlCheck);
    $stmt->bind_param("ii", $productID, $branchID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $productCategory = $tableName;
        $categoryData = $result->fetch_assoc();
        $stmt->close();
        echo "<script>console.log('Product category found: " . $productCategory . "');</script>";
        break;
    }
    
    $stmt->close();
}

// ✅ Handle product not found
if (!$productCategory) {
    die("<script>alert('Product not found in any category.'); window.location.href='homepage.php';</script>");
}

// ✅ Fetch general product info (including image, price, and quantity)
$sql = "
    SELECT 
        p.ProductID,
        p.Price,
        p.ImageID,
        p.quantity
    FROM product p
    WHERE p.ProductID = ?
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

// ✅ Fetch category-specific attributes
$categorySpecificData = [];

switch ($productCategory) {
    case 'bowlingball':
        $sql_statement = "
            SELECT 
                Name, Type, RG, DIFF, INTDIFF, weight, CoreType, CoreName, Coverstock, CoverstockType  
            FROM bowlingball 
            WHERE ProductID = ? AND BranchID = ?
            LIMIT 1
        ";
        break;

    case 'bowlingbag':
        $sql_statement = "
            SELECT 
                Name, Type, Size, color  
            FROM bowlingbag 
            WHERE ProductID = ? AND BranchID = ?
            LIMIT 1
        ";
        break;

    case 'bowlingaccessories':
        $sql_statement = "
            SELECT 
                Name, Type, Handedness  
            FROM bowlingaccessories 
            WHERE ProductID = ? AND BranchID = ?
            LIMIT 1
        ";
        break;

    case 'bowlingshoes':
        $sql_statement = "
            SELECT 
                name AS Name, size, sex  
            FROM bowlingshoes 
            WHERE ProductID = ? AND BranchID = ?
            LIMIT 1
        ";
        break;

    case 'cleaningsupplies':
        $sql_statement = "
            SELECT 
                Name, Type  
            FROM cleaningsupplies 
            WHERE ProductID = ? AND BranchID = ?
            LIMIT 1
        ";
        break;

    default:
        $sql_statement = null;
        break;
}

// ✅ Execute and store result if applicable
if ($sql_statement) {
    $stmt = $conn->prepare($sql_statement);
    $stmt->bind_param("ii", $productID, $branchID);
    $stmt->execute();
    $stmtResult = $stmt->get_result();
    if ($stmtResult->num_rows > 0) {
        $categorySpecificData = $stmtResult->fetch_assoc();
    }
    $stmt->close();
}

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
      <!-- 🖼 Product Image -->
      <div class="image-container">
        <img class="image" 
             src="./images/<?php echo htmlspecialchars($product['ImageID']); ?>" 
             alt="<?php echo htmlspecialchars($categoryData['Name']); ?>">
      </div>

      <!-- 📋 Product Details -->
      <div class="product-info">
        <div class="brand">AnimoBowl</div>
        <div class="product-name"><?php echo htmlspecialchars($categoryData['Name']); ?></div>

        <div class="price-message-container">
          <div class="price">₱<?php echo number_format($product['Price'], 2); ?></div>
          <?php if ($product['quantity'] <= 0): ?>
            <div class="sold-out-label">SOLD OUT</div>
          <?php endif; ?>
        </div>

        <!-- 🛒 Quantity Control -->
        <?php if ($product['quantity'] > 0): ?>
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

        <!-- 🧾 Product Description -->
        <div class="product-description">
          <h3>Product Information</h3>
          <div class="description-content">
            <?php 
              if (!empty($categorySpecificData)) {
                foreach ($categorySpecificData as $key => $value) {
                  if ($value === null || $value === '') continue;
                  echo "<p><strong>" . htmlspecialchars(ucfirst($key)) . ":</strong> " . htmlspecialchars($value) . "</p>";
                }
              } else {
                  echo "<p>No additional information available for this product.</p>";
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JS: Control +/− buttons -->
  <script>
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