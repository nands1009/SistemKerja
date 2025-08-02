<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            box-sizing: border-box;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 320px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        label {
font-size: 12px;
    color: #555;
    position: relative;
    top: -5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            padding: 8px;
            font-size: 13px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 8px;
            background-color: #FF2E00;
            color: white;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }

        button:hover {
            background-color: #FF2E00;
        }

        .error-message {
            color: red;
            font-size: 12px;
            text-align: center;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group input,
        .form-group select {
            margin-bottom: 10px;
        }

        /* Responsiveness */
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 15px;
            }

            button {
                font-size: 13px;
            }
        }

        /* Main styling for overlay and form */
        main {
            position: absolute;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 500px;
            /* Reduced width for main section */
            height: auto;
            padding: 30px;
            margin: 20px;
            background-color: #FF2E00;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 10px 50px;
            font-family: 'Montserrat', sans-serif;
        }

        .overlay h1 {
            font-size: 30px;
            /* Reduced font size */
            color: white;
            text-align: center;
        }

        .overlay p {
            font-size: 14px;
            color: white;
            text-align: center;
        }

        .overlay a {
            font-size: 14px;
            color: white;
            padding: 8px 20px;
            text-align: center;
            text-decoration: none;
            background-color: #FF2E00;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
        }

        .overlay a:hover {
            background-color: #FF2E00;
        }

        img {
            position: relative;
            width: 18%;
            top: -78px;
        }

        label[for="username"] {
            position: relative;
            top: -54px;
        }

        label[for="email"] {
            position: relative;
            top: -54px;
        }

        label[for="password"] {
            position: relative;
            top: -54px;
        }
    </style>
</head>

<body>
    <main class="py-5 my-auto">
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-right">
                    <h1>HAI,SELAMAT DATANG!</h1>
                    <p>Apakah kamu sudah memiliki akun? <br> klik di bawah untuk masuk</br>
                        <a class="btn" href="/login">LOGIN</a>
                </div>
            </div>
        </div>

        <div class="container">

            <h2>Registrasi Pengguna</h2>
            <img src="/img/armindo.png" alt="Logo">
            <form action="/auth/doRegister" method="post">
                <?= csrf_field() ?>

                <!-- Name Field -->
                <div class="form-floating mb-3">
                    <input type="text" name="username" id="username" class="form-control" placeholder="Nama Lengkap" required>
                    <label for="username">Nama Lengkap</label>
                </div>

                <!-- Email Field -->
                <div class="form-floating mb-3">
                    <input type="email" name="email" id="email" class="form-control" placeholder="aaa@armindojaya.co.id" required>
                    <label for="email">Email</label>
                </div>

                <!-- Password Field -->
                <div class="form-floating mb-3">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>

                <!-- Confirm Password Field -->


                <!-- Role Field -->
                <div class="form-group mb-3">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="manager">Manager</option>
                        <option value="hrd">HRD</option>
                        <option value="direksi">Direksi</option>
                    </select>
                </div>

                <!-- Divisi Field -->
                <div class="form-group mb-3">
                    <label for="divisi">Divisi</label>
                    <select name="divisi" id="divisi" class="form-control" required>
                        <option value="tidak_ada">Tidak Ada</option>
                        <option value="hrd dan ga">HRD dan GA</option>
                        <option value="produksi">Produksi</option>
                        <option value="marketing">Marketing</option>
                        <option value="gudang">Gudang</option>
                        <option value="hse">HSE</option>
                        <option value="project manager">Project Manager</option>
                        <option value="docon">Docon</option>
                        <option value="qc">QC</option>
                        <option value="it">IT</option>
                        <option value="purchasing">Purchasing</option>
                        <option value="finance">Finance</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-lg submit-button">Registrasi</button>
            </form>

            <?php if (isset($errors)): ?>
                <div class="error-message">
                    <?= implode('<br>', $errors) ?>
                </div>
            <?php endif; ?>

        </div>
    </main>

</body>

</html>