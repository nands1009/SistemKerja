<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Edit Jawaban</h2>

<div class="card">
    <div class="card-body">
        <form action="<?= site_url('adminpanel/updateanswer') ?>" method="post">
            <input type="hidden" name="id" value="<?= $question['id'] ?>">
            
            <div class="mb-3">
                <label for="question" class="form-label">Pertanyaan:</label>
                <div class="form-control bg-light"><?= $question['question'] ?></div>
            </div>
            
            <div class="mb-3">
                <label for="answer" class="form-label">Jawaban:</label>
                <textarea name="answer" id="answer" rows="5" class="form-control" required><?= $question['answer'] ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="tag" class="form-label">Tag/Kategori:</label>
                <input type="text" name="tag" id="tag" class="form-control" value="<?= $question['tag'] ?? 'general' ?>" required>
                <small class="text-muted">Tag akan digunakan untuk mengelompokkan pertanyaan sejenis.</small>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('adminpanel/answeredquestions') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Jawaban
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>