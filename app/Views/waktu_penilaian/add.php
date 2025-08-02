<!-- app/Views/waktu_penilaian/add.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tambah Waktu Penilaian | Sistem Kinerja</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Tambah Waktu Penilaian</h2>

        <form action="<?= site_url('waktu_penilaian/save') ?>" method="post">
            <div class="mb-3">
            <label for="tanggal_mulai">Tanggal Mulai</label>
    <input type="datetime-local" name="tanggal_mulai" value="<?= old('tanggal_mulai') ?>" required>
    
    <label for="tanggal_selesai">Tanggal Selesai</label>
    <input type="datetime-local" name="tanggal_selesai" value="<?= old('tanggal_selesai') ?>" required>


            <!-- Tombol Simpan -->
            <button type="submit" class="btn btn-primary">Simpan</button>

            <!-- Tombol Kembali -->
            <a href="<?= site_url('waktu_penilaian') ?>" class="btn btn-secondary ml-3">Kembali</a> <!-- Tombol Kembali -->

        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>