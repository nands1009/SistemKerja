<!-- resources/views/rencana_kerja/daindex.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapan Rencana Kerja | Sistem Kinerja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2 class="mb-4">Rekapan Rencana Kerja</h2>

        <!-- Table to display Rencana Kerja -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th> <!-- Tampilkan username -->
                    <th>Judul Rencana Kerja</th>
                    <th>Divisi</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rencana_kerja as $rencana): ?>
                    <tr>
                        <td><?= esc($rencana['id']) ?></td>
                        <td><?= esc($rencana['username']) ?></td> <!-- Tampilkan username -->
                        <td><?= esc($rencana['judul']) ?></td>
                        <td><?= esc($rencana['divisi']) ?></td>
                        <td><?= esc($rencana['tanggal']) ?></td>
                        <td><?= ucfirst($rencana['status']) ?></td>
                        <td>
                            <a href="<?= site_url('rencana_kerja/view/' . $rencana['id']) ?>" class="btn btn-info btn-sm">View</a>
                            <a href="<?= site_url('rencana_kerja/edit/' . $rencana['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>