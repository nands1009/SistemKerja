<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Penilaian</title>
    <style>
        .container-table {
            position: relative;
            background-color: white;
            height: 1042px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            border: 1px solid #ddd;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;

        }

        h1 {
            text-align: center;
    margin: 21px;
    color: #333;
    top: 23px;
    position: relative;
    right: 352px;
    font-family: 'Arial Narrow', sans-serif;
    font-weight: bold;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        th {
            background-color: #FF2E00;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e8f4e8;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-group a {
            padding: 5px 10px;
            border: 1px solid #4CAF50;
            border-radius: 5px;
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
        }

        .btn-group a:hover {
            background-color: #4CAF50;
            color: white;
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
    height: 8px;
    width: 142px;
    left: -140%;
    top: 47px;
    border-radius: 46px;
    border-color: white;
    background-color: white;
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }


i[class="fas fa-search"] {
    position: relative;
    right: 249px;
    top: 46px;
    z-index: 1;
}


    </style>
</head>

<body>
    <div class="container-table">
        <div class="container">
            <h1>Rekap Penilaian Seluruh Pegawai</h1>
            <div class="search-container">
            <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari Penilaian..." onkeyup="searchTable()">
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Nama Penilai</th>
                        <th>Jabatan</th>
                        <th>Divisi</th>
                        <th>Nilai</th>
                        <th>Catatan</th>
                        <th>Tanggal Penilaian</th>

                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                    <?php foreach ($penilaian as $item): ?>
                        <tr>
                        <td><?= $no++ ?></td>
                            <td><?= $item['username']; ?></td>
                            <td><?= $item['name']; ?></td>
                            <td><?= $item['role']; ?></td>
                            <td><?= $item['divisi']; ?></td>
                            <td><?= $item['nilai']; ?></td>
                            <td><?= $item['catatan']; ?></td>
                            <td><?= $item['tanggal_penilaian']; ?></td>
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
    const rowsPerPage = 3; // Tentukan berapa banyak data yang akan ditampilkan per halaman

    // Menyimpan data penilaian untuk contoh
    const penilaianData = <?php echo json_encode($penilaian); ?>;
    
    // Fungsi untuk menampilkan halaman sesuai dengan currentPage
    function displayPage(page) {
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const pageData = penilaianData.slice(startIndex, endIndex);
        
        // Clear previous table rows
        const tableBody = document.querySelector("tbody");
        tableBody.innerHTML = '';

        // Masukkan data ke dalam tabel
        pageData.forEach((item, index) => {
            const row = document.createElement("tr");

            const no = startIndex + index + 1;
            row.innerHTML = `
                <td>${no}</td>
                <td>${item.username}</td>
                <td>${item.name ? item.name : ""}</td>
                <td>${item.role}</td>
                <td>${item.divisi}</td>
                <td>${item.nilai}</td>
                <td>${item.catatan}</td>
                <td>${item.tanggal_penilaian}</td>
            `;
            tableBody.appendChild(row);
        });

        // Update page numbers
        updatePageNumbers();
    }

    // Fungsi untuk mengupdate nomor halaman
    function updatePageNumbers() {
        const totalPages = Math.ceil(penilaianData.length / rowsPerPage);
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

    // Fungsi untuk mengganti halaman
    function changePage(page) {
        const totalPages = Math.ceil(penilaianData.length / rowsPerPage);

        if (page === 'prev') {
            if (currentPage > 1) currentPage--;
        } else if (page === 'next') {
            if (currentPage < totalPages) currentPage++;
        } 

        displayPage(currentPage);
    }

    // Menampilkan halaman pertama saat halaman pertama dimuat
    window.onload = function() {
        displayPage(currentPage);
    }

    
    function searchTable() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll("tbody tr");

        rows.forEach(row => {
            const cells = row.querySelectorAll("td");
            const isMatch = Array.from(cells).some(cell => 
                cell.textContent.toLowerCase().includes(filter)
            );
            row.style.display = isMatch ? "" : "none";
        });
    }

    window.onload = function() {
        displayPage(currentPage);
    }

</script>

</body>

</html>