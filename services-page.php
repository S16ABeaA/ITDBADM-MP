<?php include("header.html")?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bowling Ball Services</title>
  <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <link href="./css/services-page.css" rel="stylesheet">
</head>
<body>
  <div class="content-section">
    <h1 class="page-title">Bowling Ball Services</h1>
    <p class="page-subtitle">Professional services to keep your bowling ball in perfect condition</p>

    <!-- Services Selection -->
    <div class="services-grid">
      <!-- Drilling Service -->
      <div class="service-card" data-service="drilling">
        <div class="service-icon">
          <i class="fas fa-tools"></i>
        </div>
        <h3 class="service-title">Custom Drilling</h3>
        <p class="service-description">Professional finger hole drilling tailored to your hand measurements for perfect fit and comfort.</p>
        <div class="service-price">₱1,200</div>
        <div class="service-duration">2-3 days completion</div>
        <button class="select-service-btn">Add to Cart</button>
      </div>

      <!-- Polishing Service -->
      <div class="service-card" data-service="polishing">
        <div class="service-icon">
          <i class="fa-solid fa-spray-can-sparkles"></i>
        </div>
        <h3 class="service-title">Professional Polishing</h3>
        <p class="service-description">Restore your ball's original shine and surface finish for optimal lane performance.</p>
        <div class="service-price">₱800</div>
        <div class="service-duration">1-2 days completion</div>
        <button class="select-service-btn">Add to Cart</button>
      </div>

      <!-- Sanding Service -->
      <div class="service-card" data-service="sanding">
        <div class="service-icon">
          <i class="fas fa-file-alt"></i>
        </div>
        <h3 class="service-title">Surface Sanding</h3>
        <p class="service-description">Custom surface adjustments with various grit levels to match your playing style and lane conditions.</p>
        <div class="service-price">₱600</div>
        <div class="service-duration">1 day completion</div>
        <button class="select-service-btn">Add to Cart</button>
      </div>

      <!-- Replacement Service -->
      <div class="service-card" data-service="replacement">
        <div class="service-icon">
          <i class="fas fa-sync-alt"></i>
        </div>
        <h3 class="service-title">Parts Replacement</h3>
        <p class="service-description">Complete replacement of inserts, slugs, and grips to restore your ball's performance.</p>
        <div class="service-price">₱ depends</div>
        <div class="service-duration">2-3 days completion</div>
        <button class="select-service-btn">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="cartModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add Service to Cart</h2>
        <button class="modal-close" id="closeModal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="service-info">
          <div class="service-name" id="modalServiceName">Custom Drilling</div>
          <div class="service-price-modal" id="modalServicePrice">₱1,200</div>
        </div>
        <p class="modal-question">Is the bowling ball from our shop?</p>
        <div class="ball-origin-options">
          <div class="origin-option" data-origin="yes">
            <span class="option-label">Yes</span>
            <span class="option-description">Purchased from our store</span>
          </div>
          <div class="origin-option" data-origin="no">
            <span class="option-label">No</span>
            <span class="option-description">From another store</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="modal-btn btn-cancel" id="cancelBtn">Cancel</button>
        <button class="modal-btn btn-confirm" id="confirmBtn" disabled>Add to Cart</button>
      </div>
    </div>
  </div>

  <script>
    // DOM Elements
    const modal = document.getElementById('cartModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    const cartNotification = document.getElementById('cartNotification');
    const serviceCards = document.querySelectorAll('.service-card');
    const originOptions = document.querySelectorAll('.origin-option');
    
    // Service information elements
    const modalServiceName = document.getElementById('modalServiceName');
    const modalServicePrice = document.getElementById('modalServicePrice');
    
    // Current selected service and origin
    let currentService = null;
    let selectedOrigin = null;
    
    // Service data
    const services = {
      drilling: {
        name: 'Custom Drilling',
        price: '₱1,200'
      },
      polishing: {
        name: 'Professional Polishing',
        price: '₱800'
      },
      sanding: {
        name: 'Surface Sanding',
        price: '₱600'
      },
      replacement: {
        name: 'Parts Replacement',
        price: '₱ depends'
      }
    };
    
    // Event Listeners for service cards
    serviceCards.forEach(card => {
      const serviceBtn = card.querySelector('.select-service-btn');
      const serviceType = card.getAttribute('data-service');
      
      serviceBtn.addEventListener('click', () => {
        currentService = serviceType;
        openModal(serviceType);
      });
    });
    
    // Event Listeners for origin options
    originOptions.forEach(option => {
      option.addEventListener('click', () => {
        // Remove selected class from all options
        originOptions.forEach(opt => opt.classList.remove('selected'));
        
        // Add selected class to clicked option
        option.classList.add('selected');
        
        // Enable confirm button
        confirmBtn.disabled = false;
        
        // Store selected origin
        selectedOrigin = option.getAttribute('data-origin');
      });
    });
    
    // Modal event listeners
    closeModal.addEventListener('click', closeModalFunc);
    cancelBtn.addEventListener('click', closeModalFunc);
    
    // Confirm button event listener
    confirmBtn.addEventListener('click', () => {
      if (currentService && selectedOrigin) {
        // addToCart(currentService, selectedOrigin);
        closeModalFunc();
      }
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeModalFunc();
      }
    });
    
    // Functions
    function openModal(serviceType) {
      // Update modal with service information
      modalServiceName.textContent = services[serviceType].name;
      modalServicePrice.textContent = services[serviceType].price;
      
      // Reset origin selection
      originOptions.forEach(opt => opt.classList.remove('selected'));
      confirmBtn.disabled = true;
      selectedOrigin = null;
      
      // Show modal
      modal.classList.add('active');
    }
    
    function closeModalFunc() {
      modal.classList.remove('active');
    }
    
    function addToCart(serviceType, origin) {
     
    }

  </script>

</body>
</html>
<?php include("footer.html")?>