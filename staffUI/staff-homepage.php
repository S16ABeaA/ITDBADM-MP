<?php 
require_once '../dependencies/config.php';
include('staff-header.html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Dashboard - Product Management</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link href="../css/staffCSS/staff-homepage.css" rel="stylesheet">
</head>
<body>
  <div class="content-section">
    <!-- Navigation Menu -->
    <div class="nav-menu">
      <div class="nav-row">
        <div class="nav-item active" data-category="bowling-balls">Bowling Balls</div>
        <div class="nav-item" data-category="shoes">Bowling Shoes</div>
        <div class="nav-item" data-category="bags">Bowling Bags</div>
        <div class="nav-item" data-category="accessories">Bowling Accessories</div>
        <div class="nav-item" data-category="cleaning">Cleaning Supplies</div>
        <div class="nav-item" data-category="transaction">Transactions</div>
      </div>
    </div>

  
    <!-- Stats Overview -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-bowling-ball"></i>
        </div>
        <div class="stat-number">156</div>
        <div class="stat-label">Total Products</div>
      </div>
      <?php
      $lowstockquery; 
      ?>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-number">128</div>
        <div class="stat-label">In Stock</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-number">18</div>
        <div class="stat-label">Low Stock</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-number">10</div>
        <div class="stat-label">Out of Stock</div>
      </div>
    </div>

    <div class="page-header-controls">
      <div class="header-main">
        <h1 class="page-title">Bowling Ball Inventory</h1>
        <button class="btn btn-primary" id="addProductBtn">
          <i class="fas fa-plus"></i>
          Add New Product
        </button>
      </div>
      
      <div class="header-controls">
        <div class="search-box">
          <input type="text" placeholder="Search transactions..." id="searchInput">
          <i class="fas fa-search"></i>
        </div>
        <div class="filters">
          <!-- Show different filters based on category -->
          <select class="filter-select" id="brandFilter" style="display: none;">
            <option value="">All Brands</option>
            <option value="Brunswick">Brunswick</option>
            <option value="Storm">Storm</option>
            <option value="Ebonite">Ebonite</option>
            <option value="Hammer">Hammer</option>
          </select>
          
          <select class="filter-select" id="statusFilter" style="display: none;">
            <option value="">All Status</option>
            <option value="active">In Stock</option>
            <option value="low">Low Stock</option>
            <option value="out">Out of Stock</option>
          </select>
          
          <!-- Transaction-specific filters -->
          <select class="filter-select" id="transactionStatusFilter" style="display: none;">
            <option value="">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Processing">Processing</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
          
          <select class="filter-select" id="paymentFilter" style="display: none;">
            <option value="">All Payments</option>
            <option value="Credit Card">Credit Card</option>
            <option value="PayPal">PayPal</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cash">Cash</option>
          </select>
          
          <select class="filter-select" id="deliveryFilter" style="display: none;">
            <option value="">All Delivery</option>
            <option value="Home Delivery">Home Delivery</option>
            <option value="Store Pickup">Store Pickup</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="bowling-ball-info-container">
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Brand</th> 
            <th>Type</th>
            <th>Quality</th>
            <th>Weight</th>
            <th>Core Name</th>
            <th>Core Type</th>
            <th>RG</th>
            <th>DIFF</th>
            <th>INT DIFF</th>
            <th>Coverstock</th>
            <th>Coverstock Type</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT DISTINCT bb.Name AS bbname, br.Name AS brandname, bb.Type AS bbtype, bb.Quality AS bbquality, w.weight AS weight, bb.CoreName AS corename, bb.CoreType AS coretype, w.RG AS rg, w.DIFF AS diff, w.INTDIFF AS intdiff, bb.Coverstock AS coverstock, bb.CoverstockType AS coverstocktype, pr.Price AS price, SUM(i.Quantity) AS quantity
                    FROM weight w JOIN bowlingball bb ON w.ProductID = bb.ProductID
                    JOIN product pr ON bb.ProductID = pr.ProductID
                    JOIN product_variant pv ON pr.ProductID = pv.ProductID
                    JOIN brand br ON pr.BrandID = br.BrandID
                    JOIN inventory i ON pv.VariantID = i.VariantID
                    GROUP BY bb.Name, br.Name, bb.Type, bb.Quality, w.weight, bb.CoreName, bb.CoreType, w.RG, w.DIFF, w.INTDIFF, bb.Coverstock, bb.CoverstockType, pr.Price, pv.VariantID;";
          $result = $conn->query($query);
          while ($row = $result->fetch_assoc()){
            $bbName = $row['bbname'];
            $brand = $row['brandname'];
            $type = $row['bbtype'];
            $quality = $row['bbquality'];
            $weight = $row['weight'];
            $coreName = $row['corename'];
            $coreType = $row['coretype'];
            $rg = $row['rg'];
            $diff = $row['diff'];
            $intDiff = $row['intdiff'];
            $coverstock = $row['coverstock'];
            $coverstockType = $row['coverstocktype'];
            $price = $row['price'];
            $quantity = $row['quantity'];
          ?>
  
          <tr>
            <td><?php echo $bbName;?></td>
            <td><?php echo $brand;?></td>
            <td><?php echo $type;?></td>
            <td><?php echo $quality;?></td>
            <td><?php echo $weight;?></td>
            <td><?php echo $coreName;?></td>
            <td><?php echo $coreType;?></td>
            <td><?php echo $rg;?></td>
            <td><?php echo $diff;?></td>
            <td><?php echo $intDiff;?></td>
            <td><?php echo $coverstock;?></td>
            <td><?php echo $coverstockType;?></td>
            <td><?php echo $price;?></td>
            <td><?php echo $quantity;?></td>
            <td><span class="tstatus-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="1">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr><?php }?>
        </tbody>
      </table>
    </div>

    <div class="bowling-bag-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Brand</th> 
            <th>Color</th>
            <th>Type</th>
            <th>Size</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $bgquery = "SELECT DISTINCT bg.Name as bgname, br.Name as brandname, c.color as color, bg.Size as size, bg.Type as bgtype, pr.Price as price, SUM(i.Quantity) as quantity
                      FROM color c JOIN bowlingbag bg ON c.ProductID = bg.ProductID
                      JOIN product pr ON bg.ProductID = pr.ProductID
                      JOIN brand br ON pr.BrandID = br.BrandID
                      JOIN product_variant pv ON pr.ProductID = pv.ProductID
                      JOIN inventory i ON pv.VariantID = i.VariantID
                      GROUP BY bg.Name, br.Name, c.color, bg.Size, bg.Type, pr.Price, pv.VariantID";
          $bgresult = $conn->query($bgquery);
          while($bgrow = $bgresult->fetch_assoc()){
            $bgname = $bgrow['bgname'];
            $brname = $bgrow['brandname'];
            $bgcolor = $bgrow['color'];
            $bgsize = $bgrow['size'];
            $bgtype = $bgrow['bgtype'];
            $bgprice = $bgrow['price'];
            $bgquantity = $bgrow['quantity'];
          ?>
          <tr>
            <td><?php echo $bgname;?></td>
            <td><?php echo $brname;?></td>
            <td><?php echo $bgcolor;?></td>
            <td><?php echo $bgtype;?></td>
            <td><?php echo $bgsize;?></td>
            <td><?php echo $bgprice;?></td>
            <td><?php echo $bgquantity;?></td>
            <td><span class="tstatus-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr><?php }?>
        </tbody>
      </table>
    </div>

    <div class="bowling-shoes-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Brand</th> 
            <th>Size</th>
            <th>Gender</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $bsquery = "SELECT DISTINCT bs.Name as bsname, br.Name as brandname, s.size as size, s.sex as sex, pr.Price as price, SUM(i.Quantity) as quantity
                      FROM size s JOIN bowlingshoes bs ON s.ProductID = bs.ProductID
                      JOIN product pr ON bs.ProductID = pr.ProductID
                      JOIN product_variant pv ON pr.ProductID = pv.ProductID
                      JOIN brand br ON pr.BrandID = br.BrandID
                      JOIN inventory i ON pv.VariantID = i.VariantID
                      GROUP BY bs.Name, br.Name, s.size, s.sex, pr.Price, pv.VariantID";   
          $bsresult = $conn->query($bsquery);
          while($bsrow = $bsresult->fetch_assoc()){
            $bsname = $bsrow['bsname'];
            $brname = $bsrow['brandname'];
            $bssize = $bsrow['size'];
            $bssex = $bsrow['sex'];
            $bsprice = $bsrow['price'];
            $bsquantity = $bsrow['quantity'];
          ?> 
           <tr>
            <td><?php echo $bsname;?></td>
            <td><?php echo $brname;?></td>
            <td><?php echo $bssize;?></td>
            <td><?php echo $bssex;?></td>
            <td><?php echo $bsprice;?></td>
            <td><?php echo $bsquantity;?></td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr> <?php } ?>
        </tbody>
      </table>
    </div>

    <div class="bowling-accesories-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Brand</th> 
            <th>Type</th>
            <th>Handedness</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $baquery = "SELECT DISTINCT ba.Name as baname, br.Name as brandname, ba.Type as batype, ba.Handedness as bahandedness, pr.Price as price, SUM(i.Quantity) as quantity
                      FROM bowlingaccessories ba JOIN product pr ON ba.ProductID = pr.ProductID
                      JOIN brand br ON pr.BrandID = br.BrandID
                      JOIN product_variant pv ON pr.ProductID = pv.ProductID
                      JOIN inventory i ON pv.VariantID = i.VariantID
                      GROUP BY ba.Name, br.Name, ba.Type, ba.Handedness, pr.Price, pv.VariantID";

          $baresult = $conn->query($baquery);
          while($barow = $baresult->fetch_assoc()){
            $baname = $barow['baname'];
            $brname = $barow['brandname'];
            $batype = $barow['batype'];
            $bahandedness = $barow['bahandedness'];
            $baprice = $barow['price'];
            $baquantity = $barow['quantity'];
          ?>
           <tr>
            <td><?php echo $baname;?></td>
            <td><?php echo $brname;?></td>
            <td><?php echo $batype;?></td>
            <td><?php echo $bahandedness;?></td>
            <td><?php echo $baprice;?></td>
            <td><?php echo $baquantity;?></td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr><?php }?>
        </tbody>
      </table>
    </div>

    <div class="cleaning-supplies-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Brand</th> 
            <th>Type</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $csquery = "SELECT DISTINCT cs.Name as csname, br.Name as brandname, cs.type as cstype, pr.Price as price, SUM(i.Quantity) as quantity
                      FROM cleaningsupplies cs JOIN product pr ON cs.ProductID = pr.ProductID
                      JOIN brand br ON pr.BrandID = br.BrandID
                      JOIN product_variant pv ON pr.ProductID = pv.ProductID
                      JOIN inventory i ON pv.VariantID = i.VariantID
                      GROUP BY cs.Name, br.Name, cs.type, pr.Price, pv.VariantID";
          $csresult = $conn->query($csquery);
          while($csrow = $csresult->fetch_assoc()){
            $csname = $csrow['csname'];
            $brname = $csrow['brandname'];
            $cstype = $csrow['cstype'];
            $csprice = $csrow['price'];
            $csquantity = $csrow['quantity'];
          ?>
           <tr>
            <td><?php echo $csname;?></td>
            <td><?php echo $brname;?></td>
            <td><?php echo $cstype;?></td>
            <td><?php echo $csprice;?></td>
            <td><?php echo $csquantity;?></td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr><?php }?>
        </tbody>
      </table>
    </div>

    <!-- Update the transactions table with proper status badges -->
    <div class="transactions-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Shop Branch</th> 
            <th>Date Purchased</th>
            <th>Currency</th>
            <th>Total</th>
            <th>Payment Mode</th>
            <th>Delivery Method</th>
            <th>Date Completed</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>ORD-001</td>
            <td>John Smith</td>
            <td>Downtown</td>
            <td>2024-01-15</td>
            <td>USD</td>
            <td>$156.75</td>
            <td>Credit Card</td>
            <td>Home Delivery</td>
            <td>2024-01-17</td>
            <td><span class="transaction-status-badge status-completed">Completed</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-001">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-002</td>
            <td>Sarah Johnson</td>
            <td>West Mall</td>
            <td>2024-01-16</td>
            <td>USD</td>
            <td>$89.99</td>
            <td>PayPal</td>
            <td>Store Pickup</td>
            <td>2024-01-16</td>
            <td><span class="transaction-status-badge status-completed">Completed</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-002">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-003</td>
            <td>Mike Chen</td>
            <td>East Side</td>
            <td>2024-01-17</td>
            <td>USD</td>
            <td>$234.50</td>
            <td>Credit Card</td>
            <td>Home Delivery</td>
            <td></td>
            <td><span class="transaction-status-badge status-processing">Processing</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-003">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-004</td>
            <td>Emily Davis</td>
            <td>Downtown</td>
            <td>2024-01-18</td>
            <td>EUR</td>
            <td>€145.00</td>
            <td>Bank Transfer</td>
            <td>Home Delivery</td>
            <td></td>
            <td><span class="transaction-status-badge status-pending">Pending</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-004">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-005</td>
            <td>Robert Brown</td>
            <td>North Plaza</td>
            <td>2024-01-19</td>
            <td>USD</td>
            <td>$67.25</td>
            <td>Cash</td>
            <td>Store Pickup</td>
            <td></td>
            <td><span class="transaction-status-badge status-cancelled">Cancelled</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-005">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-006</td>
            <td>Lisa Wilson</td>
            <td>West Mall</td>
            <td>2024-01-20</td>
            <td>USD</td>
            <td>$189.99</td>
            <td>Credit Card</td>
            <td>Home Delivery</td>
            <td></td>
            <td><span class="transaction-status-badge status-pending">Pending</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-006">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-007</td>
            <td>David Kim</td>
            <td>East Side</td>
            <td>2024-01-21</td>
            <td>USD</td>
            <td>$78.50</td>
            <td>PayPal</td>
            <td>Store Pickup</td>
            <td></td>
            <td><span class="transaction-status-badge status-processing">Processing</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-007">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-008</td>
            <td>Maria Garcia</td>
            <td>Downtown</td>
            <td>2024-01-22</td>
            <td>USD</td>
            <td>$345.75</td>
            <td>Credit Card</td>
            <td>Home Delivery</td>
            <td>2024-01-24</td>
            <td><span class="transaction-status-badge status-completed">Completed</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-008">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-009</td>
            <td>James Taylor</td>
            <td>North Plaza</td>
            <td>2024-01-23</td>
            <td>USD</td>
            <td>$122.30</td>
            <td>Cash</td>
            <td>Store Pickup</td>
            <td></td>
            <td><span class="transaction-status-badge status-cancelled">Cancelled</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-009">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>ORD-010</td>
            <td>Amanda Lee</td>
            <td>West Mall</td>
            <td>2024-01-24</td>
            <td>USD</td>
            <td>$278.45</td>
            <td>Credit Card</td>
            <td>Home Delivery</td>
            <td></td>
            <td><span class="transaction-status-badge status-processing">Processing</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="ORD-010">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>

<!-- modal for adding and editing bowling ball-->
<div class="modal bowling-ball-modal" id="bowlingBallModal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Add New Bowling Ball</h2>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <form id="bowlingBagForm">
        <div class="form-grid">
          <!-- Basic Information -->
          <div class="form-group">
            <label for="ballName" class="required">Ball Name</label>
            <input type="text" id="ballName" name="ballName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="ballBrand" class="required">Brand</label>
            <select id="ballBrand" name="ballBrand" required>
              <option value="">Select Brand</option>
              <option value="Storm">Storm</option>
              <option value="Brunswick">Brunswick</option>
              <option value="Ebonite">Ebonite</option>
              <option value="Hammer">Hammer</option>
              <option value="Roto Grip">Roto Grip</option>
              <option value="Motiv">Motiv</option>
              <option value="Track">Track</option>
              <option value="900 Global">900 Global</option>
            </select>
          </div>
          <div class="form-group">
            <label for="ballPrice" class="required">Price (₱)</label>
            <input type="number" id="ballPrice" name="ballPrice" step="0.01" min="0" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label for="ballStock" class="required">Stock Quantity</label>
            <input type="number" id="ballStock" name="ballStock" min="0" placeholder="0" required>
          </div>
          
          <!-- Ball Specifications -->
          <div class="form-group">
            <label for="ballWeight" class="required">Ball Weight (lbs)</label>
            <select id="ballWeight" name="ballWeight" required>
              <option value="">Select Weight</option>
              <option value="12">12 lbs</option>
              <option value="13">13 lbs</option>
              <option value="14">14 lbs</option>
              <option value="15" selected>15 lbs</option>
              <option value="16">16 lbs</option>
            </select>
          </div>
          <div class="form-group">
            <label for="ballType" class="required">Ball Type</label>
            <select id="ballType" name="ballType" required>
              <option value="">Select Type</option>
              <option value="Plastic">Plastic</option>
              <option value="Urethane">Urethane</option>
              <option value="Solid">Solid</option>
              <option value="Pearl">Pearl</option>
              <option value="Hybrid">Hybrid</option>
            </select>
          </div>
          <div class="form-group">
            <label for="ballQuality" class="required">Quality Level</label>
            <select id="ballQuality" name="ballQuality" required>
              <option value="">Select Quality</option>
              <option value="New">New</option>
              <option value="SecondHand">Second Hand</option>
            </select>
          </div>

          <!-- Core Specifications -->
          <div class="form-group">
            <label for="coreName">Core Name</label>
            <input type="text" id="coreName" name="coreName" placeholder="e.g., Radial Core, Gas Mask Core">
          </div>
          <div class="form-group">
            <label for="coreType">Core Type</label>
            <select id="coreType" name="coreType">
              <option value="">Select Core Type</option>
              <option value="Symmetric">Symmetric</option>
              <option value="Asymmetric">Asymmetric</option>
            </select>
          </div>
          <div class="form-group">
            <label for="rgValue">RG (Radius of Gyration)</label>
            <input type="number" id="rgValue" name="rgValue" step="0.01" min="2.4" max="2.8" placeholder="2.48">
          </div>
          <div class="form-group">
            <label for="diffValue">Differential (DIFF)</label>
            <input type="number" id="diffValue" name="diffValue" step="0.001" min="0" max="0.060" placeholder="0.050">
          </div>
          <div class="form-group">
            <label for="intDiffValue">Intermediate Differential (INT DIFF)</label>
            <input type="number" id="intDiffValue" name="intDiffValue" step="0.001" min="0" max="0.020" placeholder="0.015">
          </div>

          <!-- Coverstock Specifications -->
          <div class="form-group">
            <label for="coverstockName">Coverstock Name</label>
            <input type="text" id="coverstockName" name="coverstockName" placeholder="e.g., NRG Hybrid, GB 4.0">
          </div>
          <div class="form-group">
            <label for="coverstockType">Coverstock Type</label>
            <select id="coverstockType" name="coverstockType">
              <option value="">Select Coverstock Type</option>
              <option value="Solid">Solid</option>
              <option value="Pearl">Pearl</option>
              <option value="Hybrid">Hybrid</option>
            </select>
          </div>

          
          <div class="form-group full-width">
            <label for="ballDescription" class="required">Product Description</label>
            <textarea id="ballDescription" name="ballDescription" placeholder="Describe the ball's performance characteristics, intended lane conditions, and key features..." required></textarea>
          </div>

          <div class="form-group full-width">
            <label for="ballImage" class="required">Product Images</label>
            <input type="text" id="ballImage" name="ballImage">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">Add Bowling Ball</button>
        </div>
      </form>
    </div>
    
  </div>
</div>

<!-- modal for adding and editing bowling shoes-->
<div class="modal bowling-shoes-modal" id="bowlingShoesModal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Add New Bowling Shoes</h2>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <form id="bowlingShoesForm">
        <div class="form-grid">
          <!-- Basic Information -->
          <div class="form-group">
            <label for="shoeName" class="required">Shoe Name</label>
            <input type="text" id="shoeName" name="shoeName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="shoeBrand" class="required">Brand</label>
            <select id="shoeBrand" name="shoeBrand" required>
              <option value="">Select Brand</option>
              <option value="Storm">Storm</option>
              <option value="Brunswick">Brunswick</option>
              <option value="Ebonite">Ebonite</option>
              <option value="Hammer">Hammer</option>
              <option value="Roto Grip">Roto Grip</option>
              <option value="Motiv">Motiv</option>
              <option value="Track">Track</option>
              <option value="900 Global">900 Global</option>
            </select>
          </div>

          <div class="form-group">
            <label for="shoeSize" class="required">Size</label>
            <input type="number" id="shoeSize" name="shoeSize" required>
          </div>

           <div class="form-group">
            <label for="shoeGender" class="required">Gender</label>
            <input type="number" id="shoeGender" name="shoeGender" required>
          </div>
     
          <div class="form-group">
            <label for="shoePrice" class="required">Price (₱)</label>
            <input type="number" id="shoePrice" name="shoePrice" step="0.01" min="0" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label for="shoeStock" class="required">Stock Quantity</label>
            <input type="number" id="shoeStock" name="shoeStock" min="0" placeholder="0" required>
          </div>
          
          <div class="form-group full-width">
            <label for="shoeDescription" class="required">Product Description</label>
            <textarea id="shoeDescription" name="shoeDescription" placeholder="Describe the product..." required></textarea>
          </div>

          <div class="form-group full-width">
            <label for="shoeImage" class="required">Product Images</label>
            <input type="text" id="shoeImage" name="shoeImage">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" id="shoeModalCancelBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="shoeModalSubmitBtn">Add Bowling Shoe</button>
        </div>
      </form>
    </div>
    
  </div>
</div>

<!-- modal for adding and editing bowling bag-->
<div class="modal bowling-bag-modal" id="bowlingBagModal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Add New Bowling Bag</h2>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <form id="bowlingBagForm">
        <div class="form-grid">
          <!-- Basic Information -->
          <div class="form-group">
            <label for="bagName" class="required">Bag Name</label>
            <input type="text" id="bagName" name="bagName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="bagBrand" class="required">Brand</label>
            <select id="bagBrand" name="bagBrand" required>
              <option value="">Select Brand</option>
              <option value="Storm">Storm</option>
              <option value="Brunswick">Brunswick</option>
              <option value="Ebonite">Ebonite</option>
              <option value="Hammer">Hammer</option>
              <option value="Roto Grip">Roto Grip</option>
              <option value="Motiv">Motiv</option>
              <option value="Track">Track</option>
              <option value="900 Global">900 Global</option>
            </select>
          </div>

          <div class="form-group">
            <label for="bagColor" class="required">Color</label>
            <input id="bagColor" name="bagColor" required>
          </div>

           <div class="form-group">
            <label for="bagType" class="required">Type</label>
            <select id="bagType" name="bagType" required>
              <option value="">Select Type</option>
              <option value="Storm">Backpack</option>
              <option value="Brunswick">Roller</option>
              <option value="Ebonite">Tote</option>
            </select>
          </div>
     
          <div class="form-group">
            <label for="bagPrice" class="required">Price (₱)</label>
            <input type="number" id="bagPrice" name="bagPrice" step="0.01" min="0" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label for="bagStock" class="required">Stock Quantity</label>
            <input type="number" id="bagStock" name="bagStock" min="0" placeholder="0" required>
          </div>
          
          <div class="form-group full-width">
            <label for="bagDescription" class="required">Product Description</label>
            <textarea id="bagDescription" name="bagDescription" placeholder="Describe the product..." required></textarea>
          </div>

          <div class="form-group full-width">
            <label for="bagImage" class="required">Product Images</label>
            <input type="text" id="bagImage" name="bagImage">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" id="bagModalCancelBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="bagModalSubmitBtn">Add Bowling Bag</button>
        </div>
      </form>
    </div>
    
  </div>
</div>

<!-- modal for adding and editing bowling accessories-->
<div class="modal bowling-accessories-modal" id="bowlingAccessoriesModal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Add New Bowling Accessory</h2>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <form id="bowlingAccessoriesForm">
        <div class="form-grid">
          <!-- Basic Information -->
          <div class="form-group">
            <label for="accessoryName" class="required">Accessory Name</label>
            <input type="text" id="accessoryName" name="accessoryName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="accessoryBrand" class="required">Brand</label>
            <select id="accessoryBrand" name="accessoryBrand" required>
              <option value="">Select Brand</option>
              <option value="Storm">Storm</option>
              <option value="Brunswick">Brunswick</option>
              <option value="Ebonite">Ebonite</option>
              <option value="Hammer">Hammer</option>
              <option value="Roto Grip">Roto Grip</option>
              <option value="Motiv">Motiv</option>
              <option value="Track">Track</option>
              <option value="900 Global">900 Global</option>
            </select>
          </div>

           <div class="form-group">
            <label for="accessoryType" class="required">Type</label>
            <select id="accessoryType" name="accessoryType" required>
              <option value="">Select Type</option>
              <option value="Tape">Tape</option>
              <option value="Grips">Grips</option>
              <option value="Wrister">Wrister</option>
            </select>
          </div>

          <div class="form-group">
            <label for="handedness" class="required">Handedness</label>
            <select id="handedness" name="handedness" required>
              <option value="">Select Type</option>
              <option value="Left">Left</option>
              <option value="Right">Right</option>
            </select>
          </div>
     
          <div class="form-group">
            <label for="accessoryPrice" class="required">Price (₱)</label>
            <input type="number" id="accessoryPrice" name="accessoryPrice" step="0.01" min="0" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label for="accessoryStock" class="required">Stock Quantity</label>
            <input type="number" id="accessoryStock" name="accessoryStock" min="0" placeholder="0" required>
          </div>
          
          <div class="form-group full-width">
            <label for="accessoryDescription" class="required">Product Description</label>
            <textarea id="accessoryDescription" name="accessoryDescription" placeholder="Describe the product..." required></textarea>
          </div>

          <div class="form-group full-width">
            <label for="accessoryImage" class="required">Product Images</label>
            <input type="text" id="accessoryImage" name="accessoryImage">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" id="accessoryModalCancelBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="accessoryModalSubmitBtn">Add Bowling Accessory</button>
        </div>
      </form>
    </div>
    
  </div>
</div>

<!-- modal for adding and editing cleaning supplies-->
<div class="modal cleaning-supplies-modal" id="cleaningSuppliesModal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Add New Cleaning Supplies</h2>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <form id="CleaningSuppliesForm">
        <div class="form-grid">
          <!-- Basic Information -->
          <div class="form-group">
            <label for="supplyName" class="required">Supply Name</label>
            <input type="text" id="supplyName" name="supplyName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="supplyBrand" class="required">Brand</label>
            <select id="supplyBrand" name="supplyBrand" required>
              <option value="">Select Brand</option>
              <option value="Storm">Storm</option>
              <option value="Brunswick">Brunswick</option>
              <option value="Ebonite">Ebonite</option>
              <option value="Hammer">Hammer</option>
              <option value="Roto Grip">Roto Grip</option>
              <option value="Motiv">Motiv</option>
              <option value="Track">Track</option>
              <option value="900 Global">900 Global</option>
            </select>
          </div>

           <div class="form-group">
            <label for="supplyType" class="required">Type</label>
            <select id="supplyType" name="supplyType" required>
              <option value="">Select Type</option>
              <option value="Towel">Towel</option>
              <option value="Cleaner">Cleaner</option>
              <option value="Puff">Puff</option>
              <option value="Pads">Pads</option>
            </select>
          </div>
     
          <div class="form-group">
            <label for="supplyPrice" class="required">Price (₱)</label>
            <input type="number" id="supplyPrice" name="supplyPrice" step="0.01" min="0" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label for="supplyStock" class="required">Stock Quantity</label>
            <input type="number" id="supplyStock" name="supplyStock" min="0" placeholder="0" required>
          </div>
          
          <div class="form-group full-width">
            <label for="supplyDescription" class="required">Product Description</label>
            <textarea id="supplyDescription" name="supplyDescription" placeholder="Describe the product..." required></textarea>
          </div>

           <div class="form-group full-width">
            <label for="supplyImage" class="required">Product Images</label>
            <input type="text" id="supplyImage" name="supplyImage">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" id="supplyModalCancelBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="supplyModalSubmitBtn">Add Cleaning Supply</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- modal for editing transaction status-->
<div class="modal transaction-modal" id="transactionModal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Edit Transaction Status</h2>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <form id="transactionForm">
        <div class="form-grid">
          <!-- Order Information (Read Only) -->
          <div class="form-group">
            <label for="orderId">Order ID</label>
            <input type="text" id="orderId" name="orderId" readonly class="readonly-field">
          </div>
          <div class="form-group">
            <label for="customer">Customer</label>
            <input type="text" id="customer" name="customer" readonly class="readonly-field">
          </div>
          <div class="form-group">
            <label for="shopBranch">Shop Branch</label>
            <input type="text" id="shopBranch" name="shopBranch" readonly class="readonly-field">
          </div>
          <div class="form-group">
            <label for="datePurchased">Date Purchased</label>
            <input type="text" id="datePurchased" name="datePurchased" readonly class="readonly-field">
          </div>
          <div class="form-group">
            <label for="currency">Currency</label>
            <input type="text" id="currency" name="currency" readonly class="readonly-field">
          </div>
          <div class="form-group">
            <label for="total">Total</label>
            <input type="text" id="total" name="total" readonly class="readonly-field">
          </div>
          <div class="form-group">
            <label for="paymentMode">Payment Mode</label>
            <input type="text" id="paymentMode" name="paymentMode" readonly class="readonly-field">
          </div>
          <div class="form-group">
            <label for="deliveryMethod">Delivery Method</label>
            <input type="text" id="deliveryMethod" name="deliveryMethod" readonly class="readonly-field">
          </div>
          <div class="form-group">
            <label for="dateCompleted">Date Completed</label>
            <input type="text" id="dateCompleted" name="dateCompleted" readonly class="readonly-field">
          </div>
          
          <!-- Status  -->
          <div class="form-group">
            <label for="status" class="required">Status</label>
            <select id="status" name="status" required>
              <option value="">Select Status</option>
              <option value="Pending">Pending</option>
              <option value="Processing">Processing</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" id="cancelTransactionBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="updateStatusBtn">Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Category Switching Logic
  $(document).ready(function() {
      // Cache DOM elements for better performance
      const $navItems = $('.nav-item');
      const $tables = $('.bowling-ball-info-container, .bowling-bag-info-container, .bowling-shoes-info-container, .bowling-accesories-info-container, .cleaning-supplies-info-container, .transactions-info-container');
      const $pageTitle = $('.page-title');
      const $addProductBtn = $('#addProductBtn');
      const $searchInput = $('#searchInput');
      const $brandFilter = $('#brandFilter');
      const $statusFilter = $('#statusFilter');
      
      let currentCategory = 'bowling-balls';
      
      // Category configuration (edit)
      const categoryConfig = {
          'bowling-balls': {
              table: '.bowling-ball-info-container',
              title: 'Bowling Ball Inventory',
              buttonText: 'Add New Bowling Ball',
              modalId: '#bowlingBallModal',
              brands: ['Storm', 'Brunswick', 'Ebonite', 'Hammer', 'Roto Grip', 'Motiv', 'Track', '900 Global']
          },
          'shoes': {
              table: '.bowling-shoes-info-container',
              title: 'Bowling Shoes Inventory',
              buttonText: 'Add New Shoes',
              modalId: '#bowlingShoesModal',
              brands: ['Brunswick', 'Dexter', '3G', 'Storm']
          },
          'bags': {
              table: '.bowling-bag-info-container',
              title: 'Bowling Bags Inventory',
              buttonText: 'Add New Bag',
              modalId: '#bowlingBagModal',
              brands: ['Storm', 'Brunswick', 'Motiv', 'KR', 'Vise', 'Dexter']
          },
          'accessories': {
              table: '.bowling-accesories-info-container',
              title: 'Bowling Accessories Inventory',
              buttonText: 'Add New Accessory',
              modalId: '#bowlingAccessoriesModal',
              brands: ['Vise', 'Turbo', 'Genesis', 'Storm']
          },
          'cleaning': {
              table: '.cleaning-supplies-info-container',
              title: 'Cleaning Supplies Inventory',
              buttonText: 'Add Cleaning Product',
              modalId: '#cleaningSuppliesModal',
              brands: ['Storm', 'Brunswick', 'Tac Up', 'That Purple Stuff']
          },
          'transaction': {
              table: '.transactions-info-container',
              title: 'Transaction Management',
              modalId: '#transactionModal',
              searchPlaceholder: 'Search transactions...',
              hasAddButton: false
          }
      };

      // Navigation menu functionality
      $navItems.on('click', function() {
          $navItems.removeClass('active');
          $(this).addClass('active');
          
          const category = $(this).data('category');
          switchCategory(category);
      });

      // Category switching function
      function switchCategory(category) {
          try {
              console.log('Switched to category:', category);
              currentCategory = category;
              
              const config = categoryConfig[category];
              if (!config) {
                  console.error('Unknown category:', category);
                  return;
              }
              
              // Hide all tables
              $tables.addClass('hidden');
              
              // Update page header
              updatePageHeader(config);
              
              if (config.hasAddButton === false) {
                  $addProductBtn.hide();
              } else {
                  $addProductBtn.show();
              }
              $searchInput.attr('placeholder', config.searchPlaceholder || 'Search products...');
          
              // Show the selected category table if it exists
              if (config.table) {
                  $(config.table).removeClass('hidden');
              }

              updateFiltersForCategory(category);
              initializeTableFunctionality();
          
              
          } catch (error) {
              console.error('Error switching category:', error);
          }
      }

      // Update page header based on category config
      function updatePageHeader(config) {
          $pageTitle.text(config.title);
          $addProductBtn.html('<i class="fas fa-plus"></i> ' + config.buttonText);
      }

      // Update filters based on category brands
      function updateFiltersForCategory(category) {
          // Hide all filters first
          $('.filter-select').hide();
          
          // Show relevant filters based on category
          if (category === 'transaction') {
              $('#transactionStatusFilter').show();
              $('#paymentFilter').show();
              $('#deliveryFilter').show();
          } else {
              $('#brandFilter').show();
              $('#statusFilter').show();
          }
      }


      // Initialize table functionality (search, filters) for the current visible table
      function initializeTableFunctionality() {
      // Search functionality
      $('#searchInput').off('input').on('input', function() {
          const searchTerm = $(this).val().toLowerCase();
          const visibleTable = getVisibleTable();
          
          let visibleRows = 0;
          
          visibleTable.find('tbody tr').each(function() {
              const rowText = $(this).text().toLowerCase();
              const isVisible = rowText.includes(searchTerm);
              $(this).toggle(isVisible);
              if (isVisible) visibleRows++;
          });
          
          toggleNoResultsMessage(visibleTable, visibleRows === 0);
      });

      // Filter functionality for product categories
      $('#brandFilter, #statusFilter').off('change').on('change', function() {
          if (currentCategory === 'transaction') return;
          
          filterProductTable();
      });

      // Filter functionality for transactions
      $('#transactionStatusFilter, #paymentFilter, #deliveryFilter').off('change').on('change', function() {
          if (currentCategory !== 'transaction') return;
          
          filterTransactionTable();
      });
    }

    function getVisibleTable() {
        return $('.bowling-ball-info-container:not(.hidden), .bowling-bag-info-container:not(.hidden), .bowling-shoes-info-container:not(.hidden), .bowling-accesories-info-container:not(.hidden), .cleaning-supplies-info-container:not(.hidden), .transactions-info-container:not(.hidden)');
    }

    // Function to show/hide no results message
    function toggleNoResultsMessage(visibleTable, showMessage) {
        // Remove existing no results message
        visibleTable.find('.no-results-message').remove();
        
        if (showMessage) {
            const message = `
                <tr class="no-results-message">
                    <td colspan="100" style="text-align: center; padding: 40px; color: #666;">
                        <i class="fas fa-search" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <h3 style="margin: 0 0 8px 0; color: #333;">No products found</h3>
                        <p style="margin: 0; opacity: 0.7;">Try adjusting your search or filters</p>
                    </td>
                </tr>
            `;
            visibleTable.find('tbody').append(message);
        }
    }


      // Filter table rows based on search term and filters
    function filterProductTable() {
      const brand = $('#brandFilter').val();
      const status = $('#statusFilter').val();
      const visibleTable = $('.bowling-ball-info-container:not(.hidden), .bowling-bag-info-container:not(.hidden), .bowling-shoes-info-container:not(.hidden), .bowling-accesories-info-container:not(.hidden), .cleaning-supplies-info-container:not(.hidden)');
      
      let visibleRows = 0;
      
      visibleTable.find('tbody tr').each(function() {
          const $row = $(this);
          
          // Get brand from appropriate column based on table type
          let rowBrand = '';
          let rowStatus = '';
          
          const tableClass = $row.closest('[class*="-info-container"]').attr('class');
          
          if (tableClass.includes('bowling-ball')) {
              rowBrand = $row.find('td:nth-child(2)').text().trim();
              rowStatus = $row.find('.status-badge').text().toLowerCase().trim();
          } 
          else if (tableClass.includes('bowling-bag')) {
              rowBrand = $row.find('td:nth-child(2)').text().trim();
              rowStatus = $row.find('.status-badge').text().toLowerCase().trim();
          }
          else if (tableClass.includes('bowling-shoes')) {
              rowBrand = $row.find('td:nth-child(2)').text().trim();
              rowStatus = $row.find('.status-badge').text().toLowerCase().trim();
          }
          else if (tableClass.includes('bowling-accesories')) {
              rowBrand = $row.find('td:nth-child(2)').text().trim();
              rowStatus = $row.find('.status-badge').text().toLowerCase().trim();
          }
          else if (tableClass.includes('cleaning-supplies')) {
              rowBrand = $row.find('td:nth-child(2)').text().trim();
              rowStatus = $row.find('.status-badge').text().toLowerCase().trim();
          }
          
          const brandMatch = !brand || rowBrand === brand;
          const statusMatch = !status || getStatusMatch(rowStatus, status);
          
          const isVisible = brandMatch && statusMatch;
          $row.toggle(isVisible);
          if (isVisible) visibleRows++;
      });
    
      toggleNoResultsMessage(visibleTable, visibleRows === 0);
    }


      // Helper function to match status
    function getStatusMatch(rowStatus, selectedStatus) {
        const statusMap = {
            'active': 'in stock',
            'low': 'low stock',
            'out': 'out of stock'
        };
        
        return rowStatus === statusMap[selectedStatus];
    }

    function showEditTransactionModal(orderId) {
      const $row = $(`[data-id="${orderId}"]`).closest('tr');
      if ($row.length === 0) {
          console.error('Transaction not found with ID:', orderId);
          return;
      }
        
      // Populate transaction modal with data
      const cells = $row.find('td');
      $('#orderId').val(cells.eq(0).text());
      $('#customer').val(cells.eq(1).text());
      $('#shopBranch').val(cells.eq(2).text());
      $('#datePurchased').val(cells.eq(3).text());
      $('#currency').val(cells.eq(4).text());
      $('#total').val(cells.eq(5).text());
      $('#paymentMode').val(cells.eq(6).text());
      $('#deliveryMethod').val(cells.eq(7).text());
      $('#dateCompleted').val(cells.eq(8).text());
      
      // Get the current status from the badge text - FIXED: using transaction-status-badge
      const currentStatus = cells.eq(9).find('.transaction-status-badge').text().trim();
      $('#status').val(currentStatus);
      
      $('#transactionModal').fadeIn(300);
      $('body').css('overflow', 'hidden');
  }

  // Update handleTransactionFormSubmit to use transaction-status-badge
  function handleTransactionFormSubmit($form) {
      const orderId = $('#orderId').val();
      const newStatus = $('#status').val();
      
      // Update the table row with new status
      const $row = $(`[data-id="${orderId}"]`).closest('tr');
      const $statusCell = $row.find('td:nth-child(10)');
      const $dateCompletedCell = $row.find('td:nth-child(9)');
      
      // Status class mapping for the four statuses
      const statusClassMap = {
          'Pending': 'status-pending',
          'Processing': 'status-processing', 
          'Completed': 'status-completed',
          'Cancelled': 'status-cancelled'
      };
      
      // Update status badge - FIXED: using transaction-status-badge
      $statusCell.find('.transaction-status-badge')
          .removeClass('status-pending status-processing status-completed status-cancelled')
          .addClass(statusClassMap[newStatus])
          .text(newStatus);
      
      // Update date completed if status changes to Completed
      if (newStatus === 'Completed' && !$dateCompletedCell.text().trim()) {
          const today = new Date().toISOString().split('T')[0];
          $dateCompletedCell.text(today);
      }
      // Clear date completed if status changes from Completed to something else
      else if (newStatus !== 'Completed' && $dateCompletedCell.text().trim()) {
          $dateCompletedCell.text('');
      }
      
      closeModal('#transactionModal');
      alert(`Transaction ${orderId} status updated to ${newStatus}!`);
  }

  // Update the filterTransactionTable function to use transaction-status-badge
  function filterTransactionTable() {
      const status = $('#transactionStatusFilter').val();
      const payment = $('#paymentFilter').val();
      const delivery = $('#deliveryFilter').val();
      const visibleTable = $('.transactions-info-container:not(.hidden)');
      
      let visibleRows = 0;
      
      visibleTable.find('tbody tr').each(function() {
          const $row = $(this);
          const rowStatus = $row.find('td:nth-child(10)').text().trim(); // This gets the text content, not the badge class
          const rowPayment = $row.find('td:nth-child(7)').text().trim();
          const rowDelivery = $row.find('td:nth-child(8)').text().trim();
          
          const statusMatch = !status || rowStatus === status;
          const paymentMatch = !payment || rowPayment === payment;
          const deliveryMatch = !delivery || rowDelivery === delivery;
          
          const isVisible = statusMatch && paymentMatch && deliveryMatch;
          $row.toggle(isVisible);
          if (isVisible) visibleRows++;
      });
      
      toggleNoResultsMessage(visibleTable, visibleRows === 0);
  }


      // Auto-update status design based on stock
    function updateStockStatus() {
        $('tbody tr').each(function() {
            const $row = $(this);
            const stockCell = $row.find('td').filter(function() {
                const text = $(this).text().trim();
                return /^\d+$/.test(text);
            }).first();
            
            const stock = parseInt(stockCell.text()) || 0;
            const statusBadge = $row.find('.status-badge'); 
            
            if (!statusBadge.length) return;
            
            statusBadge.removeClass('status-active status-low-stock status-inactive');
            
            if (stock === 0) {
                statusBadge.addClass('status-inactive').text('Out of Stock');
            } else if (stock <= 5) {
                statusBadge.addClass('status-low-stock').text('Low Stock');
            } else {
                statusBadge.addClass('status-active').text('In Stock');
            }
        });
    }

    // Show modal for adding new product
    function showAddModal() {
        const config = categoryConfig[currentCategory];
        if (!config || !config.modalId) {
            console.error('No modal configured for category:', currentCategory);
            return;
        }
        
        // Reset form and show modal
        resetModalForm(config.modalId);
        $(config.modalId).fadeIn(300);
        $('body').css('overflow', 'hidden');
    }

    // Show modal for editing existing product
    function showEditModal(productId) {
        const config = categoryConfig[currentCategory];
        if (!config || !config.modalId) {
            console.error('No modal configured for category:', currentCategory);
            return;
        }
        
        // Find the product data from the table
        const $row = $(`[data-id="${productId}"]`).closest('tr');
        if ($row.length === 0) {
            console.error('Product not found with ID:', productId);
            return;
        }
        
        // Populate modal with existing data
        populateModalForm(config.modalId, $row, currentCategory);
        $(config.modalId).fadeIn(300);
        $('body').css('overflow', 'hidden');
    }

    // Reset modal form to empty state
    function resetModalForm(modalId) {
        const $form = $(modalId).find('form');
        $form[0].reset();
        
        // Update modal title for adding
        $(modalId).find('.modal-title').text($(modalId).find('.modal-title').text().replace('Edit', 'Add'));
        
        // Update submit button text
        const $submitBtn = $(modalId).find('button[type="submit"]');
        $submitBtn.text($submitBtn.text().replace('Update', 'Add'));
    }

        // Close modal function
    function closeModal(modalId) {
        $(modalId).fadeOut(300);
        $('body').css('overflow', 'auto');
    }

    // Initialize modal event handlers
    function initializeModalHandlers() {
        // Add product button
        $addProductBtn.off('click').on('click', showAddModal);
        
        // Edit buttons
        $('.edit-btn').off('click').on('click', function() {
            const productId = $(this).data('id');
            showEditModal(productId);
        });
        
        // Close buttons
        $('.close').off('click').on('click', function() {
            const modalId = '#' + $(this).closest('.modal').attr('id');
            closeModal(modalId);
        });
        
        // Cancel buttons
        $('[id$="CancelBtn"]').off('click').on('click', function() {
            const modalId = '#' + $(this).closest('.modal').attr('id');
            closeModal(modalId);
        });

        $('#cancelTransactionBtn').off('click').on('click', function() {
            closeModal('#transactionModal');
        });
        
        // Close modal when clicking outside
        $('.modal').off('click').on('click', function(e) {
            if (e.target === this) {
                closeModal('#' + $(this).attr('id'));
            }
        });

        $('.edit-transaction-btn').off('click').on('click', function() {
          const orderId = $(this).data('id');
          showEditTransactionModal(orderId);
        });
        
        // Form submissions (edit maybe)
        $('form').off('submit').on('submit', function(e) {
            e.preventDefault();
            handleFormSubmit($(this));
        });
    }

    // Reset filters and search
    function resetFilters() {
        $searchInput.val('');
        $brandFilter.val('');
        $statusFilter.val('');
        
        const visibleTable = $tables.filter(':not(.hidden)');
        visibleTable.find('tbody tr').show();
    }

    // Initialize everything when page loads
    function initializePage() {
        try {
            initializeTableFunctionality();
            initializeModalHandlers();
            updateStockStatus();
            
            // Set initial category
            switchCategory('bowling-balls');
            
            console.log('Product management system initialized successfully');
            
        } catch (error) {
            console.error('Error initializing page:', error);
        }
    }

    // Initialize the page
    initializePage();


    // change to php maybe
    // Populate modal form with existing data for editing
    function populateModalForm(modalId, $row, category) {
        const $form = $(modalId).find('form');
        
        switch(category) {
            case 'bowling-balls':
                populateBowlingBallForm($form, $row);
                break;
            case 'shoes':
                populateShoesForm($form, $row);
                break;
            case 'bags':
                populateBagForm($form, $row);
                break;
            case 'accessories':
                populateAccessoryForm($form, $row);
                break;
            case 'cleaning':
                populateCleaningForm($form, $row);
                break;
        }
        
        // Update modal title for editing
        $(modalId).find('.modal-title').text($(modalId).find('.modal-title').text().replace('Add', 'Edit'));
        
        // Update submit button text
        const $submitBtn = $(modalId).find('button[type="submit"]');
        $submitBtn.text($submitBtn.text().replace('Add', 'Update'));
    }

    // Add transaction form submission handler
    $('#transactionForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        handleTransactionFormSubmit($(this));
    });



      // Form population functions for each category
      function populateBowlingBallForm($form, $row) {
          const cells = $row.find('td');
          $form.find('#ballName').val(cells.eq(0).text());
          $form.find('#ballBrand').val(cells.eq(1).text());
          $form.find('#ballType').val(cells.eq(2).text());
          $form.find('#ballQuality').val(cells.eq(3).text());
          $form.find('#ballWeight').val(cells.eq(4).text().replace(' lbs', ''));
          $form.find('#coreName').val(cells.eq(5).text());
          $form.find('#coreType').val(cells.eq(6).text());
          $form.find('#rgValue').val(cells.eq(7).text());
          $form.find('#diffValue').val(cells.eq(8).text());
          $form.find('#intDiffValue').val(cells.eq(9).text());
          $form.find('#coverstockName').val(cells.eq(10).text());
          $form.find('#coverstockType').val(cells.eq(11).text());
          $form.find('#ballPrice').val(cells.eq(12).text().replace('₱', '').replace(',', ''));
          $form.find('#ballStock').val(cells.eq(13).text());
      }

      function populateShoesForm($form, $row) {
          const cells = $row.find('td');
          $form.find('#shoeName').val(cells.eq(0).text());
          $form.find('#shoeBrand').val(cells.eq(1).text());
          $form.find('#shoeColor').val(cells.eq(2).text());
          $form.find('#shoeSize').val(cells.eq(3).text());
          $form.find('#shoeGender').val(cells.eq(4).text());
          $form.find('#shoePrice').val(cells.eq(5).text().replace('₱', '').replace(',', ''));
          $form.find('#shoeStock').val(cells.eq(6).text());
      }

      function populateBagForm($form, $row) {
          const cells = $row.find('td');
          $form.find('#bagName').val(cells.eq(0).text());
          $form.find('#bagBrand').val(cells.eq(1).text());
          $form.find('#bagColor').val(cells.eq(2).text());
          $form.find('#bagType').val(cells.eq(3).text());
          $form.find('#bagSize').val(cells.eq(4).text());
          $form.find('#bagPrice').val(cells.eq(5).text().replace('₱', '').replace(',', ''));
          $form.find('#bagStock').val(cells.eq(6).text());
      }

      function populateAccessoryForm($form, $row) {
          const cells = $row.find('td');
          $form.find('#accessoryName').val(cells.eq(0).text());
          $form.find('#accessoryBrand').val(cells.eq(1).text());
          $form.find('#accessoryType').val(cells.eq(2).text());
          $form.find('#handedness').val(cells.eq(3).text());
          $form.find('#accessoryPrice').val(cells.eq(4).text().replace('₱', '').replace(',', ''));
          $form.find('#accessoryStock').val(cells.eq(5).text());
      }

      function populateCleaningForm($form, $row) {
          const cells = $row.find('td');
          $form.find('#supplyName').val(cells.eq(0).text());
          $form.find('#supplyBrand').val(cells.eq(1).text());
          $form.find('#supplyType').val(cells.eq(2).text());
          $form.find('#supplyPrice').val(cells.eq(3).text().replace('₱', '').replace(',', ''));
          $form.find('#supplyStock').val(cells.eq(4).text());
      }

          // Handle form submission  
      function handleFormSubmit($form) {
          const modalId = '#' + $form.closest('.modal').attr('id');
          const isEdit = $form.find('button[type="submit"]').text().includes('Update');
          
          // Here you would typically send data to your backend
          console.log('Form submitted:', {
              category: currentCategory,
              isEdit: isEdit,
              formData: new FormData($form[0])
          });
          
          // For demo purposes, just close the modal
          closeModal(modalId);
          
          // Show success message (you can implement this)
          alert(`${isEdit ? 'Product updated' : 'Product added'} successfully!`);
      }
  });
</script>


</body>
</html>