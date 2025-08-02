let currentPage = 1;
const rowsPerPage = 7;

// Function to search through the table
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('laporanTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < td.length; j++) {
            if (td[j] && td[j].textContent.toUpperCase().includes(filter)) {
                found = true;
                break;
            }
        }
        tr[i].style.display = found ? "" : "none";
    }

    // Update pagination after filtering
    updatePagination();
}

// Function to filter by status
function filterTable() {
    const filter = document.getElementById('statusFilter').value;
    const table = document.getElementById('laporanTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        const status = td[7] ? td[7].textContent : '';  // Assuming status is in the 8th column (index 7)
        tr[i].style.display = (filter === "" || status.indexOf(filter) > -1) ? "" : "none";
    }

    // Update pagination after filtering
    updatePagination();
}

// Function for pagination logic
function displayTablePage(page) {
    const table = document.getElementById('laporanTable');
    const rows = table.getElementsByTagName('tr');
    const startIndex = (page - 1) * rowsPerPage + 1;
    const endIndex = page * rowsPerPage + 1;

    for (let i = 1; i < rows.length; i++) {
        rows[i].style.display = (i >= startIndex && i < endIndex) ? '' : 'none';
    }

    // Update currentPage and pagination controls
    currentPage = page;
    updatePagination(rows.length - 1);
}

// Update pagination controls based on the total number of rows
function updatePagination(totalRows) {
    const pageCount = Math.ceil(totalRows / rowsPerPage);
    const pageNumbersContainer = document.getElementById('pageNumbers');
    pageNumbersContainer.innerHTML = '';  // Clear existing pagination controls

    // Display only current page (e.g., "Page 1")
    const pageText = document.createElement('span');
    pageText.textContent = `Page ${currentPage}`;
    pageNumbersContainer.appendChild(pageText);
}

// Handle page change (previous, next or specific page)
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
    displayTablePage(currentPage);
};
