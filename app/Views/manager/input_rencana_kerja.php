<div class="container-table">
    <div class="container">
        <div>
            <h2>Input Rencana Kerja</h2>
            <form action="/rencana-kerja/save" method="POST" class="rencana-form">
                <?= csrf_field() ?>

                <!-- Pesan berhasil -->
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="success-message">
                        <?= session()->getFlashdata('message'); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="judul">Judul Rencana Kerja:</label>
                    <input type="text" name="judul" id="judul" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi:</label>
                    <textarea name="deskripsi" id="deskripsi" required></textarea>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal dan Waktu:</label>
                    <input type="date" name="tanggal" id="tanggal" required>
                </div>

                <button type="submit" class="btn-submit">Simpan Rencana Kerja</button>
            </form>
        </div>
    </div>
</div>

<!-- CSS Styling -->
<style>
    label[for="status"] {
        position: relative;
        top: -16px;
    }

    select[id="status"] {
        position: relative;
        height: 47px;
        top: -9px;
        border: 1px solid #9e9e9e;
    }

    #status {
        display: block;
        visibility: visible;
        opacity: 1;
    }

    .rencana-form {
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

    .form-container {
        width: 60%;
        margin: 30px auto;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
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
        top: -5px;
        width: 760px;
        height: 150px;
        background-color: #fff;
    }

    label[for=deskripsi] {
        position: relative;
        top: -10px;
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
    width: 256px;
    height: 47px;
    top: 61px;
    right: -261px;
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