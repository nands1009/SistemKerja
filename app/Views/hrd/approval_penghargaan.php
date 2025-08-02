<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pengajuan Penghargaan</title>
    <style>
        /* Container style */
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Title style */
        h3.text-center {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #4CAF50;
        }

        /* Alert message style */
        .alert {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: #dff0d8;
            color: #3c763d;
        }

        /* Table responsive style */
        .table-responsive {
            margin-top: 20px;
        }

        /* Table styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        .table-bordered {
            border: 1px solid #ddd;
        }

        .table-striped tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }

        /* Badge styles */
        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: white;
        }

        /* Button styles */
        .btn {
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            font-weight: 600;
            margin: 0 5px;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3 class="text-center">Approval Pengajuan Penghargaan</h3>

        <?php if (session()->getFlashdata('message')) : ?>
            <div class="alert text-center">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Pegawai</th>
                        <th>Jenis Penghargaan</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengajuan as $item) : ?>
                        <tr>
                            <td><?= htmlspecialchars($item['pegawai_id']) ?></td>
                            <td><?= htmlspecialchars($item['jenis_penghargaan']) ?></td>
                            <td><?= htmlspecialchars($item['alasan']) ?></td>
                            <td>
                                <span class="badge <?= ($item['status'] === 'approved') ? 'badge-success' : 'badge-warning' ?>">
                                    <?= htmlspecialchars($item['status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="/approval/approve/<?= $item['id'] ?>" class="btn btn-success">Setujui</a>
                                <a href="/approval/reject/<?= $item['id'] ?>" class="btn btn-danger">Tolak</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>