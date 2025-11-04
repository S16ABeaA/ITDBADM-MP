<?php include("header.html");

$products = array(
  array('id' => 1, 'name' => 'Pro Performance', 'price' => '1159.99'),
  array('id' => 1, 'name' => 'Elite Striker', 'price' => '1259.99'),
  array('id' => 1, 'name' => 'Turbo Hook', 'price' => '1359.99'),
  array('id' => 1, 'name' => 'Power Curve', 'price' => '1459.99'),
  array('id' => 1, 'name' => 'Precision Master', 'price' => '1559.99'),
  array('id' => 1, 'name' => 'Pro Performance', 'price' => '1159.99'),
  array('id' => 1, 'name' => 'Elite Striker', 'price' => '1259.99'),
  array('id' => 1, 'name' => 'Turbo Hook', 'price' => '1359.99'),
  array('id' => 1, 'name' => 'Power Curve', 'price' => '1459.99'),
  array('id' => 1, 'name' => 'Precision Master', 'price' => '1559.99'),
  array('id' => 1, 'name' => 'Pro Performance', 'price' => '1159.99'),
  array('id' => 1, 'name' => 'Elite Striker', 'price' => '1259.99'),
  array('id' => 1, 'name' => 'Turbo Hook', 'price' => '1359.99'),
  array('id' => 1, 'name' => 'Power Curve', 'price' => '1459.99'),
  array('id' => 1, 'name' => 'Precision Master', 'price' => '1559.99'),
  array('id' => 1, 'name' => 'Pro Performance', 'price' => '1159.99'),
  array('id' => 1, 'name' => 'Elite Striker', 'price' => '1259.99'),
  array('id' => 1, 'name' => 'Turbo Hook', 'price' => '1359.99'),
  array('id' => 1, 'name' => 'Power Curve', 'price' => '1459.99'),
  array('id' => 1, 'name' => 'Precision Master', 'price' => '1559.99'),
  array('id' => 1, 'name' => 'Pro Performance', 'price' => '1159.99'),
  array('id' => 1, 'name' => 'Elite Striker', 'price' => '1259.99'),
  array('id' => 1, 'name' => 'Turbo Hook', 'price' => '1359.99'),
  array('id' => 1, 'name' => 'Power Curve', 'price' => '1459.99'),
  array('id' => 1, 'name' => 'Precision Master', 'price' => '1559.99'),
   array('id' => 1, 'name' => 'Turbo Hook', 'price' => '1359.99'),
  array('id' => 1, 'name' => 'Power Curve', 'price' => '1459.99')
);

// Pagination settings
$items_per_page = 25;
$total_products = count($products);
$total_pages = ceil($total_products / $items_per_page);

// Get current page from URL, default to page 1
$current_page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;

// Calculate products to show for current page
$start_index = ($current_page - 1) * $items_per_page;
$current_products = array_slice($products, $start_index, $items_per_page);

?>

<!DOCTYPE html>
<html>
<head>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="./css/view-all-products.css?v=1.1">
  <title>Bowling Balls - Page <?php echo $current_page; ?></title>
</head>
<body>
  <div class="content-section">
    <div class="bowling-ball-section">
      <h1>Bowling Balls</h1>
      <div class="filter-section">
            <span>Filter by:</span>
            <div class="filter-dropdown">
              <button class="filter-button" id="availabilityBtn">
                  Availability
                  <span>▼</span>
              </button>
              <div class="dropdown-content" id="availabilityDropdown">
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          In Stock
                      </label>
                  </div>
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          Out of Stock
                      </label>
                  </div>
              </div>
            </div>
          
            <div class="filter-dropdown">
              <button class="filter-button" id="priceBtn">
                  Price (₱)
                  <span>▼</span>
              </button>
              <div class="dropdown-content" id="priceDropdown">
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          Under 1k
                      </label>
                  </div>
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          1k - 3k 
                      </label>
                  </div>
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          3k - 5k
                      </label>
                  </div>
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          Over 5k
                      </label>
                  </div>
              </div>
            </div>

            <div class="filter-dropdown">
              <button class="filter-button" id="brandBtn">
                  Brand
                  <span>▼</span>
              </button>
              <div class="dropdown-content" id="brandDropdown">
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          Brand 1
                      </label>
                  </div>
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          Brand 2
                      </label>
                  </div>
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          Brand 3
                      </label>
                  </div>
                  <div class="dropdown-option">
                      <label>
                          <input type="checkbox" class="checkbox">
                          Brand 4
                      </label>
                  </div>
              </div>
            </div>
            <span>Sort by:</span>
                <div class="filter-dropdown sorting-section">
                    <button class="filter-button" id="sortBtn">
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

      </div>

      <!-- Page Info -->
      <?php if ($total_pages > 1): ?>
      <div class="page-info">
        Showing <?php echo $start_index + 1; ?>-<?php echo min($start_index + $items_per_page, $total_products); ?> of <?php echo $total_products; ?> products
        (Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>)
      </div>
      <?php endif; ?>


      <div class="product-grid">
        <?php
        // Loop through current page products only
        foreach ($current_products as $product) {
          echo '
          <div class="hp-product-container" onclick="location.href= `product-page.php`">
            <img class="hp-product-image" src="./images/bowlingball' . $product['id'] . '.png">
            <div class="sold-out">SOLD OUT</div>
            <h5 class="hp-product-name">' . $product['name'] . '</h5>
            <h2 class="hp-product-price">P' . $product['price'] . '</h2>
          </div>';
        }
        ?>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
      <div class="pagination">
          <!-- Previous Page -->
          <?php if ($current_page > 1): ?>
              <a href="?page=<?php echo $current_page - 1; ?>" class="prev">Previous</a>
          <?php else: ?>
              <span class="disabled">Previous</span>
          <?php endif; ?>

          <!-- Page Numbers -->
          <?php
          // Show page numbers with ellipsis for many pages
          $start_page = max(1, $current_page - 2);
          $end_page = min($total_pages, $current_page + 2);
          
          if ($start_page > 1) {
              echo '<a href="?page=1">1</a>';
              if ($start_page > 2) echo '<span>...</span>';
          }
          
          for ($i = $start_page; $i <= $end_page; $i++) {
              if ($i == $current_page) {
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

          <!-- Next Page -->
          <?php if ($current_page < $total_pages): ?>
              <a href="?page=<?php echo $current_page + 1; ?>" class="next">Next</a>
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