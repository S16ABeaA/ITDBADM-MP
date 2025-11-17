<?php 
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';
include("header.html");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first to book services.'); window.location.href='login-signup.php';</script>";
    exit();
}

$userID = $_SESSION['user_id'];

// Fetch service prices from the database
$services = [];
$servicesSQL = "SELECT ServiceID, StaffID, Type, Price, Availability FROM services WHERE Availability = 1";
$result = $conn->query($servicesSQL);

if ($result->num_rows > 0) {
    while($service = $result->fetch_assoc()) {
        $services[strtolower($service['Type'])] = $service;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bowling Ball Services</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <link href="./css/services-page.css" rel="stylesheet">
</head>
<body>
  <div class="content-section">
    <h1 class="page-title">Bowling Ball Services</h1>
    <p class="page-subtitle">Professional services to keep your bowling ball in perfect condition</p>

    <!-- Services Selection -->
    <div class="services-grid">
      <?php if (!empty($services)): ?>
        <!-- Drilling Service -->
        <?php if (isset($services['drilling'])): ?>
        <div class="service-card" data-service="drilling">
          <div class="service-icon">
            <i class="fas fa-tools"></i>
          </div>
          <h3 class="service-title">Custom Drilling</h3>
          <p class="service-description">Professional finger hole drilling tailored to your hand measurements for perfect fit and comfort.</p>
          <div class="service-price">₱<?php echo number_format($services['drilling']['Price'], 2); ?></div>
          <div class="service-duration">2-3 days completion</div>
          <button class="select-service-btn" 
                  data-service-id="<?php echo $services['drilling']['ServiceID']; ?>" 
                  data-service-name="Custom Drilling" 
                  data-service-price="<?php echo $services['drilling']['Price']; ?>">
            Add to Cart
          </button>
        </div>
        <?php endif; ?>

        <!-- Polishing Service -->
        <?php if (isset($services['polishing'])): ?>
        <div class="service-card" data-service="polishing">
          <div class="service-icon">
            <i class="fa-solid fa-spray-can-sparkles"></i>
          </div>
          <h3 class="service-title">Professional Polishing</h3>
          <p class="service-description">Restore your ball's original shine and surface finish for optimal lane performance.</p>
          <div class="service-price">₱<?php echo number_format($services['polishing']['Price'], 2); ?></div>
          <div class="service-duration">1-2 days completion</div>
          <button class="select-service-btn" 
                  data-service-id="<?php echo $services['polishing']['ServiceID']; ?>" 
                  data-service-name="Professional Polishing" 
                  data-service-price="<?php echo $services['polishing']['Price']; ?>">
            Add to Cart
          </button>
        </div>
        <?php endif; ?>

        <!-- Sanding Service -->
        <?php if (isset($services['sanding'])): ?>
        <div class="service-card" data-service="sanding">
          <div class="service-icon">
            <i class="fas fa-file-alt"></i>
          </div>
          <h3 class="service-title">Surface Sanding</h3>
          <p class="service-description">Custom surface adjustments with various grit levels to match your playing style and lane conditions.</p>
          <div class="service-price">₱<?php echo number_format($services['sanding']['Price'], 2); ?></div>
          <div class="service-duration">1 day completion</div>
          <button class="select-service-btn" 
                  data-service-id="<?php echo $services['sanding']['ServiceID']; ?>" 
                  data-service-name="Surface Sanding" 
                  data-service-price="<?php echo $services['sanding']['Price']; ?>">
            Add to Cart
          </button>
        </div>
        <?php endif; ?>

        <!-- Repair Service -->
        <?php if (isset($services['repair'])): ?>
        <div class="service-card" data-service="repair">
          <div class="service-icon">
            <i class="fas fa-sync-alt"></i>
          </div>
          <h3 class="service-title">Parts Repair</h3>
          <p class="service-description">Professional repair service for damaged ball parts to improve comfort and overall play quality.</p>
          <div class="service-price">₱<?php echo number_format($services['repair']['Price'], 2); ?></div>
          <div class="service-duration">2-3 days completion</div>
          <button class="select-service-btn" 
                  data-service-id="<?php echo $services['repair']['ServiceID']; ?>" 
                  data-service-name="Parts Repair" 
                  data-service-price="<?php echo $services['repair']['Price']; ?>">
            Add to Cart
          </button>
        </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="no-services">
          <p>No services available at the moment.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal for service selection -->
  <div class="modal-overlay" id="cartModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add Service to Cart</h2>
        <button type="button" class="modal-close" id="closeModal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="service-info">
          <div class="service-name" id="modalServiceName">Custom Drilling</div>
          <div class="service-price-modal" id="modalServicePrice">₱1,200.00</div>
          <div class="price-breakdown" id="priceBreakdown" style="font-size: 12px; color: #666; margin-top: 5px;"></div>
        </div>
        <p class="modal-question">Is the bowling ball from our shop?</p>
        <div class="ball-origin-options">
          <label class="origin-option">
            <input type="radio" name="is_from_store" value="yes" style="display: none;">
            <span class="option-label">Yes</span>
            <span class="option-description">Purchased from our store - Base price</span>
          </label>
          <label class="origin-option">
            <input type="radio" name="is_from_store" value="no" style="display: none;">
            <span class="option-label">No</span>
            <span class="option-description">From another store - Base price + 5% surcharge</span>
          </label>
        </div>
        <input type="hidden" id="modalServiceId">
      </div>
      <div class="modal-footer">
        <button type="button" class="modal-btn btn-cancel" id="cancelBtn">Cancel</button>
        <button type="button" class="modal-btn btn-confirm" id="confirmBtn" disabled>Add to Cart</button>
      </div>
    </div>
  </div>

  <script>
    // DOM Elements
    const modal = document.getElementById('cartModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    const serviceCards = document.querySelectorAll('.service-card');
    const originOptions = document.querySelectorAll('.origin-option');
    const radioInputs = document.querySelectorAll('input[name="is_from_store"]');
    
    // Service information elements
    const modalServiceName = document.getElementById('modalServiceName');
    const modalServicePrice = document.getElementById('modalServicePrice');
    const modalServiceId = document.getElementById('modalServiceId');
    const priceBreakdown = document.getElementById('priceBreakdown');
    
    // Current selected service
    let currentService = null;
    let basePrice = 0;
    
    // Event Listeners for service cards
    serviceCards.forEach(card => {
      const serviceBtn = card.querySelector('.select-service-btn');
      const serviceType = card.getAttribute('data-service');
      
      serviceBtn.addEventListener('click', () => {
        currentService = serviceType;
        const serviceId = serviceBtn.getAttribute('data-service-id');
        const serviceName = serviceBtn.getAttribute('data-service-name');
        basePrice = parseFloat(serviceBtn.getAttribute('data-service-price'));
        
        openModal(serviceId, serviceName, basePrice);
      });
    });
    
    // Event Listeners for origin options
    originOptions.forEach(option => {
      option.addEventListener('click', () => {
        // Remove selected class from all options
        originOptions.forEach(opt => opt.classList.remove('selected'));
        
        // Add selected class to clicked option
        option.classList.add('selected');
        
        // Check the radio input
        const radioInput = option.querySelector('input[type="radio"]');
        if (radioInput) {
          radioInput.checked = true;
          updatePriceDisplay(radioInput.value);
        }
        
        // Enable confirm button
        confirmBtn.disabled = false;
      });
    });
    
    // Radio input change listeners
    radioInputs.forEach(radio => {
      radio.addEventListener('change', () => {
        if (radio.checked) {
          // Remove selected class from all options
          originOptions.forEach(opt => opt.classList.remove('selected'));
          
          // Add selected class to parent label
          radio.closest('.origin-option').classList.add('selected');
          
          // Update price display
          updatePriceDisplay(radio.value);
          
          // Enable confirm button
          confirmBtn.disabled = false;
        }
      });
    });
    
    // Modal event listeners
    closeModal.addEventListener('click', closeModalFunc);
    cancelBtn.addEventListener('click', closeModalFunc);
    
    // Confirm button event listener
    confirmBtn.addEventListener('click', addServiceToCart);
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeModalFunc();
      }
    });
    
    // Functions
    function openModal(serviceId, serviceName, price) {
      // Update modal with service information
      modalServiceName.textContent = serviceName;
      modalServiceId.value = serviceId;
      basePrice = price;
      
      // Reset to default price (no surcharge)
      modalServicePrice.textContent = '₱' + basePrice.toFixed(2);
      priceBreakdown.textContent = 'Base price: ₱' + basePrice.toFixed(2);
      
      // Reset origin selection
      originOptions.forEach(opt => opt.classList.remove('selected'));
      radioInputs.forEach(radio => radio.checked = false);
      confirmBtn.disabled = true;
      confirmBtn.textContent = 'Add to Cart';
      
      // Show modal
      modal.classList.add('active');
    }
    
    function updatePriceDisplay(origin) {
      let finalPrice = basePrice;
      let breakdown = 'Base price: ₱' + basePrice.toFixed(2);
      
      if (origin === 'no') {
        const surcharge = basePrice * 0.05; // 5% surcharge
        finalPrice = basePrice + surcharge;
        breakdown = `Base: ₱${basePrice.toFixed(2)} + 5% surcharge: ₱${surcharge.toFixed(2)}`;
      }
      
      modalServicePrice.textContent = '₱' + finalPrice.toFixed(2);
      priceBreakdown.textContent = breakdown;
    }
    
    function closeModalFunc() {
      modal.classList.remove('active');
    }
    
    function addServiceToCart() {
      const serviceId = modalServiceId.value;
      const selectedOrigin = document.querySelector('input[name="is_from_store"]:checked');
      
      if (!selectedOrigin) {
        alert('Please select where the bowling ball is from.');
        return;
      }
      
      // Disable button to prevent multiple clicks
      confirmBtn.disabled = true;
      confirmBtn.textContent = 'Adding...';
      
      // Send AJAX request
      $.post('add_service_to_cart.php', {
        serviceID: serviceId,
        isFromStore: selectedOrigin.value
      }, function(response) {
        if (response.success) {
          alert(response.message);
          closeModalFunc();
          // You can update cart counter here if you have one
          if (response.cartCount) {
            updateCartCounter(response.cartCount);
          }
          
          // Log price details for debugging
          if (response.priceDetails) {
            console.log('Price breakdown:', response.priceDetails);
          }
        } else {
          alert(response.message);
          confirmBtn.disabled = false;
          confirmBtn.textContent = 'Add to Cart';
        }
      }, 'json').fail(function() {
        alert('Connection error. Please try again.');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Add to Cart';
      });
    }
    
    function updateCartCounter(count) {
      // Update cart counter in your navigation if you have one
      const cartCounter = document.getElementById('cartCounter');
      if (cartCounter) {
        cartCounter.textContent = count;
      }
    }
  </script>

</body>
</html>
<?php include("footer.html")?>