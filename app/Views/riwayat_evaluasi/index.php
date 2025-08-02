<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Evaluasi Kinerja</title>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container-table {
            position: relative;
            background-color: white;
            height: 1110px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        .nav-pills {
            background-color: #FF2E00;
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        }

        .nav-link {
            position: relative;
            width: 388px;
            height: 45px;
  
            left: 1px;
            color: white;
            text-align-last: center;
           border-radius: 20px;
        }
        .nav-pills>li.active>a, .nav-pills>li.active>a:focus, .nav-pills>li.active>a:hover {
            background-color:  #f44336;
        }
    .nav>li>a:hover{
        background-color:   #f44336;
  
            color: white;
        }


        .container {
            position: relative;
            top: 150px;
        }

        .section {
            margin-top: 20px;
        }

        table {
            margin-top: 20px;
        }

        h2 {
            position: absolute;
            top: -90px;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            color: #333;
            left: 8px;
            text-align: center;
        }

        h4 {
            margin-top: -20px;
            text-align: center;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container-table">
        <!-- Navbar Pills -->
        <div class="container">
            <h2>Riwayat Evaluasi Kinerja</h2>

            <!-- Nav Pills -->
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-penilaian-tab" data-toggle="pill" href="#pills-penilaian" role="tab" aria-controls="pills-penilaian" aria-selected="true">Riwayat Penilaian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-sp-tab" data-toggle="pill" href="#pills-sp" role="tab" aria-controls="pills-sp" aria-selected="false">Riwayat SP</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-penghargaan-tab" data-toggle="pill" href="#pills-penghargaan" role="tab" aria-controls="pills-penghargaan" aria-selected="false">Riwayat Penghargaan</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="pills-tabContent">
                <!-- Riwayat Penilaian -->
                <div class="tab-pane fade show active" id="pills-penilaian" role="tabpanel" aria-labelledby="pills-penilaian-tab">
                    <div class="section">
                        <h4>Riwayat Penilaian</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                <th>No</th>
                                <th>Tanggal dan Waktu Penilaian</th>
                                <th>Nama</th>
                                <th>Penilaian</th>
                                <th>Evaluasi Dari</th>
                                <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Memastikan data ada sebelum menampilkan tabel -->
                            <?php if (!empty($penilaian_pegawai)): ?>
                                <?php $no = 1; // Inisialisasi nomor urut 
                                ?>
                                <?php foreach ($penilaian_pegawai as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($row['tanggal_penilaian']) ?></td>
                                        <td><?= esc($row['pegawai_id']) ?></td>
                                        <td><?= esc($row['nilai']) ?></td>
                                        <td><?= esc($row['manajer_id']) ?></td>
                                        <td><?= esc($row['catatan']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Data penilaian belum tersedia.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Surat Peringatan (SP) -->
                <div class="tab-pane fade" id="pills-sp" role="tabpanel" aria-labelledby="pills-sp-tab">
                    <div class="section">
                        <h4>Riwayat Surat Peringatan (SP)</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($riwayatSP)): ?>
                                    <?php foreach ($riwayatSP as $sp): ?>
                                        <tr>
                                            <td><?= $sp['tanggal']; ?></td>
                                            <td><?= $sp['deskripsi']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Belum ada surat peringatan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Penghargaan -->
                <div class="tab-pane fade" id="pills-penghargaan" role="tabpanel" aria-labelledby="pills-penghargaan-tab">
                    <div class="section">
                        <h4>Riwayat Penghargaan</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($riwayatPenghargaan)): ?>
                                    <?php foreach ($riwayatPenghargaan as $penghargaan): ?>
                                        <tr>
                                            <td><?= $penghargaan['tanggal']; ?></td>
                                            <td><?= $penghargaan['deskripsi']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Belum ada penghargaan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk mengaktifkan Pills -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>