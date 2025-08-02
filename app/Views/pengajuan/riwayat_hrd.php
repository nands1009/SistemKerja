<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan HRD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-table {
            position: relative;
            background-color: white;
            height: 1101px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        h1 {
            position: relative;
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: -108px;
        }

        h2 {
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 51px;
            right: -116px;
            position: relative;
        }

        /* Table Styles */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
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

        /* Action Links */
        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Button styles */
        .action-buttons {
            display: flex;
            justify-content: space-around;
        }

        .action-buttons a {
            padding: 5px 15px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .action-buttons a:hover {
            background-color: #0056b3;
        }

        /* Delete Confirmation Dialog */
        .delete-button {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        /* File Upload Styling */
        .file-upload-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .file-upload-form label {
            font-weight: bold;
        }

        .file-upload-form input[type="file"] {
            padding: 10px;
            font-size: 14px;
        }

        .file-upload-form button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .file-upload-form button:hover {
            background-color: #218838;
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
            margin: 0px 15px;
            background-color: #FF2E00;
        }

        .pagination-container a:hover {
            background-color: #FF2E00;
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #FF2E00;
        }

        .pagination-container span {
            padding: 10px;
            margin: 0 5px;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <div class="container">
            <h1>Approvel Pengajuan</h1>

            <!-- Success Flash Message -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success'); ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <!-- Penghargaan Table -->
                <h2>Penghargaan</h2>
                <table id="penghargaanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Manager</th>
                            <th>Jenis Penghargaan</th>
                            <th>Alasan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($riwayat_penghargaan as $penghargaan): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $penghargaan['username']; ?></td>
                                <td><?= $penghargaan['name']; ?></td>
                                <td><?= $penghargaan['jenis_penghargaan']; ?></td>
                                <td><?= $penghargaan['alasan']; ?></td>
                                <td><?= $penghargaan['created_at']; ?></td>
                                <td><?= $penghargaan['status']; ?></td>
                                <td class="action-buttons">
                                    <?php if ($penghargaan['status'] == 'Pending'): ?>
                                        <a href="<?= site_url('/pengajuan/approve_direksi/' . $penghargaan['id'] . '/penghargaan'); ?>">Mengajukan ke Direksi</a>
                                        <!-- Form Penolakan -->
                                        <form action="<?= site_url('/pengajuan/reject/' . $penghargaan['id'] . '/penghargaan'); ?>" method="post">
                                            <textarea name="catatan_penolakan" rows="3" placeholder="Alasan Penolakan" required></textarea><br>
                                            <button type="submit" class="delete-button">Tolak Pengajuan</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div id="pagination" class="pagination-container">
    <a href="javascript:void(0);" onclick="changePage('prev', 'penghargaan')">&laquo; Prev</a>
    <span id="pageNumbersPenghargaan"></span>
    <a href="javascript:void(0);" onclick="changePage('next', 'penghargaan')">Next &raquo;</a>
</div>


        <!-- SP Table -->
        <h2>SP</h2>
        <table id="spTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pegawai</th>
                    <th>Manager</th>
                    <th>Alasan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($riwayat_sp as $sp): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $sp['username']; ?></td>
                        <td><?= $sp['name']; ?></td>
                        <td><?= $sp['alasan']; ?></td>
                        <td><?= $sp['created_at']; ?></td>
                        <td><?= $sp['status']; ?></td>
                        <td class="action-buttons">
                            <?php if ($sp['status'] == 'Pending'): ?>
                                <a href="<?= site_url('/pengajuan/approve_direksi/' . $sp['id'] . '/sp'); ?>">Mengajukan ke Direksi</a>
                                <form action="<?= site_url('/pengajuan/reject-hrd/' . $sp['id'] . '/sp'); ?>" method="post">
                                    <textarea name="catatan_penolakan" rows="3" placeholder="Alasan Penolakan" required></textarea><br>
                                    <button type="submit" class="delete-button">Tolak Pengajuan</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div id="pagination" class="pagination-container">
    <a href="javascript:void(0);" onclick="changePage('prev', 'sp')">&laquo; Prev</a>
    <span id="pageNumbersSP"></span>
    <a href="javascript:void(0);" onclick="changePage('next', 'sp')">Next &raquo;</a>
</div>
</div>
</div>
</div>

<script>
let currentPagePenghargaan = 1;
let currentPageSP = 1;
const rowsPerPage = 3; // Menentukan jumlah baris per halaman

// Fungsi untuk mengubah halaman
function changePage(direction, tableName) {
    if (tableName === 'penghargaan') {
        if (direction === 'prev' && currentPagePenghargaan > 1) {
            currentPagePenghargaan--;
        } else if (direction === 'next') {
            currentPagePenghargaan++;
        }
        showPagePenghargaan();
    } else if (tableName === 'sp') {
        if (direction === 'prev' && currentPageSP > 1) {
            currentPageSP--;
        } else if (direction === 'next') {
            currentPageSP++;
        }
        showPageSP();
    }
}

// Fungsi untuk menampilkan halaman Penghargaan
function showPagePenghargaan() {
    const table = document.getElementById('penghargaanTable');
    const rows = table.querySelectorAll('tbody tr');
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    const startIndex = (currentPagePenghargaan - 1) * rowsPerPage;
    const endIndex = currentPagePenghargaan * rowsPerPage;

    // Menyembunyikan semua baris terlebih dahulu
    rows.forEach(row => row.style.display = 'none');

    // Menampilkan baris sesuai halaman
    for (let i = startIndex; i < endIndex; i++) {
        if (rows[i]) {
            rows[i].style.display = 'table-row';
        }
    }

    // Menampilkan nomor halaman untuk tabel Penghargaan
    const pageNumbers = document.getElementById('pageNumbersPenghargaan');
    pageNumbers.innerHTML = `Page ${currentPagePenghargaan}`;
}

// Fungsi untuk menampilkan halaman SP
function showPageSP() {
    const table = document.getElementById('spTable');
    const rows = table.querySelectorAll('tbody tr');
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    const startIndex = (currentPageSP - 1) * rowsPerPage;
    const endIndex = currentPageSP * rowsPerPage;

    // Menyembunyikan semua baris terlebih dahulu
    rows.forEach(row => row.style.display = 'none');

    // Menampilkan baris sesuai halaman
    for (let i = startIndex; i < endIndex; i++) {
        if (rows[i]) {
            rows[i].style.display = 'table-row';
        }
    }

    // Menampilkan nomor halaman untuk tabel SP
    const pageNumbers = document.getElementById('pageNumbersSP');
    pageNumbers.innerHTML = `Page ${currentPageSP}`;
}

// Inisialisasi pagination ketika halaman pertama kali dimuat
window.onload = () => {
    showPagePenghargaan();
    showPageSP();
};
</script>
</body>

</html>