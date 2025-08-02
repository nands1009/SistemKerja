<div class="container-table">
<div class="container">
<h3>Riwayat Penilaian Kinerja</h3>

<?php if (session()->getFlashdata('message')) : ?>
    <div class="success-message">
        <?= session()->getFlashdata('message'); ?>
    </div>
<?php endif; ?>

<table class="table-style" id="penilaianTable">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Penilaian</th>
            <th>Pegawai</th>
            <th>Penilai</th>
            <th>Catatan</th>
            <th>Skor</th>
        </tr>
    </thead>
    <tbody>
         <?php $no = 1; ?>
        <?php foreach ($riwayat as $item) : ?>
            <tr> <td><?= $no++ ?></td>
                <td><?= $item['tanggal_penilaian'] ?></td>
                <td><?= $item['username'] ?></td>
                <td><?= $item['name_manager'] ?></td>
                <td><?= $item['catatan'] ?></td>
                <td><?= $item['nilai'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div id="pagination" class="pagination-container">
    <a href="javascript:void(0);" onclick="changePage('prev', 'penilaian')">&laquo; Prev</a>
    <span id="pageNumberspenilaian"></span>
    <a href="javascript:void(0);" onclick="changePage('next', 'penilaian')">Next &raquo;</a>
</div>
</div>
</div>


<!-- CSS Styling -->
<style>
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

    h3 {
        position: relative;
    font-size: 35px;
    text-align: center;
    margin-bottom: 115px;
    color: #333;
    font-family: 'Arial Narrow', sans-serif;
    font-weight: bold;
    right: 301px;
    top: 49px;
    }

    table.table-style {
        width: 80%;
        margin: 0 auto;
        border-collapse: collapse;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    table.table-style th,
    table.table-style td {
        padding: 12px 15px;
    text-align: left;
    border: 1px solid #ddd;
    border-radius: 8px;
}
    

    table.table-style th {
        background-color: #FF2E00;
        color: white;
        font-weight: bold;
        
    }

    table.table-style tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table.table-style tr:hover {
        background-color: #f1f1f1;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
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

<script>
    let currentPagePenilaian = 1; // Ubah menjadi currentPagePenilaian
const rowsPerPage = 5;

// Fungsi untuk mengubah halaman
function changePage(direction, tableName) {
    if (tableName === 'penilaian') {
        if (direction === 'prev' && currentPagePenilaian > 1) {
            currentPagePenilaian--;
        } else if (direction === 'next') {
            currentPagePenilaian++;
        }
        showPagePenilaian();
    }
}

// Fungsi untuk menampilkan halaman Penilaian
function showPagePenilaian() {
    const table = document.getElementById('penilaianTable');
    const rows = table.querySelectorAll('tbody tr');
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    const startIndex = (currentPagePenilaian - 1) * rowsPerPage;
    const endIndex = currentPagePenilaian * rowsPerPage;

    // Menyembunyikan semua baris terlebih dahulu
    rows.forEach(row => row.style.display = 'none');

    // Menampilkan baris sesuai halaman
    for (let i = startIndex; i < endIndex; i++) {
        if (rows[i]) {
            rows[i].style.display = 'table-row';
        }
    }

    // Menampilkan nomor halaman untuk tabel Penilaian
    const pageNumbers = document.getElementById('pageNumberspenilaian');
    pageNumbers.innerHTML = `Page ${currentPagePenilaian}`;
}

// Inisialisasi pagination ketika halaman pertama kali dimuat
window.onload = () => {
    showPagePenilaian();
};


</script>