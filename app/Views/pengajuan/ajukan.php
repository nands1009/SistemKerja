<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Penghargaan atau SP</title>
    <style>
        /* Global Styles */
        .container-table {
            position: relative;
            background-color: white;
            height: 1101px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 90px;
            color: #333;
            position: relative;
            font-size: 35px;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: 78px;
            top: -35px;
        }

        h2 {

            font-size: 1.2em;
            position: relative;
            top: -14px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
        }

        form {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            max-width: 600px;
            width: 100%;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }


        select,
        input[type="text"],
        textarea {
            width: 98%;
            padding: 4px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
            background-color: white;
        }



        textarea {
            resize: vertical;
            height: 100px;
            float: inline-end;
            padding: 4px;
            width: 100%;
        }

        /* Additional Styling for Layout */
        .form-container {
            max-width: 800px;
            margin: 30px auto;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        /* Alert Styling */
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        select option {
            color: #333;
            /* Warna teks agar lebih kontras */
            background-color: #fff;
            /* Warna background agar tetap jelas */
            font-size: 1em;
            /* Pastikan ukuran teks cukup besar */
        }

        select {
            color: #333;
            background-color: #f9f9f9;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        .option {
            color: #007bff;

        }

        button {
            position: relative;
    width: 196px;
    height: 41px;
    top: 4px;
    right: -186px;
    background-color: #FF2E00;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 8px;
    box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
    transform: translateY(-4px);
    transition: transform 600ms cubic-bezier(0.3, 0.7, 0.4, 1);

        }

        button:hover {
            background-color: #FF2E00;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
        }

        button:active {
            background-color: #FF2E00;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
            transform: translateY(3px);
            transform: translateY(-2px);
            transition: transform 34ms;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <div class="container">
            <h1>Pengajuan Penghargaan atau SP</h1>

            <!-- Success Flash Message -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success'); ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <!-- Pengajuan Penghargaan Form -->
                <form action="<?= site_url('/pengajuan/submitPenghargaan'); ?>" method="post" class="form-section">
                    <h2>Pengajuan Penghargaan</h2>

                    <label for="pegawai_id">Pilih Pegawai:</label>
                    <select name="pegawai_id" id="pegawai_id" required>
                        <?php $no = 1;
                        foreach ($pegawais as $pegawai): ?>
                            <option value="<?= $pegawai['id']; ?>"><?= $no++; ?>. <?= $pegawai['username']; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="jenis_penghargaan">Jenis Penghargaan:</label>
                    <input type="text" name="jenis_penghargaan" id="jenis_penghargaan" required>

                    <label for="alasan">Alasan Penghargaan:</label>
                    <textarea name="alasan" id="alasan" rows="4" required></textarea>

                    <button type="submit">Ajukan Penghargaan</button>
                </form>

                <!-- Pengajuan Surat Peringatan (SP) Form -->
                <form action="<?= site_url('/pengajuan/submitSP'); ?>" method="post" class="form-section">
                    <h2>Pengajuan Surat Peringatan (SP)</h2>

                    <label for="pegawai_id_sp">Pilih Pegawai:</label>
                    <select name="pegawai_id" id="pegawai_id_sp" required>
                        <?php $no = 1;
                        foreach ($pegawais as $pegawai): ?>
                            <option value="<?= $pegawai['id']; ?>"><?= $no++; ?>. <?= $pegawai['username']; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="alasan_sp">Alasan SP:</label>
                    <textarea name="alasan" id="alasan_sp" rows="4" required></textarea>

                    <button type="submit">Ajukan SP</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>