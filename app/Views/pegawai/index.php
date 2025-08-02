<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Pegawai | Sistem Kinerja</title>

    <!-- Bootstrap CSS -->
    <style>
        .container-table {
            position: relative;
            background-color: white;
            height: 1044px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            padding: 20px;
        }

        table th {
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
            border-radius: 8px;
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
            right: -343%;
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
            right: -395%;
            top: 79px;
            z-index: 1;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
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

        h2 {
            position: relative;
            font-size: 35px;
            margin-top: -18px;
            color: #333;
            font-weight: bold;
            top: 48px;
            font-family: 'Arial Narrow', sans-serif;
            right: 0%;
        }

        .badge-success {
            position: relative;
            font-size: 14px;
            right: 31px;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <div class="container">
            <h2 class="mb-4">Data Pegawai</h2>

            <!-- Input Pencarian -->
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari Laporan..." onkeyup="searchTable()">
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Divisi</th>
                        <th>Tanggal Daftar</th>
                        <th>Status Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pegawaiTable">
                    <?php $no = 1; ?>
                    <?php foreach ($pegawai as $peg): ?>
                        <tr class="pegawai-row">
                            <td><?= $no++ ?></td>
                            <td><?= $peg['username'] ?></td>
                            <td><?= $peg['email'] ?></td>
                            <td><?= ucfirst($peg['role']) ?></td>
                            <td><?= $peg['divisi'] ?></td>
                            <td><?= $peg['created_at'] ?></td>
                            <td>
                                <?php if ($peg['approved'] == 'Pending'): ?>
                                    <!-- Tombol untuk mengganti status dari Pending ke Approved -->
                                    <a href="<?= site_url('pegawai/approve/' . $peg['id']) ?>"
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menyetujui pengguna ini?')">
                                        <i class="fas fa-check"></i> Setujui
                                    </a>
                                <?php elseif ($peg['approved'] == 'Approved'): ?>
                                    <!-- Status Approved sudah disetujui -->
                                    <span class="badge badge-success">Approved</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('pegawai/delete/' . $peg['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div id="pagination" class="pagination-container">
                <a href="javascript:void(0);" id="prevPage" onclick="changePage('prev')" class="disabled">&laquo; prev</a>
                <span id="pageNumbers">page</span>
                <a href="javascript:void(0);" id="nextPage" onclick="changePage('next')">next &raquo;</a>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const rowsPerPage = 10;
        const table = document.getElementById('pegawaiTable');
        let rows = Array.from(table.querySelectorAll('tr')); // Convert NodeList to Array
        let filteredRows = rows.slice(0); // Include all rows initially, excluding header.

        // Function to display filtered rows on the table
        function displayTable() {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = currentPage * rowsPerPage;

            // Menyembunyikan semua baris terlebih dahulu
            rows.forEach(row => row.style.display = 'none');

            // Menampilkan hanya baris yang sesuai dengan halaman saat ini
            filteredRows.slice(startIndex, endIndex).forEach((row, index) => {
                row.style.display = ''; // Menampilkan baris
                const rowNumber = startIndex + index + 1; // Menghitung nomor urut
                row.querySelector('td').textContent = rowNumber; // Memperbarui nomor urut pada kolom pertama
            });

            updatePagination(totalPages);
        }

        // Update pagination display
        function updatePagination(totalPages) {
            const pageNumbers = document.getElementById('pageNumbers');
            pageNumbers.innerHTML = `page ${currentPage}`;
        }

        // Change page for pagination
        function changePage(direction) {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (direction === 'prev' && currentPage > 1) {
                currentPage--; // Pergi ke halaman sebelumnya
            } else if (direction === 'next' && currentPage < totalPages) {
                currentPage++; // Pergi ke halaman berikutnya
            }
            displayTable(); // Menampilkan tabel dengan halaman yang diperbarui
        }

        // Function to filter rows based on search
        function searchTable() {
            const input = document.getElementById('searchInput').value.toUpperCase();
            const allRows = Array.from(table.querySelectorAll('tr')); // Mengambil semua baris, termasuk header
            filteredRows = allRows.slice(1).filter(row => { // Mulai dari baris pertama data (bukan header)
                let rowContainsSearchTerm = false;
                const cols = row.getElementsByTagName('td');
                // Loop melalui setiap kolom pada baris
                for (let i = 0; i < cols.length; i++) {
                    if (cols[i].textContent.toUpperCase().indexOf(input) > -1) {
                        rowContainsSearchTerm = true;
                        break; // Tidak perlu memeriksa kolom lainnya jika sudah ada yang cocok
                    }
                }
                return rowContainsSearchTerm;
            });

            // Mengatur ulang pagination dan menampilkan tabel
            currentPage = 1;
            displayTable();
        }

        // Initialize table display
        displayTable();
    </script>
</body>

</html>