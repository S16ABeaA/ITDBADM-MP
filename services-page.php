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
</body>
</html>
<?php include("footer.html")?>