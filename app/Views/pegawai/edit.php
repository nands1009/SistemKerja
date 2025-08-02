<!-- app/Views/pegawai/edit.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Pegawai | Sistem Kinerja</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Data Pegawai</h2>

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

        <!-- Form untuk mengedit pegawai -->
        <form action="<?= site_url('pegawai/update/' . $pegawai['id']) ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= $pegawai['username'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= $pegawai['email'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="pegawai" <?= ($pegawai['role'] == 'pegawai') ? 'selected' : '' ?>>Pegawai</option>
                    <option value="manager" <?= ($pegawai['role'] == 'manager') ? 'selected' : '' ?>>Manager</option>
                    <option value="admin" <?= ($pegawai['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="hrd" <?= ($pegawai['role'] == 'hrd') ? 'selected' : '' ?>>HRD</option>
                    <option value="direksi" <?= ($pegawai['role'] == 'direksi') ? 'selected' : '' ?>>Direksi</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>