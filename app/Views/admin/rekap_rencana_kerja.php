<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Rencana Kerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-table {
            position: relative;
            background-color: white;
            height: 1044px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            visibility: visible;
            padding: 20px;
        }

        h3 {
            position: relative;
    font-size: 35px;
    margin-top: -18px;
    color: #333;
    font-weight: bold;
    top: 48px;
    font-family: 'Arial Narrow', sans-serif;
    right: 0%;
        }

        .table-container {
            overflow-y: auto;
            overflow-x: auto;
            width: 100%;
            margin: 15px auto;
            border-radius: 8px;
            height: 695px; /* Set fixed height for the scroll */
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px 15px;
    text-align: justify;
    border: 1px solid #ddd;
    vertical-align: super;
        }

        table th {
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
            border-radius: 8px;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td {
            color: #333;
        }

        .search-container {
            position: relative;
            height: 97px;
            width: 276px;
            text-align: center;
            margin-bottom: 20px;
        }

        .search-container input[type=text] {
            position: relative;
    right: -471%;
    top: 79px;
    height: 22px;
    width: 142px;
    border-radius: 46px;
    border-color: white;
    background-color: white;
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        i[class="fas fa-search"] {
            position: relative;

    right: -522%;
    top: 79px;
    z-index: 1;
}

        .pagination-container {
            text-align: center;
            margin-top: 20px;
        }

        .pagination-container a {
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin: 0 5px;
            background-color: #FF2E00;
        }

        .pagination-container a:hover {
            background-color: #FF2E00;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 130%;
            max-width: 800px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .close {
            color: black;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        table thead th {
    position: sticky;
    top: 0;
    background-color: #FF2E00;
    z-index: 30;
    white-space: nowrap;
}
#namaPegawai {
  width: 191px;
}

#divisi{
    width: 145px;
}

#manager{
    width: 191px;
}
    </style>
</head>

<body>
<div class="container-table">
    <h3>Rekap Riwayat Rencana Kerja</h3>

    <!-- Pencarian dan Filter -->
    <div class="search-container">
    <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari Rencana..." onkeyup="searchTable()">
        <select id="statusFilter" onchange="filterTable()">
            <option value="">Pilih Status</option>
            <option value="Approved">Approved</option>
            <option value="Pending">Pending</option>
            <option value="Rejected">Rejected</option>
        </select>
    </div>

    <!-- Tabel Data -->
    <div class="table-container">
        <table class="table-style" id="laporanTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th id="namaPegawai">Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Tanggal</th>
                    <th id="divisi">Divisi</th>
                    <th id="manager">Manager</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php $no = 1; ?>
                <?php foreach ($rencanaKerja as $item): ?>
                    <tr class="tableRow">
                        <td><?= $no++; ?></td>
                        <td><?= $item['username']; ?></td>
                        <td><?= $item['role']; ?></td>
                        <td><?= $item['judul']; ?></td>
                        <td><?= $item['deskripsi']; ?></td>
                        <td><?= $item['tanggal']; ?></td>
                        <td><?= $item['divisi']; ?></td>
                        <td><?= $item['name']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div id="pagination" class="pagination-container">
        <a href="javascript:void(0);" onclick="changePage('prev')">&laquo; Prev</a>
        <span id="pageNumbers"></span>
        <a href="javascript:void(0);" onclick="changePage('next')">Next &raquo;</a>
    </div>
</div>

<script>
    // Global variables for pagination
    const rowsPerPage = 7;
    let currentPage = 1;
    let filteredRows = [...document.querySelectorAll('.tableRow')];

    // Function to update the table display based on current page
    function displayTable() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // Hide all rows first
        filteredRows.forEach(row => row.style.display = 'none');

        // Show the rows for the current page
        filteredRows.slice(start, end).forEach(row => row.style.display = '');

        // Update the pagination controls
        updatePagination();
    }

    // Function to update the pagination controls
    function updatePagination() {
        const pageCount = Math.ceil(filteredRows.length / rowsPerPage);
        const pageNumbersContainer = document.getElementById('pageNumbers');
        pageNumbersContainer.innerHTML = '';

      
        const currentPageButton = document.createElement('span');
    currentPageButton.textContent = `Page ${currentPage}`;
    pageNumbersContainer.appendChild(currentPageButton);
}

    

    // Function to change the page (prev/next or specific page)
    function changePage(page) {
        if (page === 'prev' && currentPage > 1) {
            currentPage--;
        } else if (page === 'next' && currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
            currentPage++;
        } else if (typeof page === '') {
            currentPage = page;
        }
        displayTable();
    }

    // Function to search the table
    function searchTable() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        filteredRows = [...document.querySelectorAll('.tableRow')].filter(row => {
            return row.innerText.toLowerCase().includes(query);
        });
        currentPage = 1; // Reset to first page on search
        displayTable();
    }

    // Function to filter the table based on status
    function filterTable() {
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        filteredRows = [...document.querySelectorAll('.tableRow')].filter(row => {
            const status = row.cells[6].innerText.toLowerCase(); // Assuming status is in the 7th column
            return statusFilter === '' || status === statusFilter;
        });
        currentPage = 1; // Reset to first page on filter
        displayTable();
    }

    // Initial display setup
    displayTable();
</script>

</body>

</html>
