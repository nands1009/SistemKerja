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

        h2 {
            position: relative;
            font-size: 35px;
            text-align: justify;
            margin-top: -12px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: 1px;
            top: 41px;
        }


        .table-container {
            overflow-y: auto;
            overflow-x: auto;
            width: 100%;
            margin: 15px auto;
            border-radius: 8px;
            height: 695px;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px 16px;
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
            left: 50%;
            transform: translateX(-50%);
        }

        .search-container input[type=text] {
            height: 22px;
            width: 142px;
            border-radius: 46px;
            border-color: white;
            background-color: white;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            padding: 8px;
            position: relative;
            top: 69px;
            right: -240%;
        }

        i[class="fas fa-search"] {
            position: absolute;
            top: 81px;
            left: 308%;
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

        table thead th {
            position: sticky;
            top: 0;
            background-color: #FF2E00;
            z-index: 30;
            white-space: nowrap;
        }

        #nama {

            width: 16%;
        }

        #judul {
            width: 20%;
        }

        #tanggal {
            width: 11%;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <h2>Approval Laporan Kerja Pegawai</h2>
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari Laporan..." onkeyup="searchTable()">
        </div>
        <div class="table-container">
            <table id="reportTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th id="tanggal">Tanggal & Jam Laporan</th>
                        <th id="nama">Nama Pegawai</th>
                        <th>Divisi</th>
                        <th id="judul">Judul</th>
                        <th>Dokumen</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($laporan_kerja as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $item['tanggal']; ?></td>
                            <td><?= $item['username']; ?></td>
                            <td><?= $item['divisi']; ?></td>
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
                            <td>
                            
                                <?php if ($item['status_approval'] == 'Pending'): ?>
                                    <!-- Button Setujui (hanya muncul jika status adalah 'pending') -->
                                    <a href="<?= site_url('approval/approvemanager/' . $item['laporan_ID']); ?>"
                                        class="btn btn-success"
                                        id="approve-button-<?= $item['laporan_ID']; ?>"
                                        onclick="approveAction(<?= $item['laporan_ID']; ?>)">Setujui</a>

                                    <!-- Form Penolakan (hanya muncul jika status adalah 'pending') -->
                                    <form action="<?= site_url('approval/reject/' . $item['laporan_ID']); ?>" method="post" id="reject-form-<?= $item['laporan_ID']; ?>">
                                        <label for="catatan_penolakan">Alasan Penolakan:</label>
                                        <textarea name="catatan_penolakan" required></textarea>
                                        <button type="submit" class="btn btn-danger">Tolak</button>
                                    </form>
                                <?php elseif ($item['status_approval'] == 'approved'): ?>
                                    <!-- Status Approved, tombol Setujui dan Tolak disembunyikan -->
                                    <span class="btn btn-success disabled">Approved</span>
                                <?php elseif ($item['status_approval'] == 'rejected'): ?>
                                    <!-- Status Rejected, tombol Setujui dan Tolak disembunyikan -->
                                    <span class="btn btn-danger disabled">Rejected</span>
                                <?php endif; ?>
                            
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

    <script>
        let currentPage = 1;
        const rowsPerPage = 5;
        const table = document.getElementById('reportTable');
        let rows = Array.from(table.querySelectorAll('tbody tr'));
        let filteredRows = rows;

        function displayTable() {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = currentPage * rowsPerPage;

            rows.forEach(row => row.style.display = 'none');
            filteredRows.slice(startIndex, endIndex).forEach(row => row.style.display = '');

            updatePagination(totalPages);
        }

        function updatePagination(totalPages) {
            const pageNumbers = document.getElementById('pageNumbers');
            pageNumbers.innerHTML = `Page ${currentPage} `;

            const prevButton = document.querySelector('#pagination a:nth-child(1)');
            const nextButton = document.querySelector('#pagination a:nth-child(3)');

            if (currentPage === 1) {
                prevButton.style.pointerEvents = 'none';
            } else {
                prevButton.style.pointerEvents = 'auto';
            }

            if (currentPage === totalPages) {
                nextButton.style.pointerEvents = 'none';
            } else {
                nextButton.style.pointerEvents = 'auto';
            }
        }

        function changePage(direction) {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (direction === 'prev' && currentPage > 1) {
                currentPage--;
            } else if (direction === 'next' && currentPage < totalPages) {
                currentPage++;
            }
            displayTable();
        }

        function searchTable() {
            const input = document.getElementById('searchInput').value.toUpperCase();
            filteredRows = rows.filter(row => {
                let rowContainsSearchTerm = false;
                const cols = row.getElementsByTagName('td');
                for (let i = 0; i < cols.length; i++) {
                    const cell = cols[i];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        if (text.toUpperCase().indexOf(input) > -1) {
                            rowContainsSearchTerm = true;
                            break;
                        }
                    }
                }
                return rowContainsSearchTerm;
            });

            currentPage = 1;
            displayTable();
        }

        window.onload = () => {
            displayTable();
        };
    </script>
</body>

</html>