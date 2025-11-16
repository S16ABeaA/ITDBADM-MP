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
    
    <?php
    $productcount = 0;
    $prodquery = "SELECT DISTINCT ProductID FROM product";
    $prodresult = $conn->query($prodquery);
    $productcount = $prodresult->num_rows;
    ?>
    <!-- Stats Overview -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-bowling-ball"></i>
        </div>
        <div class="stat-number"><?php echo $productcount; ?></div>
        <div class="stat-label">Total Products</div>
      </div>
      <?php
      $instockproducts = 0;
      $instockquery = "SELECT DISTINCT ProductID, SUM(quantity) FROM product
                       GROUP BY ProductID
                       HAVING SUM(quantity) >=10"; 
      $instockresult = $conn->query($instockquery);
      $instockproducts = $instockresult->num_rows;
      ?>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-number"><?php echo $instockproducts;?></div>
        <div class="stat-label">In Stock</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <?php
        $lowstockproducts = 0;
        $lowstockquery = "SELECT DISTINCT ProductID, SUM(quantity) FROM product
                          GROUP BY ProductID
                          HAVING SUM(quantity) < 10 AND SUM(quantity) >= 1";
        $lowstockresult = $conn->query($lowstockquery);
        $lowstockproducts = $lowstockresult->num_rows;
        ?>
        <div class="stat-number"><?php echo $lowstockproducts; ?></div>
        <div class="stat-label">Low Stock</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <?php
        $nostockproducts = 0;
        $nostockquery = "SELECT DISTINCT ProductID, SUM(quantity) FROM product
                         GROUP BY ProductID
                         HAVING SUM(quantity) = 0";
        $nostockresult = $conn->query($nostockquery);
        $nostockproducts = $nostockresult->num_rows;
        ?>
        <div class="stat-number"><?php echo $nostockproducts; ?></div>
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
          <tr data-id="<?php echo $orderId;?>">
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
          $query = "SELECT DISTINCT bb.ProductID as bbproductid, bb.Name as bbname, br.Name as brandname, bb.Type as bbtype, bb.Quality as bbquality, bb.weight as weight, bb.CoreName as corename, bb.CoreType as coretype, bb.RG as rg, bb.DIFF as diff, bb.INTDIFF as intdiff, bb.Coverstock as coverstock, bb.CoverstockType as coverstocktype, pr.Price as price, SUM(pr.quantity) as quantity
                    FROM bowlingball bb JOIN product pr ON bb.ProductID = pr.ProductID
                    JOIN brand br ON pr.BrandID = br.BrandID
                    GROUP BY bb.Name, br.Name, bb.Type, bb.Quality, bb.weight, bb.CoreName, bb.CoreType, bb.RG, bb.DIFF, bb.INTDIFF, bb.Coverstock, bb.CoverstockType, pr.Price, pr.ProductID";
          $result = $conn->query($query);
          while ($row = $result->fetch_assoc()){
            $bbproductID = $row['bbproductid'];
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
  
          <tr data-id="<?php echo $bbproductID;?>">
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
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $bbproductID;?>">
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
          $bgquery = "SELECT bg.ProductID as bgproductid, bg.Name as bgname, br.Name as brandname, bg.color as color, bg.Size as size, bg.Type as bgtype, pr.Price as price, SUM(pr.quantity) as quantity
                      FROM bowlingbag bg JOIN product pr ON bg.ProductID = pr.ProductID
                      JOIN brand br ON pr.BrandID = br.BrandID
                      GROUP BY bg.Name, br.Name, bg.color, bg.Size, bg.Type, pr.Price, pr.ProductID";
          $bgresult = $conn->query($bgquery);
          while($bgrow = $bgresult->fetch_assoc()){
            $bgproductID = $bgrow['bgproductid'];
            $bgname = $bgrow['bgname'];
            $brname = $bgrow['brandname'];
            $bgcolor = $bgrow['color'];
            $bgsize = $bgrow['size'];
            $bgtype = $bgrow['bgtype'];
            $bgprice = $bgrow['price'];
            $bgquantity = $bgrow['quantity'];
          ?>
          <tr data-id="<?php echo $bgproductID;?>">
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
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $bgproductID;?>">
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
          $bsquery = "SELECT bs.ProductID as bsproductid, bs.Name as bsname, br.Name as brandname, bs.Size as size, bs.sex as sex, pr.Price as price, SUM(pr.quantity) as quantity
                      FROM bowlingshoes bs JOIN product pr ON bs.ProductID = pr.ProductID
                      JOIN brand br ON pr.BrandID = br.BrandID
                      GROUP BY bs.Name, br.Name, bs.Size, bs.sex, pr.Price, pr.ProductID";   
          $bsresult = $conn->query($bsquery);
          while($bsrow = $bsresult->fetch_assoc()){
            $bsproductID = $bsrow['bsproductid'];
            $bsname = $bsrow['bsname'];
            $brname = $bsrow['brandname'];
            $bssize = $bsrow['size'];
            $bssex = $bsrow['sex'];
            $bsprice = $bsrow['price'];
            $bsquantity = $bsrow['quantity'];
          ?> 
           <tr data-id="<?php echo $bsproductID;?>">
            <td><?php echo $bsname;?></td>
            <td><?php echo $brname;?></td>
            <td><?php echo $bssize;?></td>
            <td><?php echo $bssex;?></td>
            <td><?php echo $bsprice;?></td>
            <td><?php echo $bsquantity;?></td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $bsproductID;?>">
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
          $baquery = "SELECT ba.ProductID as baproductid, ba.Name as baname, br.Name as brandname, ba.Type as batype, ba.Handedness as bahandedness, pr.Price as price, SUM(pr.quantity) as quantity
                      FROM bowlingaccessories ba JOIN product pr ON ba.ProductID = pr.ProductID
                      JOIN brand br ON pr.BrandID = br.BrandID
                      GROUP BY ba.Name, br.Name, ba.Type, ba.Handedness, pr.Price, pr.ProductID";

          $baresult = $conn->query($baquery);
          while($barow = $baresult->fetch_assoc()){
            $baproductID = $barow['baproductid'];
            $baname = $barow['baname'];
            $brname = $barow['brandname'];
            $batype = $barow['batype'];
            $bahandedness = $barow['bahandedness'];
            $baprice = $barow['price'];
            $baquantity = $barow['quantity'];
          ?>
           <tr data-id="<?php echo $baproductID;?>">
            <td><?php echo $baname;?></td>
            <td><?php echo $brname;?></td>
            <td><?php echo $batype;?></td>
            <td><?php echo $bahandedness;?></td>
            <td><?php echo $baprice;?></td>
            <td><?php echo $baquantity;?></td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $baproductID;?>">
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
          $csquery = "SELECT DISTINCT cs.ProductID as csproductid, cs.Name as csname, br.Name as brandname, cs.type as cstype, pr.Price as price, SUM(pr.quantity) as quantity
                      FROM cleaningsupplies cs JOIN product pr ON cs.ProductID = pr.ProductID
                      JOIN brand br ON pr.BrandID = br.BrandID
                      GROUP BY cs.Name, br.Name, cs.type, pr.Price, pr.ProductID";
          $csresult = $conn->query($csquery);
          while($csrow = $csresult->fetch_assoc()){
            $csproductID = $csrow['csproductid'];
            $csname = $csrow['csname'];
            $brname = $csrow['brandname'];
            $cstype = $csrow['cstype'];
            $csprice = $csrow['price'];
            $csquantity = $csrow['quantity'];
          ?>
           <tr data-id="<?php echo $csproductID;?>">
            <td><?php echo $csname;?></td>
            <td><?php echo $brname;?></td>
            <td><?php echo $cstype;?></td>
            <td><?php echo $csprice;?></td>
            <td><?php echo $csquantity;?></td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $csproductID;?>">
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
          <?php 
          $orderquery = "SELECT o.OrderID as orderId, u.FirstName as firstname, u.LastName as lastname, CONCAT(u.FirstName, ' ', u.LastName) as customer, a.City as shopbranch, o.DatePurchased as datepurchased, cur.Currency_Name as currency, o.Total as total, o.PaymentMode as paymentmode, o.DeliveryMethod as deliverymethod, o.DateCompleted as datecompleted, o.Status as status
                         FROM orders o JOIN users u ON o.CustomerID = u.UserID
                         JOIN branches b ON o.BranchID = b.BranchID
                         JOIN address a ON b.AddressID = a.AddressID
                         JOIN currency cur ON o.CurrencyID = cur.CurrencyID";
          $orderresult = $conn->query($orderquery);
          while($orderrow = $orderresult->fetch_assoc()){ 
            $orderId = $orderrow['orderId'];
            $customer = $orderrow['customer'];
            $shopbranch = $orderrow['shopbranch'];
            $datepurchased = $orderrow['datepurchased'];
            $currency = $orderrow['currency'];
            $total = $orderrow['total'];
            $paymentmode = $orderrow['paymentmode'];
            $deliverymethod = $orderrow['deliverymethod'];
            $datecompleted = $orderrow['datecompleted'];
            $status = $orderrow['status'];
            ?>
          <tr data-id="<?php echo $orderId;?>">
            <td><?php echo $orderId;?></td>
            <td><?php echo $customer;?></td>
            <td><?php echo $shopbranch;?></td>
            <td><?php echo $datepurchased;?></td>
            <td><?php echo $currency;?></td>
            <td><?php echo $total;?></td>
            <td><?php echo $paymentmode;?></td>
            <td><?php echo $deliverymethod;?></td>
            <td><?php echo $datecompleted;?></td>
            <td><span class="transaction-status-badge status-completed"><?php echo $status;?></span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-transaction-btn" data-id="<?php echo $orderId;?>">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr><?php }?>
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
          <input type="hidden" id="ballID" name="ballID" value="<?php echo $bbproductID;?>">
          <div class="form-group">
            <label for="ballName" class="required">Ball Name</label>
            <input type="text" id="ballName" name="ballName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="ballBrand" class="required">Brand</label>
            <select id="ballBrand" name="ballBrand" required>
              <option value="">Select Brand</option>
              <option value="1">Storm</option>
              <option value="6">Brunswick</option>
              <option value="8">Ebonite</option>
              <option value="3">Hammer</option>
              <option value="9">Roto Grip</option>
              <option value="2">Motiv</option>
              <option value="4">Track</option>
              <option value="7">900 Global</option>
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
              <option value="Symetric">Symmetric</option>
              <option value="Asymetric">Asymmetric</option>
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
          <button type="submit" name="insertedit_bb" class="btn btn-primary" id="submitBtn">Add Bowling Ball</button>
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
          <input type="hidden" id="shoeID" name="shoeID" value="<?php echo $bsproductID;?>">
          <div class="form-group">
            <label for="shoeName" class="required">Shoe Name</label>
            <input type="text" id="shoeName" name="shoeName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="shoeBrand" class="required">Brand</label>
            <select id="shoeBrand" name="shoeBrand" required>
              <option value="">Select Brand</option>
              <option value="1">Storm</option>
              <option value="6">Brunswick</option>
              <option value="8">Ebonite</option>
              <option value="3">Hammer</option>
              <option value="9">Roto Grip</option>
              <option value="2">Motiv</option>
              <option value="4">Track</option>
              <option value="7">900 Global</option>
            </select>
          </div>

          <div class="form-group">
            <label for="shoeSize" class="required">Size</label>
            <input type="number" id="shoeSize" name="shoeSize" required>
          </div>

          <div class="form-group">
            <label for="shoeGender" class="required">Gender</label>
            <select id="shoeGender" name="shoeGender" required>
              <option value="">Select Sex</option>
              <option value="M">Male</option>
              <option value="F">Female</option>
            </select>
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
          <button type="submit" name="insertedit_bs" class="btn btn-primary" id="shoeModalSubmitBtn">Add Bowling Shoe</button>
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
          <input type="hidden" id="bagID" name="bagID" value="<?php echo $bgproductID;?>">
          <div class="form-group">
            <label for="bagName" class="required">Bag Name</label>
            <input type="text" id="bagName" name="bagName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="bagBrand" class="required">Brand</label>
            <select id="bagBrand" name="bagBrand" required>
              <option value="">Select Brand</option>
              <option value="1">Storm</option>
              <option value="6">Brunswick</option>
              <option value="8">Ebonite</option>
              <option value="3">Hammer</option>
              <option value="9">Roto Grip</option>
              <option value="2">Motiv</option>
              <option value="4">Track</option>
              <option value="7">900 Global</option>
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
          <button type="submit" name="insertedit_bg" class="btn btn-primary" id="bagModalSubmitBtn">Add Bowling Bag</button>
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
          <input type="hidden" id="accessoryID" name="accessoryID" value="<?php echo $baproductID;?>">
          <div class="form-group">
            <label for="accessoryName" class="required">Accessory Name</label>
            <input type="text" id="accessoryName" name="accessoryName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="accessoryBrand" class="required">Brand</label>
            <select id="accessoryBrand" name="accessoryBrand" required>
              <option value="">Select Brand</option>
              <option value="1">Storm</option>
              <option value="6">Brunswick</option>
              <option value="8">Ebonite</option>
              <option value="3">Hammer</option>
              <option value="9">Roto Grip</option>
              <option value="2">Motiv</option>
              <option value="4">Track</option>
              <option value="7">900 Global</option>
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
          <button type="submit" name="insertedit_ba" class="btn btn-primary" id="accessoryModalSubmitBtn">Add Bowling Accessory</button>
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
          <input type="hidden" id="cleaningID" name="cleaningID" value="<?php echo $csproductID;?>">
          <div class="form-group">
            <label for="supplyName" class="required">Supply Name</label>
            <input type="text" id="supplyName" name="supplyName" placeholder="e.g., Phantom, Hy-Road, Game Breaker" required>
          </div>
          <div class="form-group">
            <label for="supplyBrand" class="required">Brand</label>
            <select id="supplyBrand" name="supplyBrand" required>
              <option value="">Select Brand</option>
              <option value="1">Storm</option>
              <option value="6">Brunswick</option>
              <option value="8">Ebonite</option>
              <option value="3">Hammer</option>
              <option value="9">Roto Grip</option>
              <option value="2">Motiv</option>
              <option value="4">Track</option>
              <option value="7">900 Global</option>
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
          <button type="submit" name="insertedit_cs" class="btn btn-primary" id="supplyModalSubmitBtn">Add Cleaning Supply</button>
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
          <button type="submit" name="update_order" class="btn btn-primary" id="updateStatusBtn">Update Status</button>
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
      
      /* ===== Image Upload Handlers =====
      // Handle image upload for all product types
      $('#ballImageUpload').on('click', function() {
          $('#ballImage').click();
      });
      
      $('#ballImage').on('change', function() {
          const file = this.files[0];
          if(file) {
              const fileName = file.name;
              $('#ballImageUpload').find('p').text(fileName);
              $('#ballImageUpload').addClass('has-file');
              console.log('Image selected:', fileName);
          }
      });
      
      // Handle drag and drop for image upload
      $('#ballImageUpload').on('dragover', function(e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).addClass('dragover');
      });
      
      $('#ballImageUpload').on('dragleave', function(e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).removeClass('dragover');
      });
      
      $('#ballImageUpload').on('drop', function(e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).removeClass('dragover');
          
          const files = e.originalEvent.dataTransfer.files;
          if(files.length > 0) {
              $('#ballImage')[0].files = files;
              const fileName = files[0].name;
              $('#ballImageUpload').find('p').text(fileName);
              $('#ballImageUpload').addClass('has-file');
              console.log('Image dropped:', fileName);
          }
      });*/
      
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
      // Search ONLY in the transactions table to avoid matching product rows with same data-id
      const $transactionsTable = $('.transactions-info-container:not(.hidden)');
      const $row = $transactionsTable.find(`tbody tr[data-id="${orderId}"]`).first();
      
      if ($row.length === 0) {
          console.error('Transaction not found with ID:', orderId);
          console.log('Attempted lookup with orderId:', orderId);
          return;
      }
        
      // Populate transaction modal with data
      // Get cells directly from this row only (direct children)
      const $cells = $row.find('> td');
      
      // Log for debugging
      console.log('Populating transaction modal for order:', orderId);
      console.log('Total cells found in row:', $cells.length);
      console.log('Full row HTML:', $row.html());
      
      // Map cells to form fields (matches table columns)
      // Column 0: Order ID, Column 1: Customer, Column 2: Shop Branch, Column 3: Date Purchased
      // Column 4: Currency, Column 5: Total, Column 6: Payment Mode, Column 7: Delivery Method
      // Column 8: Date Completed, Column 9: Status, Column 10: Actions
      $('#orderId').val($cells.eq(0).text().trim());
      $('#customer').val($cells.eq(1).text().trim());
      $('#shopBranch').val($cells.eq(2).text().trim());
      $('#datePurchased').val($cells.eq(3).text().trim());
      $('#currency').val($cells.eq(4).text().trim());
      $('#total').val($cells.eq(5).text().trim());
      $('#paymentMode').val($cells.eq(6).text().trim());
      $('#deliveryMethod').val($cells.eq(7).text().trim());
      $('#dateCompleted').val($cells.eq(8).text().trim());
      
      // Get the current status from the badge text (column 9)
      // The status is inside a span with class transaction-status-badge
      const $statusCell = $cells.eq(9);
      const currentStatus = $statusCell.find('.transaction-status-badge').text().trim();
      console.log('Status cell HTML:', $statusCell.html());
      console.log('Current status value:', currentStatus);
      $('#status').val(currentStatus);
      
      $('#transactionModal').fadeIn(300);
      $('body').css('overflow', 'hidden');
  }

    // Transaction form submit — sends AJAX request to update order in database
    function handleTransactionFormSubmit($form) {
    const $submitBtn = $form.find('button[type="submit"]');
    const originalBtnText = $submitBtn.text();
    $submitBtn.prop('disabled', true).text('Processing...');

    // Read and normalize form values
    let orderId = $form.find('#orderId').val();
    orderId = orderId ? orderId.toString().trim() : '';
    const newStatus = $form.find('#status').val();
    const dateCompleted = $form.find('#dateCompleted').val();

      // Validate
      if(!orderId || !newStatus) {
        showNotification('Error', 'Order ID and Status are required.', 'error');
        $submitBtn.prop('disabled', false).text(originalBtnText);
        return;
      }

      // Find the corresponding table row (before sending request) - search ONLY in transactions table
      const $transactionsTable = $('.transactions-info-container:not(.hidden)');
      let $row = $transactionsTable.find(`tbody tr[data-id="${orderId}"]`).first();
      // Fallback: match by first <td> text if data-id lookup fails (handles older markup)
      if($row.length === 0) {
        $row = $transactionsTable.find('tbody tr').filter(function() {
          return $(this).find('td').eq(0).text().trim() === orderId;
        }).first();
      }

      if($row.length === 0) {
        showNotification('Error', 'Transaction not found in the table.', 'error');
        $submitBtn.prop('disabled', false).text(originalBtnText);
        return;
      }

      // Prepare AJAX payload
      const formData = new FormData();
      formData.append('update_order', '1');
      formData.append('orderId', orderId);
      formData.append('status', newStatus);
      if(newStatus === 'Completed' && !dateCompleted) {
        const today = new Date().toISOString().split('T')[0];
        formData.append('dateCompleted', today);
      } else {
        formData.append('dateCompleted', dateCompleted || '');
      }

      // Send AJAX request
      $.ajax({
        url: '../update_orders.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response){
          console.log('Server response:', response);
          if(response.success){
            // Update table row with new status
            updateTransactionTableRow(orderId, newStatus, dateCompleted);
            closeModal('#transactionModal');
            showNotification('Success', response.message, 'success');
          } else {
            showNotification('Error', response.message, 'error');
          }
        },
        error: function(xhr, status, error){
          console.error('AJAX Error:', {status: xhr.status, error: error, response: xhr.responseText});
          let errorMsg = 'An error occurred while updating the transaction.';
          try {
            const errorResponse = JSON.parse(xhr.responseText);
            errorMsg = errorResponse.message || errorMsg;
          } catch(e) {
            // Response is not JSON
          }
          showNotification('Error', errorMsg, 'error');
        },
        complete: function(){
          $submitBtn.prop('disabled', false).text(originalBtnText);
        }
      });
    }

    // Helper function to update transaction row in table
    function updateTransactionTableRow(orderId, newStatus, dateCompleted) {
      // Normalize orderId and search ONLY in transactions table
      orderId = orderId ? orderId.toString().trim() : '';
      const $transactionsTable = $('.transactions-info-container:not(.hidden)');
      let $row = $transactionsTable.find(`tbody tr[data-id="${orderId}"]`).first();
      
      if($row.length === 0) {
        $row = $transactionsTable.find('tbody tr').filter(function() {
          return $(this).find('td').eq(0).text().trim() === orderId;
        }).first();
      }

      if($row.length === 0) {
        console.warn('Transaction row not found for ID:', orderId);
        return;
      }

      // Status class mapping
      const statusClassMap = {
        'Pending': 'status-pending',
        'Processing': 'status-processing',
        'Completed': 'status-completed',
        'Cancelled': 'status-cancelled'
      };

      // Update status badge (column 10)
      const $statusCell = $row.find('td:nth-child(10)');
      const $badge = $statusCell.find('.transaction-status-badge');
      if(!$badge.length) {
        $statusCell.html(`<span class="transaction-status-badge"></span>`);
      }
      $statusCell.find('.transaction-status-badge')
        .removeClass('status-pending status-processing status-completed status-cancelled')
        .addClass(statusClassMap[newStatus] || '')
        .text(newStatus);

      // Update date completed (column 9) if status is Completed
      if(newStatus === 'Completed' && dateCompleted) {
        $row.find('td:nth-child(9)').text(dateCompleted);
      } else if(newStatus !== 'Completed') {
        $row.find('td:nth-child(9)').text('');
      }
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
        
        /*// Reset image upload display
        const $imageUpload = $(modalId).find('.image-upload');
        $imageUpload.find('p').text('Click to upload product images');
        $imageUpload.removeClass('has-file');*/
        
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

    function handleFormSubmit($form) {
        const modalId = '#' + $form.closest('.modal').attr('id');
        const isEdit = $form.find('button[type="submit"]').text().includes('Update');
        const category = currentCategory;
        const formData = new FormData($form[0]);
        
      // Determine backend flag and endpoint per category
      let backendFlag = '';
      let targetURL = '';
      switch (category) {
        case 'bowling-balls':
          backendFlag = 'insertedit_bb';
          targetURL = isEdit ? '../update_bowlingball.php' : '../insert_bowlingball.php';
          break;
        case 'shoes':
          backendFlag = 'insertedit_bs';
          targetURL = isEdit ? '../update_bowlingshoes.php' : '../insert_bowlingshoes.php';
          break;
        case 'bags':
          backendFlag = 'insertedit_bg';
          targetURL = isEdit ? '../update_bowlingbags.php' : '../insert_bowlingbags.php';
          break;
        case 'accessories':
          backendFlag = 'insertedit_ba';
          targetURL = isEdit ? '../update_bowlingaccessories.php' : '../insert_bowlingaccessories.php';
          break;
        case 'cleaning':
          backendFlag = 'insertedit_cs';
          targetURL = isEdit ? '../update_cleaningsupplies.php' : '../insert_cleaningsupplies.php';
          break;
        default:
          console.error('Unknown category:', category);
          return;
      }

      // Append proper flag expected by backend
      formData.append(backendFlag, '1');
        
        // Show loading state
        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: targetURL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response){
                console.log('Server response:', response);
                
                if(response.success){
                    // Close modal
                    closeModal(modalId);
                    
                    // Update DOM - add or update table row
                    if(isEdit){
                        updateTableRow(response.data, category);
                        showNotification('Success', response.message, 'success');
                    } else {
                        addTableRow(response.data, category);
                        showNotification('Success', response.message, 'success');
                    }
                    
                    // Reset form
                    $form[0].reset();
                } else {
                    showNotification('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error){
                console.error('AJAX Error:', {status: xhr.status, error: error, response: xhr.responseText});
                let errorMsg = 'An error occurred while processing your request.';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMsg = errorResponse.message || errorMsg;
                } catch(e) {
                    // Response is not JSON
                }
                
                showNotification('Error', errorMsg, 'error');
            },
            complete: function(){
                // Restore button state
                $submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    }
    
    // Helper function to add a new row to the table
    function addTableRow(data, category) {
      let newRow = '';
      const visibleTable = getVisibleTable();
        
      switch(category) {
        case 'bowling-balls':
          newRow = `
            <tr data-id="${data.productId}">
              <td>${data.ballName}</td>
              <td>${data.ballBrand}</td>
              <td>${data.ballType}</td>
              <td>${data.ballQuality}</td>
              <td>${data.ballWeight}</td>
              <td>${data.coreName}</td>
              <td>${data.coreType}</td>
              <td>${data.rgValue}</td>
              <td>${data.diffValue}</td>
              <td>${data.intDiffValue}</td>
              <td>${data.coverstockName}</td>
              <td>${data.coverstockType}</td>
              <td>${data.ballPrice}</td>
              <td>${data.ballStock}</td>
              <td>
                <span class="status-badge status-active">In Stock</span>
              </td>
              <td>
                <button class="edit-btn" data-id="${data.productId}"><i class="fas fa-edit"></i></button>
                <button class="delete-btn" data-id="${data.productId}"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          `;
          break;
        case 'shoes':
          newRow = `
            <tr data-id="${data.productId}">
              <td>${data.shoeName}</td>
              <td>${data.shoeBrand}</td>
              <td>${data.shoeSize}</td>
              <td>${data.shoeGender}</td>
              <td>${data.shoePrice}</td>
              <td>${data.shoeStock}</td>
              <td><span class="status-badge status-active">In Stock</span></td>
              <td class="action-cell"><div class="action-buttons"><button class="btn btn-warning btn-sm edit-btn" data-id="${data.productId}"><i class="fas fa-edit"></i></button></div></td>
            </tr>
          `;
          break;
        case 'bags':
          newRow = `
            <tr data-id="${data.productId}">
              <td>${data.bagName}</td>
              <td>${data.bagBrand}</td>
              <td>${data.bagColor}</td>
              <td>${data.bagType}</td>
              <td>${data.bagSize}</td>
              <td>${data.bagPrice}</td>
              <td>${data.bagStock}</td>
              <td><span class="status-badge status-active">In Stock</span></td>
              <td class="action-cell"><div class="action-buttons"><button class="btn btn-warning btn-sm edit-btn" data-id="${data.productId}"><i class="fas fa-edit"></i></button></div></td>
            </tr>
          `;
          break;
        case 'accessories':
          newRow = `
            <tr data-id="${data.productId}">
              <td>${data.accessoryName}</td>
              <td>${data.accessoryBrand}</td>
              <td>${data.accessoryType}</td>
              <td>${data.handedness}</td>
              <td>${data.accessoryPrice}</td>
              <td>${data.accessoryStock}</td>
              <td><span class="status-badge status-active">In Stock</span></td>
              <td class="action-cell"><div class="action-buttons"><button class="btn btn-warning btn-sm edit-btn" data-id="${data.productId}"><i class="fas fa-edit"></i></button></div></td>
            </tr>
          `;
          break;
        case 'cleaning':
          newRow = `
            <tr data-id="${data.productId}">
              <td>${data.supplyName}</td>
              <td>${data.supplyBrand}</td>
              <td>${data.supplyType}</td>
              <td>${data.supplyPrice}</td>
              <td>${data.supplyStock}</td>
              <td><span class="status-badge status-active">In Stock</span></td>
              <td class="action-cell"><div class="action-buttons"><button class="btn btn-warning btn-sm edit-btn" data-id="${data.productId}"><i class="fas fa-edit"></i></button></div></td>
            </tr>
          `;
          break;
        // Add other categories as needed
      }
        
      if(newRow) {
        visibleTable.find('tbody').append(newRow);
        initializeTableFunctionality();
        initializeModalHandlers();
      }
    }
    
    // Helper function to update an existing row in the table
    function updateTableRow(data, category) {
      const $row = $(`[data-id="${data.productId}"]`).closest('tr');
        
      if($row.length === 0) {
        console.warn('Row not found for product ID:', data.productId);
        return;
      }
        
      switch(category) {
        case 'bowling-balls':
          $row.find('td:eq(0)').text(data.ballName);
          $row.find('td:eq(1)').text(data.ballBrand);
          $row.find('td:eq(2)').text(data.ballType);
          $row.find('td:eq(3)').text(data.ballQuality);
          $row.find('td:eq(4)').text(data.ballWeight);
          $row.find('td:eq(5)').text(data.coreName);
          $row.find('td:eq(6)').text(data.coreType);
          $row.find('td:eq(7)').text(data.rgValue);
          $row.find('td:eq(8)').text(data.diffValue);
          $row.find('td:eq(9)').text(data.intDiffValue);
          $row.find('td:eq(10)').text(data.coverstockName);
          $row.find('td:eq(11)').text(data.coverstockType);
          $row.find('td:eq(12)').text(data.ballPrice);
          $row.find('td:eq(13)').text(data.ballStock);
          break;
        case 'shoes':
          $row.find('td:eq(0)').text(data.shoeName);
          $row.find('td:eq(1)').text(data.shoeBrand);
          $row.find('td:eq(2)').text(data.shoeSize);
          $row.find('td:eq(3)').text(data.shoeGender);
          $row.find('td:eq(4)').text(data.shoePrice);
          $row.find('td:eq(5)').text(data.shoeStock);
          break;
        case 'bags':
          $row.find('td:eq(0)').text(data.bagName);
          $row.find('td:eq(1)').text(data.bagBrand);
          $row.find('td:eq(2)').text(data.bagColor);
          $row.find('td:eq(3)').text(data.bagType);
          $row.find('td:eq(4)').text(data.bagSize);
          $row.find('td:eq(5)').text(data.bagPrice);
          $row.find('td:eq(6)').text(data.bagStock);
          break;
        case 'accessories':
          $row.find('td:eq(0)').text(data.accessoryName);
          $row.find('td:eq(1)').text(data.accessoryBrand);
          $row.find('td:eq(2)').text(data.accessoryType);
          $row.find('td:eq(3)').text(data.handedness);
          $row.find('td:eq(4)').text(data.accessoryPrice);
          $row.find('td:eq(5)').text(data.accessoryStock);
          break;
        case 'cleaning':
          $row.find('td:eq(0)').text(data.supplyName);
          $row.find('td:eq(1)').text(data.supplyBrand);
          $row.find('td:eq(2)').text(data.supplyType);
          $row.find('td:eq(3)').text(data.supplyPrice);
          $row.find('td:eq(4)').text(data.supplyStock);
          break;
        // Add other categories as needed
      }
        
      updateStockStatus();
    }
    
    // Helper function to show notifications (toast/alert)
    function showNotification(title, message, type) {
        // Simple implementation using alert; can be replaced with a toast library
        const prefix = type === 'error' ? '❌' : '✅';
        alert(`${prefix} ${title}\n\n${message}`);
        console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
    }
  });
</script>
</body>
</html>