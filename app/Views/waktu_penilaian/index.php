<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Waktu Penilaian | Sistem Kinerja</title>

    <!-- Bootstrap CSS -->
    <style>
        .container-table {
            position: relative;
            background-color: white;
            height: 1044px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            visibility: visible;
            padding: 20px;
        }

        h2 {
            position: relative;
            font-size: 35px;
            margin-top: -18px;
            color: #333;
            font-weight: bold;
            top: 48px;
            font-family: 'Arial Narrow', sans-serif;
            right: 0%;
        }

        /* Style umum untuk tabel */
        .table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            margin-top: 86px;
        }

        /* Style untuk header tabel */
        .table-container {
            overflow-y: auto;
            overflow-x: auto;
            width: 129%;
            margin: 15px auto;
            border-radius: 8px;
            height: 695px;
            position: relative;
            right: 169px;
        }

        table {
            width: 129%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px 15px;
            text-align: justify;
            border: 1px solid #ddd;
            vertical-align: super;
        }

        table th {
            background-color: #FF2E00;
            color: white;
            font-weight: bold;
            border-radius: 8px;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td {
            color: #333;
        }

        .btn-submit {
            position: relative;
            width: 173px;
            height: 47px;
            top: 73px;
            right: 0px;
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
            transition: transform 34ms;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            right: 10px;
            top: 0;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Waktu Penilaian</title>
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-submit {
            position: relative;
            width: 173px;
            height: 47px;
            top: 74px;
            right: -1px;
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

        .btn-danger {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-danger:hover {
            background-color: #d32f2f;
        }

        /* Table styles */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table, .th, .td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container-table">
    <h2>Pengaturan Waktu Penilaian</h2>

    <!-- Button to Add New Waktu Penilaian -->
    <a href="javascript:void(0);" onclick="openModal()" class="btn-submit mb-3">Tambah Waktu Penilaian</a>

    <!-- Table to display Waktu Penilaian -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NO</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($waktu_penilaian as $penilaian):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $penilaian['tanggal_mulai'] ?></td>
                    <td><?= $penilaian['tanggal_selesai'] ?></td>
                    <td>
                        <a href="<?= site_url('waktu_penilaian/delete/' . $penilaian['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete this record?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modal-body">
            <!-- Form Edit or Content to be Injected Here -->
            <form action="<?= site_url('waktu_penilaian/save') ?>" method="post">
                <div class="mb-3">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="datetime-local" name="tanggal_mulai" value="<?= old('tanggal_mulai') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input type="datetime-local" name="tanggal_selesai" value="<?= old('tanggal_selesai') ?>" required>
                </div>

                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Function to open the modal
    function openModal() {
        modal.style.display = "block";
    }

    // Function to close the modal
    function closeModal() {
        modal.style.display = "none";
    }

    // Close the modal when the user clicks anywhere outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

</body>
</html>
