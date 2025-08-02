<!-- resources/views/rencana_kerja/add.php -->

<body>
    <div class="container">
        <h2 class="create">Tambah Rencana Kerja</h2>

        <form action="<?= site_url('rencana_kerja/save') ?>" method="POST">

            <label for="judul" class="form-label">Judul</label>
            <input type="text" class="form-control" id="judul" name="judul" required>


            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>


            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>

            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>


</body>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;

    }

    h2[class="create"]{
        position: relative;
        right: -181px;
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

    form[action="<?= site_url('rencana_kerja/save') ?>"] {
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
        width: 173px;
    height: 47px;
    top: 10px;
    right: -280px;
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

    button[type="submit"]:hover {
        background-color: #FF2E00;
        color: white;
        box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
    }

    button[type="submit"]:active {
        background-color: #FF2E00;
        color: white;
        box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
        transform: translateY(3px);
        transform: translateY(-2px);
        transition: transform 34ms;
    }


    label[for="status"] {
        position: relative;
        top: -55px;
        font-weight: bold;
    }

    select[id="status"] {
        position: relative;
        height: 47px;
        top: -56px;
        display: block;
        width: 100%;
        right: -1px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    label[for="tanggal"] {
        position: relative;
        top: -38px;
        font-weight: bold;
    }

    input[type="date"] {
        position: relative;
        width: 75rem;
        top: -39px;
    }

    .container h1 {
        margin-left: 221px;
        font-family: 'Arial Narrow', sans-serif;
        font-weight: bold;
    }
</style>


</html>