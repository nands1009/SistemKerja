<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            position: relative;
            padding: 20px;
            border-radius: 10px;
            top: 28px;
        }

        .table-style {
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
        }

        .search-container {
            position: relative;
    height: 97px;
    width: 276px;
    text-align: center;
    right: -94rem;
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

        .container-table {
            position: relative;
            background-color: white;
            height: 1044px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            visibility: visible
            
        }

        h3 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }


        table.table-style th,
        table.table-style td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table.table-style th {
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            height: 45px;
            font-size: 15px;
        }

        table.table-style tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table.table-style tr:hover {
            background-color: #f1f1f1;
        }

        table.table-style td {
            color: #333;
        }

        /* Styling for pagination */

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
 h3{
    position: relative;
    font-size: 35px;
    text-align: justify;
    margin-top: 4px;
    color: #333;
    font-family: 'Arial Narrow', sans-serif;
    font-weight: bold;
    right: 5px;
 }
         /* Modal Styles */
         .modal {
            display: none;
            /* Awalnya modal disembunyikan */
            position: fixed;
            z-index: 1;
            /* Modal akan tampil di atas konten lainnya */
            left: 0;
            top: 0;
            width: 100%;
            /* Lebar penuh layar */
            height: 100%;
            /* Tinggi penuh layar */

            background-color: rgba(0, 0, 0, 0.4);
            /* Latar belakang gelap dengan transparansi */
        }

        /* Style untuk konten modal */
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
        /* Button-container adjustment */
    </style>
</head>

<body>
    <div class="container-table">
        <div class="container">
            <h3>Riwayat Rencana Kerja</h3>
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari Laporan..." onkeyup="searchTable()">
                <select id="statusFilter" onchange="filterTable()">
                    <option value="">Pilih Status</option>
                    <option value="Approved">Approved</option>
                    <option value="Pending">Pending</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>

            <table class="table-style" id="laporanTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Tanggal</th>
                        <th>Aksi</th> <!-- Kolom untuk tombol Edit -->
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($rencana_kerja as $rencana) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $rencana['user_name'] ?></td>
                            <td><?= $rencana['judul'] ?></td>
                            <td><?= $rencana['deskripsi'] ?></td>
                            <td><?= $rencana['tanggal'] ?></td>
                            <td>
                                <?php if (session()->get('user_id') == $rencana['user_id']): ?>
                                    <a href="javascript:void(0);" onclick="openModal(<?= $rencana['id']; ?>)" class="btn btn-warning">EDIT</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Pagination Controls -->
            <div id="pagination" class="pagination-container">
                <a href="javascript:void(0);" onclick="changePage('prev')">&laquo; Prev</a>
                <span id="pageNumbers"></span>
                <a href="javascript:void(0);" onclick="changePage('next')">Next &raquo;</a>
            </div>
        </div>
    </div>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>
    <script>
        function openModal(id) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "<?= site_url('/rencana-kerja-hrd/edit/') ?>" + id, true);
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

    </script>


    <script src="/js/riwayat_rencana_kerjamanager.js"></script>


</body>

</html>