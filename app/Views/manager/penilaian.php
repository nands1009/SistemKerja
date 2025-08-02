<div class="container-table">
    <div class="card-body">
        <h3>Penilaian Pegawai Divisi</h3>
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari Laporan..." onkeyup="searchTable()">

        </div>


        <?php if (session()->getFlashdata('message')) : ?>
            <div class="success-message">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>

        <table class="table-style">
            <thead>
                <tr>
                    <th>Nama Pegawai</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pegawais as $pegawai) : ?>
                    <tr>
                        <td><?= $pegawai['username'] ?></td>
                        <td>
                            <form action="/penilaian/savePenilaian" method="POST">
                                <input type="hidden" name="pegawai_id" value="<?= $pegawai['id'] ?>">
                                <input type="number" name="nilai" min="1" max="5" required>
                        </td>
                        <td>
                            <textarea name="catatan" placeholder="Masukkan catatan penilaian" required></textarea>
                        </td>
                        <td>
                            <button type="submit" class="btn-submit">Berikan Penilaian</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
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
</div>

<!-- CSS Styling -->
<style>
    button[type="submit"] {
        position: relative;
        width: 206px;
        height: 47px;
        top: 3px;
        right: -10px;
        background-color: #FF2E00;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 8px;
        box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
        transform: translateY(-4px);
        transition: transform 600ms cubic-bezier(0.3, 0.7, 0.4, 1);
    }

    button[type="submit"]:hover {
        background-color: #FF2E00;
        color: white;
        box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;

    }

    button[type="submit"]:active {
        background-color: #FF2E00;
        color: white;
        box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
        transform: translateY(3px);
        transform: translateY(-2px);
        transition: transform 34ms;
    }

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
        left: -65%;
        top: 14px;
        border-radius: 46px;
        border-color: white;
        background-color: white;
        box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    }

    i[class="fas fa-search"] {
        position: relative;
        right: 38px;
        top: 15px;
        z-index: 1;
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

    .card {
        margin-bottom: 20px;
        border-radius: 10px;
        border: 1px solid #ddd;
    }

    .card-body {
        position: relative;
        padding: 20px;
        border-radius: 10px;
        top: 28px;
    }

    h3 {
        position: relative;
        font-size: 35px;
        top: -15px;
        text-align: justify;
        margin-top: 17px;
        color: #333;
        font-family: 'Arial Narrow', sans-serif;
        font-weight: bold;
        right: -147px;
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
    }

    table.table-style th {
        background-color: #FF2E00;
        color: white;
        font-weight: bold;
        border-radius: 8px;
    }

    table.table-style tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table.table-style tr:hover {
        background-color: #f1f1f1;
    }

    .btn-submit {
        background-color: #2196F3;
        color: white;
        padding: 8px 15px;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-submit:hover {
        background-color: #0b7dda;
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

    textarea {
    width: 100%;
    padding: 4px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    }

    input[type="number"] {
        width: 50px;
        padding: 5px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>

<script>
    let currentPage = 1;
    const rowsPerPage = 5; // Jumlah baris per halaman
    let filteredRows = []; // Untuk menyimpan baris yang telah difilter

    /**
     * Fungsi untuk mencari data pegawai
     */
    function searchTable() {
        // Ambil nilai input search
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();

        // Ambil tabel dan baris-baris data
        const table = document.querySelector('.table-style');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const rows = tbody.getElementsByTagName('tr');

        // Reset filtered rows
        filteredRows = [];

        // Loop melalui semua baris dan cari yang cocok
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');

            // Cari di kolom nama (indeks 0)
            const nameCell = cells[0];
            if (nameCell) {
                const text = nameCell.textContent || nameCell.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    // Jika cocok, tampilkan baris dan tambahkan ke filteredRows
                    filteredRows.push(row);
                }
            }
        }

        // Reset ke halaman pertama dan tampilkan hasil
        currentPage = 1;
        displayTablePage(currentPage);
    }

    /**
     * Fungsi untuk menampilkan halaman tabel
     */
    function displayTablePage(page) {
        // Ambil tabel
        const table = document.querySelector('.table-style');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const rows = tbody.getElementsByTagName('tr');

        // Tentukan baris yang akan ditampilkan (semua atau yang difilter)
        const displayRows = filteredRows.length > 0 ? filteredRows : Array.from(rows);
        const totalRows = displayRows.length;

        // Hitung total halaman
        const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));

        // Validasi halaman saat ini
        if (page < 1) page = 1;
        if (page > totalPages) page = totalPages;
        currentPage = page;

        // Hitung indeks awal dan akhir untuk baris yang ditampilkan
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, totalRows);

        // Sembunyikan semua baris terlebih dahulu
        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = 'none';
        }

        // Tampilkan hanya baris untuk halaman saat ini
        for (let i = startIndex; i < endIndex; i++) {
            if (i < displayRows.length) {
                displayRows[i].style.display = '';
            }
        }

        // Perbarui tampilan pagination
        updatePagination(totalRows);
    }

    /**
     * Fungsi untuk memperbarui tampilan pagination dengan format "Page X"
     */
    function updatePagination(totalRows) {
        const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));
        const pageNumbersContainer = document.getElementById('pageNumbers');

        if (!pageNumbersContainer) return;

        // Bersihkan container
        pageNumbersContainer.innerHTML = '';

        // Tentukan rentang halaman yang ditampilkan
        const maxPageButtons = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPageButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxPageButtons - 1);

        // Sesuaikan startPage jika perlu
        if (endPage - startPage + 1 < maxPageButtons && startPage > 1) {
            startPage = Math.max(1, endPage - maxPageButtons + 1);
        }

        // Buat tombol untuk setiap halaman dengan format "Page X"


        // Perbarui status tombol Prev dan Next


        const pageNumbers = document.getElementById('pageNumbers');
        pageNumbers.innerHTML = `Page ${currentPage}`;
        // Jika tidak perlu info halaman, hapus bagian di bawah ini
        /* 
        // Tambahkan info halaman
        const pageInfo = document.createElement('span');
        pageInfo.textContent = ` ${currentPage} dari ${totalPages} `;
        pageInfo.style.margin = '0 10px';
        pageInfo.style.color = '#333';
        pageInfo.style.fontWeight = 'bold';
        
        pageNumbersContainer.appendChild(pageInfo);
        */
    }

    /**
     * Fungsi untuk berpindah halaman
     */
    function changePage(page) {
        const table = document.querySelector('.table-style');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const rows = tbody.getElementsByTagName('tr');
        const displayRows = filteredRows.length > 0 ? filteredRows : Array.from(rows);
        const totalPages = Math.max(1, Math.ceil(displayRows.length / rowsPerPage));

        if (page === 'prev') {
            currentPage = Math.max(1, currentPage - 1);
        } else if (page === 'next') {
            currentPage = Math.min(totalPages, currentPage + 1);
        } else {
            currentPage = parseInt(page);
        }

        displayTablePage(currentPage);
    }

    // Jalankan inisialisasi saat dokumen dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Bind event listener untuk search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            // Hapus event handler inline jika ada
            searchInput.removeAttribute('onkeyup');

            // Tambahkan event listener
            searchInput.addEventListener('keyup', searchTable);
        }

        // Ubah teks tombol prev/next jika diinginkan


        if (prevButton) {
            prevButton.innerHTML = '&laquo; Prev';
        }

        if (nextButton) {
            nextButton.innerHTML = 'Next &raquo;';
        }

        // Tampilkan halaman pertama
        displayTablePage(1);
    });

    // Fallback dengan window.onload
    window.onload = function() {
        // Pastikan event listener untuk search sudah terpasang
        const searchInput = document.getElementById('searchInput');
        if (searchInput && !searchInput._hasListener) {
            searchInput.removeAttribute('onkeyup');
            searchInput.addEventListener('keyup', searchTable);
            searchInput._hasListener = true;
        }

        // Tampilkan halaman pertama jika belum dilakukan
        if (document.querySelector('.table-style tbody tr')) {
            displayTablePage(1);
        }
    };
</script>