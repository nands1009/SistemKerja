<?= $this->extend('dashboard/admin.php') ?>

<?= $this->section('content')?>
<?= $this->include('dashboard/admin/layouts/card-name') ?>
<?= $this->include('adminpanel/dashboard') ?>
<?= $this->include('dashboard/admin/layouts/most_frequent_questions_chart') ?>
<?= $this->include('dashboard/admin/layouts/pertanyaan') ?>



<?= $this->endSection()?>