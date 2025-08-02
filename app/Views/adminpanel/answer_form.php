<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Jawab Pertanyaan' ?></title>
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
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <h2 class="mb-4">Jawab Pertanyaan</h2>
                
                <div class="card">
                    <div class="card-body">
                        <form action="<?= site_url('adminpanel/saveanswer') ?>" method="post">
                            <input type="hidden" name="id" value="<?= $question['id'] ?>">
                            
                            <div class="mb-3">
                                <label for="question" class="form-label">Pertanyaan:</label>
                                <div class="form-control bg-light"><?= $question['question'] ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="answer" class="form-label">Jawaban:</label>
                                <textarea name="answer" id="answer" rows="5" class="form-control" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tag" class="form-label">Tag/Kategori:</label>
                                <input type="text" name="tag" id="tag" class="form-control" value="general" required>
                                <small class="text-muted">Tag akan digunakan untuk mengelompokkan pertanyaan sejenis.</small>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="<?= site_url('adminpanel/pendingquestions') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Jawaban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>