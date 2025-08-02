let currentPage = 1;
const rowsPerPage = 5; // Set items per page

// Function to search through the table
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('rencanaTable');
    const tr = table.getElementsByTagName('tr');

    let visibleRows = 0;
    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < td.length; j++) {
            if (td[j] && td[j].textContent.toUpperCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        tr[i].style.display = found ? "" : "none";
        if (found) visibleRows++;
    }

    updatePagination(visibleRows);
}

// Function for pagination logic
function displayTablePage(page) {
    const table = document.getElementById('rencanaTable');
    const rows = table.getElementsByTagName('tr');
    const startIndex = (page - 1) * rowsPerPage + 1;
    const endIndex = page * rowsPerPage + 1;

    for (let i = 1; i < rows.length; i++) {
        rows[i].style.display = (i >= startIndex && i < endIndex) ? '' : 'none';
    }

    // Update currentPage variable and page numbers display
    currentPage = page;
    updatePagination(rows.length - 1);
}

// Update pagination controls based on the total number of rows
function updatePagination(totalRows) {
    const pageCount = Math.ceil(totalRows / rowsPerPage);
    const pageNumbersContainer = document.getElementById('pageNumbers');
    pageNumbersContainer.innerHTML = ''; // Clear current pagination

    // Display current page as "Page X"
    const pageText = document.createElement('span');
    pageText.textContent = `Page ${currentPage}`;
    pageNumbersContainer.appendChild(pageText);
}

// Handle previous/next page navigation
function changePage(page) {
    if (page === 'prev') {
        currentPage = Math.max(1, currentPage - 1);
    } else if (page === 'next') {
        const table = document.getElementById('rencanaTable');
        const totalRows = table.getElementsByTagName('tr').length - 1; // Exclude header row
        const pageCount = Math.ceil(totalRows / rowsPerPage);
        currentPage = Math.min(pageCount, currentPage + 1);
    } else {
        currentPage = page;
    }

    displayTablePage(currentPage);
    const pageNumbersText = document.getElementById('pageNumbers');
    pageNumbersText.textContent = `Page ${currentPage}`;
}

// Initialize table page on load
window.onload = () => {
    displayTablePage(currentPage);
};
