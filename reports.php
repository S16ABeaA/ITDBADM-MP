<?php
require_once 'dependencies/config.php';
require_once 'dependencies/session.php';
// Get branch ID from session (set by header via set_branch.php)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$branchId = $_SESSION['staff_branch_id'] ?? null;
$branchFilterSQL = '';
if ($branchId) {
  $branchFilterSQL = ' AND BranchID = ' . intval($branchId);
}

// Get appropriate database connection
$role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'staff';
$conn = getDBConnection($role);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports - Bowling Management System</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://unpkg.com/@fortawesome/fontawesome-free@6.7.2/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="../css/adminCSS/admin-header.css" rel="stylesheet">
  <link href="../css/adminCSS/reports.css" rel="stylesheet">
  <style>
    .reports-container {
      padding: 20px;
      max-width: 1400px;
      margin: 0 auto;
    }

    .reports-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 2px solid #e0e0e0;
    }

    .reports-title {
      font-size: 2.5rem;
      color: #2c3e50;
      margin: 0;
    }

    .date-range-selector {
      display: flex;
      gap: 15px;
      align-items: center;
    }

    .date-input {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }

    .generate-report-btn {
      background: #3498db;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }

    .generate-report-btn:hover {
      background: #2980b9;
    }

    .reports-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .report-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-left: 4px solid #3498db;
    }

    .report-card.sales {
      border-left-color: #2ecc71;
    }

    .report-card.inventory {
      border-left-color: #e74c3c;
    }

    .report-card.avg-order {
      border-left-color: #9b59b6;
    }

    .report-card.financial {
      border-left-color: #f39c12;
    }

    .report-card-header {
      display: flex;
      justify-content: between;
      align-items: center;
      margin-bottom: 15px;
      gap:10px;
    }

    .report-card-title {
      font-size: 1.2rem;
      color: #2c3e50;
      margin: 0;
    }

    .report-card-value {
      font-size: 2rem;
      font-weight: bold;
      color: #2c3e50;
      margin: 10px 0;
    }

    .report-card-change {
      font-size: 0.9rem;
      color: #7f8c8d;
    }

    .change-positive {
      color: #27ae60;
    }

    .change-negative {
      color: #e74c3c;
    }

    .charts-container {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      margin-bottom: 30px;
    }

    .chart-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .chart-title {
      font-size: 1.3rem;
      color: #2c3e50;
      margin-bottom: 15px;
      text-align: center;
    }

    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
    }

    .detailed-reports {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .report-table th,
    .report-table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ecf0f1;
    }

    .report-table th {
      background: #f8f9fa;
      font-weight: 600;
      color: #2c3e50;
    }

    .report-table tr:hover {
      background: #f8f9fa;
    }

    .export-buttons {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }

    .export-btn {
      padding: 8px 16px;
      border: 1px solid #ddd;
      background: white;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      transition: all 0.3s;
    }

    .export-btn:hover {
      background: #f8f9fa;
    }

    .export-btn.pdf {
      border-color: #e74c3c;
      color: #e74c3c;
    }

    .export-btn.excel {
      border-color: #27ae60;
      color: #27ae60;
    }

    .export-btn.csv {
      border-color: #3498db;
      color: #3498db;
    }

    .report-filters {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .filter-select {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      background: white;
      min-width: 150px;
    }

    .quick-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 25px;
    }

    .quick-stat {
      text-align: center;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 6px;
    }

    .quick-stat-value {
      font-size: 1.5rem;
      font-weight: bold;
      color: #2c3e50;
      margin: 5px 0;
    }

    .quick-stat-label {
      font-size: 0.9rem;
      color: #7f8c8d;
    }

    .no-data {
      text-align: center;
      padding: 40px;
      color: #7f8c8d;
      font-style: italic;
    }
  </style>
</head>
<body>
  <?php include('admin-header-fragment.html'); ?>
  
  <div class="content-section">
    <div class="reports-container">
      <!-- Reports Header -->
      <div class="reports-header">
        <h1 class="reports-title">Analytics & Reports</h1>
      </div>
     
      <!-- Key Metrics Cards -->
     <div class="reports-grid">
        <?php
          $totalrevenue = "SELECT SUM(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m') THEN Total END) AS revenue_this_month,
                                  SUM(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m') THEN Total END) AS revenue_last_month
                            FROM orders WHERE 1=1" . $branchFilterSQL;
          $revenueresult = $conn->query($totalrevenue);
          $revenuedata = $revenueresult->fetch_assoc();
          $thismonthrevenue = $revenuedata['revenue_this_month'];
          $lastmonthrevenue = $revenuedata['revenue_last_month'];
          if ($lastmonthrevenue > 0) {
            $revenueGrowth = (($thismonthrevenue - $lastmonthrevenue) / $lastmonthrevenue) * 100;
          } else {
            $revenueGrowth = $thismonthrevenue > 0 ? 100 : 0; 
          }
          $revenueGrowthClass = $revenueGrowth >= 0 ? 'change-positive' : 'change-negative';
          $revenueSign = $revenueGrowthClass > 0 ? '+' : "";
        ?>

        <div class="report-card sales">
          <div class="report-card-header">
            <h3 class="report-card-title">Total Revenue</h3>
            <i class="fa-solid fa-money-bill" style="color: #2ecc71; font-size: 1.5rem;"></i>
          </div>
          <div class="report-card-value"><?php echo number_format($thismonthrevenue, 2); ?></div>
          <div class="report-card-change <?php echo $revenueGrowthClass?>"><?php echo $revenueSign; echo number_format($revenueGrowth, 1); ?>% from last month
          </div>
        </div>

        <?php
          $ordercomp = "SELECT SUM(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m') THEN 1 ELSE 0 END) AS this_month,
                        SUM(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m')
                        THEN 1 ELSE 0 END) AS last_month, COUNT(*) as total_orders
                        FROM orders WHERE 1=1" . $branchFilterSQL;
          $orderresult = $conn->query($ordercomp);
          $orderdata = $orderresult->fetch_assoc();
          $totalorders = $orderdata['total_orders'];
          $thismonthorders = $orderdata['this_month'];
          $lastmonthorders = $orderdata['last_month'];
          $growth = 0;
          if ($lastmonthorders > 0) {
            $growth = (($thismonthorders - $lastmonthorders) / $lastmonthorders) * 100;
          } else $growth = $thismonthorders > 0 ? 100 : 0;
          $orderGrowthClass = $growth >= 0 ? 'change-positive' : 'change-negative';
          $orderSign = $orderGrowthClass > 0 ? '+' : "";
        ?>

        <div class="report-card total-order">
          <div class="report-card-header">
            <h3 class="report-card-title">Total Orders</h3>
            <i class="fas fa-shopping-cart" style="color: #3498db; font-size: 1.5rem;"></i>
          </div>
          <div class="report-card-value"><?php echo $totalorders; ?></div>
          <div class="report-card-change <?php echo $orderGrowthClass ?>"> 
            <?php echo $orderSign; echo number_format($growth, 1); ?>% from last month
          </div>
        </div>


        <?php
          $avgsalesmonth = "SELECT AVG(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m') THEN Total END) AS avg_this_month,
                                  AVG(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m') THEN Total END) AS avg_last_month
                            FROM orders WHERE 1=1" . $branchFilterSQL;
          $avgsalesresult = $conn->query($avgsalesmonth);
          $avgsalesdata = $avgsalesresult->fetch_assoc();
          $avgthismonth = $avgsalesdata['avg_this_month'];
          $avglastmonth = $avgsalesdata['avg_last_month'];
          if ($avglastmonth > 0) {
              $avgGrowth = (($avgthismonth - $avglastmonth) / $avglastmonth) * 100;
          } 
          else {
              $avgGrowth = $avgthismonth > 0 ? 100 : 0;
          }
          $avgGrowthClass = $avgGrowth >= 0 ? 'change-positive' : 'change-negative';
          $avgSign = $avgGrowth > 0 ? '+' : "";
        ?>
      

        <div class="report-card avg-order">
          <div class="report-card-header">
            <h3 class="report-card-title">Avg. Order Value</h3>
            <i class="fas fa-chart-bar" style="color: #9b59b6; font-size: 1.5rem;"></i>
          </div>
          <div class="report-card-value">₱<?php echo number_format($avgthismonth, 2); ?></div>
          <div class="report-card-change <?php echo $avgGrowthClass?>"><?php echo $avgSign; echo number_format($avgGrowth, 1); ?>% from last month</div>
        </div>

        <div class="report-card inventory">
          <div class="report-card-header">
            <h3 class="report-card-title">Low Stock Items</h3>
            <i class="fas fa-exclamation-triangle" style="color: #e74c3c; font-size: 1.5rem;"></i>
          </div>
          <?php
            $lowstockproducts = 0;
            $branchProductFilter = empty($branchFilterSQL) ? '' : ' AND BranchID = ' . intval($branchId);
            $lowstockquery = "SELECT DISTINCT ProductID, SUM(quantity) FROM product
                              WHERE 1=1" . $branchProductFilter . "
                              GROUP BY ProductID
                              HAVING SUM(quantity) < 10 AND SUM(quantity) >= 1";
            $lowstockresult = $conn->query($lowstockquery);
            $lowstockproducts = $lowstockresult->num_rows;
          ?>
          <div class="report-card-value"><?php echo $lowstockproducts; ?></div>
        </div>

      </div>

      <!-- Key Metrics Cards -->
    <?php
    $newcustomers = "SELECT
    (SELECT COUNT(*)
     FROM user_logs
     WHERE Status = 'Created'
       AND MONTH(Time) = MONTH(CURRENT_DATE())
       AND YEAR(Time) = YEAR(CURRENT_DATE())
    ) AS current_users,
    (SELECT COUNT(*)
     FROM user_logs
     WHERE Status = 'Created'
       AND MONTH(Time) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
       AND YEAR(Time) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
    ) AS last_users";
    $customerresult = $conn->query($newcustomers);
    $customerdata = $customerresult->fetch_assoc();
    $currentusers = $customerdata['current_users'];
    $lastusers = $customerdata['last_users'];
    if ($lastusers > 0) {
        $customerGrowth = (($currentusers - $lastusers) / $lastusers) * 100;
    } 
    else {
        $customerGrowth = $currentusers > 0 ? 100 : 0;
    }
    ?>
     <div class="reports-grid">
        <div class="report-card customers">
          <div class="report-card-header">
            <h3 class="report-card-title">New Customers</h3>
            <i class="fas fa-users" style="color: #9b59b6; font-size: 1.5rem;"></i>
          </div>
          <div class="report-card-value"><?php echo $currentusers; ?></div>
          <div class="report-card-change <?php echo $customerGrowth >= 0 ? 'change-positive' : 'change-negative'; ?>">
            <?php echo ($customerGrowth >= 0 ? '+' : '') . number_format($customerGrowth, 1); ?>% growth
          </div>
        </div>

      </div>

      <!-- Charts Section -->
    <div class="charts-container">
      <div class="chart-card">
        <h3 class="chart-title">Sales Performance</h3>
        <div class="chart-container">
          <canvas id="salesChart"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <h3 class="chart-title">Product Categories</h3>
        <div class="chart-container">
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Additional Charts -->
    <div class="charts-container">
      <div class="chart-card">
        <h3 class="chart-title">Monthly Revenue Trend</h3>
        <div class="chart-container">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>

      <!--  
      <div class="chart-card">
        <h3 class="chart-title">Branch Performance</h3>
        <div class="chart-container">
          <canvas id="branchChart"></canvas>
        </div>
      </div>
    </div>-->

    <!-- Detailed Reports Section -->
    <div class="detailed-reports">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>Top Selling Products</h3>
      </div>

      <table class="report-table">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Units Sold</th>
            <th>Revenue</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $topselling = "SELECT COALESCE(bb.Name, bs.Name, bg.Name, ba.Name, cs.Name) AS ProductName, p.Type AS Category, SUM(od.Quantity) AS UnitsSold, SUM(od.Quantity * od.Price) AS TotalRevenue
                        FROM orderdetails od
                        JOIN orders o ON o.OrderID = od.OrderID
                        JOIN product p ON p.ProductID = od.ProductID
                        LEFT JOIN bowlingball bb ON bb.ProductID = p.ProductID AND bb.BranchID = p.BranchID
                        LEFT JOIN bowlingshoes bs ON bs.ProductID = p.ProductID AND bs.BranchID = p.BranchID
                        LEFT JOIN bowlingbag bg ON bg.ProductID = p.ProductID AND bg.BranchID = p.BranchID
                        LEFT JOIN bowlingaccessories ba ON ba.ProductID = p.ProductID AND ba.BranchID = p.BranchID
                        LEFT JOIN cleaningsupplies cs ON cs.ProductID = p.ProductID AND cs.BranchID = p.BranchID
                        WHERE 1=1" . (empty($branchFilterSQL) ? '' : ' AND o.BranchID = ' . intval($branchId)) . "
                        GROUP BY ProductName, Category
                        ORDER BY UnitsSold DESC, TotalRevenue DESC";
          $topsellingresult = $conn->query($topselling);
          while ($topsellingdata = $topsellingresult->fetch_assoc()) {
            $productname = $topsellingdata['ProductName'];
            $category = $topsellingdata['Category'];
            $unitssold = $topsellingdata['UnitsSold'];
            $totalrevenue = $topsellingdata['TotalRevenue'];
          ?>
          <tr>
            <td><?php echo htmlspecialchars($productname); ?></td>
            <td><?php echo htmlspecialchars($category); ?></td>
            <td><?php echo htmlspecialchars($unitssold); ?></td>
            <td>₱<?php echo number_format($totalrevenue, 2); ?></td>
          </tr> <?php } ?>
        </tbody>
      </table>
    </div> 

    <!-- Low Stock Alert -->
    <div class="detailed-reports" style="margin-top: 30px;">
      <h3 style="color: #e74c3c; margin-bottom: 20px;">
        <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
      </h3>
      <table class="report-table">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Current Stock</th>
            <th>Last Ordered</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $lowstockalert = "SELECT COALESCE(bb.Name, bs.Name, bg.Name, ba.Name, cs.Name) AS ProductName, p.quantity AS CurrentStock, 
                          (SELECT MAX(o.DatePurchased) FROM orderdetails od JOIN orders o ON o.OrderID = od.OrderID WHERE od.ProductID = p.ProductID) AS LastOrdered
                            FROM product p
                            LEFT JOIN bowlingball bb ON bb.ProductID = p.ProductID AND bb.BranchID = p.BranchID
                            LEFT JOIN bowlingshoes bs ON bs.ProductID = p.ProductID AND bs.BranchID = p.BranchID
                            LEFT JOIN bowlingbag bg ON bg.ProductID = p.ProductID AND bg.BranchID = p.BranchID
                            LEFT JOIN bowlingaccessories ba ON ba.ProductID = p.ProductID AND ba.BranchID = p.BranchID
                            LEFT JOIN cleaningsupplies cs ON cs.ProductID = p.ProductID AND cs.BranchID = p.BranchID
                            WHERE p.quantity < 10 AND p.quantity >=1" . (empty($branchFilterSQL) ? '' : ' AND p.BranchID = ' . intval($branchId)) . "
                            ORDER BY p.quantity ASC";
          $lowstockresult = $conn->query($lowstockalert);
          while ($lowstockdata = $lowstockresult->fetch_assoc()) {
            $productname = $lowstockdata['ProductName'];
            $currentstock = $lowstockdata['CurrentStock'];
            $lastordered = $lowstockdata['LastOrdered'];
          ?>
          <tr>
            <td><?php echo htmlspecialchars($productname); ?></td>
            <td><?php echo htmlspecialchars($currentstock); ?></td>
            <td><?php echo htmlspecialchars($lastordered); ?></td>
          </tr> <?php } ?>
        </tbody>
      </table>
    </div>  
        
    <!-- User Deletion Log -->
    <div class="detailed-reports" style="margin-top: 30px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>User Deletion Log</h3>
      </div>

      <table class="report-table">
        <thead>
          <tr>
            <th>Log ID</th>
            <th>User ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Deleted At</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $userdeletion = "SELECT LogID as log_id, UserID as user_id, Username as username, Role as role, Time as DeletedAt FROM user_logs WHERE Status = 'Deleted' ORDER BY Time DESC";
          $deletionresult = $conn->query($userdeletion);
          if ($deletionresult->num_rows > 0) {
            while ($deletiondata = $deletionresult->fetch_assoc()) {
              $logid = $deletiondata['log_id'];
              $userid = $deletiondata['user_id'];
              $username = $deletiondata['username'];
              $role = $deletiondata['role'];
              $deletedat = $deletiondata['DeletedAt'];
          ?>
          <tr>
            <td><?php echo $logid; ?></td>
            <td><?php echo $userid; ?></td>
            <td><?php echo $username; ?></td>
            <td><?php echo $role; ?></td>
            <td><?php echo $deletedat; ?></td>
          </tr><?php }} ?>
        </tbody>
      </table>
    </div>

      <!-- Currency Changes Log -->
    <div class="detailed-reports" style="margin-top: 30px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>Currency Changes Log</h3>
      </div>

      <table class="report-table">
        <thead>
          <tr>
            <th>Log ID</th>
            <th>Currency</th>
            <th>Previous Rate</th>
            <th>New Rate</th>
            <th>Changed At</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $currencychange = "SELECT LogID as log_id, currency, previous_rate, new_rate, date_time FROM currency_changes_log ORDER BY date_time DESC";
          $currencyresult = $conn->query($currencychange);
          if ($currencyresult->num_rows > 0) {
            while ($currencydata = $currencyresult->fetch_assoc()) {
              $logid = $currencydata['log_id'];
              $currency = $currencydata['currency'];
              $previousrate = $currencydata['previous_rate'];
              $newrate = $currencydata['new_rate'];
              $datetime = $currencydata['date_time'];
          ?>
          <tr>
            <td><?php echo $logid; ?></td>
            <td><?php echo $currency; ?></td>
            <td><?php echo $previousrate; ?></td>
            <td><?php echo $newrate; ?></td>
            <td><?php echo $datetime; ?></td>
          </tr> <?php }}?>
        </tbody>
      </table>
    </div>

    <!-- Inventory Log -->
    <div class="detailed-reports" style="margin-top: 30px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>Inventory Log</h3>
      </div>

      <table class="report-table">
        <thead>
          <tr>
            <th>Log ID</th>
            <th>Name</th>
            <th>Branch ID</th>
            <th>Old Quantity</th>
            <th>New Quantity</th>
            <th>Price</th>
            <th>Change Type</th>
            <th>Changed At</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $inventorylog = "SELECT LogID, Name, BranchID, OldQuantity, NewQuantity, Price, ChangeType, ChangedAt FROM inventory_log WHERE BranchID = " . intval($branchId) . " ORDER BY ChangedAt DESC";
          $inventoryresult = $conn->query($inventorylog);
          if ($inventoryresult->num_rows > 0) {
            while ($inventorydata = $inventoryresult->fetch_assoc()) {
              $logid = $inventorydata['LogID'];
              $name = $inventorydata['Name'];
              $branchid = $inventorydata['BranchID'];
              $oldquantity = $inventorydata['OldQuantity'];
              $newquantity = $inventorydata['NewQuantity'];
              $price = $inventorydata['Price'];
              $changetype = $inventorydata['ChangeType'];
              $changedat = $inventorydata['ChangedAt'];
          ?>
          <tr>
            <td><?php echo $logid; ?></td>
            <td><?php echo $name; ?></td>
            <td><?php echo $branchid; ?></td>
            <td><?php echo $oldquantity; ?></td>
            <td><?php echo $newquantity; ?></td>
            <td><?php echo $price; ?></td>
            <td><?php echo $changetype; ?></td>
            <td><?php echo $changedat; ?></td>
          </tr> <?php }}?>
        </tbody>
      </table>
    </div>
  </div>
</div>

  <script>
    $(document).ready(function() {
      // Initialize Charts
      initializeCharts();
      
      // Generate Report Button
      $('#generateReport').on('click', function() {
        generateReport();
      });

      // Filter Change Events
      $('#reportType, #branchFilter, #categoryFilter, #timeFrame').on('change', function() {
        updateReport();
      });

      function initializeCharts() {
        // Get branch ID from PHP
        const branchId = <?php echo $branchId ? intval($branchId) : 'null'; ?>;
        const branchParam = branchId ? `?branch_id=${branchId}` : '';

        // Sales Performance Chart

       fetch(`sales-data.php${branchParam}`)
      .then(response => response.json())
      .then(data => {
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
          type: 'line',
          data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
              label: 'Sales (₱)',
              data: data, // Access the array inside the data property
              borderColor: '#3498db',
              backgroundColor: 'rgba(52, 152, 219, 0.1)',
              borderWidth: 2,
              fill: true
            }]
          },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                  }
                }
              }
            }
        });
    });

        fetch(`products_category.php${branchParam}`)
  .then(response => response.json())
  .then(stats => {
      
    const ctx = document.getElementById('categoryChart').getContext('2d');

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: stats.labels,
        datasets: [{
          data: stats.data,
          backgroundColor: [
            '#3498db',
            '#2ecc71',
            '#e74c3c',
            '#9b59b6',
            '#f39c12'
          ]
        }]
      },
      options: {
        plugins:{
          tooltip:{
            callbacks:{
              label: function(context) {
              let label = context.label || '';
              let value = context.raw || 0;
              return `${label}: ${value}%`;
              }
            }
          }
        }, 
        responsive: true,
        maintainAspectRatio: false
      }
    });

  });

  fetch(`revenue_trend.php${branchParam}`)
  .then(r => r.json())
  .then(data => {
    const labels = data.map(d => d.label);
    const revenue = data.map(d => d.revenue);
    const ctx = document.getElementById('revenueChart').getContext('2d');
     new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Revenue',
            data: revenue,
            borderColor: '#2ecc71',
            backgroundColor: '#2ecc71',
            borderWidth: 2,
            tension: 0.3,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              label: (ctx) => `${ctx.dataset.label}: ₱${ctx.raw.toLocaleString()}`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: value => '₱' + value.toLocaleString()
            }
          }
        }
      }
    });
  });


        /*// Branch Performance Chart
        const branchCtx = document.getElementById('branchChart').getContext('2d');
        new Chart(branchCtx, {
          type: 'radar',
          data: {
            labels: ['Sales', 'Profit', 'Customers', 'Inventory Turnover', 'Service Quality'],
            datasets: [{
              label: 'Downtown',
              data: [85, 78, 92, 76, 88],
              borderColor: '#3498db',
              backgroundColor: 'rgba(52, 152, 219, 0.2)'
            }, {
              label: 'West Mall',
              data: [78, 85, 80, 82, 85],
              borderColor: '#2ecc71',
              backgroundColor: 'rgba(46, 204, 113, 0.2)'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              r: {
                beginAtZero: true,
                max: 100
              }
            }
          }
        });*/
      }

      function generateReport() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const reportType = $('#reportType').val();
        const branch = $('#branchFilter').val();
        const category = $('#categoryFilter').val();
        const timeFrame = $('#timeFrame').val();

        // Show loading state
        $('#generateReport').html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        
        // Simulate API call
        setTimeout(() => {
          // Update report data based on filters
          updateReportData(startDate, endDate, reportType, branch, category, timeFrame);
          $('#generateReport').html('<i class="fas fa-sync-alt"></i> Generate Report');
          
          // Show success message
          alert('Report generated successfully for the selected period!');
        }, 1500);
      }

      function updateReport() {
        const reportType = $('#reportType').val();
        // Update UI based on report type
        updateReportUI(reportType);
      }

      function updateReportUI(reportType) {
        // Hide/show sections based on report type
        $('.reports-grid, .charts-container, .detailed-reports').show();
        
        switch(reportType) {
          case 'sales':
            $('.chart-title').first().text('Sales Performance');
            break;
          case 'inventory':
            $('.chart-title').first().text('Inventory Analysis');
            break;
          case 'customer':
            $('.chart-title').first().text('Customer Analytics');
            break;
          case 'financial':
            $('.chart-title').first().text('Financial Overview');
            break;
        }
      }

      function updateReportData(startDate, endDate, reportType, branch, category, timeFrame) {
        // This function would typically make an API call to get updated data
        console.log('Updating report with:', {
          startDate,
          endDate,
          reportType,
          branch,
          category,
          timeFrame
        });
        
        // Update quick stats with new data (simulated)
        const newRevenue = Math.floor(1245680 * (1 + Math.random() * 0.1));
        $('.quick-stat-value').first().text('₱' + newRevenue.toLocaleString());
      }

      // Initialize the reports page
      updateReportUI('sales');
    });
  </script>
</body>
</html>