<?php
require_once '../dependencies/config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports - Bowling Management System</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="../css/staffCSS/staff-homepage.css" rel="stylesheet">
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

    .report-card.customers {
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
  <?php include('admin-header.html')?>
  
  <div class="content-section">
    <!-- Navigation Menu -->
    <div class="nav-menu">
      <div class="nav-row">
        <div class="nav-item" data-category="bowling-balls">Bowling Balls</div>
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
        <div class="nav-item active" data-category="reports">Reports</div>
      </div>
    </div>

    <div class="reports-container">
      <!-- Reports Header -->
      <div class="reports-header">
        <h1 class="reports-title">Analytics & Reports</h1>
      </div>

      <!-- Quick Stats -->
       <?php
       $totalrevenue = "SELECT SUM(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m') THEN Total END) AS revenue_this_month,
                               SUM(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m') THEN Total END) AS revenue_last_month
                        FROM orders";
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
      <div class="quick-stats">
        <div class="quick-stat">
          <div class="quick-stat-label">Total Revenue</div>
          <div class="quick-stat-value">₱<?php echo number_format($thismonthrevenue, 2); ?></div>
          <div class="report-card-change <?php echo $revenueGrowthClass?>"><?php echo $revenueSign; echo number_format($revenueGrowth, 1); ?>% from last month
        </div>
        </div>

        <?php
        $ordercomp = "SELECT SUM(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m') THEN 1 ELSE 0 END) AS this_month,
                      SUM(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m')
                      THEN 1 ELSE 0 END) AS last_month, COUNT(*) as total_orders
                      FROM orders";
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
        <div class="quick-stat">
          <div class="quick-stat-label">Total Orders</div>
          <div class="quick-stat-value"><?php echo $totalorders; ?></div>
          <div class="report-card-change <?php echo $orderGrowthClass ?>">
            <?php echo $orderSign; echo number_format($growth, 1); ?>% from last month
          </div>
        </div>
        <?php
        $avgsalesmonth = "SELECT AVG(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m') THEN Total END) AS avg_this_month,
                                 AVG(CASE WHEN DATE_FORMAT(DatePurchased, '%Y-%m') = DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m') THEN Total END) AS avg_last_month
                          FROM orders";
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
        <div class="quick-stat">
          <div class="quick-stat-label">Avg. Order Value</div>
          <div class="quick-stat-value">₱<?php echo number_format($avgthismonth, 2); ?></div>
          <div class="report-card-change <?php echo $avgGrowthClass?>">
            <?php echo $avgSign; echo number_format($avgGrowth, 1); ?>% from last month
          </div>
        </div>
        <div class="quick-stat">
          <div class="quick-stat-label">Customer Growth</div>
          <div class="quick-stat-value">+287</div>
          <div class="report-card-change change-positive">+15.7% from last month</div>
        </div>
      </div>

      <!-- Report Filters -->
      <div class="report-filters">
        <select class="filter-select" id="reportType">
          <option value="sales">Sales Report</option>
          <option value="inventory">Inventory Report</option>
          <option value="customer">Customer Report</option>
          <option value="financial">Financial Report</option>
        </select>
        
        <select class="filter-select" id="branchFilter">
          <option value="">All Branches</option>
          <option value="downtown">Downtown</option>
          <option value="west-mall">West Mall</option>
          <option value="east-side">East Side</option>
          <option value="north-plaza">North Plaza</option>
        </select>
        
        <select class="filter-select" id="categoryFilter">
          <option value="">All Categories</option>
          <option value="bowling-balls">Bowling Balls</option>
          <option value="shoes">Bowling Shoes</option>
          <option value="bags">Bowling Bags</option>
          <option value="accessories">Accessories</option>
          <option value="cleaning">Cleaning Supplies</option>
        </select>
        
        <select class="filter-select" id="timeFrame">
          <option value="daily">Daily</option>
          <option value="weekly" selected>Weekly</option>
          <option value="monthly">Monthly</option>
          <option value="quarterly">Quarterly</option>
          <option value="yearly">Yearly</option>
        </select>
      </div>

      <!-- Key Metrics Cards -->
      <div class="reports-grid">
        <div class="report-card sales">
          <div class="report-card-header">
            <h3 class="report-card-title">Total Sales</h3>
            <i class="fas fa-shopping-cart" style="color: #2ecc71; font-size: 1.5rem;"></i>
          </div>
          <div class="report-card-value"><?php echo number_format($thismonthrevenue, 2); ?></div>
          <div class="report-card-change <?php echo $revenueGrowthClass?>"><?php echo $revenueSign; echo number_format($revenueGrowth, 1); ?>% from last month
        </div>
        </div>

        <div class="report-card inventory">
          <div class="report-card-header">
            <h3 class="report-card-title">Low Stock Items</h3>
            <i class="fas fa-exclamation-triangle" style="color: #e74c3c; font-size: 1.5rem;"></i>
          </div>
          <?php
            $lowstockproducts = 0;
            $lowstockquery = "SELECT DISTINCT ProductID, SUM(quantity) FROM product
                              GROUP BY ProductID
                              HAVING SUM(quantity) < 10 AND SUM(quantity) >= 1";
            $lowstockresult = $conn->query($lowstockquery);
            $lowstockproducts = $lowstockresult->num_rows;
        ?>
          <div class="report-card-value"><?php echo $lowstockproducts; ?></div>
          <div class="report-card-change change-negative">+3 from last week</div>
        </div>

        <div class="report-card customers">
          <div class="report-card-header">
            <h3 class="report-card-title">New Customers</h3>
            <i class="fas fa-users" style="color: #9b59b6; font-size: 1.5rem;"></i>
          </div>
          <div class="report-card-value">287</div>
          <div class="report-card-change change-positive">+15.7% growth</div>
        </div>

        <div class="report-card financial">
          <div class="report-card-header">
            <h3 class="report-card-title">Profit Margin</h3>
            <i class="fas fa-chart-line" style="color: #f39c12; font-size: 1.5rem;"></i>
          </div>
          <div class="report-card-value">32.8%</div>
          <div class="report-card-change change-positive">+2.1% improvement</div>
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

        <div class="chart-card">
          <h3 class="chart-title">Branch Performance</h3>
          <div class="chart-container">
            <canvas id="branchChart"></canvas>
          </div>
        </div>
      </div>

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
              <th>Profit</th>
              <th>Stock Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Phantom Bowling Ball</td>
              <td>Bowling Balls</td>
              <td>156</td>
              <td>₱1,267,478</td>
              <td>₱415,892</td>
              <td><span class="status-badge status-active">In Stock</span></td>
            </tr>
            <tr>
              <td>Tournament Roller Pro</td>
              <td>Bowling Bags</td>
              <td>89</td>
              <td>₱382,699</td>
              <td>₱125,456</td>
              <td><span class="status-badge status-active">In Stock</span></td>
            </tr>
            <tr>
              <td>Pro Performance Shoes</td>
              <td>Bowling Shoes</td>
              <td>67</td>
              <td>₱234,567</td>
              <td>₱76,890</td>
              <td><span class="status-badge status-low-stock">Low Stock</span></td>
            </tr>
            <tr>
              <td>Elite Grip Set</td>
              <td>Accessories</td>
              <td>234</td>
              <td>₱187,654</td>
              <td>₱61,567</td>
              <td><span class="status-badge status-active">In Stock</span></td>
            </tr>
            <tr>
              <td>Premium Cleaner Kit</td>
              <td>Cleaning Supplies</td>
              <td>178</td>
              <td>₱156,789</td>
              <td>₱51,345</td>
              <td><span class="status-badge status-active">In Stock</span></td>
            </tr>
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
              <th>Minimum Required</th>
              <th>Status</th>
              <th>Last Ordered</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Black Widow 2.0</td>
              <td>3</td>
              <td>15</td>
              <td><span class="status-badge status-low-stock">Critical</span></td>
              <td>2024-01-15</td>
            </tr>
            <tr>
              <td>Pro Performance Shoes (Size 9)</td>
              <td>2</td>
              <td>10</td>
              <td><span class="status-badge status-low-stock">Critical</span></td>
              <td>2024-01-10</td>
            </tr>
            <tr>
              <td>Compact Single Bag</td>
              <td>5</td>
              <td>12</td>
              <td><span class="status-badge status-low-stock">Low</span></td>
              <td>2024-01-18</td>
            </tr>
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
            $userdeletion = "SELECT LogID as log_id, UserID as user_id, Username as username, Role as role, DeletedAt FROM user_deletion_log ORDER BY DeletedAt DESC";
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
            $inventorylog = "SELECT LogID, Name, BranchID, OldQuantity, NewQuantity, Price, ChangeType, ChangedAt FROM inventory_log ORDER BY ChangedAt DESC";
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
        // Sales Performance Chart

       fetch('sales-data.php')
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

        fetch('products_category.php')
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

  fetch('revenue_trend.php')
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


        // Branch Performance Chart
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
        });
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