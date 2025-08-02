<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 320px;
            height: 401px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 16px;
            font-size: 1.8rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 4px;
            /* Increased gap between form elements */
        }

        label {
font-size: 14px;
    color: #555;
    position: relative;
    top: -3px;
        }

        input[type="email"],
        input[type="password"] {
            padding: 9px;
            font-size: 13px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            position: relative;
            padding: 12px;
            background-color: #FF2E00;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
            top: 52px;
        }

        button:hover {
            background-color: #FF2E00;
        }

        .error-message {
            color: red;
            font-size: 14px;
            /* Increased font size */
            text-align: center;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        /* Responsiveness */
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 20px;
            }

            button {
                font-size: 15px;
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
            height: 593px;
            padding: 30px;
            margin: 20px;
            background-color: #FF2E00;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 10px 50px;
            font-family: 'Montserrat', sans-serif;
        }

        .overlay h1 {
            font-size: 30px;
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
    </style>
</head>

<body>
    <main class="py-5 my-auto">
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-right">
                    <h1>HAI,SELAMAT DATANG KEMBALI!</h1>
                    <p>Apakah kamu belum memiliki akun?
                        <br>
                        klik di bawa untuk mendaftar
                        </br>
                        <a class="waves-effect waves-dark btn" href="/register">DAFTAR</a>
                </div>
            </div>
        </div>

        <div class="container">
            <h2>Login Pengguna</h2>
            <img src="/img/armindo.png" alt="Logo">
            <form action="/auth/doLogin" method="post">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
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