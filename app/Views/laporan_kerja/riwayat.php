<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan Kerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-table {
            position: relative;
            background-color: white;
            height: 1110px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        h2 {
            position: relative;
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: 170px;
        }

        .table-container {
            margin: 20px auto;
            max-width: 90%;
        }

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-container input[type=text] {
            position: relative;
            height: 12px;
            width: 150px;
            left: 478%;
            top: 26px;
            border-color: white;
            background-color: white;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        input[type="text"],
        select {
            padding: 8px;
            width: 250px;
            border-radius: 53px;
            color: black;
        }

        select {
            margin-left: 10px;
        }

        table {
            position: relative;
            top: 15px;
            width: 143%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: white;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            border-radius: 10px;
            text-align: left;
            right: 230px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        th {
            background-color: #FF2E00;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            color: #555;
            text-align: left;
        }

        /* Pagination Style */
        .pagination-container {
            text-align: center;
            margin-top: 20px;
        }

        .pagination-container a {
            padding: 10px;
            margin: 0 5px;
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        .pagination-container a:hover {
            background-color: #f1f1f1;
            border-radius: 5px;
        }

        .pagination-container span {
            padding: 10px;
            margin: 0 5px;
        }


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
            top: -102px;
            right: -58px;
            padding: 20px;
            border: 1px solid #888;
            width: 130%;
            max-width: 800px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        div [class="modal-contentDetail"] {
            background-color: #0056b3;
        }

        .modal .modal-contentDetail {
            position: relative;
            background-color: #fff;
            margin: 15% auto;
            top: -102px;
            right: -58px;
            padding: 20px;
            border: 1px solid #888;
            width: 130%;
            max-width: 800px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .modal h2 {
            top: 6px;
            right: -250px;
            color: black;
        }

        /* Style untuk tombol close (x) */
        .close {
            color: black;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
            cursor: pointer;
        }

        /* Hover effect untuk tombol close */
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Style untuk konten dalam modal */
        #modal-body {
            font-family: Arial, sans-serif;
            line-height: 1.6;

        }

        .approved {
            background-color: #28a745;
            /* Warna hijau untuk approved */
            color: white;
        }

        .rejected {
            background-color: #dc3545;
            /* Warna merah untuk rejected */
            color: white;
        }

        .pending {
            background-color: #ffc107;
            /* Warna kuning untuk pending */
            color: black;
        }

        i[class="fas fa-search"] {
            position: absolute;
            top: 133px;
            z-index: 1;
            right: 40px;
        }

        .pagination-container {
            text-align: center;
            margin-top: 30px;
        }

        .pagination-container a {
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin: 0px -11px;
            background-color: #FF2E00;
        }

        .pagination-container a:hover {
            background-color: #FF2E00;
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin: 0px -11px;
            background-color: #FF2E00;
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
            margin-bottom: -48px;

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
            top: 58px;
            transform: translateY(-50%);
            color: black;
            right: -212%;
            font-size: 13px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <div class="container">
            <h2>Riwayat Laporan Kerja</h2>
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
                <table id="laporanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Lengkap</th>
                            <th>Judul Laporan</th>
                            <th>Jabatan</th>
                            <th>Divisi</th>
                            <th>Status Project</th>
                            <th>Status Approvel</th>
                            <th>Catatan Penolakan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($laporan)) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($laporan as $row) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($row['tanggal']) ?></td>
                                    <td><?= esc($row['username']) ?></td>
                                    <td><?= esc($row['judul']) ?></td>
                                    <td><?= esc($row['role']) ?></td>
                                    <td><?= esc($row['divisi']) ?></td>
                                    <td><?= esc($row['status']) ?></td>

                                    <td class="<?= $row['status_approval']; ?>"><?= ucfirst($row['status_approval']); ?></td>
                                    </td>

                                    <td><?= esc($row['catatan_penolakan']) ?></td>

                                    <td>

                                        <a href="javascript:void(0);" onclick="openModalDetail(<?= $row['id']; ?>)" class="btn btn-info">DETAILS</a>
                                        <a href="javascript:void(0);" onclick="openModal(<?= $row['id']; ?>)" class="btn btn-warning">EDIT</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                        <?php endif; ?><!-- Data Tabel akan diisi disini oleh PHP -->
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
    </div>

    <!-- Modal -->

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <!-- Modal for Details -->
    <div id="myModalDetails" class="modal">
        <div class="modal-contentDetail">
            <span class="close" onclick="closeModalDetail()">&times;</span>
            <h2>Details Laporan Kerja</h2>
            <div id="modal-body-detail"></div>
        </div>
    </div>

    <script>
        // Open Modal and load content dynamically
        function openModal(id) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "<?= site_url('laporan_kerja/edit/') ?>" + id, true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    // Inject the content of the modal body
                    document.getElementById("modal-body").innerHTML = xhr.responseText;
                    document.getElementById("myModal").style.display = "block"; // Menampilkan modal utama
                }
            };
            xhr.send();
        }

        // Close Modal
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        // Open Modal for Details and load content dynamically
        function openModalDetail(id) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "<?= site_url('laporan_kerja/details/') ?>" + id, true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    // Inject the content of the modal body (detail)
                    document.getElementById("modal-body-detail").innerHTML = xhr.responseText;
                    document.getElementById("myModalDetails").style.display = "block"; // Menampilkan modal detail
                }
            };
            xhr.send();
        }

        // Close Modal for Details
        function closeModalDetail() {
            document.getElementById("myModalDetails").style.display = "none";
        }
    </script>


    </script>
    <script src="/js/laporan_kerja.js"></script>

</body>

</html>