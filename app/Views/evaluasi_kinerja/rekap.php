<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Evaluasi Kinerja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Rekap Penilaian</h2>

        <!-- Table to display Rekap Evaluasi Kinerja -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Divisi</th>
                    <th>Penilaian</th>
                    <th>Skor</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($evaluasi_kinerja as $evaluasi): ?>
                    <tr>
                        <td><?= esc($evaluasi['id']) ?></td>
                        <td><?= esc($evaluasi['username']) ?></td>
                        <td><?= esc($evaluasi['divisi']) ?></td>
                        <td><?= esc($evaluasi['penilaian']) ?></td>
                        <td><?= esc($evaluasi['skor']) ?></td>
                        <td><?= esc($evaluasi['tanggal']) ?></td>
                        <td>
                            <a href="<?= site_url('evaluasi_kinerja/view/' . $evaluasi['id']) ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>