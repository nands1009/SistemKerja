<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pertanyaan - Admin Panel</title>
</head>
<body>
    <h1>Tambah Pertanyaan Baru</h1>
    <form action="/admin/save" method="POST">
        <label for="question">Pertanyaan</label>
        <input type="text" name="question" required />
        <label for="answer">Jawaban</label>
        <textarea name="answer" required></textarea>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>
