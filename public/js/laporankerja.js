document.addEventListener('DOMContentLoaded', function() {
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const selectedOption = document.getElementById('selected-option');
  
    // Add event listener to each dropdown item
    dropdownItems.forEach(item => {
      item.addEventListener('click', function() {
        // Set the text of the selected option
        selectedOption.textContent = `Selected: ${item.textContent}`;
        
        // Optional: Close the dropdown menu after selection
        const dropdownMenu = item.closest('.dropdown-menu');
        const dropdownToggle = dropdownMenu.previousElementSibling;
        dropdownToggle.setAttribute('aria-expanded', 'false');
        dropdownMenu.classList.remove('show');
      });
    });
  });