<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-body-bot {
            position: relative;
            background-color: white;
            height: 609px;
            top: 11px;
            width: 52%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        .card-body-bot {
    position: relative;
    padding: 20px;
    top: 57px;
    width: 99%;
    right: -4px;
}
        

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #FF2E00;
            font-weight: bold;
            color: white;
            border-radius: 8px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }

        .btn-primary {
            background-color: #FF2E00;
            color: white;
            border: none;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #FF2E00;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .text-center {
            text-align: center;
        }

        .card-title-bot {
            position: relative;
            top: 42px;
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: -26px;
        }



        .col-md-10 {
            width: 83.33%;
        }

        .py-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .form-label {
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .text-muted {
            color: #6c757d;
        }

        .alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 70%;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 15px;
        }

        .modal-title {
            margin: 0;
            font-size: 1.5rem;
        }

        .modal-body {
            padding: 10px 0;
        }

        .modal-footer {
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            margin-top: 15px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: relative;
            top: -42px;
            right: -14px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header h3 {
            position: relative;
            text-align: center;
            font-size: 24px;
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
            margin: 0px 18px;
            background-color: #FF2E00;
        }

        .pagination-container a:hover {
            background-color: #FF2E00;
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin: 0px 18px;
            background-color: #FF2E00;
        }
    </style>
</head>

<body>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="container-body-bot">
        <h1 class="card-title-bot">Pertanyaan Terbaru yang Belum Dijawab</h1>

        <div class="card-body-bot">
            <?php if (empty($pendingQuestions)) : ?>
                <p class="text-center">Tidak ada pertanyaan yang belum dijawab.</p>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pertanyaan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach (array_slice($pendingQuestions, 0, 5) as $question) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $question['question'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($question['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                            onclick="openAnswerModal('<?= $question['id'] ?>', '<?= htmlspecialchars($question['question'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-reply"></i> Jawab
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <div id="pagination" class="pagination-container">
                <a href="javascript:void(0);" onclick="changePage('prev')">&laquo; Prev</a>
                <span id="pageNumbers"></span>
                <a href="javascript:void(0);" onclick="changePage('next')">Next &raquo;</a>
            </div>
        </div>
    </div>


    <!-- Modal Jawab Pertanyaan -->
    <div id="answerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Jawab Pertanyaan</h3>
                <span class="close" onclick="closeAnswerModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="answerForm" action="<?= site_url('adminpanel/saveanswer') ?>" method="post">
                    <input type="hidden" name="id" id="questionId">

                    <div class="mb-3">
                        <label for="question" class="form-label">Pertanyaan:</label>
                        <div id="questionText" class="form-control bg-light"></div>
                    </div>

                    <div class="mb-3">
                        <label for="answer" class="form-label">Jawaban:</label>
                        <textarea name="answer" id="answer" rows="5" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tag" class="form-label">Tag/Kategori:</label>
                        <input type="text" name="tag" id="tag" class="form-control" value="general" required>
                        <small class="text-muted">Tag akan digunakan untuk mengelompokkan pertanyaan sejenis.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button class="btn btn-secondary" onclick="closeAnswerModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="btn btn-primary" onclick="submitAnswerForm()">
                    <i class="fas fa-save"></i> Simpan Jawaban
                </button>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        var modal = document.getElementById("answerModal");
        var answerForm = document.getElementById("answerForm");

        function openAnswerModal(questionId, questionText) {
            document.getElementById("questionId").value = questionId;
            document.getElementById("questionText").textContent = questionText;
            document.getElementById("answer").value = "";
            document.getElementById("tag").value = "general";
            modal.style.display = "block";
        }

        function closeAnswerModal() {
            modal.style.display = "none";
        }

        function submitAnswerForm() {
            if (document.getElementById("answer").value.trim() === "") {
                alert("Silakan isi jawaban terlebih dahulu");
                return;
            }

            answerForm.submit();
        }

        // Close the modal if clicked outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                closeAnswerModal();
            }
        }

        // Pagination functions
        let currentPage = 1;
        const questionsPerPage = 5;
        const totalQuestions = <?= count($pendingQuestions) ?>;
        const totalPages = Math.ceil(totalQuestions / questionsPerPage);

        function renderQuestions() {
            const slicedQuestions = <?= json_encode($pendingQuestions) ?>.slice((currentPage - 1) * questionsPerPage, currentPage * questionsPerPage);

            const tableBody = document.querySelector(".table tbody");
            tableBody.innerHTML = "";

            slicedQuestions.forEach((question, index) => {
                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${(currentPage - 1) * questionsPerPage + index + 1}</td>
                    <td>${question.question}</td>
                    <td>${new Date(question.created_at).toLocaleString()}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="openAnswerModal('${question.id}', '${question.question}')">
                            <i class="fas fa-reply"></i> Jawab
                        </button>
                    </td>
                `;

                tableBody.appendChild(row);
            });

            document.getElementById("pageNumbers").innerHTML = `Page ${currentPage} of ${totalPages}`;
            document.querySelector("a[onclick='changePage(\"prev\")']").style.visibility = currentPage === 1 ? 'hidden' : 'visible';
            document.querySelector("a[onclick='changePage(\"next\")']").style.visibility = currentPage === totalPages ? 'hidden' : 'visible';
        }

        function changePage(direction) {
            if (direction === "prev" && currentPage > 1) {
                currentPage--;
            } else if (direction === "next" && currentPage < totalPages) {
                currentPage++;
            }
            renderQuestions();
        }

        window.onload = function() {
            renderQuestions();
        }
    </script>
</body>

</html>