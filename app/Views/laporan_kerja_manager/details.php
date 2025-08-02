<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details Laporan Kerja</title>
    <style>
        /* Container untuk menampilkan konten */
        .container-details {
            width: 100%;
            height: 609px;
            position: relative;
            overflow: auto;
            overflow-x: hidden;
            /* Hide horizontal scrollbar */
            overflow-y: scroll;
            top: 10px;
            background-color: white;
        }

        .card {
            top: 25px;
            right: 0px;

            border-radius: 8px;
            width: 100%;
            /* Lebar otomatis */
            max-width: 800px;
            /* Lebar maksimum untuk kartu */
            height: auto;
            /* Menyesuaikan tinggi otomatis */
            padding: 20px;
            display: flex;
            flex-direction: row;
            gap: 20px;
            /* Jarak antar gambar dan teks */
            text-align: left;
            box-sizing: border-box;
            /* Memastikan padding tidak mengubah ukuran */

        }

        .card img {
            width: 100%;
            height: 292px;
            max-width: 299px;
            border-radius: 8px;
        }

        .card .card-content {
            flex: 1;

        }

        .card h3 {
            font-size: 20px;
            color: #333;
            margin: 10px 0;
        }

        .card p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
            text-align: left;
            line-height: 25px;
            display: flex;
            flex-direction: column;
            gap: 1px;
            align-items: flex-start;
        }

        .card .status {
            font-size: 14px;
            font-weight: bold;
            color: #FF2E00;
        }
    </style>
</head>

<body>
    <div class="container-details">
        <div class="card-container">
            <?php if (!empty($laporan)) : ?>
                <?php $row = $laporan; ?>
                <div class="card">
                <?php
                        $fileExtension = strtolower(pathinfo($row['foto_dokumen'], PATHINFO_EXTENSION));
                        ?>

                        <?php if ($fileExtension == 'pdf') : ?>
                            <!-- Menampilkan Ikon PDF menggunakan Font Awesome -->
                            <div style="display: inline-block;align-items: center;margin-top: 25px;">
                                <!-- Ikon PDF dari Font Awesome -->
                                <i class="fas fa-file-pdf" style="position: relative;font-size: 230px;color: #FF2E00;/* margin-right: 151px; *//* margin-left: 21px; */"></i>
                                <a href="<?= base_url('uploads/' . $row['foto_dokumen']) ?>" target="_blank" style="font-size: 30px;color: #FF2E00;text-decoration: none;text-align: center;">Lihat PDF</a>
                            </div>

                        <?php elseif (in_array($fileExtension, ['doc', 'docx'])) : ?>
                            <!-- Menampilkan Ikon Word menggunakan Font Awesome -->
                            <div style="display: inline-block;align-items: center;margin-top: 25px;">
                                <!-- Ikon Word -->
                                <i class="fas fa-file-word" style="position: relative;font-size: 230px;color: #2a9fd6;/* margin-right: 151px; *//* margin-left: 21px; */"></i>
                                <a href="<?= base_url('uploads/' . $row['foto_dokumen']) ?>" target="_blank" style="font-size: 30px;color: #2a9fd6;text-decoration: none;text-align: center;">Lihat Word</a>
                            </div>
                            <!-- Tombol untuk download Word -->

                        <?php elseif (in_array($fileExtension, ['xls', 'xlsx'])) : ?>
                            <!-- Menampilkan Ikon Excel menggunakan Font Awesome -->
                            <div style="display: inline-block;align-items: center;margin-top: 25px;">
                                <!-- Ikon Excel -->
                                <i class="fas fa-file-excel" style="position: relative;font-size: 230px;color: #1d9e40;/* margin-right: 151px; *//* margin-left: 21px; */"></i>
                                <a href="<?= base_url('uploads/' . $row['foto_dokumen']) ?>" target="_blank" style="font-size: 30px;color: #1d9e40;text-decoration: none;text-align: center;">Lihat Excel</a>
                            </div>
                            <!-- Tombol untuk download Excel -->

                        <?php elseif (in_array($fileExtension, ['ppt', 'pptx'])) : ?>
                            <!-- Menampilkan Ikon PowerPoint menggunakan Font Awesome -->
                            <div style="display: inline-block;align-items: center;margin-top: 25px;">
                                <!-- Ikon PowerPoint -->
                                <i class="fas fa-file-powerpoint" style="position: relative;font-size: 230px;color: #e34f26;/* margin-right: 151px; *//* margin-left: 21px; */"></i>
                                <a href="<?= base_url('uploads/' . $row['foto_dokumen']) ?>" target="_blank" style="font-size: 30px;color: #e34f26;text-decoration: none;text-align: center;">Lihat PowerPoint</a>
                            </div>
                            <!-- Tombol untuk download PowerPoint -->


                        <?php else : ?>
                            <!-- Menampilkan gambar jika bukan PDF, DOC, DOCX, XLS, atau XLSX -->
                            <img src="<?= !empty($row['foto_dokumen']) ? base_url('uploads/' . $row['foto_dokumen']) : 'https://via.placeholder.com/150?text=No+Image+Available'; ?>" alt="Document Image" style="max-width: 334px;height: 112px auto;width: 314px;">
                        <?php endif; ?>


                    <!-- Konten Card -->
                    <div class="card-content">
                        <p><strong>Nama:</strong> <?= esc($row['username']) ?></p>
                        <p><strong>Judul laporan:</strong> <?= esc($row['judul']) ?></p>
                        <p><strong>Divisi:</strong> <?= esc($row['divisi']) ?></p>
                        <p><strong>Jabatan:</strong> <?= esc($row['role']) ?></p>
                        <p><strong>Deskripsi:</strong> <?= esc($row['deskripsi']) ?></p>
                        <p><strong>Status Project:</strong> <?= esc($row['status']) ?></p>
                        <p class="status"><strong>Status Approval:</strong> <?= esc($row['status_approval']) ?></p>
                    </div>
                </div>
            <?php else : ?>
                <div class="no-data">
                    <p>No Data Available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
