<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Pertanyaan Yang Sudah Dijawab</h2>

<div class="card">
    <div class="card-body">
        <?php if (empty($questions)) : ?>
            <p class="text-center py-3">Tidak ada pertanyaan yang sudah dijawab.</p>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pertanyaan</th>
                            <th>Jawaban</th>
                            <th>Tag</th>
                            <th>Tanggal Dijawab</th>
                            <th>Dijawab Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $question) : ?>
                            <tr>
                                <td><?= $question['id'] ?></td>
                                <td><?= $question['question'] ?></td>
                                <td><?= substr($question['answer'], 0, 100) . (strlen($question['answer']) > 100 ? '...' : '') ?></td>
                                <td><?= $question['tag'] ?? 'general' ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($question['updated_at'])) ?></td>
                                <td><?= $question['answered_by'] ?? 'Admin' ?></td>
                                <td>
                                    <a href="<?= site_url('adminpanel/editanswer/' . $question['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
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
<?= $this->endSection() ?>