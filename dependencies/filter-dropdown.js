// Dropdown functionality
const availabilityBtn = document.getElementById('availabilityBtn');
const availabilityDropdown = document.getElementById('availabilityDropdown');
const priceBtn = document.getElementById('priceBtn');
const priceDropdown = document.getElementById('priceDropdown');
const brandBtn = document.getElementById('brandBtn');
const brandDropdown = document.getElementById('brandDropdown');

// Modal functionality
const showModalBtn = document.getElementById('showModalBtn');
const filterModal = document.getElementById('filterModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelBtn = document.getElementById('cancelBtn');
const applyBtn = document.getElementById('applyBtn');
    
// Toggle dropdowns
function toggleDropdown(button, dropdown) {
// Close all other dropdowns
document.querySelectorAll('.dropdown-content').forEach(dd => {
    if (dd !== dropdown) {
    dd.classList.remove('show');
    }
});
    
// Remove active class from all buttons
document.querySelectorAll('.filter-button').forEach(btn => {
    if (btn !== button) {
    btn.classList.remove('active');
    }
});
    
// Toggle current dropdown
dropdown.classList.toggle('show');
button.classList.toggle('active');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
// Close dropdowns if clicking outside
if (!event.target.matches('.filter-button') && !event.target.closest('.dropdown-content')) {
    document.querySelectorAll('.dropdown-content').forEach(dropdown => {
    dropdown.classList.remove('show');
    });
    document.querySelectorAll('.filter-button').forEach(button => {
    button.classList.remove('active');
    });
}
});

// Show modal
function showModal() {
if (filterModal) {
    filterModal.classList.add('show');
}
}

// Hide modal
function hideModal() {
if (filterModal) {
    filterModal.classList.remove('show');
}
}

// Event listeners for dropdowns
if (availabilityBtn && availabilityDropdown) {
availabilityBtn.addEventListener('click', () => toggleDropdown(availabilityBtn, availabilityDropdown));
}

if (priceBtn && priceDropdown) {
priceBtn.addEventListener('click', () => toggleDropdown(priceBtn, priceDropdown));
}

if (brandBtn && brandDropdown) {
brandBtn.addEventListener('click', () => toggleDropdown(brandBtn, brandDropdown));
}

// Event listeners for modal
if (showModalBtn) {
showModalBtn.addEventListener('click', showModal);
}

if (closeModalBtn) {
closeModalBtn.addEventListener('click', hideModal);
}

if (cancelBtn) {
cancelBtn.addEventListener('click', hideModal);
}

if (applyBtn) {
applyBtn.addEventListener('click', hideModal);
}

// Hide modal when clicking outside
if (filterModal) {
filterModal.addEventListener('click', function(event) {
    if (event.target === filterModal) {
    hideModal();
    }
});
}

// Also close modal with Escape key
document.addEventListener('keydown', function(event) {
if (event.key === 'Escape' && filterModal && filterModal.classList.contains('show')) {
    hideModal();
}
});

// Add this to your existing JavaScript
const sortBtn = document.getElementById('sortBtn');
const sortDropdown = document.getElementById('sortDropdown');

if (sortBtn && sortDropdown) {
sortBtn.addEventListener('click', () => toggleDropdown(sortBtn, sortDropdown));
    
// Handle sort selection
sortDropdown.addEventListener('click', function(event) {
    if (event.target.classList.contains('dropdown-option')) {
    const selectedValue = event.target.getAttribute('data-value');
    const selectedText = event.target.textContent;
            
    // Update button text to show selected sort option
    sortBtn.innerHTML = `${selectedText} <span>▼</span>`;
            
    // Close dropdown
    sortDropdown.classList.remove('show');
    sortBtn.classList.remove('active');
            
    //check
    console.log('Selected sort:', selectedValue);
    }
});
}