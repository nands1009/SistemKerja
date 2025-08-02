<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pengajuan Penghargaan</title>
    <style>
        /* General Body Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Container for Centering the Content */
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Title Styles */
        h3 {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        /* Success Message */
        .success-message {
            padding: 15px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        /* Table Header Styling */
        thead {
            background-color: #007bff;
            color: white;
        }

        th {
            font-size: 16px;
        }

        /* Table Row Alternating Background */
        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Badge for Status */
        td span {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            color: white;
        }

        .badge-approved {
            background-color: #28a745;
        }

        .badge-pending {
            background-color: #ffc107;
        }

        .badge-rejected {
            background-color: #dc3545;
        }

        /* Button Styles */
        .btn-approve,
        .btn-reject {
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            margin: 5px;
            display: inline-block;
        }

        .btn-approve {
            background-color: #28a745;
        }

        .btn-reject {
            background-color: #dc3545;
        }

        /* Button Hover Effects */
        .btn-approve:hover {
            background-color: #218838;
        }

        .btn-reject:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Approval Pengajuan Penghargaan</h3>

        <?php if (session()->getFlashdata('message')) : ?>
            <div class="success-message">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>

        <table>
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
                        <td><?= $item['pegawai_id'] ?></td>
                        <td><?= $item['jenis_penghargaan'] ?></td>
                        <td><?= $item['alasan'] ?></td>
                        <td>
                            <span class="badge 
                                <?= ($item['status'] === 'approved') ? 'badge-approved' : ($item['status'] === 'pending' ? 'badge-pending' : 'badge-rejected') ?>">
                                <?= $item['status'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="/approval-direksi/approve/<?= $item['id'] ?>" class="btn-approve">Setujui</a>
                            <a href="/approval-direksi/reject/<?= $item['id'] ?>" class="btn-reject">Tolak</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>