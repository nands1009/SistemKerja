<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pertanyaan Asisten Virtual</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

        }

        .container-table {
            position: relative;
            background-color: white;
            height: 1110px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styling */
        h1 {
            margin-bottom: 36px;
            position: relative;
            font-size: 35px;
            color: #333;
            font-weight: bold;
            top: 24px;
            font-family: 'Arial Narrow', sans-serif;
            right: 0%;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            animation: slideInRight 0.5s ease-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            border-left: 5px solid #2e7d32;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            border-left: 5px solid #c62828;
        }

        /* Button Styling */

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            position: relative;
            width: 237px;
            height: 42px;
            top: 17px;
            right: 0px;
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

        .btn-primary:hover,
        .btn-primary:focus {

            background: #FF2E00;
        }


        .btn-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.6);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.6);
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        /* Table Styling */
        .table-container {
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeInUp 1s ease-out;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table thead {
            color: white;
            background: #FF2E00;
        }

        table th {
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
            border-radius: 8px;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            border-radius: 8px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: white;
            margin: 3% auto;
            padding: 0;
            border: none;
            width: 90%;
            max-width: 600px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideInDown 0.4s ease-out;
            overflow: hidden;
        }

        .modal-header {
            color: #333;
            padding: 20px 30px;
            position: relative;
        }

        .modal-header h5 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .close:hover {
            color: #ff6b6b;
            transform: translateY(-50%) scale(1.2);
        }

        .modal-body {
            padding: 30px;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Status Badge */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-answered {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
        }

        .status-pending {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
        }

        /* Animations */


        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            h1 {
                font-size: 2rem;
            }

            .table-container {
                overflow-x: auto;
            }

            .modal-content {
                width: 95%;
                margin: 10% auto;
            }

            .modal-body {
                padding: 20px;
            }

            .btn {
                padding: 10px 15px;
                font-size: 11px;
            }

            .table th,
            .table td {
                padding: 10px 8px;
                font-size: 12px;
            }
        }

        /* Custom Scrollbar */



        #status {
            width: 100%;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            color: #333;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 0.375rem;
            appearance: none;
            /* Menonaktifkan tampilan default pada beberapa browser */
            outline: none;
            /* Menghilangkan outline pada input focus */
            box-shadow: none;
            /* Menghilangkan shadow di focus */
        }

        /* Tampilan saat dropdown di hover */
        #status:hover {
            border-color: #007bff;
        }

        /* Tampilan saat dropdown dalam kondisi focus */
        #status:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
        }

        #tag {
            width: 100%;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            color: #333;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 0.375rem;
            appearance: none;
            /* Menonaktifkan tampilan default pada beberapa browser */
            outline: none;
            /* Menghilangkan outline pada input focus */
            box-shadow: none;
            /* Menghilangkan shadow di focus */
        }

        /* Tampilan saat dropdown di hover */
        #tag:hover {
            border-color: #007bff;
        }

        /* Tampilan saat dropdown dalam kondisi focus */
        #tag:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
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
        <div class="container mt-5">
            <h1>Pertanyaan Asisten Virtual</h1>

            <!-- Display success or error messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Button to open the modal for adding a question -->
            <button class="btn btn-primary mb-3" onclick="openCreateForm()">
                <i class="fas fa-plus"></i> Tambah Pertanyaan
            </button>

            <!-- Table showing the questions -->
            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pertanyaan</th>
                            <th>Jawaban</th>
                            <th>Status</th>
                            <th>Tag</th>
                            <th>Answered By</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($questions) && is_array($questions)): ?>
                            <?php foreach ($questions as $index => $question): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= esc($question['question']); ?></td>
                                    <td><?= esc($question['answer'] ?? ''); ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($question['status']); ?>">
                                            <?= esc($question['status']); ?>
                                        </span>
                                    </td>
                                    <td><?= esc($question['tag'] ?? ''); ?></td>
                                    <td><?= esc($question['answered_by'] ?? ''); ?></td>
                                    <td>
                                        <button class="btn btn-warning" onclick="openEditForm(<?= $question['id']; ?>, '<?= esc($question['question'], 'js'); ?>', '<?= esc($question['answer'] ?? '', 'js'); ?>', '<?= esc($question['tag'] ?? '', 'js'); ?>', '<?= esc($question['status'], 'js'); ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteQuestion(<?= $question['id']; ?>)">
    <i class="fas fa-trash"></i> Hapus
</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px;">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                                    <br>Tidak ada data pertanyaan terjawab
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            </div>
            <div id="pagination" class="pagination-container">
                <a href="javascript:void(0);" id="prevPage" onclick="changePage('prev')" class="disabled">&laquo; prev</a>
                <span id="pageNumbers">page</span>
                <a href="javascript:void(0);" id="nextPage" onclick="changePage('next')">next &raquo;</a>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div id="questionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="questionModalLabel">Tambah Pertanyaan</h5>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
               <form id="questionForm" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="questionId">
                    <div class="form-group">
                        <label for="question"><i class="fas fa-question-circle"></i> Pertanyaan:</label>
                        <input type="text" name="question" id="question" class="form-control" required placeholder="Masukkan pertanyaan...">
                    </div>
                    <div class="form-group">
                        <label for="answer"><i class="fas fa-comment-dots"></i> Jawaban:</label>
                        <textarea name="answer" id="answer" class="form-control" required placeholder="Masukkan jawaban..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tag"><i class="fas fa-tags"></i> Tag:</label>
                        <select name="tag" id="tag" class="form-control">
                            <option value="general">general</option>
                            <option value="pengajuan_penghargaan">pengajuan-penghargaan</option>
                            <option value="laporan_kerja">laporan-kerja</option>
                            <option value="evaluasi_kinerja">evaluasi-kinerja</option>
                            <option value="pengaturan_nlp">pengaturan-nlp</option>
                            <option value="penilaian">penilaian</option>
                            <option value="rencana_kerja">rencana-kerja</option>
                            <option value="waktu_penilaian">waktu-penilaian</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status"><i class="fas fa-flag"></i> Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="answered" <?= 'answered' ? 'selected' : '' ?>>Answered</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="saveButton">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to open the "Add Question" form modal
        function openCreateForm() {
            // Reset the form fields
            document.getElementById('questionForm').reset();
document.getElementById('questionForm').action = '<?= base_url('questions/save') ?>';
            document.getElementById('questionModalLabel').textContent = 'Tambah Pertanyaan';
            document.getElementById('questionId').value = '';
            document.getElementById('saveButton').innerHTML = '<i class="fas fa-save"></i> Simpan';

            // Show the modal
            document.getElementById('questionModal').style.display = 'block';
        }

        // Function to open the "Edit Question" form modal
        function openEditForm(id, question, answer, tag, status) {
            // Mengubah action form untuk mengarahkan ke update dengan ID yang benar
            document.getElementById('questionForm').action = '<?= base_url('questions/update/') ?>' + id;

            // Mengubah judul modal menjadi "Edit Pertanyaan"
            document.getElementById('questionModalLabel').textContent = 'Edit Pertanyaan';

            // Mengubah teks pada tombol menjadi "Update"
            document.getElementById('saveButton').innerHTML = '<i class="fas fa-edit"></i> Update';

            // Mengisi form dengan nilai yang sudah ada
            document.getElementById('questionId').value = id;
            document.getElementById('question').value = question;
            document.getElementById('answer').value = answer;
            document.getElementById('tag').value = tag;
            document.getElementById('status').value = status;

            // Menampilkan modal
            document.getElementById('questionModal').style.display = 'block';
        }


        // Function to delete question
      function deleteQuestion(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('questions/delete/') ?>' + id;

        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
    }
}


        // Function to close the modal
        function closeModal() {
            document.getElementById('questionModal').style.display = 'none';
        }

        // Close modal if the user clicks anywhere outside of the modal content
        window.onclick = function(event) {
            if (event.target === document.getElementById('questionModal')) {
                closeModal();
            }
        }

        // Add smooth scrolling and button effects
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Auto hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
                let currentPage = 1;
        let entriesPerPage = 7;

        // Fungsi untuk menghitung total data yang aktual
        function getTotalDataCount() {
            const tableBody = document.querySelector('tbody');
            const allRows = Array.from(tableBody.querySelectorAll('tr'));
            
            // Filter hanya baris yang memiliki data (bukan pesan "tidak ada data")
            const dataRows = allRows.filter(row => {
                const firstCell = row.querySelector('td:first-child');
                return firstCell && !firstCell.hasAttribute('colspan');
            });
            
            return dataRows.length;
        }

        // Fungsi untuk mendapatkan baris data aktual
        function getDataRows() {
            const tableBody = document.querySelector('tbody');
            const allRows = Array.from(tableBody.querySelectorAll('tr'));
            
            // Filter hanya baris yang memiliki data (bukan pesan "tidak ada data")
            return allRows.filter(row => {
                const firstCell = row.querySelector('td:first-child');
                return firstCell && !firstCell.hasAttribute('colspan');
            });
        }

        // Fungsi untuk menampilkan data berdasarkan halaman
        function displayPaginatedData() {
            const dataRows = getDataRows();
            const totalEntries = dataRows.length;
            
            // Jika tidak ada data, tampilkan pesan dan update kontrol
            if (totalEntries === 0) {
                showNoDataMessage();
                updatePaginationControls(0);
                return;
            }
            
            // Hitung total halaman
            const totalPages = Math.ceil(totalEntries / entriesPerPage);
            
            // Pastikan currentPage dalam range yang valid
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }
            if (currentPage < 1) {
                currentPage = 1;
            }
            
            // Hitung indeks start dan end
            const startIndex = (currentPage - 1) * entriesPerPage;
            const endIndex = startIndex + entriesPerPage;
            
            // Sembunyikan semua baris data terlebih dahulu
            dataRows.forEach(row => {
                row.style.display = 'none';
            });
            
            // Tampilkan baris sesuai halaman dan update nomor urut
            const visibleRows = dataRows.slice(startIndex, endIndex);
            visibleRows.forEach((row, index) => {
                row.style.display = '';
                // Update nomor urut berdasarkan posisi global
                const noCell = row.querySelector('td:first-child');
                if (noCell) {
                    noCell.textContent = startIndex + index + 1;
                }
            });
            
            updatePaginationControls(totalEntries);
        }

        // Fungsi untuk menampilkan pesan tidak ada data
        function showNoDataMessage() {
            const tableBody = document.querySelector('tbody');
            
            // Hapus pesan lama jika ada
            const existingMessage = tableBody.querySelector('.no-data-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            // Sembunyikan semua baris data
            const dataRows = getDataRows();
            dataRows.forEach(row => {
                row.style.display = 'none';
            });
            
            // Tambahkan pesan tidak ada data
            const noDataRow = document.createElement('tr');
            noDataRow.className = 'no-data-message';
            noDataRow.innerHTML = `
                <td colspan="7" style="text-align: center; padding: 30px;">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                    <br>Tidak ada data pertanyaan
                </td>
            `;
            tableBody.appendChild(noDataRow);
        }

        // Fungsi untuk mengupdate kontrol pagination
        function updatePaginationControls(totalEntries) {
            const totalPages = Math.ceil(totalEntries / entriesPerPage);
            const pageNumbers = document.getElementById('pageNumbers');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            const paginationInfo = document.getElementById('paginationInfo');
            
            // Update info pagination
            if (totalEntries === 0) {
                paginationInfo.textContent = 'Tidak ada data untuk ditampilkan';
            } else {
                const startEntry = (currentPage - 1) * entriesPerPage + 1;
                const endEntry = Math.min(currentPage * entriesPerPage, totalEntries);
                paginationInfo.textContent = `Menampilkan ${startEntry} - ${endEntry} dari ${totalEntries} entri`;
            }
            
            // Clear page numbers
            pageNumbers.innerHTML = '';
            
            if (totalPages > 0) {
                // Generate page numbers dengan logika yang lebih baik
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
                
                // Adjust startPage jika endPage kurang dari maxVisiblePages
                if (endPage - startPage < maxVisiblePages - 1) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }
                
                // Tambahkan tombol "First" jika diperlukan
                if (startPage > 1) {
                    const firstBtn = createPageButton(1, '1');
                    pageNumbers.appendChild(firstBtn);
                    
                    if (startPage > 2) {
                        const dotsSpan = document.createElement('span');
                        dotsSpan.textContent = '...';
                        dotsSpan.style.padding = '12px 8px';
                        dotsSpan.style.color = '#666';
                        pageNumbers.appendChild(dotsSpan);
                    }
                }
                
                // Generate page numbers
                for (let i = startPage; i <= endPage; i++) {
                    const pageBtn = createPageButton(i, i.toString());
                    pageNumbers.appendChild(pageBtn);
                }
                
                // Tambahkan tombol "Last" jika diperlukan
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const dotsSpan = document.createElement('span');
                        dotsSpan.textContent = '...';
                        dotsSpan.style.padding = '12px 8px';
                        dotsSpan.style.color = '#666';
                        pageNumbers.appendChild(dotsSpan);
                    }
                    
                    const lastBtn = createPageButton(totalPages, totalPages.toString());
                    pageNumbers.appendChild(lastBtn);
                }
            } else {
                pageNumbers.innerHTML = '<span style="padding: 12px 16px; color: #666;">Halaman 0</span>';
            }
            
            // Update prev/next buttons
            if (prevBtn) {
                if (currentPage === 1 || totalPages === 0) {
                    prevBtn.classList.add('disabled');
                } else {
                    prevBtn.classList.remove('disabled');
                }
            }
            
            if (nextBtn) {
                if (currentPage === totalPages || totalPages === 0) {
                    nextBtn.classList.add('disabled');
                } else {
                    nextBtn.classList.remove('disabled');
                }
            }
        }

        // Fungsi helper untuk membuat tombol halaman
        function createPageButton(pageNum, displayText) {
            const pageBtn = document.createElement('a');
            pageBtn.href = 'javascript:void(0);';
            pageBtn.className = `pagination-btn ${pageNum === currentPage ? 'active' : ''}`;
            pageBtn.textContent = displayText;
            pageBtn.onclick = () => goToPage(pageNum);
            return pageBtn;
        }

        // Fungsi untuk mengubah halaman
        function changePage(direction) {
            const totalEntries = getTotalDataCount();
            const totalPages = Math.ceil(totalEntries / entriesPerPage);
            
            if (direction === 'prev' && currentPage > 1) {
                currentPage--;
                displayPaginatedData();
            } else if (direction === 'next' && currentPage < totalPages) {
                currentPage++;
                displayPaginatedData();
            }
        }

        // Fungsi untuk pergi ke halaman tertentu
        function goToPage(page) {
            const totalEntries = getTotalDataCount();
            const totalPages = Math.ceil(totalEntries / entriesPerPage);
            
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                displayPaginatedData();
            }
        }

        // Fungsi untuk mengubah jumlah entri per halaman
        function changeEntriesPerPage() {
            const selectElement = document.getElementById('entriesPerPage');
            if (selectElement) {
                entriesPerPage = parseInt(selectElement.value);
                currentPage = 1; // Reset ke halaman pertama
                displayPaginatedData();
            }
        }

        // Fungsi untuk refresh pagination setelah operasi CRUD
        function refreshPagination() {
            // Tunggu sebentar untuk memastikan DOM sudah terupdate
            setTimeout(() => {
                const totalEntries = getTotalDataCount();
                const totalPages = Math.ceil(totalEntries / entriesPerPage);
                
                // Jika halaman saat ini melebihi total halaman, pindah ke halaman terakhir
                if (currentPage > totalPages && totalPages > 0) {
                    currentPage = totalPages;
                }
                
                displayPaginatedData();
            }, 100);
        }

        // Inisialisasi pagination saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Tunggu sebentar untuk memastikan semua elemen sudah ter-render
            setTimeout(() => {
                displayPaginatedData();
            }, 200);
        });

        // Override fungsi deleteQuestion untuk refresh pagination
        

        // Event listener untuk form submission (untuk operasi create/update)
        document.getElementById('questionForm').addEventListener('submit', function(e) {
            // Di implementasi nyata, form akan disubmit ke server
            // Untuk demo, kita hanya refresh pagination
            setTimeout(() => {
                refreshPagination();
                closeModal();
            }, 500);
        });
    </script>
</body>

</html>