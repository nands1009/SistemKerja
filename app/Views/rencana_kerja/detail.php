<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rencana Kerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-details {
            width: 100%;
            min-height: 609px;
            position: relative;

            padding-top: 10px;
            padding-bottom: 10px;
        }

        .form h2 {
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
        }

        .card {
            margin: 20px auto;
            border-radius: 8px;
            width: 100%;
            max-width: 800px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            text-align: left;
            top: 57px;
            box-sizing: border-box;
            background-color: #f9f9f9;

        }

        .card h3 {
            font-size: 22px;
            color: #333;
            margin: 10px 0;
        }

        .card p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }

        .status {
            font-size: 14px;
            font-weight: bold;
            color: #FF2E00;
        }

        .card .detail-item {
            margin-bottom: 8px;
        }

        .no-data {
            text-align: center;
            width: 100%;
            font-size: 18px;
            color: #888;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #FF2E00;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #cc2400;
        }

        .card-container-detairencana h2{
            color: #333333;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-details">
        <div class="card-container-detairencana">
            <h2>Detail Rencana Kerja</h2>

            <?php if (!empty($rencana)) : ?>
                <div class="card">
                    <div class="detail-item"><strong>Judul:</strong> <?= esc($rencana['judul']); ?></div>
                    <div class="detail-item"><strong>Deskripsi:</strong> <?= esc($rencana['deskripsi']); ?></div>
                    <div class="detail-item"><strong>Tanggal:</strong> <?= esc($rencana['tanggal']); ?></div>
                </div>
            <?php else : ?>
                <p class="no-data">No Data Available</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>