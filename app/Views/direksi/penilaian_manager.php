<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Manager</title>
    <style>
        .card-body {
            position: relative;
            padding: 20px;
            border-radius: 10px;
            top: 28px;
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

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            position: relative;
            margin-top: 62px;
            color: #333;
            top: -66px;
            font-size: 35px;
            text-align: justify;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: -146px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        table th {
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .btn-submit {
            position: relative;
            width: 173px;
            height: 47px;
            top: 2px;
            right: -9px;
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

        .btn-submit:hover {
            background-color: #FF2E00;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
        }

        .btn-submit:active {
            background-color: #FF2E00;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
            transform: translateY(3px);
            transform: translateY(-2px);
            transition: transform 34ms;
        }

        /* Notification Style */
        .notification {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .notification.error {
            background-color: #f44336;
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
    </style>
</head>

<body>
    <div class="container-table">
        <div class="card-body">
            <h1>Penilaian Manager</h1>

            <!-- Display success message if exists -->
            <?php if (session()->getFlashdata('message')) : ?>
                <div class="notification">
                    <?= session()->getFlashdata('message') ?>
                </div>
            <?php endif; ?>

            <!-- Menampilkan data pengguna -->
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pengguna</th>
                        <th>Divisi</th>
                        <th>Nilai</th>
                        <th>Catatan Penilaian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($users as $user) : ?>
                        <tr class="table-row">
                            <td><?= $no++ ?></td>
                            <td><?= $user['username'] ?></td>
                            <td><?= $user['divisi'] ?></td>
                            <td>
                                <form action="/direksi/savePenilaian" method="POST">
                                    <input type="hidden" name="pegawai_id" value="<?= $user['id'] ?>">
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

    <script>
let currentPage = 1;
const rowsPerPage = 3;  // Sesuaikan jumlah baris per halaman
const users = <?php echo json_encode($users); ?>;

function displayPage(page) {
    const startIndex = (page - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const pageData = users.slice(startIndex, endIndex);

    // Menyembunyikan dan menampilkan baris sesuai dengan halaman yang dipilih
    const tableRows = document.querySelectorAll('.table-row');
    tableRows.forEach((row, index) => {
        row.style.display = (index >= startIndex && index < endIndex) ? '' : 'none';
    });

    updatePageNumbers(); // Memperbarui nomor halaman setelah menampilkan data
}

function updatePageNumbers() {
    const totalPages = Math.ceil(users.length / rowsPerPage);
    const pageNumbersContainer = document.getElementById('pageNumbers');
    pageNumbers.innerHTML = `Page ${currentPage} `;


        for (let i = 1; i <= totalPages; i++) {
            const pageNumberLink = document.createElement('a');
            pageNumberLink.href = "javascript:void(0);";
            pageNumberLink.textContent = i;
            pageNumberLink.classList.add('');
            pageNumberLink.onclick = () => changePage(i);

            if (i === currentPage) {
                pageNumberLink.style.fontWeight = 'bold';
            }

            pageNumbersContainer.appendChild(pageNumberLink);
        }
    }
function changePage(page) {
    const totalPages = Math.ceil(users.length / rowsPerPage);

    // Menangani perubahan halaman sebelumnya dan berikutnya
    if (page === 'prev') {
        if (currentPage > 1) currentPage--;
    } else if (page === 'next') {
        if (currentPage < totalPages) currentPage++;
    } else {
        currentPage = page;
    }

    displayPage(currentPage);  // Menampilkan halaman setelah perubahan
}

// Inisialisasi tampilan halaman pertama ketika halaman dimuat
window.onload = function () {
    displayPage(currentPage);
}

</script>

</body>

</html>
