// Global variables
let currentPage = 1;
const rowsPerPage = 3; // Changed from 1 to 10 for better UX
let allTableRows = [];
let filteredRows = [];

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeTable();
    bindEvents();
});

// Initialize table and store all rows
function initializeTable() {
    const table = getTable();
    if (!table) {
        console.error('Table not found during initialization');
        return;
    }
    
    // Store all rows (excluding header)
    const rows = table.getElementsByTagName('tr');
    allTableRows = Array.from(rows).slice(1); // Skip header row
    filteredRows = [...allTableRows]; // Copy all rows initially
    
    console.log('Table initialized with', allTableRows.length, 'rows');
    
    // Display first page
    currentPage = 1;
    displayCurrentPage();
    updatePaginationControls();
}

// Get table element with fallback options
function getTable() {
    return document.getElementById('laporanTable') || 
           document.querySelector('table') || 
           document.querySelector('.table');
}

// Bind events to elements
function bindEvents() {
    // Search input with debounce
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchTable, 300);
        });
    }
    
    // Date inputs
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    if (startDate && endDate) {
        startDate.addEventListener('change', filterByDateRange);
        endDate.addEventListener('change', filterByDateRange);
    }
    
    // Status filter
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', filterByStatus);
    }
    
    console.log('Events bound successfully');
}

// Main filter function by date range
function filterByDateRange() {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    if (!startDateInput || !endDateInput) {
        console.error('Date input fields not found');
        return;
    }
    
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    
    console.log('Filtering by date range:', startDate, 'to', endDate);
    
    // Validate date range
    if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
        alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
        return;
    }
    
    filteredRows = allTableRows.filter(row => {
        const cells = row.getElementsByTagName('td');
        if (cells.length < 2) return false;
        
        const dateCell = cells[1].textContent.trim(); // Date column
        
        // If no date filter is applied, show all rows
        if (!startDate && !endDate) return true;
        
        // Parse date from various formats
        const rowDate = parseTableDate(dateCell);
        if (!rowDate) return false;
        
        // Check date range
        if (startDate && rowDate < new Date(startDate)) return false;
        if (endDate && rowDate > new Date(endDate)) return false;
        
        return true;
    });
    
    console.log('Filtered rows:', filteredRows.length);
    resetToFirstPage();
}

// Parse date from table cell
function parseTableDate(dateString) {
    if (!dateString) return null;
    
    // Handle format: "2025-05-27 18:15:26" or "2025-05-27"
    const dateMatch = dateString.match(/^(\d{4}-\d{2}-\d{2})/);
    if (dateMatch) {
        return new Date(dateMatch[1]);
    }
    
    // Handle format: "27/05/2025"
    const ddmmyyyyMatch = dateString.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
    if (ddmmyyyyMatch) {
        return new Date(ddmmyyyyMatch[3], ddmmyyyyMatch[2] - 1, ddmmyyyyMatch[1]);
    }
    
    return null;
}

// Filter by year
function filterByYear() {
    const yearFilter = document.getElementById('yearFilter');
    if (!yearFilter) return;
    
    const year = yearFilter.value;
    console.log('Filtering by year:', year);
    
    if (year) {
        // Set date range for the whole year
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        
        if (startDate && endDate) {
            startDate.value = `${year}-01-01`;
            endDate.value = `${year}-12-31`;
            filterByDateRange();
        }
    } else {
        resetFilters();
    }
}

// Search function
function searchTable() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    const searchTerm = searchInput.value.toLowerCase().trim();
    console.log('Searching for:', searchTerm);
    
    if (!searchTerm) {
        // If search is empty, reset to all rows (but maintain other filters)
        applyAllFilters();
        return;
    }
    
    filteredRows = filteredRows.filter(row => {
        const cells = row.getElementsByTagName('td');
        
        // Search in all visible columns (exclude actions column)
        for (let i = 0; i < cells.length - 1; i++) {
            const cellText = cells[i].textContent.toLowerCase();
            if (cellText.includes(searchTerm)) {
                return true;
            }
        }
        return false;
    });
    
    console.log('Search results:', filteredRows.length, 'rows');
    resetToFirstPage();
}

// Filter by status
function filterByStatus() {
    const statusFilter = document.getElementById('statusFilter');
    if (!statusFilter) return;
    
    const status = statusFilter.value;
    console.log('Filtering by status:', status);
    
    if (!status) {
        applyAllFilters();
        return;
    }
    
    filteredRows = allTableRows.filter(row => {
        const cells = row.getElementsByTagName('td');
        // Check status columns (adjust indices based on your table structure)
        const statusProject = cells[6] ? cells[6].textContent.trim() : '';
        const statusApproval = cells[7] ? cells[7].textContent.trim() : '';
        
        return statusProject.toLowerCase().includes(status.toLowerCase()) ||
               statusApproval.toLowerCase().includes(status.toLowerCase());
    });
    
    resetToFirstPage();
}

// Apply all filters (useful when one filter is cleared)
function applyAllFilters() {
    // Start with all rows
    filteredRows = [...allTableRows];
    
    // Apply date filter
    const startDate = document.getElementById('startDate')?.value;
    const endDate = document.getElementById('endDate')?.value;
    
    if (startDate || endDate) {
        filteredRows = filteredRows.filter(row => {
            const cells = row.getElementsByTagName('td');
            const dateCell = cells[1]?.textContent.trim();
            const rowDate = parseTableDate(dateCell);
            
            if (!rowDate) return false;
            if (startDate && rowDate < new Date(startDate)) return false;
            if (endDate && rowDate > new Date(endDate)) return false;
            
            return true;
        });
    }
    
    // Apply status filter
    const statusFilter = document.getElementById('statusFilter')?.value;
    if (statusFilter) {
        filteredRows = filteredRows.filter(row => {
            const cells = row.getElementsByTagName('td');
            const statusProject = cells[6]?.textContent.trim() || '';
            const statusApproval = cells[7]?.textContent.trim() || '';
            
            return statusProject.toLowerCase().includes(statusFilter.toLowerCase()) ||
                   statusApproval.toLowerCase().includes(statusFilter.toLowerCase());
        });
    }
    
    // Apply search filter
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase().trim();
    if (searchTerm) {
        filteredRows = filteredRows.filter(row => {
            const cells = row.getElementsByTagName('td');
            for (let i = 0; i < cells.length - 1; i++) {
                if (cells[i].textContent.toLowerCase().includes(searchTerm)) {
                    return true;
                }
            }
            return false;
        });
    }
    
    resetToFirstPage();
}

// Display current page
function displayCurrentPage() {
    const table = getTable();
    if (!table) return;
    
    // Hide all data rows first
    allTableRows.forEach(row => {
        row.style.display = 'none';
    });
    
    // Calculate pagination
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = Math.min(startIndex + rowsPerPage, filteredRows.length);
    
    // Show rows for current page
    for (let i = startIndex; i < endIndex; i++) {
        if (filteredRows[i]) {
            filteredRows[i].style.display = '';
            
            // Update row number
            const numberCell = filteredRows[i].getElementsByTagName('td')[0];
            if (numberCell) {
                numberCell.textContent = i + 1;
            }
        }
    }
    
    // Show "no data" message if needed
    if (filteredRows.length === 0) {
        showNoDataMessage();
    }
    
    updatePaginationControls();
}

// Show no data message
function showNoDataMessage() {
    const table = getTable();
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    if (!tbody) return;
    
    // Remove existing no-data row
    const existingNoData = tbody.querySelector('.no-data-row');
    if (existingNoData) existingNoData.remove();
    
    // Add no data row
    const noDataRow = document.createElement('tr');
    noDataRow.className = 'no-data-row';
    noDataRow.innerHTML = `

    `;
    tbody.appendChild(noDataRow);
}

// Update pagination controls
function updatePaginationControls() {
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
    const pageNumbers = document.getElementById('pageNumbers');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    
    // Update page info
    if (pageNumbers) {
        pageNumbers.textContent = `Page ${currentPage|| 1}`;
    }
    
    // Update button states
    if (prevButton) {
        prevButton.disabled = currentPage <= 1;
        prevButton.style.opacity = currentPage <= 1 ? '0.5' : '1';
        prevButton.style.cursor = currentPage <= 1 ? 'not-allowed' : 'pointer';
    }
    
    if (nextButton) {
        nextButton.disabled = currentPage >= totalPages;
        nextButton.style.opacity = currentPage >= totalPages ? '0.5' : '1';
        nextButton.style.cursor = currentPage >= totalPages ? 'not-allowed' : 'pointer';
    }
    
    console.log(`Page ${currentPage} of ${totalPages}, showing ${filteredRows.length} rows`);
}

// Handle page changes
function changePage(direction) {
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
    
    if (direction === 'prev' && currentPage > 1) {
        currentPage--;
    } else if (direction === 'next' && currentPage < totalPages) {
        currentPage++;
    } else if (typeof direction === 'number') {
        currentPage = Math.max(1, Math.min(direction, totalPages));
    }
    
    displayCurrentPage();
}

// Reset to first page
function resetToFirstPage() {
    currentPage = 1;
    displayCurrentPage();
}

// Quick date range presets
function setDateRange(preset) {
    const today = new Date();
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    if (!startDateInput || !endDateInput) {
        console.error('Date input fields not found');
        return;
    }
    
    let startDate, endDate;
    
    switch(preset) {
        case 'today':
            startDate = new Date(today);
            endDate = new Date(today);
            break;
            
        case 'thisWeek':
            startDate = new Date(today);
            startDate.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
            endDate = new Date(today);
            break;
            
        case 'thisMonth':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today);
            break;
            
        case 'thisYear':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today);
            break;
            
        default:
            console.error('Unknown preset:', preset);
            return;
    }
    
    // Format dates for input (YYYY-MM-DD)
    startDateInput.value = formatDateForInput(startDate);
    endDateInput.value = formatDateForInput(endDate);
    
    // Apply filter
    filterByDateRange();
}

// Format date for input field
function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Reset all filters
function resetFilters() {
    console.log('Resetting all filters');
    
    // Clear all input values
    const inputs = [
        'searchInput', 'statusFilter', 'yearFilter', 
        'startDate', 'endDate', 'dateFilter'
    ];
    
    inputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) input.value = '';
    });
    
    // Reset filtered rows to all rows
    filteredRows = [...allTableRows];
    
    // Reset to first page and display
    resetToFirstPage();
    
    console.log('All filters reset, showing', filteredRows.length, 'rows');
}

// Legacy functions for backward compatibility
function filterByDate() {
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter && dateFilter.value) {
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        
        if (startDate && endDate) {
            startDate.value = dateFilter.value;
            endDate.value = dateFilter.value;
            filterByDateRange();
        }
    }
}

function filterTable() {
    filterByStatus();
}

function displayTablePage(page) {
    currentPage = page;
    displayCurrentPage();
}

function displayFilteredTablePage(page) {
    currentPage = page;
    displayCurrentPage();
}

function updatePagination(totalRows) {
    updatePaginationControls();
}

// Modal functions (placeholders)


// Utility functions
function exportTableToCSV() {
    const table = getTable();
    if (!table) return;
    
    let csv = [];
    const headerRow = table.querySelector('thead tr');
    if (headerRow) {
        const headers = Array.from(headerRow.cells).map(cell => 
            `"${cell.textContent.trim()}"`
        );
        csv.push(headers.join(','));
    }
    
    // Export only filtered rows
    filteredRows.forEach(row => {
        const cells = Array.from(row.cells).map(cell => 
            `"${cell.textContent.trim()}"`
        );
        csv.push(cells.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'laporan_kerja.csv';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Initialize on window load (fallback)
window.addEventListener('load', function() {
    if (allTableRows.length === 0) {
        initializeTable();
    }
});