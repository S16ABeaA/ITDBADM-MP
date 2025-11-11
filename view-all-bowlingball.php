<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html");

// Check if branch is selected
if (!isset($_SESSION['selected_branch_id']) || empty($_SESSION['selected_branch_id'])) {
    header('Location: select-branch.php');
    exit();
}

$branchId = $_SESSION['selected_branch_id'];

// Pagination settings
$items_per_page = 20; // edit this later on to show more items per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Get filter values from query string
$availability = isset($_GET['availability']) ? $_GET['availability'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';

// Build SQL WHERE clause for filters
$where = ["p.BranchID = ?"];
$params = [$branchId];
$param_types = "i";

if ($availability === 'in') {
  $where[] = 'p.quantity > 0';
} elseif ($availability === 'out') {
  $where[] = 'p.quantity <= 0';
}

if ($price === 'under1k') {
  $where[] = 'p.Price < 1000';
} elseif ($price === '1k-3k') {
  $where[] = 'p.Price >= 1000 AND p.Price < 3000';
} elseif ($price === '3k-5k') {
  $where[] = 'p.Price >= 3000 AND p.Price < 5000';
} elseif ($price === 'over5k') {
  $where[] = 'p.Price >= 5000';
}

if ($brand !== '' && $brand !== 'all') {
  $where[] = "brand.Name = ?";
  $params[] = $brand;
  $param_types .= "s";
}

$whereSQL = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// Fetch all brands from brand table for filter dropdown
$brands = [];
$brandSql = "SELECT DISTINCT brand.Name FROM brand 
  JOIN product ON brand.BrandID = product.BrandID 
  JOIN bowlingball ON product.ProductID = bowlingball.ProductID 
  WHERE product.BranchID = ?
  ORDER BY brand.Name ASC";
$brandStmt = $conn->prepare($brandSql);
$brandStmt->bind_param('i', $branchId);
$brandStmt->execute();
$brandRes = $brandStmt->get_result();
if ($brandRes) {
  while ($row = $brandRes->fetch_assoc()) {
    $brands[] = $row['Name'];
  }
  $brandRes->free();
}
$brandStmt->close();

// Count total bowling balls with filters
$countSql = "SELECT COUNT(DISTINCT b.ProductID) as cnt FROM bowlingball b 
  JOIN product p ON b.ProductID = p.ProductID
  JOIN brand ON brand.BrandID = p.BrandID
  $whereSQL";
  
$total_products = 0;
$countStmt = $conn->prepare($countSql);

// Bind parameters for count query
if ($params) {
    $countStmt->bind_param($param_types, ...$params);
}

$countStmt->execute();
$countRes = $countStmt->get_result();
if ($countRes) {
  $row = $countRes->fetch_assoc();
  $total_products = (int)$row['cnt'];
  $countRes->free();
}
$countStmt->close();

$total_pages = ceil($total_products / $items_per_page);

// Fetch bowling balls for current page with filters
$sql = "
  SELECT DISTINCT
    p.ProductID,
    p.ImageID,
    b.Name,
    b.weight,
    p.Price,
    p.quantity,
    brand.Name AS BrandName
  FROM bowlingball b
  JOIN product p ON b.ProductID = p.ProductID
  JOIN brand ON brand.BrandID = p.BrandID
  $whereSQL
  ORDER BY p.ProductID ASC
  LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

// Add limit and offset to parameters
$params[] = $items_per_page;
$params[] = $offset;
$param_types .= "ii";

// Bind all parameters
if ($params) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$current_products = [];
while ($row = $result->fetch_assoc()) {
  $current_products[] = $row;
}
$stmt->close();

?>
<!DOCTYPE html>
<html>
<head>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="./css/view-all-products.css?v=1.1">
  <title>Bowling Balls - Page <?php echo $page; ?></title>
</head>
<body>
  <div class="content-section">
    <div class="bowling-ball-section">
    <h1>Bowling Balls</h1>

    <form method="get" class="filter-section" style="margin-bottom: 1em;">
        <span>Filter by:</span>
        <?php include("dependencies/filter_dropdowns.php"); ?>
        <button type="submit" class="filter-apply-btn">Apply Filters</button>
    </form>


    <!-- Page Info -->
    <?php if ($total_pages > 1): ?>
    <div class="page-info">
    Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $items_per_page, $total_products); ?> of <?php echo $total_products; ?> products
    (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)
    </div>
    <?php endif; ?>

    <div class="product-grid">
    <?php
    foreach ($current_products as $product) {
      $id = (int)$product['ProductID'];
      $img = htmlspecialchars($product['ImageID'] ?: 'images/placeholder.png');
      $name = htmlspecialchars($product['Name'] ?? 'Unnamed');
      $weight = isset($product['weight']) ? (int)$product['weight'] : '';
      $price = htmlspecialchars($product['Price']);
      $quantity = (int)$product['quantity'];
      $soldOut = ($quantity <= 0);
      
      $displayName = $weight ? ($name . ' - ' . $weight . 'lbs') : $name;
      
      echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
      echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $displayName . '">';
      if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
      echo '  <h5 class="hp-product-name">' . $displayName . '</h5>';
      echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
      echo '</div>';
    }
    ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['availability']) ? '&availability=' . $_GET['availability'] : ''; ?><?php echo isset($_GET['price']) ? '&price=' . $_GET['price'] : ''; ?><?php echo isset($_GET['brand']) ? '&brand=' . $_GET['brand'] : ''; ?>" class="prev">Previous</a>
      <?php else: ?>
        <span class="disabled">Previous</span>
      <?php endif; ?>

      <?php
      $start_page = max(1, $page - 2);
      $end_page = min($total_pages, $page + 2);
      if ($start_page > 1) {
        echo '<a href="?page=1' . (isset($_GET['availability']) ? '&availability=' . $_GET['availability'] : '') . (isset($_GET['price']) ? '&price=' . $_GET['price'] : '') . (isset($_GET['brand']) ? '&brand=' . $_GET['brand'] : '') . '">1</a>';
        if ($start_page > 2) echo '<span>...</span>';
      }
      for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $page) {
          echo '<span class="current">' . $i . '</span>';
        } else {
          echo '<a href="?page=' . $i . (isset($_GET['availability']) ? '&availability=' . $_GET['availability'] : '') . (isset($_GET['price']) ? '&price=' . $_GET['price'] : '') . (isset($_GET['brand']) ? '&brand=' . $_GET['brand'] : '') . '">' . $i . '</a>';
        }
      }
      if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) echo '<span>...</span>';
        echo '<a href="?page=' . $total_pages . (isset($_GET['availability']) ? '&availability=' . $_GET['availability'] : '') . (isset($_GET['price']) ? '&price=' . $_GET['price'] : '') . (isset($_GET['brand']) ? '&brand=' . $_GET['brand'] : '') . '">' . $total_pages . '</a>';
      }
      ?>
      <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['availability']) ? '&availability=' . $_GET['availability'] : ''; ?><?php echo isset($_GET['price']) ? '&price=' . $_GET['price'] : ''; ?><?php echo isset($_GET['brand']) ? '&brand=' . $_GET['brand'] : ''; ?>" class="next">Next</a>
      <?php else: ?>
        <span class="disabled">Next</span>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    </div>
  </div>
  <script src="dependencies/filter-dropdown.js?v=1.1"></script>
</body>
</html>