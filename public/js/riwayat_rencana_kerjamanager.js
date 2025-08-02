let currentPage = 1;
const rowsPerPage = 6;
let filteredRows = [];  // Store filtered rows

// Function to search through the table
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('laporanTable');
    const tr = table.getElementsByTagName('tr');

    filteredRows = [];  // Reset filteredRows every time search is applied

    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < td.length; j++) {
            if (td[j] && td[j].textContent.toUpperCase().includes(filter)) {
                found = true;
                break;
            }
        }
        if (found) {
            filteredRows.push(tr[i]);
            tr[i].style.display = "";  // Show the row found
        } else {
            tr[i].style.display = "none";  // Hide the row not found
        }
    }

    // If input is empty, show all rows
    if (filter === '') {
        for (let i = 1; i < tr.length; i++) {
            tr[i].style.display = "";  // Show all rows
        }
        filteredRows = []; // Reset filteredRows
    }

    // Reset to the first page after searching
    currentPage = 1;

    // Display the table for the current page
    displayTablePage(currentPage);
}

// Function to filter by status
function filterTable() {
    const filter = document.getElementById('statusFilter').value;
    const table = document.getElementById('laporanTable');
    const tr = table.getElementsByTagName('tr');

    filteredRows = [];  // Reset filteredRows every time filter is applied

    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        const status = td[7] ? td[7].textContent : '';  // Assuming status is in column 8 (index 7)
        if (filter === "" || status.indexOf(filter) > -1) {
            filteredRows.push(tr[i]);
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }

    // If filter is empty, show all rows
    if (filter === '') {
        for (let i = 1; i < tr.length; i++) {
            tr[i].style.display = "";  // Show all rows
        }
        filteredRows = []; // Reset filteredRows
    }

    // Reset to the first page after filter
    currentPage = 1;

    // Display the table for the current page
    displayTablePage(currentPage);
}

// Function to display the table page
function displayTablePage(page) {
    const table = document.getElementById('laporanTable');
    const rows = filteredRows.length > 0 ? filteredRows : table.getElementsByTagName('tr');
    const startIndex = (page - 1) * rowsPerPage;
    const endIndex = page * rowsPerPage;

    // Show rows for the current page
    for (let i = 1; i < rows.length; i++) {
        rows[i].style.display = (i >= startIndex && i < endIndex) ? '' : 'none';
    }

    // Update currentPage and pagination controls
    currentPage = page;
    updatePagination(filteredRows.length > 0 ? filteredRows.length : rows.length);
}

// Update pagination controls based on the total number of rows
function updatePagination(totalRows) {
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    const pageNumbersContainer = document.getElementById('pageNumbers');
    pageNumbersContainer.innerHTML = '';  // Clear existing pagination controls

    // Show current page number
    const currentPageButton = document.createElement('span');
    currentPageButton.textContent = `Page ${currentPage}`;
    pageNumbersContainer.appendChild(currentPageButton);
}

// Handle page change (previous, next, or specific page)
function changePage(page) {
    if (page === 'prev') {
        currentPage = Math.max(1, currentPage - 1);
    } else if (page === 'next') {
        currentPage = Math.min(Math.ceil(document.getElementById('laporanTable').rows.length / rowsPerPage), currentPage + 1);
    } else {
        currentPage = page;
    }
    displayTablePage(currentPage);
}

// Initialize table page on load
window.onload = () => {
    displayTablePage(currentPage);  // Only shows the first page when loading
};
