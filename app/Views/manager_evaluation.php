<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Evaluation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <h3 class="text-center">Evaluation of Managers</h3>

        <!-- Display success or error message -->
        <?php if (session()->getFlashdata('message')) : ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= \Config\Services::validation()->listErrors(); ?>
            </div>
        <?php endif; ?>

        <!-- Evaluation Form -->
        <form action="/manager-evaluation/submitEvaluation" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="manager_id">Select Manager</label>
                <select name="manager_id" id="manager_id" class="form-control" required>
                    <option value="">-- Select Manager --</option>
                    <?php foreach ($managers as $manager): ?>
                        <option value="<?= $manager['id']; ?>"><?= $manager['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="score">Score (1-5)</label>
                <input type="number" name="score" class="form-control" required min="1" max="5">
            </div>

            <div class="form-group">
                <label for="comments">Comments</label>
                <textarea name="comments" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Evaluation</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>