<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Data Berdasarkan Tag</title>

    <style>
        /* Styling untuk div kontainer */
        .container-body-data {
            position: relative;
            background-color: white;
            height: auto;
            min-height: 85rem;
            margin: 30px auto;
            width: 157rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 25px;
            transition: all 0.3s ease;
            margin-top: -56rem;
            border-radius: 30px 30px 30px 30px;
            top: -28px;
            right: -5px;
        }

        .container-body-data:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        /* Styling untuk heading */
        h3 {
            position: relative;
            top: -22px;
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: -8px;
        }


        /* Search Bar styling */
        .search-container {
            margin: 25px 0;
            position: relative;
            max-width: 600px;
        }

        .search-container input {
            padding: 3px 8px;
            width: 48%;
            font-size: 1rem;
            border: none;
            border-radius: 30px;
            background-color: #f1f3f6;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .search-container input:focus {
            outline: none;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1), 0 0 0 3px rgba(255, 46, 0, 0.2);
        }

        .search-container::after {
            content: "\f002";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 315px;
            top: 30%;
            transform: translateY(-50%);
            color: #aaa;
        }

        /* Styling untuk tabel */
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            top: -52px;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: #333;
        }

        table th,
        table td {
            padding: 15px 20px;
            text-align: left;
        }

        table th {
            background-color: #FF2E00;
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.85rem;
            position: sticky;
            top: 0;
            border-radius: 8px;
        }

        table th:first-child {
            border-top-left-radius: 10px;
        }

        table th:last-child {
            border-top-right-radius: 10px;
        }

        table tr {
            background-color: #fff;
            transition: all 0.3s ease;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f0f7ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        table td {
            border-bottom: 1px solid #eaeaea;
        }

        /* Tag styling */
        .tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            background-color: #e1f5fe;
            color: #0277bd;
            font-weight: 500;
            font-size: 0.85rem;
        }

        /* Styling untuk frequency badge */
        .frequency-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 8px;
            border-radius: 12px;
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
        }

        /* Date styling */
        .date-cell {
            color: #666;
            font-size: 0.9rem;
        }

        /* Pagination styling */
        .pagination-container-other {
            text-align: center;
            margin-top: -16px;
        }

        .pagination-container-other a {
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin: 0px 18px;
            background-color: #FF2E00;
        }

        .pagination-container-other a:hover {
            background-color: #FF2E00;
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin: 0px 18px;
            background-color: #FF2E00;
        }

        #pageDetailNumbers {
            font-weight: 600;
            color: #555;
            padding: 0 15px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container-body-data {
                width: 95%;
                padding: 15px;
            }

            h3 {
                font-size: 1.5rem;
            }

            table th,
            table td {
                padding: 10px 15px;
            }

            .search-container input {
                padding: 10px 15px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-container {
            animation: fadeIn 0.5s ease-out;
        }

        /* No data message styling */
        .no-data {
            text-align: center;
            padding: 40px 0;
            color: #aaa;
            font-size: 1.2rem;
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="container-body-data">
        <h3>Detail Pertanyaan</h3>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Cari pertanyaan..." onkeyup="filterTable()">
        </div>

        <div class="table-container">
            <table id="questionTable">
                <thead>
                    <tr>
                        <th>Pertanyaan</th>
                        <th>Tag</th>
                        <th>Frekuensi</th>
                        <th>Pertama Ditanyakan</th>
                        <th>Terakhir Ditanyakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($frequentQuestions) && is_array($frequentQuestions) && count($frequentQuestions) > 0): ?>
                        <?php foreach ($frequentQuestions as $question): ?>
                            <tr>
                                <td><?= esc($question['question']) ?></td>
                                <td><span class="tag"><?= esc($question['tag']) ?></span></td>
                                <td class="text-center"><span class="frequency-badge"><?= esc($question['frequency']) ?></span></td>
                                <td class="date-cell"><?= date('d M Y, H:i', strtotime(esc($question['created_at']))) ?></td>
                                <td class="date-cell"><?= date('d M Y, H:i', strtotime(esc($question['last_asked_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">
                                <i class="fas fa-inbox"></i>
                                Tidak ada data pertanyaan yang tersedia
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="paginationDetail" class="pagination-container-other">
            <a href="javascript:void(0);" onclick="changeDetailPage('prev')"><i class="fas fa-chevron-left"></i> Prev</a>
            <span id="pageDetailNumbers"></span>
            <a href="javascript:void(0);" onclick="changeDetailPage('next')">Next <i class="fas fa-chevron-right"></i></a>
        </div>
    </div>

    <!-- Javascript untuk paginasi dan pencarian -->
    <script>
        // Paginasi 
        let detailCurrentPage = 1;
        const detailRowsPerPage = 9;

        // Function untuk handle paginasi
        function changeDetailPage(direction) {
            const table = document.getElementById("questionTable");
            if (!table) return; // Pastikan tabel ada

            const rows = Array.from(table.getElementsByTagName("tr")).filter(row =>
                row.parentElement.tagName === "TBODY"
            );

            const visibleRows = rows.filter(row => !row.classList.contains('filtered-out'));
            const totalPages = Math.ceil(visibleRows.length / detailRowsPerPage);

            if (direction === 'prev') {
                if (detailCurrentPage > 1) detailCurrentPage--;
            } else if (direction === 'next') {
                if (detailCurrentPage < totalPages) detailCurrentPage++;
            }

            displayDetailPage(detailCurrentPage, visibleRows, totalPages);
        }

        function displayDetailPage(page, rows, totalPages) {
            const startIndex = (page - 1) * detailRowsPerPage;
            const endIndex = startIndex + detailRowsPerPage;

            // Hide all rows
            rows.forEach(row => row.style.display = "none");

            // Show the rows for the current page
            rows.slice(startIndex, endIndex).forEach(row => row.style.display = "");

            // Update the page numbers
            const pageNumbersContainer = document.getElementById("pageDetailNumbers");
            if (pageNumbersContainer) {
                pageNumbersContainer.innerHTML = `${page} / ${totalPages || 1}`;
            }

            // Update visibility of navigation buttons
            const prevButton = document.querySelector("#paginationDetail a[onclick=\"changeDetailPage('prev')\"]");
            const nextButton = document.querySelector("#paginationDetail a[onclick=\"changeDetailPage('next')\"]");

            if (prevButton) prevButton.style.visibility = (page === 1 || totalPages === 0) ? 'none' : 'visible';
            if (nextButton) nextButton.style.visibility = (page === totalPages || totalPages === 0) ? 'none' : 'visible';
        }

        // Function for search
        function filterTable() {
            const query = document.getElementById("searchInput").value.toLowerCase();
            const table = document.getElementById("questionTable");
            if (!table) return;

            const rows = Array.from(table.querySelectorAll("tbody tr"));
            let visibleCount = 0;

            rows.forEach(row => {
                const cells = Array.from(row.getElementsByTagName("td"));
                const text = cells.map(cell => cell.textContent.toLowerCase()).join(" ");

                if (text.includes(query)) {
                    row.style.display = "";
                    row.classList.remove('filtered-out');
                    visibleCount++;
                } else {
                    row.style.display = "none";
                    row.classList.add('filtered-out');
                }
            });

            // Jika tidak ada hasil ditemukan
            const noDataRow = table.querySelector(".no-data-row");
            if (visibleCount === 0 && !noDataRow) {
                const tbody = table.querySelector("tbody");
                const noDataCell = document.createElement("tr");
                noDataCell.className = "no-data-row";
                noDataCell.innerHTML = '<td colspan="5" class="no-data"><i class="fas fa-search"></i>Tidak ada pertanyaan yang cocok dengan pencarian</td>';
                tbody.appendChild(noDataCell);
            } else if (visibleCount > 0 && noDataRow) {
                noDataRow.remove();
            }

            // Reset pagination dan tampilkan halaman pertama setelah pencarian
            detailCurrentPage = 1;
            const visibleRows = rows.filter(row => !row.classList.contains('filtered-out'));
            const totalPages = Math.ceil(visibleRows.length / detailRowsPerPage);
            displayDetailPage(detailCurrentPage, visibleRows, totalPages);
        }

        // Inisialisasi paginasi saat halaman dimuat
        window.addEventListener('load', function() {
            const table = document.getElementById("questionTable");
            if (table) {
                const rows = Array.from(table.querySelectorAll("tbody tr"));
                const totalPages = Math.ceil(rows.length / detailRowsPerPage);
                displayDetailPage(detailCurrentPage, rows, totalPages);
            }
        });
    </script>
</body>

</html>