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

// Get product type from query string
$type = $_GET['type'];
$valid_types = ['bowlingball', 'bowlingbag', 'bowlingaccessories', 'cleaningsupplies', 'bowlingshoes'];
if (!in_array($type, $valid_types)) {
    die("Invalid product type.");
}

// Map type to table, extra column, and label
$productTables = [
    'bowlingball' => ['table' => 'bowlingball', 'extra_column' => 'weight', 'label' => 'Bowling Balls'],
    'bowlingbag' => ['table' => 'bowlingbag', 'extra_column' => 'size', 'label' => 'Bowling Bags'],
    'bowlingaccessories' => ['table' => 'bowlingaccessories', 'extra_column' => 'type', 'label' => 'Accessories'],
    'cleaningsupplies' => ['table' => 'cleaningsupplies', 'extra_column' => 'type', 'label' => 'Cleaning Supplies'],
    'bowlingshoes' => ['table' => 'bowlingshoes', 'extra_column' => 'size', 'label' => 'Bowling Shoes'],
];

$table = $productTables[$type]['table'];
$extra_column = $productTables[$type]['extra_column'];
$label = $productTables[$type]['label'];

// Pagination settings
$items_per_page = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Get filter values
$availability = $_GET['availability'] ?? '';
$price = $_GET['price'] ?? '';
$brand = $_GET['brand'] ?? '';

// Build SQL WHERE clause
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

// Fetch all brands for filter dropdown
$brands = [];
$brandSql = "SELECT DISTINCT brand.Name 
             FROM brand 
             JOIN product ON brand.BrandID = product.BrandID 
             JOIN $table ON product.ProductID = $table.ProductID 
             WHERE product.BranchID = ?
             ORDER BY brand.Name ASC";
$brandStmt = $conn->prepare($brandSql);
$brandStmt->bind_param('i', $branchId);
$brandStmt->execute();
$brandRes = $brandStmt->get_result();
while ($row = $brandRes->fetch_assoc()) {
    $brands[] = $row['Name'];
}
$brandStmt->close();

// Count total products with filters
$countSql = "SELECT COUNT(DISTINCT b.ProductID) as cnt 
             FROM $table b
             JOIN product p ON b.ProductID = p.ProductID
             JOIN brand ON brand.BrandID = p.BrandID
             $whereSQL";

$countStmt = $conn->prepare($countSql);
if ($params) $countStmt->bind_param($param_types, ...$params);
$countStmt->execute();
$countRes = $countStmt->get_result();
$total_products = ($countRes) ? (int)$countRes->fetch_assoc()['cnt'] : 0;
$countStmt->close();

$total_pages = ceil($total_products / $items_per_page);

// Fetch products for current page
$sql = "SELECT DISTINCT
            p.ProductID,
            p.ImageID,
            b.Name,
            b.$extra_column,
            p.Price,
            p.quantity,
            brand.Name AS BrandName
        FROM $table b
        JOIN product p ON b.ProductID = p.ProductID
        JOIN brand ON brand.BrandID = p.BrandID
        $whereSQL
        ORDER BY p.ProductID ASC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$params[] = $items_per_page;
$params[] = $offset;
$param_types .= "ii";
$stmt->bind_param($param_types, ...$params);
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
  <title><?php echo $label; ?> - Page <?php echo $page; ?></title>
</head>
<body>
<div class="content-section">
    <div class="bowling-ball-section">
        <h1><?php echo $label; ?></h1>

        <form method="get" class="filter-section" style="margin-bottom: 1em;">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
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
            <?php foreach ($current_products as $product): 
                $id = (int)$product['ProductID'];
                $img = htmlspecialchars($product['ImageID'] ?: 'images/placeholder.png');
                $name = htmlspecialchars($product['Name'] ?? 'Unnamed');
                $extra = $product[$extra_column] ?? '';
                $priceVal = htmlspecialchars($product['Price']);
                $quantity = (int)$product['quantity'];
                $soldOut = ($quantity <= 0);
                $displayName = $extra ? ($name . ' - ' . $extra) : $name;
            ?>
            <div class="hp-product-container" onclick="location.href='product-page.php?id=<?php echo $id; ?>'">
                <img class="hp-product-image" src="./images/<?php echo $img; ?>" alt="<?php echo $displayName; ?>">
                <?php if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>'; ?>
                <h5 class="hp-product-name"><?php echo $displayName; ?></h5>
                <h2 class="hp-product-price">₱<?php echo number_format($priceVal, 2); ?></h2>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?type=<?php echo $type; ?>&page=<?php echo $page - 1; ?><?php echo $availability ? '&availability=' . $availability : ''; ?><?php echo $price ? '&price=' . $price : ''; ?><?php echo $brand ? '&brand=' . $brand : ''; ?>" class="prev">Previous</a>
            <?php else: ?>
                <span class="disabled">Previous</span>
            <?php endif; ?>

            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            if ($start_page > 1) {
                echo '<a href="?type='.$type.'&page=1">1</a>';
                if ($start_page > 2) echo '<span>...</span>';
            }
            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $page) echo '<span class="current">' . $i . '</span>';
                else echo '<a href="?type='.$type.'&page='.$i.'">' . $i . '</a>';
            }
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) echo '<span>...</span>';
                echo '<a href="?type='.$type.'&page='.$total_pages.'">' . $total_pages . '</a>';
            }
            ?>

            <?php if ($page < $total_pages): ?>
                <a href="?type=<?php echo $type; ?>&page=<?php echo $page + 1; ?>" class="next">Next</a>
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