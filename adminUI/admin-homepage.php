<?php
require_once '../dependencies/config.php'; 
include('admin-header.html')
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Product Management</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link href="../css/adminCSS/admin-homepage.css" rel="stylesheet">
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
      <div class="nav-row">
        <div class="nav-item" data-category="currency">Currency</div>
        <div class="nav-item" data-category="branch">Branch</div>
        <div class="nav-item" data-category="address">Address</div>
        <div class="nav-item" data-category="services">Services</div>
        <div class="nav-item" data-category="users">Users</div>
      </div>
    </div>
    <?php
    $productcount = 0;
    $prodquery = "SELECT DISTINCT ProductID FROM product";
    $prodresult = $conn->query($prodquery);
    $productcount = $prodresult->num_rows;
    ?>
    <!-- Stats Overview -->
    <div id="stats-grid" class="stats-grid">
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
        <div class="stat-number"><?php echo $instockproducts; ?></div>
        <div class="stat-label">In Stock</div>
      </div>
      <?php
        $lowstockproducts = 0;
        $lowstockquery = "SELECT DISTINCT ProductID, SUM(quantity) FROM product
                          GROUP BY ProductID
                          HAVING SUM(quantity) < 10 AND SUM(quantity) >= 1";
        $lowstockresult = $conn->query($lowstockquery);
        $lowstockproducts = $lowstockresult->num_rows;
        ?>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-number"><?php echo $lowstockproducts; ?></div>
        <div class="stat-label">Low Stock</div>
      </div>
      <?php
        $nostockproducts = 0;
        $nostockquery = "SELECT DISTINCT ProductID, SUM(quantity) FROM product
                         GROUP BY ProductID
                         HAVING SUM(quantity) = 0";
        $nostockresult = $conn->query($nostockquery);
        $nostockproducts = $nostockresult->num_rows;
        ?>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-times-circle"></i>
        </div>
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
    <div class="bowling-ball-info-container hidden">
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
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $bbproductID;?>">
                  <i class="fas fa-trash"></i>
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
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $bgproductID;?>">
                  <i class="fas fa-trash"></i>
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
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $bsproductID;?>">
                  <i class="fas fa-trash"></i>
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
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $baproductID;?>">
                  <i class="fas fa-trash"></i>
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
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $csproductID;?>">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr><?php }?>
        </tbody>
      </table>
    </div>

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

    <div class="currency-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Currency Name</th>
            <th>Rate</th> 
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $curquery = "SELECT CurrencyID as currencyid, Currency_Name as curname, Currency_Rate currate FROM currency";
          $curresult = $conn->query($curquery);
          while($currow = $curresult->fetch_assoc()){
            $currencyid = $currow['currencyid'];
            $curname = $currow['curname'];
            $currate = $currow['currate'];
          ?>
          <tr data-id="<?php echo $currencyid;?>">
            <td><?php echo $curname;?></td>
            <td><?php echo $currate;?></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $currencyid;?>">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $currencyid;?>">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr><?php } ?>
        </tbody>
      </table>
    </div>

    <div class="branch-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Branch ID</th>
            <th>City</th> 
            <th>Street</th>
            <th>Zip Code</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $branchquery = "SELECT b.BranchID as branchId, a.City as city, a.Street as street, a.zip_code as zipcode
                          FROM branches b JOIN address a ON b.AddressID = a.AddressID";
          $branchresult = $conn->query($branchquery);
          while($branchrow = $branchresult->fetch_assoc()){
            $branchId = $branchrow['branchId'];
            $city = $branchrow['city'];
            $street = $branchrow['street'];
            $zipcode = $branchrow['zipcode'];
          ?>
          <tr>
            <td><?php echo $branchId;?></td>
            <td><?php echo $city;?></td>
            <td><?php echo $street;?></td>
            <td><?php echo $zipcode;?></td>
          </tr><?php } ?>
        </tbody>
      </table>
    </div>

    <div class="address-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Address ID</th>
            <th>City</th> 
            <th>Street</th>
            <th>Zip Code</th>
            <th>Action</th>
          </tr>
          <?php
          $addressquery = "SELECT AddressID as addressId, City as city, Street as street, zip_code as zipcode FROM address";
          $addressresult = $conn->query($addressquery);
          while($addressrow = $addressresult->fetch_assoc()){
            $addressId = $addressrow['addressId'];
            $city = $addressrow['city'];
            $street = $addressrow['street'];
            $zipcode = $addressrow['zipcode'];
          ?>
        </thead>
        <tbody>
          <tr data-id="<?php echo $addressId;?>">
            <td><?php echo $addressId;?></td>
            <td><?php echo $city;?></td>
            <td><?php echo $street;?></td>
            <td><?php echo $zipcode;?></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $addressId;?>">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $addressId;?>">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr><?php } ?>
        </tbody>
      </table>
    </div>

    <div class="services-info-container hidden">
      <table>
        <thead>
          <tr>
            <th>Service ID</th>
            <th>Type </th>
            <th>Price</th>
            <th>Staff</th>
            <th>Availability</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $servicequery = "SELECT se.ServiceID as serviceId, se.Type as servicetype, se.Price as serviceprice, CONCAT(u.FirstName, ' ', u.LastName) as staff, se.Availability as serviceavailability
                           FROM services se JOIN users u ON se.StaffID = u.UserID
                           WHERE u.Role = 'Staff'";
          $serviceresult = $conn->query($servicequery);
          while($servicerow = $serviceresult->fetch_assoc()){
            $serviceId = $servicerow['serviceId'];
            $servicetype = $servicerow['servicetype'];
            $serviceprice = $servicerow['serviceprice'];
            $staff = $servicerow['staff'];
            $serviceavailability = $servicerow['serviceavailability'];
          ?>
          <tr data-id="<?php echo $serviceId;?>">
            <td><?php echo $serviceId;?></td>
            <td><?php echo $servicetype;?></td>
            <td><?php echo $serviceprice;?></td>
            <td><?php echo $staff;?></td>
            <td><?php echo $serviceavailability;?></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $serviceId;?>">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr><?php } ?>
        </tbody>
      </table>
    </div>

    <div class="user-info-container hidden">
     <table>
      <thead>
        <tr>
          <th>User ID</th>
          <th>First Name</th> 
          <th>Last Name</th>
          <th>Contact No</th>
          <th>Email</th>
          <th>Role</th>
          <th>City</th> 
          <th>Street</th>
          <th>Zip Code</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $userquery = "SELECT u.UserID as userId, u.FirstName as firstname, u.LastName as lastname, u.MobileNumber as contactnumber, u.Email as email, u.Role as role, a.City as city, a.Street as street, a.zip_code as zipcode 
                     FROM users u JOIN address a ON u.AddressID = a.AddressID
                     ORDER BY u.UserID ASC";
        $userresult = $conn->query($userquery);
        while($userrow = $userresult->fetch_assoc()){
          $userId = $userrow['userId'];
          $firstname = $userrow['firstname'];
          $lastname = $userrow['lastname'];
          $contactnumber = $userrow['contactnumber'];
          $email = $userrow['email'];
          $role = $userrow['role'];
          $city = $userrow['city'];
          $street = $userrow['street'];
          $zipcode = $userrow['zipcode'];
        ?>
        <tr data-id="<?php echo $userId;?>">
          <td><?php echo $userId;?></td>
          <td><?php echo $firstname;?></td>
          <td><?php echo $lastname;?></td>
          <td><?php echo $contactnumber;?></td>
          <td><?php echo $email;?></td>
          <td><?php echo $role;?></td>
          <td><?php echo $city;?></td>
          <td><?php echo $street;?></td>
          <td><?php echo $zipcode;?></td>
          <td class="action-cell">
            <div class="action-buttons">
              <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $userId;?>">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $userId;?>">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr><?php } ?>
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
            <button type="submit" class="btn btn-primary" id="updateStatusBtn">Update Status</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- modal for adding and editing currency -->
  <div class="modal currency-modal" id="currencyModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add New Currency</h2>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <form id="currencyForm">
          <div class="form-grid">
            <input type="hidden" id="currencyID" name="currencyID" value="">
            <div class="form-group">
              <label for="currencyCode" class="required">Currency Code</label>
              <input type="text" id="currencyCode" name="currencyCode" placeholder="e.g., USD, EUR, JPY" maxlength="3" required>
            </div>
            <div class="form-group">
              <label for="exchangeRate" class="required">Rate</label>
              <input type="number" id="exchangeRate" name="exchangeRate" step="0.01" min="0" placeholder="0.00" required>
            </div>
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="currencyModalCancelBtn">Cancel</button>
            <button type="submit" name="insertedit_currency" class="btn btn-primary" id="currencyModalSubmitBtn">Add Currency</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- modal for adding and editing address -->
  <div class="modal address-modal" id="addressModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add New Address</h2>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <form id="addressForm">
          <div class="form-grid">
            <input type="hidden" id="addressID" name="addressID" value="">
            <div class="form-group">
              <label for="addressCity" class="required">City</label>
              <input type="text" id="addressCity" name="addressCity" placeholder="e.g., Manila, Quezon City" required>
            </div>
            <div class="form-group">
              <label for="addressStreet" class="required">Street</label>
              <input type="text" id="addressStreet" name="addressStreet" placeholder="e.g., Taft Ave, Main Street" required>
            </div>
            <div class="form-group">
              <label for="addressZipCode" class="required">Zip Code</label>
              <input type="text" id="addressZipCode" name="addressZipCode" placeholder="e.g., 1000" required>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="addressModalCancelBtn">Cancel</button>
            <button type="submit" class="btn btn-primary" id="addressModalSubmitBtn">Add Address</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- modal for adding and editing services -->
  <div class="modal services-modal" id="servicesModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add New Service</h2>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <form id="servicesForm">
          <div class="form-grid">
            <input type="hidden" id="serviceID" name="serviceID" value="">
            <div class="form-group">
              <label for="serviceName" class="required">Service ID</label>
              <input type="text" id="serviceName" name="serviceName" placeholder="e.g., Ball Drilling, Ball Polishing, Lane Maintenance" required>
            </div>
            <div class="form-group">
              <label for="serviceType" class="required">Service Type</label>
              <select id="serviceType" name="serviceType" required>
                <option value="">Select Type</option>
                <option value="Drilling">Drilling</option>
                <option value="Polishing">Polishing</option>
                <option value="Repair">Repair</option>
                
              </select>
            </div>
            <div class="form-group">
              <label for="servicePrice" class="required">Price (₱)</label>
              <input type="number" id="servicePrice" name="servicePrice" step="0.01" min="0" placeholder="0.00" required>
            </div>
            <div class="form-group">
              <label for="serviceStaff" class="required">Assigned Staff</label>
              <select id="serviceStaff" name="serviceStaff" required>
                <option value="">Select Staff</option>
                <option value="Made">Made</option>
                <option value="Juan">Juan</option>
                <option value="Maria">Maria</option>
                <option value="Pedro">Pedro</option>
              </select>
            </div>
            <div class="form-group">
              <label for="serviceAvailability" class="required">Availability</label>
              <select id="serviceAvailability" name="serviceAvailability" required>
                <option value="">Select Availability</option>
                <option value="Available">Available</option>
                <option value="Unavailable">Unavailable</option>
                <option value="Limited">Limited</option>
              </select>
            </div>
            <div class="form-group full-width">
              <label for="serviceDescription" class="required">Service Description</label>
              <textarea id="serviceDescription" name="serviceDescription" placeholder="Describe the service in detail, what it includes, requirements, etc..." required></textarea>
            </div>
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="servicesModalCancelBtn">Cancel</button>
            <button type="submit" class="btn btn-primary" id="servicesModalSubmitBtn">Add Service</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- modal for adding and editing users -->
  <div class="modal users-modal" id="usersModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add New User</h2>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <form id="usersForm">
          <div class="form-grid">
            <input type="hidden" id="userID" name="userID" value="">
            <!-- Personal Information -->
            <div class="form-group">
              <label for="userFirstName" class="required">First Name</label>
              <input type="text" id="userFirstName" name="userFirstName" placeholder="e.g., John" required>
            </div>
            <div class="form-group">
              <label for="userLastName" class="required">Last Name</label>
              <input type="text" id="userLastName" name="userLastName" placeholder="e.g., Smith" required>
            </div>
            <div class="form-group">
              <label for="userEmail" class="required">Email</label>
              <input type="email" id="userEmail" name="userEmail" placeholder="e.g., john.smith@email.com" required>
            </div>
            <div class="form-group">
              <label for="userPhone" class="required">Phone Number</label>
              <input type="tel" id="userPhone" name="userPhone" placeholder="e.g., +63 912 345 6789" required>
            </div>
            
            <!-- Account Information -->
            <div class="form-group">
              <label for="userRole" class="required">Role</label>
              <select id="userRole" name="userRole" required>
                <option value="">Select Role</option>
                <option value="Admin">Administrator</option>
                <option value="Staff">Staff</option>
                <option value="Manager">Manager</option>
                <option value="Customer">Customer</option>
              </select>
            </div>
            
            <!-- Address Information -->
            <div class="form-group">
              <label for="userCity" class="required">City</label>
              <input type="text" id="userCity" name="userCity" placeholder="e.g., Manila" required>
            </div>
            <div class="form-group">
              <label for="userStreet" class="required">Street</label>
              <input type="text" id="userStreet" name="userStreet" placeholder="e.g., 123 Main Street" required>
            </div>
            <div class="form-group">
              <label for="userZipCode" class="required">Zip Code</label>
              <input type="text" id="userZipCode" name="userZipCode" placeholder="e.g., 1000" required>
            </div>     
            <div class="form-group">
              <label for="userStatus" class="required">Status</label>
              <select id="userStatus" name="userStatus" required>
                <option value="">Select Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Suspended">Suspended</option>
              </select>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="usersModalCancelBtn">Cancel</button>
            <button type="submit" class="btn btn-primary" id="usersModalSubmitBtn">Add User</button>
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
      const $tables = $('.bowling-ball-info-container, .bowling-bag-info-container, .bowling-shoes-info-container, .bowling-accesories-info-container, .cleaning-supplies-info-container, .transactions-info-container, .currency-info-container, .branch-info-container, .address-info-container, .services-info-container, .user-info-container');
      const $pageTitle = $('.page-title');
      const $addProductBtn = $('#addProductBtn');
      const $searchInput = $('#searchInput');
      const $brandFilter = $('#brandFilter');
      const $statusFilter = $('#statusFilter');
      const $statsGrid = $('#stats-grid');
      const $filters = $('.filters');
      
      let currentCategory = 'bowling-balls';
      
      // Category configuration
      const categoryConfig = {
          'bowling-balls': {
              table: '.bowling-ball-info-container',
              title: 'Bowling Ball Inventory',
              buttonText: 'Add New Bowling Ball',
              modalId: '#bowlingBallModal',
              searchPlaceholder: 'Search bowling balls...',
              hasAddButton: true,
              hasStats: true,
              hasFilters: true,
              brands: ['Storm', 'Brunswick', 'Ebonite', 'Hammer', 'Roto Grip', 'Motiv', 'Track', '900 Global']
          },
          'shoes': {
              table: '.bowling-shoes-info-container',
              title: 'Bowling Shoes Inventory',
              buttonText: 'Add New Shoes',
              modalId: '#bowlingShoesModal',
              searchPlaceholder: 'Search shoes...',
              hasAddButton: true,
              hasStats: true,
              hasFilters: true,
              brands: ['Brunswick', 'Dexter', '3G', 'Storm']
          },
          'bags': {
              table: '.bowling-bag-info-container',
              title: 'Bowling Bags Inventory',
              buttonText: 'Add New Bag',
              modalId: '#bowlingBagModal',
              searchPlaceholder: 'Search bags...',
              hasAddButton: true,
              hasStats: true,
              hasFilters: true,
              brands: ['Storm', 'Brunswick', 'Motiv', 'KR', 'Vise', 'Dexter']
          },
          'accessories': {
              table: '.bowling-accesories-info-container',
              title: 'Bowling Accessories Inventory',
              buttonText: 'Add New Accessory',
              modalId: '#bowlingAccessoriesModal',
              searchPlaceholder: 'Search accessories...',
              hasAddButton: true,
              hasStats: true,
              hasFilters: true,
              brands: ['Vise', 'Turbo', 'Genesis', 'Storm']
          },
          'cleaning': {
              table: '.cleaning-supplies-info-container',
              title: 'Cleaning Supplies Inventory',
              buttonText: 'Add Cleaning Product',
              modalId: '#cleaningSuppliesModal',
              searchPlaceholder: 'Search cleaning supplies...',
              hasAddButton: true,
              hasStats: true,
              hasFilters: true,
              brands: ['Storm', 'Brunswick', 'Tac Up', 'That Purple Stuff']
          },
          'transaction': {
              table: '.transactions-info-container',
              title: 'Transaction Management',
              modalId: '#transactionModal',
              searchPlaceholder: 'Search transactions...',
              hasAddButton: false,
              hasStats: false,
              hasFilters: true
          },
          'currency': {
              table: '.currency-info-container',
              title: 'Currency Management',
              buttonText: 'Add New Currency',
              modalId: '#currencyModal',
              searchPlaceholder: 'Search currencies...',
              hasAddButton: true,
              hasStats: false,
              hasFilters: false
          },
          'branch': {
              table: '.branch-info-container',
              title: 'Branch Management',
              searchPlaceholder: 'Search branches...',
              hasAddButton: false,
              hasStats: false,
              hasFilters: false
          },
          'address': {
              table: '.address-info-container',
              title: 'Address Management',
              buttonText: 'Add New Address',
              modalId: '#addressModal',
              searchPlaceholder: 'Search addresses...',
              hasAddButton: true,
              hasStats: false,
              hasFilters: false
          },
          'services': {
              table: '.services-info-container',
              title: 'Services Management',
              buttonText: 'Add New Service',
              modalId: '#servicesModal',
              searchPlaceholder: 'Search services...',
              hasAddButton: false,
              hasStats: false,
              hasFilters: false
          },
          'users': {
              table: '.user-info-container',
              title: 'User Management',
              buttonText: 'Add New User',
              modalId: '#usersModal',
              searchPlaceholder: 'Search users...',
              hasAddButton: true,
              hasStats: false,
              hasFilters: false
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
              console.log('Switching to category:', category);
              currentCategory = category;
              
              const config = categoryConfig[category];
              if (!config) {
                  console.error('Unknown category:', category);
                  return;
              }
              
              // Hide all tables
              $tables.addClass('hidden');
              
              // Show/hide stats grid
              if(config.hasStats === false) {
                  $statsGrid.hide();
              } else {
                  $statsGrid.show();
              }
              
              // Show/hide filters section
              if(config.hasFilters === false) {
                  $filters.hide();
              } else {
                  $filters.show();
              }
              
              // Update page header
              updatePageHeader(config);
              
              // Show/hide add button
              if (config.hasAddButton === false) {
                  $addProductBtn.hide();
              } else {
                  $addProductBtn.show();
                  $addProductBtn.html('<i class="fas fa-plus"></i> ' + config.buttonText);
              }
  
              // Update search placeholder
              $searchInput.attr('placeholder', config.searchPlaceholder || 'Search products...');
          
              // Show the selected category table
              if (config.table) {
                  $(config.table).removeClass('hidden');
              }

              // Update filters for category
              updateFiltersForCategory(category);
              
              // Reset search and filters
              resetFilters();
              
              // Initialize table functionality
              initializeTableFunctionality();
              
          } catch (error) {
              console.error('Error switching category:', error);
          }
      }

      // Update page header based on category config
      function updatePageHeader(config) {
          $pageTitle.text(config.title);
      }

      // Update filters based on category
      function updateFiltersForCategory(category) {
          // Hide all filters first
          $('.filter-select').hide();
          
          const config = categoryConfig[category];
          if (!config.hasFilters) return;
          
          // Show relevant filters based on category
          if (category === 'transaction') {
              $('#transactionStatusFilter').show();
              $('#paymentFilter').show();
              $('#deliveryFilter').show();
          } else if (config.hasFilters) {
              $('#brandFilter').show();
              $('#statusFilter').show();
              
              // Update brand filter options for product categories
              if (config.brands) {
                  $('#brandFilter').empty().append('<option value="">All Brands</option>');
                  config.brands.forEach(brand => {
                      $('#brandFilter').append(`<option value="${brand}">${brand}</option>`);
                  });
              }
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
              if (!categoryConfig[currentCategory].hasFilters || currentCategory === 'transaction') return;
              filterProductTable();
          });

          // Filter functionality for transactions
          $('#transactionStatusFilter, #paymentFilter, #deliveryFilter').off('change').on('change', function() {
              if (currentCategory !== 'transaction') return;
              filterTransactionTable();
          });
      }

      function getVisibleTable() {
          const config = categoryConfig[currentCategory];
          return $(config.table);
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
                          <h3 style="margin: 0 0 8px 0; color: #333;">No items found</h3>
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
          const searchTerm = $('#searchInput').val().toLowerCase();
          const visibleTable = getVisibleTable();
          
          let visibleRows = 0;
          
          visibleTable.find('tbody tr').each(function() {
              const $row = $(this);
              const rowText = $row.text().toLowerCase();
              
              // Get brand from appropriate column (usually 2nd column)
              const rowBrand = $row.find('td:nth-child(2)').text().trim();
              
              // Get status from status badge
              const rowStatus = $row.find('.status-badge, .tstatus-badge').text().toLowerCase().trim();
              
              const brandMatch = !brand || rowBrand === brand;
              const statusMatch = !status || getStatusMatch(rowStatus, status);
              const searchMatch = !searchTerm || rowText.includes(searchTerm);
              
              const isVisible = brandMatch && statusMatch && searchMatch;
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

      function filterTransactionTable() {
          const status = $('#transactionStatusFilter').val();
          const payment = $('#paymentFilter').val();
          const delivery = $('#deliveryFilter').val();
          const searchTerm = $('#searchInput').val().toLowerCase();
          const visibleTable = getVisibleTable();
          
          let visibleRows = 0;
          
          visibleTable.find('tbody tr').each(function() {
              const $row = $(this);
              const rowText = $row.text().toLowerCase();
              const rowStatus = $row.find('td:nth-child(10)').text().trim();
              const rowPayment = $row.find('td:nth-child(7)').text().trim();
              const rowDelivery = $row.find('td:nth-child(8)').text().trim();
              
              const statusMatch = !status || rowStatus === status;
              const paymentMatch = !payment || rowPayment === payment;
              const deliveryMatch = !delivery || rowDelivery === delivery;
              const searchMatch = !searchTerm || rowText.includes(searchTerm);
              
              const isVisible = statusMatch && paymentMatch && deliveryMatch && searchMatch;
              $row.toggle(isVisible);
              if (isVisible) visibleRows++;
          });
          
          toggleNoResultsMessage(visibleTable, visibleRows === 0);
      }

      function showEditTransactionModal(orderId) {
          const $row = $(`.edit-transaction-btn[data-id="${orderId}"]`).closest('tr');
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
          
          // Get the current status from the badge text
          const currentStatus = cells.eq(9).find('.transaction-status-badge').text().trim();
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

      // Auto-update status design based on stock
      function updateStockStatus() {
          $('tbody tr').each(function() {
              const $row = $(this);
              const stockCell = $row.find('td').filter(function() {
                  const text = $(this).text().trim();
                  return /^\d+$/.test(text);
              }).first();
              
              const stock = parseInt(stockCell.text()) || 0;
              const statusBadge = $row.find('.status-badge, .tstatus-badge'); 
              
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

      // Show modal for adding new item
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

      // Show modal for editing existing item
      function showEditModal(itemId) {
          const config = categoryConfig[currentCategory];
          if (!config || !config.modalId) {
              console.error('No modal configured for category:', currentCategory);
              return;
          }
          
          // Find the item data from the VISIBLE table only (avoid matching items in other tables)
          const visibleTable = getVisibleTable();
          let $row = visibleTable.find(`tbody tr[data-id="${itemId}"]`).first();

          // Fallback: try to find an edit button inside the visible table
          if ($row.length === 0) {
            $row = visibleTable.find(`.edit-btn[data-id="${itemId}"]`).closest('tr').first();
          }

          // Final fallback: global lookup (backwards compatibility)
          if ($row.length === 0) {
            $row = $(`.edit-btn[data-id="${itemId}"]`).closest('tr').first();
          }

          if ($row.length === 0) {
            console.error('Item not found with ID:', itemId);
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
          if ($submitBtn.text().includes('Update')) {
              $submitBtn.text($submitBtn.text().replace('Update', 'Add'));
          }
      }

      // Close modal function
      function closeModal(modalId) {
          $(modalId).fadeOut(300);
          $('body').css('overflow', 'auto');
      }

      // Initialize modal event handlers
      function initializeModalHandlers() {
          // Add item button
          $addProductBtn.off('click').on('click', showAddModal);
          
          // Edit buttons
          $(document).on('click', '.edit-btn', function() {
              const itemId = $(this).data('id');
              showEditModal(itemId);
          });
          
          // Transaction edit buttons
          $(document).on('click', '.edit-transaction-btn', function() {
              const orderId = $(this).data('id');
              showEditTransactionModal(orderId);
          });
          
          // Close buttons
          $('.close').off('click').on('click', function() {
              const modalId = '#' + $(this).closest('.modal').attr('id');
              closeModal(modalId);
          });
          
          // Cancel buttons
          $(document).on('click', '[id$="CancelBtn"]', function() {
              const modalId = '#' + $(this).closest('.modal').attr('id');
              closeModal(modalId);
          });
          
          // Close modal when clicking outside
          $('.modal').off('click').on('click', function(e) {
              if (e.target === this) {
                  closeModal('#' + $(this).attr('id'));
              }
          });

          // Form submissions
          $('form').off('submit').on('submit', function(e) {
              e.preventDefault();
              handleFormSubmit($(this));
          });
      }

      // Reset filters and search
      function resetFilters() {
          $searchInput.val('');
          $('.filter-select').val('');
          const visibleTable = getVisibleTable();
          visibleTable.find('tbody tr').show();
          toggleNoResultsMessage(visibleTable, false);
      }

      // Delete confirmation modal
      const deleteModalHTML = `
      <div class="modal delete-confirm-modal" id="deleteConfirmModal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
          <div class="modal-header">
            <h2 class="modal-title">Confirm Delete</h2>
            <span class="close">&times;</span>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this item? This action cannot be undone.</p>
            <div class="delete-item-info">
              <strong>Item:</strong> <span id="deleteItemName">-</span><br>
              <strong>ID:</strong> <span id="deleteItemId">-</span>
            </div>
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="cancelDeleteBtn">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
          </div>
        </div>
      </div>
      `;

      // Add the modal to the page if not exists
      if ($('#deleteConfirmModal').length === 0) {
          $('body').append(deleteModalHTML);
      }

      // Initialize delete functionality
      function initializeDeleteHandlers() {
            // Delete buttons
            $(document).on('click', '.delete-btn', function() {
              const itemId = $(this).data('id');
              const $row = $(this).closest('tr');
              const itemName = $row.find('td:first').text();
              // Use the currently visible category so the delete modal knows the type
              showDeleteModal(itemId, itemName, currentCategory || 'item');
            });
          
          // Delete modal handlers
          $('#cancelDeleteBtn').off('click').on('click', function() {
              closeModal('#deleteConfirmModal');
          });
          
          $('.delete-confirm-modal .close').off('click').on('click', function() {
              closeModal('#deleteConfirmModal');
          });
          
          $('#confirmDeleteBtn').off('click').on('click', function() {
              const itemId = $(this).data('item-id');
              const itemType = $(this).data('item-type');
              deleteItem(itemId, itemType);
          });
          
          // Close modal when clicking outside
          $('#deleteConfirmModal').off('click').on('click', function(e) {
              if (e.target === this) {
                  closeModal('#deleteConfirmModal');
              }
          });
      }

      // Show delete confirmation modal
      function showDeleteModal(itemId, itemName, itemType) {
          $('#deleteItemId').text(itemId);
          $('#deleteItemName').text(itemName);
          $('#confirmDeleteBtn')
              .data('item-id', itemId)
              .data('item-type', itemType);
          
          $('#deleteConfirmModal').fadeIn(300);
          $('body').css('overflow', 'hidden');
      }

      // Delete item function
      function deleteItem(itemId, itemType) {
          console.log(`Deleting ${itemType} with ID:`, itemId);
          
          // Determine endpoint and post flag based on category
          let endpoint = '';
          let postFlag = '';
          let idFieldName = '';
          let isProduct = false;
          
          switch(itemType) {
              case 'currency':
                  endpoint = '../delete_currency.php';
                  postFlag = 'delete_currency';
                  idFieldName = 'currencyID';
                  break;
              case 'address':
                  endpoint = '../delete_address.php';
                  postFlag = 'delete_address';
                  idFieldName = 'addressID';
                  break;
              case 'services':
                  endpoint = '../delete_services.php';
                  postFlag = 'delete_service';
                  idFieldName = 'serviceID';
                  break;
              case 'users':
                  endpoint = '../delete_users.php';
                  postFlag = 'delete_user';
                  idFieldName = 'userID';
                  break;
              case 'bowling-balls':
                  endpoint = '../delete_bowlingballs.php';
                  postFlag = 'delete_bowlingball';
                  idFieldName = 'productID';
                  isProduct = true;
                  break;
              case 'bags':
                  endpoint = '../delete_bowlingbags.php';
                  postFlag = 'delete_bowlingbag';
                  idFieldName = 'productID';
                  isProduct = true;
                  break;
              case 'shoes':
                  endpoint = '../delete_bowlingshoes.php';
                  postFlag = 'delete_bowlingshoes';
                  idFieldName = 'productID';
                  isProduct = true;
                  break;
              case 'accessories':
                  endpoint = '../delete_bowlingaccessories.php';
                  postFlag = 'delete_bowlingaccessories';
                  idFieldName = 'productID';
                  isProduct = true;
                  break;
              case 'cleaning':
                  endpoint = '../delete_cleaningsupplies.php';
                  postFlag = 'delete_cleaningsupplies';
                  idFieldName = 'productID';
                  isProduct = true;
                  break;
              default:
                  showNotification('Error', 'Delete not yet implemented for this category.', 'error');
                  closeModal('#deleteConfirmModal');
                  return;
          }
          
          // Find the row and extract branch ID if it's a product
          const visibleTable = getVisibleTable();
          let $row = visibleTable.find(`tbody tr[data-id="${itemId}"]`).first();
          
          // Fallback: find via delete button
          if ($row.length === 0) {
              $row = visibleTable.find(`.delete-btn[data-id="${itemId}"]`).closest('tr').first();
          }
          
          // Prepare delete request
          const formData = new FormData();
          formData.append(postFlag, '1');
          formData.append(idFieldName, itemId);
          
          // Add branch ID for products if available
          if (isProduct && $row.length > 0) {
              const branchId = $row.data('branch-id');
              if (branchId) {
                  formData.append('branchID', branchId);
              }
          }
          
          // Send AJAX request to delete backend
          $.ajax({
              url: endpoint,
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              dataType: 'json',
              success: function(response) {
                  console.log('Delete response:', response);
                  if (response.success) {
                      if ($row.length > 0) {
                          $row.fadeOut(300, function() {
                              $(this).remove();
                              
                              // Update stats if it's a product
                              if (categoryConfig[currentCategory] && categoryConfig[currentCategory].hasStats) {
                                  updateStatsAfterDelete();
                              }
                              
                              // Show success message
                              showNotification('Success', response.message, 'success');
                          });
                      } else {
                          showNotification('Success', response.message, 'success');
                      }
                  } else {
                      showNotification('Error', response.message, 'error');
                  }
                  closeModal('#deleteConfirmModal');
              },
              error: function(xhr, status, error) {
                  console.error('Delete AJAX Error:', {status: xhr.status, error: error, response: xhr.responseText});
                  let errorMsg = 'An error occurred while deleting the item.';
                  
                  try {
                      const errorResponse = JSON.parse(xhr.responseText);
                      errorMsg = errorResponse.message || errorMsg;
                  } catch(e) {
                      // Response is not JSON
                  }
                  
                  showNotification('Error', errorMsg, 'error');
                  closeModal('#deleteConfirmModal');
              }
          });
      }

      // Update stats after deletion
      function updateStatsAfterDelete() {
          console.log('Stats should be updated after deletion');
          // Implement your stats update logic here
      }

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
              case 'currency':
                  populateCurrencyForm($form, $row);
                  break;
              case 'address':
                  populateAddressForm($form, $row);
                  break;
              case 'services':
                  populateServicesForm($form, $row);
                  break;
              case 'users':
                  populateUsersForm($form, $row);
                  break;
          }
          
          // Update modal title for editing
          $(modalId).find('.modal-title').text($(modalId).find('.modal-title').text().replace('Add', 'Edit'));
          
          // Update submit button text
          const $submitBtn = $(modalId).find('button[type="submit"]');
          if ($submitBtn.text().includes('Add')) {
              $submitBtn.text($submitBtn.text().replace('Add', 'Update'));
          }
      }

      // Form population functions
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
          $form.find('#shoeSize').val(cells.eq(2).text());
          $form.find('#shoeGender').val(cells.eq(3).text());
          $form.find('#shoePrice').val(cells.eq(4).text().replace('₱', '').replace(',', ''));
          $form.find('#shoeStock').val(cells.eq(5).text());
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

      function populateCurrencyForm($form, $row) {
          const cells = $row.find('td');
          const currencyId = $row.data('id');
          $form.find('#currencyID').val(currencyId);
          $form.find('#currencyCode').val(cells.eq(0).text());
          $form.find('#exchangeRate').val(cells.eq(1).text());
      }

      function populateAddressForm($form, $row) {
          const cells = $row.find('td');
          const addressId = $row.data('id');
          $form.find('#addressID').val(addressId);
          // Table columns: 0=AddressID, 1=City, 2=Street, 3=ZipCode
          $form.find('#addressCity').val(cells.eq(1).text());
          $form.find('#addressStreet').val(cells.eq(2).text());
          $form.find('#addressZipCode').val(cells.eq(3).text());
      }

      function populateServicesForm($form, $row) {
          const cells = $row.find('td');
          const serviceId = $row.data('id');
          $form.find('#serviceID').val(serviceId);
          // Table columns: 0=ServiceID, 1=Type, 2=Price, 3=Staff, 4=Availability
          // Modal fields: #serviceName holds Service ID (labelled Service ID), #serviceType is Type
          $form.find('#serviceName').val(cells.eq(0).text());
          $form.find('#serviceType').val(cells.eq(1).text());
          $form.find('#servicePrice').val(cells.eq(2).text());
          $form.find('#serviceStaff').val(cells.eq(3).text());
          $form.find('#serviceAvailability').val(cells.eq(4).text());
      }

      function populateUsersForm($form, $row) {
          const cells = $row.find('td');
          const userId = $row.data('id');
          $form.find('#userID').val(userId);
          // Table columns: 0=UserID, 1=FirstName, 2=LastName, 3=ContactNo, 4=Email, 5=Role, 6=City, 7=Street, 8=ZipCode
          $form.find('#userFirstName').val(cells.eq(1).text());
          $form.find('#userLastName').val(cells.eq(2).text());
          $form.find('#userPhone').val(cells.eq(3).text());
          $form.find('#userEmail').val(cells.eq(4).text());
          $form.find('#userRole').val(cells.eq(5).text());
          $form.find('#userCity').val(cells.eq(6).text());
          $form.find('#userStreet').val(cells.eq(7).text());
          $form.find('#userZipCode').val(cells.eq(8).text());
          // No status column in table; leave #userStatus as default or current selection
      }

      // Helper function to show notifications
      function showNotification(title, message, type) {
          // Simple implementation using alert; can be replaced with a toast library
          const prefix = type === 'error' ? '❌' : '✅';
          alert(`${prefix} ${title}\n\n${message}`);
          console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
      }

      // Helper function to add a new row to the table
      function addTableRow(data, category) {
          let newRow = '';
          const visibleTable = getVisibleTable();
          
          switch(category) {
              case 'currency':
                  newRow = `
                      <tr data-id="${data.currencyId}">
                          <td>${data.currencyCode}</td>
                          <td>${data.exchangeRate}</td>
                          <td class="action-cell"><div class="action-buttons"><button class="btn btn-warning btn-sm edit-btn" data-id="${data.currencyId}"><i class="fas fa-edit"></i></button></div></td>
                      </tr>
                  `;
                  break;
              case 'address':
                  newRow = `
                      <tr data-id="${data.addressId}">
                          <td>${data.city}</td>
                          <td>${data.street}</td>
                          <td>${data.zipCode}</td>
                          <td class="action-cell"><div class="action-buttons"><button class="btn btn-warning btn-sm edit-btn" data-id="${data.addressId}"><i class="fas fa-edit"></i></button></div></td>
                      </tr>
                  `;
                  break;
              case 'services':
                  newRow = `
                      <tr data-id="${data.serviceId}">
                          <td>${data.serviceType}</td>
                          <td>${data.price}</td>
                          <td>${data.availability}</td>
                          <td class="action-cell"><div class="action-buttons"><button class="btn btn-warning btn-sm edit-btn" data-id="${data.serviceId}"><i class="fas fa-edit"></i></button></div></td>
                      </tr>
                  `;
                  break;
              case 'users':
                  newRow = `
                      <tr data-id="${data.userId}">
                          <td>${data.firstName}</td>
                          <td>${data.lastName}</td>
                          <td>${data.phone}</td>
                          <td>${data.email}</td>
                          <td>${data.role}</td>
                          <td>${data.city}</td>
                          <td>${data.street}</td>
                          <td>${data.zipCode}</td>
                          <td>${data.status}</td>
                          <td class="action-cell"><div class="action-buttons"><button class="btn btn-warning btn-sm edit-btn" data-id="${data.userId}"><i class="fas fa-edit"></i></button></div></td>
                      </tr>
                  `;
                  break;
          }
          
          if(newRow) {
              visibleTable.find('tbody').append(newRow);
              initializeModalHandlers();
          }
      }
      
      // Helper function to update an existing row in the table
      function updateTableRow(data, category) {
          let itemId;
          switch(category) {
              case 'currency':
                  itemId = data.currencyId;
                  break;
              case 'address':
                  itemId = data.addressId;
                  break;
              case 'services':
                  itemId = data.serviceId;
                  break;
              case 'users':
                  itemId = data.userId;
                  break;
          }
          
          const $row = $(`[data-id="${itemId}"]`).closest('tr');
          
          if($row.length === 0) {
              console.warn('Row not found for item ID:', itemId);
              return;
          }
          
          switch(category) {
              case 'currency':
                  $row.find('td:eq(0)').text(data.currencyCode);
                  $row.find('td:eq(1)').text(data.exchangeRate);
                  break;
              case 'address':
                  $row.find('td:eq(0)').text(data.city);
                  $row.find('td:eq(1)').text(data.street);
                  $row.find('td:eq(2)').text(data.zipCode);
                  break;
              case 'services':
                  $row.find('td:eq(0)').text(data.serviceType);
                  $row.find('td:eq(1)').text(data.price);
                  $row.find('td:eq(2)').text(data.availability);
                  break;
              case 'users':
                  $row.find('td:eq(0)').text(data.firstName);
                  $row.find('td:eq(1)').text(data.lastName);
                  $row.find('td:eq(2)').text(data.phone);
                  $row.find('td:eq(3)').text(data.email);
                  $row.find('td:eq(4)').text(data.role);
                  $row.find('td:eq(5)').text(data.city);
                  $row.find('td:eq(6)').text(data.street);
                  $row.find('td:eq(7)').text(data.zipCode);
                  $row.find('td:eq(8)').text(data.status);
                  break;
          }
      }

      // Handle form submission  
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
        case 'currency':
          backendFlag = 'insertedit_currency';
          targetURL = isEdit ? '../update_currency.php' : '../insert_currency.php';
          break;
        case 'address':
          backendFlag = 'insertedit_address';
          targetURL = isEdit ? '../update_address.php' : '../insert_address.php';
          break;
        case 'services':
          backendFlag = 'insertedit_service';
          targetURL = isEdit ? '../update_services.php' : '../insert_services.php';
          break;
        case 'users':
          backendFlag = 'insertedit_user'; 
          targetURL = isEdit ? '../update_users.php' : '../insert_users.php';
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

      // Add transaction form submission handler
      $('#transactionForm').off('submit').on('submit', function(e) {
          e.preventDefault();
          handleTransactionFormSubmit($(this));
      });

      // Initialize everything when page loads
      function initializePage() {
          try {
              initializeModalHandlers();
              initializeDeleteHandlers();
              initializeTableFunctionality();
              updateStockStatus();
              
              // Set initial category
              switchCategory('bowling-balls');
              
              console.log('Management system initialized successfully');
              
          } catch (error) {
              console.error('Error initializing page:', error);
          }
      }

      // Initialize the page
      initializePage();
  });
</script>

</body>
</html>