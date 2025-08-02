<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Chatbot</title>
    <meta name="csrf-token" content="<?= csrf_token() ?>" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .answer-form {
            margin-top: 10px;
        }

        .answer-form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .answer-form button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .answer-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

<h1>Admin Panel - Pertanyaan yang Belum Terjawab</h1>

<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Pertanyaan</th>
            <th>Jawab</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($chatbot_data)): ?>
            <?php foreach ($chatbot_data as $question): ?>
            <tr>
                <td><?= esc($question['id']) ?></td>
                <td><?= esc($question['question']) ?></td>
                <td>
                    <!-- Form untuk admin memberikan jawaban -->
                    <form action="/admin/updateAnswer/<?= esc($question['id']) ?>" method="POST" class="answer-form" data-id="<?= esc($question['id']) ?>">
                        <!-- Token CSRF -->
                        <?= csrf_field(); ?>
                        
                        <textarea name="answer" placeholder="Masukkan jawaban..." required></textarea>
                        <button type="submit">Berikan Jawaban</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Tidak ada pertanyaan yang perlu dijawab.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Menggunakan AJAX untuk memperbarui jawaban tanpa me-refresh halaman
    $('.answer-form').on('submit', function(event) {
        event.preventDefault();

        var form = $(this);
        var questionId = form.data('id');
        var answer = form.find('textarea[name="answer"]').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token from meta tag

        $.ajax({
            url: '/admin/updateAnswer/' + questionId,
            type: 'POST',
            data: {
                answer: answer,
                csrf_token: csrfToken // Pass CSRF token here
            },
            success: function(response) {
                // Update tampilan jawaban di chatbox
                $('#chatbox-' + questionId).html('<strong>Admin:</strong> ' + response.answer);
                alert('Jawaban berhasil diperbarui!');
                form.closest('tr').remove();  // Menghapus pertanyaan yang telah dijawab dari daftar admin
            },
            error: function() {
                alert('Terjadi kesalahan. Coba lagi.');
            }
        });
    });
</script>

</body>

</html>
