<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rencana Kerja Pegawai</title>

    <style>
        /* Styling untuk judul halaman */
        h2 {
            font-size: 40px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            margin-left: -3px;

        }

        /* Styling untuk tombol Tambah Rencana Kerja */
        .btn-primary {
            background-color: #FF2E00;
            border-color: #FF2E00;
            margin-top: 55px;
            margin-left: 0px;
        }

        .btn-primary:hover {
            background-color: #ee6e73;
            border-color: #ee6e73;
        }



        /* Styling untuk tabel */
        table {
            width: 100%;
            margin-top: 32px;
            border-collapse: collapse;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            border-radius: 10px;
            text-align: left;
        }

        th,
        td {
            padding: 15px;
            text-align: left;

            border-radius: 8px;
        }

        th {
            background-color: #FF2E00;
            color: white;
            border-radius: 8px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            color: #495057;
        }

        /* Styling untuk tombol Edit */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        /* Styling untuk pesan jika data kosong */
        .text-center {
            font-size: 1.2rem;
            color: #6c757d;
        }

        /* Styling untuk seluruh container */
        .container-table {
            position: relative;
            background-color: white;
            width: 95%;
            height: 98rem;
            top: 51px;
            margin-left: 3%;
            border-radius: 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        /* Modal styling */
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
            height: 630px;
            right: -58px;
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



        a[class="btn"] {
            position: absolute;
            top: 108px;
            height: 34px;
            width: 179px;
            right: 1188px;
            background-color: #FF2E00;
            color: white;
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            border-radius: 8px;
            box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
            transform: translateY(-4px);
            transition: transform 600ms cubic-bezier(0.3, 0.7, 0.4, 1);

        }


        a[class="btn"]:hover,
        a[class="btn"]:focus {
            background-color: #FF2E00;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
        }

        a[class="btn"]:active {
            background-color: #FF2E00;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
            transform: translateY(3px);
            transform: translateY(-2px);
            transition: transform 34ms;
        }

        /* Styling untuk pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            font-family: Arial, sans-serif;
        }

        .pagination-container a {
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #FF2E00;
            color: white;
            text-decoration: none;
            font-size: 14px;
            margin: 0 5px;
            transition: background-color 0.3s ease;
        }

        .pagination-container a:hover {
            background-color: #FF2E00;
            color: white;
        }

   
        

        .pagination-container a:disabled {
            background-color: #ccc;
            color: #666;
            pointer-events: none;
        }

.search-container i {
    position: absolute;
    top: 132px;
    transform: translateY(-50%);
    color: black;
    right: 210px;
    font-size: 13px;
    z-index: 1;
    display: block;
}


.search-container input {
    position: relative;
    width: 161px;
    height: 27px;
    padding: 10px 4px 11px 7px;
    font-size: 16px;

    border-radius: 70px;
    outline: none;
    left: 101rem;
    top: 28px;
    border: 1px solid white;

    transition: border-color 0.3s ease;
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
}

.search-container input:focus {
 
    box-shadow: 0 1px 0 0 #ff5722;
    border: 1px solid white;
    background-color: #fff; /* Change border color when input is focused */
}

.search-container input::placeholder {

    background-color: #fff;
}
    </style>
</head>

<body>

    <div class="container-table">
        <div class="container">
            <h2>Rencana Kerja</h2>
            <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="textindex" id="searchInput" placeholder="Cari Rencana..." onkeyup="searchTable()">
            <a href="javascript:void(0);" onclick="openModalcreate()" class="btn">Tambah Rencana Kerja</a>
            <table class="table table-bordered" id="rencanaTable">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rencana_kerja)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        foreach ($rencana_kerja as $rencana): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($rencana['judul']) ?></td>
                                <td><?= esc($rencana['tanggal']) ?></td>
                                <td><a href="javascript:void(0);" onclick="openModaldetail(<?= $rencana['id']; ?>)" class="btn btn-warning">Detail</a>
                                    <a href="javascript:void(0);" onclick="openModal(<?= $rencana['id']; ?>)" class="btn btn-warning">EDIT</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div id="pagination" class="pagination-container">
                <a href="javascript:void(0);" onclick="changePage('prev')">&laquo; Prev</a>
                <span id="pageNumbers"></span>
                <a href="javascript:void(0);" onclick="changePage('next')">Next &raquo;</a>
            </div>
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

    <div id="myModalCreate" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModalCreate()">&times;</span>
            <div id="modal-body-create"></div> <!-- Unique ID for modal body -->
        </div>
    </div>

    <div id="myModaldetail" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModalDetail()">&times;</span>
            <div id="modal-body-detail"></div> <!-- Unique ID for modal body -->
        </div>
    </div>


    <script>
        // Open Modal and load content dynamically
        function openModal(id) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?= site_url('rencana_kerja/edit/') ?>" + id, true);
        xhr.onload = function() {
            if (xhr.status == 200) {
                document.getElementById("modal-body").innerHTML = xhr.responseText;
                document.getElementById("myModal").style.display = "block"; // Show modal
            }
        };
        xhr.send();
    }

    // Close Edit Modal
    function closeModal() {
        document.getElementById("myModal").style.display = "none";
    }

    // Open Create Modal and load content dynamically
    function openModalcreate() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?= site_url('rencana_kerja/add/') ?>", true); // Adjusted URL for create
        xhr.onload = function() {
            if (xhr.status == 200) {
                document.getElementById("modal-body-create").innerHTML = xhr.responseText;
                document.getElementById("myModalCreate").style.display = "block"; // Show modal
            }
        };
        xhr.send();
    }

    // Close Create Modal
    function closeModalCreate() {
        document.getElementById("myModalCreate").style.display = "none";
    }

    // Open Detail Modal and load content dynamically
    function openModaldetail(id) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?= site_url('rencana_kerja/detail/') ?>" + id, true); // Adjusted URL for detail
        xhr.onload = function() {
            if (xhr.status == 200) {
                document.getElementById("modal-body-detail").innerHTML = xhr.responseText;
                document.getElementById("myModaldetail").style.display = "block"; // Show modal
            }
        };
        xhr.send();
    }

    // Close Detail Modal
    function closeModalDetail() {
        document.getElementById("myModaldetail").style.display = "none";
    }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="/js/rencang_kerja.js"></script>

</body>

</html>