<!-- resources/views/evaluasi_kinerja/add.php untuk manager dn direksi-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Evaluasi Kinerja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Tambah Evaluasi Kinerja</h2>

        <form action="<?= site_url('evaluasi_kinerja/save') ?>" method="POST">
            <?= csrf_field() ?> <!-- Untuk keamanan CSRF -->

            <div class="mb-3">
                <label for="penilaian" class="form-label">Penilaian</label>
                <textarea class="form-control" id="penilaian" name="penilaian" required></textarea>
            </div>

            <div class="mb-3">
                <label for="skor" class="form-label">Skor (1-10)</label>
                <input type="number" class="form-control" id="skor" name="skor" min="1" max="10" required>
            </div>

            <input type="hidden" name="user_id" value="<?= esc($user_id) ?>">

            <button type="submit" class="btn btn-primary">Simpan Evaluasi</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>