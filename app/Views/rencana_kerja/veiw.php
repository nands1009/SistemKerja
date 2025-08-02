<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rencana Kerja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Detail Rencana Kerja</h2>

        <div class="mb-3">
            <strong>Judul:</strong>
            <p><?= esc($rencana['judul']) ?></p>
        </div>
        <div class="mb-3">
            <strong>Deskripsi:</strong>
            <p><?= esc($rencana['deskripsi']) ?></p>
        </div>
        <div class="mb-3">
            <strong>Tanggal:</strong>
            <p><?= esc($rencana['tanggal']) ?></p>
        </div>
        <div class="mb-3">
            <strong>Status:</strong>
            <p><?= ucfirst($rencana['status']) ?></p>
        </div>

        <a href="<?= site_url('rencana_kerja') ?>" class="btn btn-secondary">Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>