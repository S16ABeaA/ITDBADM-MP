<?php
if (!isset($availability)) $availability = '';
if (!isset($price)) $price = '';
if (!isset($brand)) $brand = '';
if (!isset($brands)) $brands = [];
?>

<!-- Availability Filter -->
<div class="filter-dropdown">
  <button type="button" class="filter-button" id="availabilityBtn">
    Availability <span>▼</span>
  </button>
  <div class="dropdown-content" id="availabilityDropdown">
    <div class="dropdown-option">
      <label>
        <input type="radio" name="availability" value="" class="checkbox" <?= $availability === '' ? 'checked' : '' ?>>
        All
      </label>
    </div>
    <div class="dropdown-option">
      <label>
        <input type="radio" name="availability" value="in" class="checkbox" <?= $availability === 'in' ? 'checked' : '' ?>>
        In Stock
      </label>
    </div>
    <div class="dropdown-option">
      <label>
        <input type="radio" name="availability" value="out" class="checkbox" <?= $availability === 'out' ? 'checked' : '' ?>>
        Out of Stock
      </label>
    </div>
  </div>
</div>

<!-- Price Filter -->
<div class="filter-dropdown">
  <button type="button" class="filter-button" id="priceBtn">
    Price (₱) <span>▼</span>
  </button>
  <div class="dropdown-content" id="priceDropdown">
    <?php
      $priceOptions = [
        '' => 'All',
        'under1k' => 'Under 1k',
        '1k-3k' => '1k - 3k',
        '3k-5k' => '3k - 5k',
        'over5k' => 'Over 5k'
      ];

      foreach ($priceOptions as $val => $label) {
        $checked = ($price === $val) ? 'checked' : '';
        echo '<div class="dropdown-option"><label><input type="radio" name="price" value="'. $val .'" class="checkbox" '. $checked .'>'. $label .'</label></div>';
      }
    ?>
  </div>
</div>

<!-- Brand Filter -->
<div class="filter-dropdown">
  <button type="button" class="filter-button" id="brandBtn">
    Brand <span>▼</span>
  </button>
  <div class="dropdown-content" id="brandDropdown">
    <div class="dropdown-option">
      <label>
        <input type="radio" name="brand" value="all" class="checkbox" <?= $brand === '' || $brand === 'all' ? 'checked' : '' ?>>
        All
      </label>
    </div>
    <?php
      foreach ($brands as $b) {
        $checked = ($brand === $b) ? 'checked' : '';
        echo '<div class="dropdown-option"><label><input type="radio" name="brand" value="'. htmlspecialchars($b) .'" class="checkbox" '. $checked .'>'. htmlspecialchars($b) .'</label></div>';
      }
    ?>
  </div>
</div>