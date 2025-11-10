<?php include('admin-header.html')?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Dashboard - Product Management</title>
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

    <!-- Stats Overview -->
    <div id="stats-grid" class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-bowling-ball"></i>
        </div>
        <div class="stat-number">156</div>
        <div class="stat-label">Total Products</div>
      </div>
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
          <!-- Sample Data Row 1 -->
          <tr>
            <td>Phantom</td>
            <td>Storm</td>
            <td>Reactive</td>
            <td>Professional</td>
            <td>15 lbs</td>
            <td>Radial Core</td>
            <td>Asymmetric</td>
            <td>2.48</td>
            <td>0.050</td>
            <td>0.015</td>
            <td>NRG Hybrid</td>
            <td>Hybrid</td>
            <td>₱8,129.99</td>
            <td>15</td>
            <td><span class="tstatus-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="1">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="1">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          
          <!-- Sample Data Row 2 -->
          <tr>
            <td>Game Breaker 4</td>
            <td>Ebonite</td>
            <td>Reactive</td>
            <td>Performance</td>
            <td>15 lbs</td>
            <td>GB4 Core</td>
            <td>Symmetric</td>
            <td>2.50</td>
            <td>0.045</td>
            <td>0.010</td>
            <td>GB 4.0</td>
            <td>Pearl</td>
            <td>₱7,899.99</td>
            <td>8</td>
            <td><span class="tstatus-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="2">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="2">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          
          <!-- Sample Data Row 3 -->
          <tr>
            <td>Black Widow 2.0</td>
            <td>Hammer</td>
            <td>Reactive</td>
            <td>Professional</td>
            <td>16 lbs</td>
            <td>Gas Mask Core</td>
            <td>Asymmetric</td>
            <td>2.53</td>
            <td>0.052</td>
            <td>0.018</td>
            <td>Aggression Solid</td>
            <td>Solid</td>
            <td>₱8,499.99</td>
            <td>3</td>
            <td><span class="tstatus-badge status-low-stock">Low Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="3">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="3">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
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
          
          <tr>
            <td>Tournament Roller Pro</td>
            <td>Storm</td>
            <td>Black/Red</td>
            <td>Roller Bag</td>
            <td>3-Ball</td>
            <td>₱4,299.99</td>
            <td>12</td>
            <td><span class="tstatus-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="101">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td>Elite Tote Deluxe</td>
            <td>Brunswick</td>
            <td>Navy Blue/Silver</td>
            <td>Tote Bag</td>
            <td>2-Ball</td>
            <td>₱2,899.99</td>
            <td>8</td>
            <td><span class="tstatus-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="102">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="102">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td>Compact Single Bag</td>
            <td>Motiv</td>
            <td>Charcoal Gray</td>
            <td>Single Bag</td>
            <td>1-Ball</td>
            <td>₱1,499.99</td>
            <td>3</td>
            <td><span class="tstatus-badge status-low-stock">Low Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="103">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="103">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
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
           <tr>
            <td>Tournament Roller Pro</td>
            <td>Storm</td>
            <td>S</td>
            <td>Male</td>
            <td>₱4,299.99</td>
            <td>12</td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="101">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td>Elite Tote Deluxe</td>
            <td>Brunswick</td>
            <td>L</td>
            <td>Male</td>
            <td>₱2,899.99</td>
            <td>8</td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="102">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="102">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td>Compact Single Bag</td>
            <td>Motiv</td>
            <td>XL</td>
            <td>Male</td>
            <td>₱1,499.99</td>
            <td>3</td>
            <td><span class="status-badge status-low-stock">Low Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="103">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="103">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
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
           <tr>
            <td>Grip</td>
            <td>Storm</td>
            <td>Grip</td>
            <td>Right</td>
            <td>₱1,299.99</td>
            <td>12</td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="101">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td>Elite Tote Deluxe</td>
            <td>Brunswick</td>
            <td>Grip</td>
            <td>Right</td>
            <td>₱2,899.99</td>
            <td>8</td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="102">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="102">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td>Compact Single Bag</td>
            <td>Motiv</td>
            <td>Grip</td>
            <td>Left</td>
            <td>₱1,499.99</td>
            <td>3</td>
            <td><span class="status-badge status-low-stock">Low Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="103">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="103">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
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
           <tr>
            <td>Towel</td>
            <td>Storm</td>
            <td>Towel</td>
            <td>₱299.99</td>
            <td>12</td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="101">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td>Pads</td>
            <td>Brunswick</td>
            <td>Pads</td>
            <td>₱2,899.99</td>
            <td>8</td>
            <td><span class="status-badge status-active">In Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="102">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="102">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td>Cleaner</td>
            <td>Motiv</td>
            <td>Cleaner</td>
            <td>₱1,499.99</td>
            <td>3</td>
            <td><span class="status-badge status-low-stock">Low Stock</span></td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="103">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="103">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
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
          <tr>
            <td>PHP</td>
            <td>1</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="101">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>KRW</td>
            <td>24.82</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="102">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="102">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>USD</td>
            <td>58.81</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="103">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="103">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
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
          <tr>
            <td>1</td>
            <td>Manila</td>
            <td>Taft Ave</td>
            <td>1000</td>
          </tr>
          <tr>
           <td>1</td>
            <td>Manila</td>
            <td>Taft Ave</td>
            <td>1000</td>
          </tr>
          <tr>
            <td>1</td>
            <td>Manila</td>
            <td>Taft Ave</td>
            <td>1000</td>
          </tr>
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
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Manila</td>
            <td>Taft Ave</td>
            <td>1000</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="101">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
           <td>2</td>
            <td>Manila</td>
            <td>Taft Ave</td>
            <td>1000</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="102">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="102">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>3</td>
            <td>Manila</td>
            <td>Taft Ave</td>
            <td>1000</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="103">
                  <i class="fas fa-edit"></i>
                </button>
                 <button class="btn btn-danger btn-sm delete-btn" data-id="103">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
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
          <tr>
            <td>1</td>
            <td>Drill</td>
            <td>200</td>
            <td>wokao</td>
            <td>Available</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="101">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
           <td>2</td>
            <td>Polish</td>
            <td>200</td>
            <td>made</td>
            <td>Available</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="102">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
             <td>3</td>
            <td>Repair</td>
            <td>200</td>
            <td>nima</td>
            <td>Available</td>
            <td class="action-cell">
              <div class="action-buttons">
                <button class="btn btn-warning btn-sm edit-btn" data-id="103">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </td>
          </tr>
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
        <tr>
          <td>USR-001</td>
          <td>John</td>
          <td>Smith</td>
          <td>+63 912 345 6789</td>
          <td>john.smith@email.com</td>
          <td><span class="user-status-badge role-admin">Administrator</span></td>
          <td>Manila</td>
          <td>123 Main Street</td>
          <td>1000</td>
          <td class="action-cell">
            <div class="action-buttons">
              <button class="btn btn-warning btn-sm edit-btn" data-id="USR-001">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-danger btn-sm delete-btn" data-id="USR-001">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>USR-002</td>
          <td>Maria</td>
          <td>Garcia</td>
          <td>+63 917 987 6543</td>
          <td>maria.garcia@email.com</td>
          <td><span class="user-status-badge role-staff">Staff</span></td>
          <td>Quezon City</td>
          <td>456 Oak Avenue</td>
          <td>1000</td>
          <td class="action-cell">
            <div class="action-buttons">
              <button class="btn btn-warning btn-sm edit-btn" data-id="USR-002">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-danger btn-sm delete-btn" data-id="USR-002">
                <i class="fas fa-trash"></i>
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
              <div class="image-upload-container">
                <div class="image-upload" id="ballImageUpload">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload product images</p>
                  <span class="upload-hint">Recommended: 800x800px, PNG or JPG</span>
                  <input type="file" id="ballImage" name="ballImage" accept="image/*" style="display: none;" multiple>
                </div>
              </div>
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
              <div class="image-upload-container">
                <div class="image-upload" id="imageUpload">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload product images</p>
                  <span class="upload-hint">Recommended: 800x800px, PNG or JPG</span>
                  <input type="file" id="shoeImage" name="shoeImage" accept="image/*" style="display: none;" multiple>
                </div>
              </div>
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
              <div class="image-upload-container">
                <div class="image-upload" id="imageUpload">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload product images</p>
                  <span class="upload-hint">Recommended: 800x800px, PNG or JPG</span>
                  <input type="file" id="bagImage" name="bagImage" accept="image/*" style="display: none;" multiple>
                </div>
              </div>
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
              <div class="image-upload-container">
                <div class="image-upload" id="imageUpload">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload product images</p>
                  <span class="upload-hint">Recommended: 800x800px, PNG or JPG</span>
                  <input type="file" id="accessoryImage" name="accessoryImage" accept="image/*" style="display: none;" multiple>
                </div>
              </div>
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
              <div class="image-upload-container">
                <div class="image-upload" id="imageUpload">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload product images</p>
                  <span class="upload-hint">Recommended: 800x800px, PNG or JPG</span>
                  <input type="file" id="supplyImage" name="supplyImage" accept="image/*" style="display: none;" multiple>
                </div>
              </div>
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
            <button type="submit" class="btn btn-primary" id="currencyModalSubmitBtn">Add Currency</button>
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

  <!-- modal for editing services -->
  <div class="modal services-modal" id="servicesModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add New Service</h2>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <form id="servicesForm">
          <div class="form-grid">
            <div class="form-group">
              <label for="serviceName" class="required">Service ID</label>
              <input type="text" id="serviceName" name="serviceName" readonly>
            </div>
            <div class="form-group">
              <label for="serviceType" class="required">Service Type</label>
              <select id="serviceType" name="serviceType" readonly>
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
                <option value="Administrator">Administrator</option>
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
              title: 'Branches',
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

      function handleTransactionFormSubmit($form) {
          const orderId = $('#orderId').val();
          const newStatus = $('#status').val();
          
          // Update the table row with new status
          const $row = $(`.edit-transaction-btn[data-id="${orderId}"]`).closest('tr');
          const $statusCell = $row.find('td:nth-child(10)');
          const $dateCompletedCell = $row.find('td:nth-child(9)');
          
          // Status class mapping
          const statusClassMap = {
              'Pending': 'status-pending',
              'Processing': 'status-processing', 
              'Completed': 'status-completed',
              'Cancelled': 'status-cancelled'
          };
          
          // Update status badge
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
          
          // Find the item data from the table
          const $row = $(`.edit-btn[data-id="${itemId}"]`).closest('tr');
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
              showDeleteModal(itemId, itemName, 'item');
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
          
          // Find and remove the row from the table
          const $deleteBtn = $(`.delete-btn[data-id="${itemId}"]`);
          const $row = $deleteBtn.closest('tr');
          
          if ($row.length > 0) {
              $row.fadeOut(300, function() {
                  $(this).remove();
                  
                  // Update stats if it's a product
                  if (categoryConfig[currentCategory].hasStats) {
                      updateStatsAfterDelete();
                  }
                  
                  // Show success message
                  alert(`Item deleted successfully!`);
              });
          }
          
          closeModal('#deleteConfirmModal');
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
          $form.find('#currencyCode').val(cells.eq(0).text());
          $form.find('#exchangeRate').val(cells.eq(1).text());
      }

      function populateAddressForm($form, $row) {
          const cells = $row.find('td');
          $form.find('#addressCity').val(cells.eq(1).text());
          $form.find('#addressStreet').val(cells.eq(2).text());
          $form.find('#addressZipCode').val(cells.eq(3).text());
      }

      function populateServicesForm($form, $row) {
          const cells = $row.find('td');
          $form.find('#serviceName').val(cells.eq(1).text());
          $form.find('#serviceType').val(cells.eq(1).text());
          $form.find('#servicePrice').val(cells.eq(2).text());
          $form.find('#serviceStaff').val('Made'); // Default staff
          $form.find('#serviceAvailability').val(cells.eq(3).text());
      }

      function populateUsersForm($form, $row) {
          const cells = $row.find('td');
          $form.find('#userFirstName').val(cells.eq(1).text());
          $form.find('#userLastName').val(cells.eq(2).text());
          $form.find('#userPhone').val(cells.eq(3).text());
          $form.find('#userEmail').val(cells.eq(4).text());
          $form.find('#userRole').val(cells.eq(5).text().replace('Administrator', 'Administrator').replace('Staff', 'Staff'));
          $form.find('#userCity').val(cells.eq(6).text());
          $form.find('#userStreet').val(cells.eq(7).text());
          $form.find('#userZipCode').val(cells.eq(8).text());
          $form.find('#userStatus').val('Active');
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
          
          // Show success message
          alert(`${isEdit ? 'Item updated' : 'Item added'} successfully!`);
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