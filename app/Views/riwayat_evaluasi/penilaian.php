<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Evaluasi Kinerja</title>
    <style>
        /* General body and container styles */

        /* Card styles */
        .container-table {
            position: relative;
            background-color: white;
            height: 1042px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .card-body {
            position: relative;
            padding: 20px;
            border-radius: 10px;
            top: -29px;
        }

        /* Title styles */
        .card-title {
            color: #333;
            font-size: 40px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        th,
        td {
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        /* Header styles */
        th {
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            height: 45px;
            font-size: 15px;
        }

        /* Row styles */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Empty data message */
        td[colspan="6"] {
            text-align: center;
            font-style: italic;
            color: #888;
            padding: 20px 0;
        }

        /* Button styles */
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #218838;
        }

        /* Additional styles for links */
        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            color: #0056b3;
        }

        h5[class="card-title"] {
            position: relative;
            font-size: 35px;
            top: 8px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: -2px;

        }

        /* Responsive design for mobile screens */
        @media (max-width: 768px) {
            table {
                font-size: 14px;
                padding: 10px;
            }

            th,
            td {
                padding: 8px 10px;
            }

            /* Adjust search input */
            .search-container {
                display: flex;
                justify-content: flex-end;
                margin-bottom: 10px;
            }
        }

        /* Pagination container */
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

        .search-container {
            position: relative;
            height: 97px;
            width: 276px;
            text-align: center;
            right: -133rem;
            margin-bottom: -41px;
        }

        .search-container input[type=text] {
            position: relative;
            height: 22px;
            width: 142px;
            left: -11%;
            top: 14px;
            border-radius: 46px;
            border-color: white;
            background-color: white;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        i[class="fas fa-search"] {
            position: relative;
            right: -112px;
            top: 15px;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <div class="card-body">
            <h5 class="card-title">Penilaian Kinerja</h5>

            <!-- Search functionality (aligned to the right) -->
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari Laporan..." onkeyup="searchTable()">

            </div>

            <!-- Table -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal dan Waktu Penilaian</th>
                        <th>Nama</th>
                        <th>Penilaian</th>
                        <th>Evaluasi Dari</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <!-- Dynamic Table Content -->
                    <?php if (!empty($penilaian_pegawai)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($penilaian_pegawai as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($row['tanggal_penilaian']) ?></td>
                                <td><?= esc($row['nama_pegawai']) ?></td>
                                <td><?= esc($row['nilai']) ?></td>
                                <td><?= esc($row['nama_evaluator']) ?></td>
                                <td><?= esc($row['catatan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Data penilaian belum tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination Controls (centered) -->
            <div id="pagination" class="pagination-container">
                <a href="javascript:void(0);" onclick="changePage('prev')">&laquo; Prev</a>
                <span id="pageNumbers"></span>
                <a href="javascript:void(0);" onclick="changePage('next')">Next &raquo;</a>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const recordsPerPage = 7;

        // Sample data (replace this with your actual PHP data)
        const penilaianData = <?php echo json_encode($penilaian_pegawai); ?>;

        function renderTable(filteredData = penilaianData) {
            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = '';
            const start = (currentPage - 1) * recordsPerPage;
            const end = start + recordsPerPage;
            const paginatedData = filteredData.slice(start, end);

            if (paginatedData.length > 0) {
                paginatedData.forEach((row, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${start + index + 1}</td>
                        <td>${row.tanggal_penilaian}</td>
                        <td>${row.nama_pegawai}</td>
                        <td>${row.nilai}</td>
                        <td>${row.nama_evaluator}</td>
                        <td>${row.catatan}</td>
                    `;
                    tableBody.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="6" class="text-center">Data penilaian belum tersedia.</td>`;
                tableBody.appendChild(tr);
            }

            updatePagination(filteredData);
        }

        function changePage(direction) {
            const totalPages = Math.ceil(penilaianData.length / recordsPerPage);
            if (direction === 'prev' && currentPage > 1) {
                currentPage--;
            } else if (direction === 'next' && currentPage < totalPages) {
                currentPage++;
            }
            renderTable();
        }

        function updatePagination(filteredData) {
            const totalPages = Math.ceil(filteredData.length / recordsPerPage);
            const pageNumbers = document.getElementById('pageNumbers');
            pageNumbers.innerHTML = `Page ${currentPage}`;
        }

        function searchTable() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const filteredData = penilaianData.filter(row =>
                row.nama_pegawai.toLowerCase().includes(query) ||
                row.nilai.toLowerCase().includes(query)
            );
            renderTable(filteredData);
        }

        // Initial rendering of the table
        renderTable();
    </script>
</body>

</html>