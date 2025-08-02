<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Surat Peringatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .card-body {
            position: relative;
            padding: 20px;
            border-radius: 10px;
            top: -40px;
        }

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

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }

        th {
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
            border-radius: 8px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 5px;
            text-decoration: none;
            background-color: #FF2E00;
            color: white;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #FF2E00;
            color: white;
        }

        .btn:focus {
            background-color: #FF2E00;
            color: white;
        }


        .search-container {
            position: relative;
            height: 117px;
            width: 276px;
            text-align: center;
            right: -126rem;
            margin-bottom: -41px;
            top: 37px;
        }


        .search-container input[type=text] {
            position: relative;
            height: 17px;
            width: 138px;
            left: -13%;
            top: 26px;
            border-radius: 48px;
            border-color: white;
            background-color: white;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        i[class="fas fa-search"] {
            position: relative;
            right: -103px;
            top: 26px;
            z-index: 1;

        }

        h1 {
            position: relative;
            font-size: 35px;
            top: 25px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: -74px;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <div class="card-body">
            <h1>Riwayat Surat Peringatan</h1>
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari Laporan..." onkeyup="searchTable()">
            </div>
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Penerima</th>
                        <th>Pengirim</th>
                        <th>Tanggal Menerima</th>
                        <th>Jenis SP</th>
                        <th>Detail SP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sppegawai)): ?>
                        <?php $no = 1;
                        foreach ($sppegawai as $sp): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($sp['username']) ?></td>
                                <td><?= esc($sp['manager_id']) ?></td>
                                <td><?= esc($sp['updated_at']) ?></td>
                                <td><?= esc($sp['file_name']) ?></td>
                                <td>
                                    <a href="<?= base_url('sp/download_pdf/' . $sp['file_name']) ?>" class="btn">Download PDF</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-data">Belum ada surat peringatan yang dikirim.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div id="pagination" class="pagination-container">
                <a href="javascript:void(0);" onclick="changePage('prev')">&laquo; Prev</a>
                <span id="pageNumbers"></span>
                <a href="javascript:void(0);" onclick="changePage('next')">Next &raquo;</a>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const rowsPerPage = 5;
        const table = document.getElementById('dataTable');
        const rows = table.getElementsByTagName('tr');
        const pageNumbers = document.getElementById('pageNumbers');

        function searchTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toUpperCase();
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName("td");
                let rowMatches = false;
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            rowMatches = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = rowMatches ? "" : "none";
            }
        }

        function changePage(direction) {
            if (direction === 'next') {
                currentPage++;
            } else if (direction === 'prev') {
                currentPage--;
            }
            displayPage();
        }

        function displayPage() {
            const startRow = (currentPage - 1) * rowsPerPage + 1;
            const endRow = startRow + rowsPerPage - 1;
            let pageCount = 0;

            for (let i = 1; i < rows.length; i++) {
                if (i >= startRow && i <= endRow) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }

                if (i > endRow) {
                    break;
                }
            }

            pageNumbers.textContent = `Page ${currentPage}`;
        }

        displayPage(); // Initial page display
    </script>
</body>

</html>