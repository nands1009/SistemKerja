<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Laporan Kerja</title>
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

        h1 {
            position: relative;
            font-size: 35px;
            margin-top: 8px;
            color: #333;
            font-weight: bold;
            top: 10px;
            font-family: 'Arial Narrow', sans-serif;
            right: 14%;
        }

        .table-container {
            overflow-y: auto;
            overflow-x: auto;
            width: 129%;
            margin: 15px auto;
            border-radius: 8px;
            height: 695px;
            position: relative;
            right: 169px;
        }


        table {
            width: 129%;
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
            right: -545%;
            top: 37px;
            height: 22px;
            width: 142px;
            border-radius: 46px;
            border-color: white;
            background-color: white;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        i[class="fas fa-search"] {
            position: relative;
            right: -453%;
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

        #divisi {
            width: 145px;
        }

        #manager {
            width: 191px;
        }

        .search-container {
            display: flex;
            justify-content: left;
            gap: 5px;
            padding: 17px;
            border-radius: 12px;
            width: 483px;
            position: relative;
            left: -188px;
            text-align: center;
            margin-bottom: -25px;

        }

        .search-container .filter-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Date inputs */
        .search-container input[type="date"] {
            padding: 10px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            background: #fff;
            color: #495057;
            transition: all 0.3s ease;
            min-width: 139px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 13px;
        }

        .search-container input[type="date"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            transform: translateY(-1px);
        }

        .search-container input[type="date"]:hover {
            border-color: #80bdff;
        }

        /* Right section - Search box */
        .search-section {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .search-section label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            white-space: nowrap;
        }

        .search-section input[type="text"] {
            padding: 10px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            min-width: 200px;
            background: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .search-section input[type="text"]:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
            transform: translateY(-1px);
        }

        .search-section input[type="text"]:hover {
            border-color: #80e5a3;
        }

        /* Button Styles */
        .btn {
            padding: 7px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: 2px solid transparent;
            width: 94px;
        }

        .btn-primary:hover {

            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }

        /* Date Presets Styles */
        .date-presets {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
            width: 86px;
        }

        .btn-sm:hover {
            border-color: #adb5bd;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: #212529;
        }

        .btn-sm:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Special styling for Reset button */
        .btn-sm:last-child {
            color: white;
            border-color: #dc3545;
            margin-left: auto;
        }

        .btn-sm:last-child:hover {
            background: linear-gradient(135deg, #c82333 0%, #a02622 100%);
            border-color: #bd2130;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .search-container i {
            position: absolute;
            top: 71px;
            transform: translateY(-50%);
            color: black;
            right: -213%;
            font-size: 13px;
            display: block;
        }
        .btn-danger{
            background: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <div class="container">
            <h1>Rekap Laporan Kerja Seluruh Pegawai</h1>
            <div class="search-container">
                <!-- Date Range Filter seperti di screenshot -->
                <input type="date" id="startDate" placeholder="dd/mm/yyyy">
                <input type="date" id="endDate" placeholder="dd/mm/yyyy">
                <button type="button" onclick="filterByDateRange()" class="btn btn-primary">Filter</button>
                <button type="button" onclick="resetFilters()" class="btn btn-sm">Reset</button>

                <!-- Search Box -->
                <div style="float: right;">
                    <input type="text" id="searchInput" placeholder="Cari Rencana..." onkeyup="searchTable()"><i class="fas fa-search"></i></input>
                </div>
            </div>

            <div class="table-container">
                <table id="reportTable">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th id="namaPegawai">Nama Pegawai</th>
                            <th>Divisi</th>
                            <th id="manager">Nama Manager</th>
                            <th>Judul</th>
                            <th>Dokumen</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Tanggal Laporan</th>
                            <th>Catatan Penolakan</th>
                            <th>Status Approval</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($laporan as $item): ?>

                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['username']; ?></td>
                                <td><?= $item['divisi']; ?></td>
                                <td><?= $item['name']; ?></td>
                                <td><?= $item['judul']; ?></td>
                                <td>
                                    <?php if ($item['foto_dokumen']): ?>
                                        <a href="<?= base_url('uploads/' . $item['foto_dokumen']); ?>" target="_blank">Lihat Dokumen</a>
                                    <?php else: ?>
                                        Tidak Ada Dokumen
                                    <?php endif; ?>
                                </td>
                                <td><?= $item['deskripsi']; ?></td>
                                <td><?= $item['status']; ?></td>
                                <td><?= $item['tanggal']; ?></td>
                                <td><?= $item['catatan_penolakan']; ?></td>
                                <td><?= $item['status_approval']; ?></td>
                                <!-- Tambahkan di dalam kolom Aksi pada tabel -->
                                <td>
                                    <a href="<?= site_url('admin/deleteAllData/' . $item['id']) ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="pagination-container">
                <a href="javascript:void(0);" onclick="changePage('prev')">&laquo; Prev</a>
                <span id="pageNumbers"></span>
                <a href="javascript:void(0);" onclick="changePage('next')">Next &raquo;</a>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentPage = 1;
        const rowsPerPage = 5; // Changed from 1 to 5 for better UX
        let allTableRows = [];
        let filteredRows = [];

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            initializeTable();
            bindEvents();
        });

        // Initialize table and store all rows
        function initializeTable() {
            const table = document.getElementById('reportTable');
            if (!table) {
                console.error('Table not found during initialization');
                return;
            }

            // Store all rows (excluding header)
            const rows = table.getElementsByTagName('tr');
            allTableRows = Array.from(rows).slice(1); // Skip header row
            filteredRows = [...allTableRows]; // Copy all rows initially

            // Display first page
            currentPage = 1;
            displayCurrentPage();
            updatePaginationControls();
        }

        // Display current page
        function displayCurrentPage() {
            const table = document.getElementById('reportTable');
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
                }
            }

            updatePaginationControls();
        }

        // Update pagination controls
        function updatePaginationControls() {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            const pageNumbers = document.getElementById('pageNumbers');

            // Update page info
            if (pageNumbers) {
                pageNumbers.textContent = `Page ${currentPage || 1}`;
            }
        }

        // Handle page changes
        function changePage(direction) {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

            if (direction === 'prev' && currentPage > 1) {
                currentPage--;
            } else if (direction === 'next' && currentPage < totalPages) {
                currentPage++;
            }

            displayCurrentPage();
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('searchInput').value = '';

            filteredRows = [...allTableRows];
            currentPage = 1;
            displayCurrentPage();
        }

        // Search function
        function searchTable() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.toLowerCase().trim();

            if (!searchTerm) {
                filteredRows = [...allTableRows];
            } else {
                filteredRows = allTableRows.filter(row => {
                    const cells = row.getElementsByTagName('td');
                    for (let i = 0; i < cells.length - 1; i++) {
                        const cellText = cells[i].textContent.toLowerCase();
                        if (cellText.includes(searchTerm)) {
                            return true;
                        }
                    }
                    return false;
                });
            }

            currentPage = 1;
            displayCurrentPage();
        }

        // Filter by date range
        function filterByDateRange() {
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            filteredRows = allTableRows.filter(row => {
                const cells = row.getElementsByTagName('td');
                const dateCell = cells[8].textContent.trim(); // Assume date is in the 8th column

                const rowDate = new Date(dateCell);
                return (!startDate || rowDate >= new Date(startDate)) && (!endDate || rowDate <= new Date(endDate));
            });

            currentPage = 1;
            displayCurrentPage();
        }
    </script>
</body>

</html>