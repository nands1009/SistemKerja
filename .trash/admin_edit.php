<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pertanyaan</title>
</head>
<body>
    <h1>Edit Pertanyaan</h1>

    <!-- Form untuk admin mengedit jawaban -->
    <form action="/admin/updateAnswer/<?= $chatbot['id'] ?>" method="POST">
        
        <!-- CSRF Token -->
        <?= csrf_field(); ?>

        <!-- Menampilkan Pertanyaan (tidak dapat diedit jika hanya jawaban yang ingin diubah) -->
        <label for="question">Pertanyaan</label>
        <input type="text" name="question" value="<?= esc($chatbot['question']) ?>" required readonly />

        <!-- Menampilkan Jawaban Admin -->
        <label for="answer">Jawaban</label>
        <textarea name="answer" required><?= esc($chatbot['answer']) ?></textarea>

        <!-- Tombol untuk memperbarui jawaban -->
        <button type="submit">Perbarui Jawaban</button>
    </form>

</body>
</html>
