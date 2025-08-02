<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pertanyaan Belum Dijawab' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark text-white py-4 min-vh-100">
                <h3 class="text-center mb-4">Admin Panel</h3>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= site_url('adminpanel') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white active" href="<?= site_url('adminpanel/pendingquestions') ?>">
                            <i class="fas fa-question-circle me-2"></i> Pertanyaan Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= site_url('adminpanel/answeredquestions') ?>">
                            <i class="fas fa-check-circle me-2"></i> Pertanyaan Terjawab
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= site_url('auth/logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 py-4">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <h2 class="mb-4">Pertanyaan Yang Belum Dijawab</h2>
                
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($questions)) : ?>
                            <p class="text-center py-3">Tidak ada pertanyaan yang belum dijawab.</p>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Pertanyaan</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($questions as $question) : ?>
                                            <tr>
                                                <td><?= $question['id'] ?></td>
                                                <td><?= $question['question'] ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($question['created_at'])) ?></td>
                                                <td>
                                                    <span class="badge bg-warning">Pending</span>
                                                </td>
                                                <td>
                                                    <a href="<?= site_url('adminpanel/answerquestion/' . $question['id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-reply"></i> Jawab
                                                    </a>
                                                    <a href="<?= site_url('adminpanel/deletequestion/' . $question['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>