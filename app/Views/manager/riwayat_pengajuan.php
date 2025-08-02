<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        table.table-style {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table.table-style th,
        table.table-style td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table.table-style th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }

        table.table-style tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table.table-style tr:hover {
            background-color: #f1f1f1;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .table-style a {
            color: #2196F3;
            text-decoration: none;
        }

        .table-style a:hover {
            text-decoration: underline;
        }

        /* Responsiveness */
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 15px;
            }

            table.table-style th,
            table.table-style td {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Riwayat Pengajuan</h3>

        <?php if (session()->getFlashdata('message')) : ?>
            <div class="success-message">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>

        <table class="table-style">
            <thead>
                <tr>
                    <th>Jenis Pengajuan</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($riwayat as $item) : ?>
                    <tr>
                        <td><?= $item['jenis_pengajuan'] ?></td>
                        <td><?= $item['status'] ?></td>
                        <td><?= $item['catatan'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>