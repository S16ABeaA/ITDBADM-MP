<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html");

// ✅ Ensure branch is selected
if (!isset($_SESSION['selected_branch_id']) || empty($_SESSION['selected_branch_id'])) {
    header('Location: select-branch.php');
    exit();
}

$branchId = $_SESSION['selected_branch_id'];

// ✅ Pagination settings
$items_per_page = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// ✅ Filters
$availability = $_GET['availability'] ?? '';
$price = $_GET['price'] ?? '';
$brand = $_GET['brand'] ?? '';

// ✅ Build WHERE clause
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

// ✅ Fetch brands for filter dropdown
$brands = [];
$brandSql = "SELECT DISTINCT brand.Name FROM brand 
  JOIN product ON brand.BrandID = product.BrandID 
  JOIN bowlingshoes ON product.ProductID = bowlingshoes.ProductID 
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

// ✅ Count total products
$countSql = "SELECT COUNT(DISTINCT s.ProductID) as cnt FROM bowlingshoes s
  JOIN product p ON s.ProductID = p.ProductID
  JOIN brand ON brand.BrandID = p.BrandID
  $whereSQL";

$countStmt = $conn->prepare($countSql);
if ($params) {
    $countStmt->bind_param($param_types, ...$params);
}
$countStmt->execute();
$countRes = $countStmt->get_result();
$total_products = ($countRes && $countRes->num_rows > 0) ? (int)$countRes->fetch_assoc()['cnt'] : 0;
$countStmt->close();

$total_pages = ceil($total_products / $items_per_page);

// Fetch bowling shoes for current page with filters
$sql = "
  SELECT DISTINCT
    p.ProductID,
    p.ImageID,
    s.Name,
    s.size,
    p.Price,
    p.quantity,
    brand.Name AS BrandName
  FROM bowlingshoes s
  JOIN product p ON s.ProductID = p.ProductID
  JOIN brand ON brand.BrandID = p.BrandID
  $whereSQL
  ORDER BY p.ProductID ASC
  LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$params[] = $items_per_page;
$params[] = $offset;
$param_types .= "ii";

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
  <title>Bowling Shoes - Page <?php echo $page; ?></title>
</head>
<body>
  <div class="content-section">
    <div class="bowling-shoes-section">
    <h1>Bowling Shoes</h1>
    
    <!-- ✅ Filters Section -->
    <form method="get" class="filter-section" style="margin-bottom: 1em;">
        <span>Filter by:</span>
        <?php include("dependencies/filter_dropdowns.php"); ?>
        <button type="submit" class="filter-apply-btn">Apply Filters</button>
    </form>

    <?php if ($total_pages > 1): ?>
    <div class="page-info">
    Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $items_per_page, $total_products); ?> of <?php echo $total_products; ?> products
    (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)
    </div>
    <?php endif; ?>

    <!-- ✅ Product Grid -->
    <div class="product-grid">
    <?php
    foreach ($current_products as $product) {
      $id = (int)$product['ProductID'];
      $img = htmlspecialchars($product['ImageID'] ?: 'images/placeholder.png');
      $name = htmlspecialchars($product['Name'] ?? 'Unnamed');
      $size = isset($product['size']) ? htmlspecialchars($product['size']) : '';
      $price = htmlspecialchars($product['Price']);
      $quantity = (int)$product['quantity'];
      $soldOut = ($quantity <= 0);
      
      $displayName = $size ? ($name . ' - Size ' . $size) : $name;
      
      echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
      echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $displayName . '">';
      if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
      echo '  <h5 class="hp-product-name">' . $displayName . '</h5>';
      echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
      echo '</div>';
    }
    ?>
    </div>

    <!-- ✅ Pagination -->
    <?php if ($total_pages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?page=<?php echo $page - 1; ?>" class="prev">Previous</a>
        <?php else: ?>
          <span class="disabled">Previous</span>
        <?php endif; ?>

        <?php
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);
        if ($start_page > 1) {
          echo '<a href="?page=1">1</a>';
          if ($start_page > 2) echo '<span>...</span>';
        }
        for ($i = $start_page; $i <= $end_page; $i++) {
          echo $i == $page ? '<span class="current">'.$i.'</span>' : '<a href="?page='.$i.'">'.$i.'</a>';
        }
        if ($end_page < $total_pages) {
          if ($end_page < $total_pages - 1) echo '<span>...</span>';
          echo '<a href="?page='.$total_pages.'">'.$total_pages.'</a>';
        }
        ?>
        
        <?php if ($page < $total_pages): ?>
          <a href="?page=<?php echo $page + 1; ?>" class="next">Next</a>
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