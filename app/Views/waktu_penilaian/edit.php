<!-- app/Views/waktu_penilaian/edit.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Waktu Penilaian | Sistem Kinerja</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Waktu Penilaian</h2>

        <!-- Menampilkan error validation jika ada -->
        <?php if (session()->get('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->get('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form untuk mengedit waktu penilaian -->
        <form action="<?= site_url('waktu_penilaian/update/' . $waktu_penilaian['id']) ?>" method="post">
            <div class="mb-3">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?= $waktu_penilaian['tanggal_mulai'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?= $waktu_penilaian['tanggal_selesai'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <!-- Tombol Kembali -->
            <a href="<?= site_url('waktu_penilaian') ?>" class="btn btn-secondary ml-3">Kembali</a> <!-- Tombol Kembali -->

        </form>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>