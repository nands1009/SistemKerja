<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kerja - <?= esc($laporan[0]['role']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
        }

        .content {
            width: 100%;
            margin: 30px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #FF2E00;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <div class="content">
        <h2>Laporan Kerja - <?= esc($laporan[0]['role']); ?></h2>
        <p><strong>Nama User:</strong> <?= esc($laporan[0]['username']); ?></p>
        <p><strong>Divisi:</strong> <?= esc($laporan[0]['divisi']); ?></p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Judul Laporan</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($laporan as $row) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= esc($row['tanggal']); ?></td>
                    <td><?= esc($row['judul']); ?></td>
                    <td><?= esc($row['deskripsi']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>