<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan Kerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;

        }


        .form h2 {

            position: relative;
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            top: -83px;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: -21px;

        }

        form {
            position: relative;
            right: 210px;
            width: 798px;
            height: 513px;
            top: 41px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 8px;
        }

        label[for="judul"] {
            position: relative;
            top: -60px;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;

        }

        label[for="deskripsi"] {
            position: relative;
            top: -53px;
            font-weight: bold;
            display: block;

        }

        textarea[id="deskripsi"] {
            position: relative;
            top: -46px;

            display: block;
            height: 140px;
            padding: 13px;
            border: 1px solid #ccc;
        }

        input[type="text"] {
            width: 97%;
            position: relative;
            padding: 10px;
            top: -53px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            color: black;
        }

        label[for="foto_dokumen"] {
            position: relative;
            top: -35px;
            font-weight: bold;
            display: block;
            height: 140px;
        }

        input[type="file"] {
            position: relative;
            top: -147px;
            font-weight: bold;
            display: block;
            height: 23px;
        }

        button[type="submit"] {
            position: relative;
            top: -92px;
            right: -309px;
            background-color: #FF2E00;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #ee6e73;
        }

        label[for="status"] {
            position: relative;
            top: -135px;
        }


        select[id="status"] {
            position: relative;
            height: 47px;
            top: -134px;
            display: block;
            width: 100%;
            right: 9px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Laporan Kerja</h2>

        <!-- Form untuk mengedit laporan -->
        <form action="<?= site_url('laporan_kerja/update/' . $laporan[0]->id); ?>" method="post" enctype="multipart/form-data">

            <!-- CSRF Token for security -->
            <?= csrf_field(); ?>

            <label for="judul">Judul Laporan:</label>
            <input type="text" id="judul" name="judul" value="<?= $laporan[0]->judul ?>" required>

            <label for="deskripsi">Deskripsi Laporan:</label>
            <textarea id="deskripsi" name="deskripsi" required><?= $laporan[0]->deskripsi ?></textarea>

            <label for="foto_dokumen">Foto / Dokumen:</label>
            <input type="file" id="foto_dokumen" name="foto_dokumen">

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Pending" <?= $laporan[0]->status == ('Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="berjalan" <?= $laporan[0]->status == ('berjalan') ? 'selected' : '' ?>>Berjalan</option>
                <option value="selesai" <?= $laporan[0]->status == ('selesai') ? 'selected' : '' ?>>Selesai</option>
            </select>

            <!-- Hidden field untuk menyimpan nama dokumen yang sudah ada jika tidak ada dokumen baru yang di-upload -->
            <input type="hidden" name="existing_foto_dokumen" value="<?= $laporan[0]->foto_dokumen ?>">

            <button type="submit">Perbarui Laporan</button>
        </form>
    </div>
</body>

</html>