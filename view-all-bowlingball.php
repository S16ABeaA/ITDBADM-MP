<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html");

// Pagination settings

$items_per_page = 5; // edit this later on to show more items per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Get filter values from query string
$availability = isset($_GET['availability']) ? $_GET['availability'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';


// Build SQL WHERE clause for filters
$where = [];
if ($availability === 'in') {
  $where[] = 'i.Quantity > 0';
} elseif ($availability === 'out') {
  $where[] = 'i.Quantity <= 0';
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
  $where[] = "brand.Name = '" . addslashes($brand) . "'";
}
$whereSQL = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// Fetch all brands from brand table for filter dropdown
$brands = [];
$brandSql = "SELECT DISTINCT brand.Name FROM brand 
  JOIN product ON brand.BrandID = product.BrandID 
  JOIN bowlingball ON product.ProductID = bowlingball.ProductID 
  ORDER BY brand.Name ASC";
$brandRes = $conn->query($brandSql);
if ($brandRes) {
  while ($row = $brandRes->fetch_assoc()) {
    $brands[] = $row['Name'];
  }
  $brandRes->free();
}



// Count total bowling balls with filters
$countSql = "SELECT COUNT(DISTINCT b.ProductID) as cnt FROM bowlingball b 
  JOIN product p ON b.ProductID = p.ProductID
  JOIN brand ON brand.BrandID = p.BrandID
  JOIN product_variant pv ON p.ProductID = pv.ProductID 
  JOIN inventory i ON pv.VariantID = i.VariantID
  $whereSQL";
$total_products = 0;
if ($countRes = $conn->query($countSql)) {
  $row = $countRes->fetch_assoc();
  $total_products = (int)$row['cnt'];
  $countRes->free();
}
$total_pages = ceil($total_products / $items_per_page);



// Fetch bowling balls for current page with filters
$sql = "
  SELECT DISTINCT
    p.ProductID,
    p.ImageID,
    b.Name,
    p.Price,
    i.Quantity,
    brand.Name AS BrandName
  FROM bowlingball b
  JOIN product p ON b.ProductID = p.ProductID
  JOIN brand ON brand.BrandID = p.BrandID
  JOIN product_variant pv ON p.ProductID = pv.ProductID 
  JOIN inventory i ON pv.VariantID = i.VariantID
  $whereSQL
  ORDER BY p.ProductID ASC
  LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $items_per_page, $offset);
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
      <div class="filter-dropdown">
        <button type="button" class="filter-button" id="availabilityBtn">
          Availability
          <span>▼</span>
        </button>
        <div class="dropdown-content" id="availabilityDropdown">
          <div class="dropdown-option">
            <label>
              <input type="radio" name="availability" value="in" class="checkbox" <?php if($availability==='in') echo 'checked'; ?>>
              In Stock
            </label>
          </div>
          <div class="dropdown-option">
            <label>
              <input type="radio" name="availability" value="out" class="checkbox" <?php if($availability==='out') echo 'checked'; ?>>
              Out of Stock
            </label>
          </div>
          <div class="dropdown-option">
            <label>
              <input type="radio" name="availability" value="" class="checkbox" <?php if($availability==='') echo 'checked'; ?>>
              All
            </label>
          </div>
        </div>
      </div>
      <div class="filter-dropdown">
        <button type="button" class="filter-button" id="priceBtn">
          Price (₱)
          <span>▼</span>
        </button>
        <div class="dropdown-content" id="priceDropdown">
          <div class="dropdown-option">
            <label>
              <input type="radio" name="price" value="under1k" class="checkbox" <?php if($price==='under1k') echo 'checked'; ?>>
              Under 1k
            </label>
          </div>
          <div class="dropdown-option">
            <label>
              <input type="radio" name="price" value="1k-3k" class="checkbox" <?php if($price==='1k-3k') echo 'checked'; ?>>
              1k - 3k 
            </label>
          </div>
          <div class="dropdown-option">
            <label>
              <input type="radio" name="price" value="3k-5k" class="checkbox" <?php if($price==='3k-5k') echo 'checked'; ?>>
              3k - 5k
            </label>
          </div>
          <div class="dropdown-option">
            <label>
              <input type="radio" name="price" value="over5k" class="checkbox" <?php if($price==='over5k') echo 'checked'; ?>>
              Over 5k
            </label>
          </div>
          <div class="dropdown-option">
            <label>
              <input type="radio" name="price" value="" class="checkbox" <?php if($price==='') echo 'checked'; ?>>
              All
            </label>
          </div>
        </div>
      </div>
      <div class="filter-dropdown">
        <button type="button" class="filter-button" id="brandBtn">
          Brand
          <span>▼</span>
        </button>
        <div class="dropdown-content" id="brandDropdown">
          <?php
          echo '<div class="dropdown-option"><label><input type="radio" name="brand" value="all" class="checkbox" '.($brand===''||$brand==='all'?'checked':'').'>All</label></div>';
          foreach ($brands as $b) {
            echo '<div class="dropdown-option"><label><input type="radio" name="brand" value="'.htmlspecialchars($b).'" class="checkbox" '.($brand===$b?'checked':'').'>'.htmlspecialchars($b).'</label></div>';
          }
          ?>
        </div>
      </div>
      <span>Sort by:</span>
      <div class="filter-dropdown sorting-section">
        <button type="button" class="filter-button" id="sortBtn">
          Sort
          <span>▼</span>
        </button>
        <div class="dropdown-content" style="right:0; left:auto;" id="sortDropdown">
          <div class="dropdown-option" data-value="featured">Featured</div>
          <div class="dropdown-option" data-value="bestselling">Best Selling</div>
          <div class="dropdown-option" data-value="newest">Latest</div>
          <div class="dropdown-option" data-value="price-low">Price: Low to High</div>
          <div class="dropdown-option" data-value="price-high">Price: High to Low</div>
          <div class="dropdown-option" data-value="name-asc">Name: A to Z</div>
          <div class="dropdown-option" data-value="name-desc">Name: Z to A</div>
        </div>
      </div>
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
      $price = htmlspecialchars($product['Price']);
      $quantity = (int)$product['Quantity'];
      $soldOut = ($quantity <= 0);
      echo '<div class="hp-product-container" onclick="location.href=\'product-page.php?id=' . $id . '\'">';
      echo '  <img class="hp-product-image" src="./images/' . $img . '" alt="' . $name . '">';
      if ($soldOut) echo '<div class="sold-out">SOLD OUT</div>';
      echo '  <h5 class="hp-product-name">' . $name . '</h5>';
      echo '  <h2 class="hp-product-price">₱' . number_format($price, 2) . '</h2>';
      echo '</div>';
    }
    ?>
    </div>

    <!-- Pagination -->
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
        if ($i == $page) {
          echo '<span class="current">' . $i . '</span>';
        } else {
          echo '<a href="?page=' . $i . '">' . $i . '</a>';
        }
      }
      if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) echo '<span>...</span>';
        echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
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
  <script>
  // Dropdown functionality
  const availabilityBtn = document.getElementById('availabilityBtn');
  const availabilityDropdown = document.getElementById('availabilityDropdown');
  const priceBtn = document.getElementById('priceBtn');
  const priceDropdown = document.getElementById('priceDropdown');
  const brandBtn = document.getElementById('brandBtn');
  const brandDropdown = document.getElementById('brandDropdown');
    
  // Modal functionality
  const showModalBtn = document.getElementById('showModalBtn');
  const filterModal = document.getElementById('filterModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const applyBtn = document.getElementById('applyBtn');
    
  // Toggle dropdowns
  function toggleDropdown(button, dropdown) {
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-content').forEach(dd => {
      if (dd !== dropdown) {
        dd.classList.remove('show');
      }
    });
        
    // Remove active class from all buttons
    document.querySelectorAll('.filter-button').forEach(btn => {
      if (btn !== button) {
        btn.classList.remove('active');
      }
    });
        
    // Toggle current dropdown
    dropdown.classList.toggle('show');
    button.classList.toggle('active');
  }
    
  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    // Close dropdowns if clicking outside
    if (!event.target.matches('.filter-button') && !event.target.closest('.dropdown-content')) {
      document.querySelectorAll('.dropdown-content').forEach(dropdown => {
        dropdown.classList.remove('show');
      });
      document.querySelectorAll('.filter-button').forEach(button => {
        button.classList.remove('active');
      });
    }
  });
    
  // Show modal
  function showModal() {
    if (filterModal) {
      filterModal.classList.add('show');
    }
  }
    
  // Hide modal
  function hideModal() {
    if (filterModal) {
      filterModal.classList.remove('show');
    }
  }
    
  // Event listeners for dropdowns
  if (availabilityBtn && availabilityDropdown) {
    availabilityBtn.addEventListener('click', () => toggleDropdown(availabilityBtn, availabilityDropdown));
  }
    
  if (priceBtn && priceDropdown) {
    priceBtn.addEventListener('click', () => toggleDropdown(priceBtn, priceDropdown));
  }

  if (brandBtn && brandDropdown) {
    brandBtn.addEventListener('click', () => toggleDropdown(brandBtn, brandDropdown));
  }
    
  // Event listeners for modal
  if (showModalBtn) {
    showModalBtn.addEventListener('click', showModal);
  }
    
  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', hideModal);
  }
    
  if (cancelBtn) {
    cancelBtn.addEventListener('click', hideModal);
  }
    
  if (applyBtn) {
    applyBtn.addEventListener('click', hideModal);
  }
    
  // Hide modal when clicking outside
  if (filterModal) {
    filterModal.addEventListener('click', function(event) {
      if (event.target === filterModal) {
        hideModal();
      }
    });
  }

  // Also close modal with Escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && filterModal && filterModal.classList.contains('show')) {
      hideModal();
    }
  });

  // Add this to your existing JavaScript
const sortBtn = document.getElementById('sortBtn');
const sortDropdown = document.getElementById('sortDropdown');

if (sortBtn && sortDropdown) {
  sortBtn.addEventListener('click', () => toggleDropdown(sortBtn, sortDropdown));
    
  // Handle sort selection
  sortDropdown.addEventListener('click', function(event) {
    if (event.target.classList.contains('dropdown-option')) {
      const selectedValue = event.target.getAttribute('data-value');
      const selectedText = event.target.textContent;
            
      // Update button text to show selected sort option
      sortBtn.innerHTML = `${selectedText} <span>▼</span>`;
            
      // Close dropdown
      sortDropdown.classList.remove('show');
      sortBtn.classList.remove('active');
            
      //check
      console.log('Selected sort:', selectedValue);
    }
  });
}
  </script>
</body>
</html>