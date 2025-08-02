<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Penghargaan atau SP</title>
    <style>


        h3 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
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

        form {
            margin-bottom: 40px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        h4 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        label {
            font-size: 14px;
            margin-bottom: 8px;
            color: #555;
            display: block;
        }

        select,
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
            margin-bottom: 15px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }

        button:hover {
            background-color: #45a049;
        }

        textarea {
            height: 100px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Pengajuan Penghargaan atau SP</h3>

        <?php if (session()->getFlashdata('message')) : ?>
            <div class="success-message">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>

        <form action="/pengajuan/managersubmitPenghargaan" method="POST">
            <h4>Pengajuan Penghargaan</h4>
            <div class="form-group">
                <label for="pegawai_id">Pegawai</label>
                <select name="pegawai_id" required>
                    <!-- Loop pegawai -->
                    <?php foreach ($pegawai as $pegawai) : ?>
                        <option value="<?= $pegawai['id'] ?>"><?= $pegawai['username'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jenis_penghargaan">Jenis Penghargaan</label>
                <input type="text" name="jenis_penghargaan" required>
            </div>

            <div class="form-group">
                <label for="alasan">Alasan Penghargaan</label>
                <textarea name="alasan" required></textarea>
            </div>

            <button type="submit">Ajukan Penghargaan</button>
        </form>

        <form action="/pengajuan/submitSP" method="POST">
            <h4>Pengajuan Surat Peringatan (SP)</h4>
            <div class="form-group">
                <label for="pegawai_id">Pegawai</label>
                <select name="pegawai_id" required>
                    <!-- Loop pegawai -->
                    <?php foreach ($pegawai as $pegawai) : ?>
                        <option value="<?= $pegawai['id'] ?>"><?= $pegawai['username'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="alasan">Alasan SP</label>
                <textarea name="alasan" required></textarea>
            </div>

            <button type="submit">Ajukan SP</button>
        </form>
    </div>
</body>

</html>