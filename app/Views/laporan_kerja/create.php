<body>
    <div class="container-table">
        <div class="container">
            <div>
                <h2>Lembar Laporan Kerja</h2>
                <form action="/laporan_kerja/store" method="post" enctype="multipart/form-data" class="laporan-form">
                    <?= csrf_field() ?>
                    <!-- Display success message if available -->
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>

                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="judul">Judul Laporan</label>
                        <input type="text" name="judul" id="judul" value="<?= old('judul') ?>" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" value="<?= old('tanggal') ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" required class="form-control"><?= old('deskripsi') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="foto_dokumen">Foto / Dokumen</label>
                        <input type="file" name="foto_dokumen" id="foto_dokumen" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required class="form-control">
                            <option value="Pending" <?= old('status') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="berjalan" <?= old('status') == 'berjalan' ? 'selected' : '' ?>>Berjalan</option>
                            <option value="selesai" <?= old('status') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn-submit">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add this CSS to style the form -->
    <style>
        label[for="status"] {
            position: relative;
            top: 47px;
        }

        select[id="status"] {
            position: relative;

            height: 47px;
            top: 47px;
        }

        .laporan-form {
            position: relative;
            height: 700px;
            width: 800px;
            top: 80px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        }

        .container-table {
            position: relative;
            background-color: white;
            height: 1000px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;

        }

        h2 {
            position: relative;
            top: 20px;
            right: 22%;
            font-size: 35px;
            text-align: center;
            margin-top: 30px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type=file] {
            position: relative;
            top: 50px;
            width: 760px;
            height: 50px;
        }


        label[for=foto_dokumen] {
            position: relative;
            top: 47px;
        }

        textarea[name=deskripsi] {
            position: relative;
            top: 20px;
            width: 760px;
            height: 150px;
        }

        label[for=deskripsi] {
            position: relative;
            top: 17px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            position: relative;
            width: 100%;
            padding: 13px;
            font-size: 14px;
            border-radius: 2px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
        }

        .btn-submit {
            position: relative;
            width: 173px;
            height: 47px;
            top: 61px;
            right: -9px;
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

        .btn-submit:hover {
            background-color: #FF2E00;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
        }

        .btn-submit:active {
            background-color: #FF2E00;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
            transform: translateY(3px);
            transform: translateY(-2px);
            transition: transform 34ms;
        }


        .text-center {
            text-align: center;
        }

        /* Styling for the success message */
        .alert-success {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }


        
    </style>



</body>

</html>