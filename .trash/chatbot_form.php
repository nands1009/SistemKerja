<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asisten Virtual</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .assistant-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .card-header {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }

        .card-body {
            flex-grow: 1;
            padding: 10px;
            margin-bottom: 15px;
            max-height: 300px;
            overflow-y: auto;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .input-group {
            display: flex;
            margin-top: 10px;
        }

        .input-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            flex-grow: 1;
            margin-right: 10px;
        }

        .input-group button {
            background-color: #FF2E00;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .input-group button:hover {
            background-color: #e50000;
        }

        .response {
            margin-top: 10px;
            padding: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .user-message {
            background-color: #e9e9ff;
            text-align: left;
        }

        .assistant-message {
            background-color: #d9f7be;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="assistant-card">
        <div class="card-header">
            Asisten Virtual
        </div>

        <div class="card-body">
            <!-- Menampilkan pesan pengguna -->
            <?php if (isset($message)): ?>
                <div class="response user-message">
                    <strong>Anda: </strong><?= esc($message) ?>
                </div>
            <?php endif; ?>

            <!-- Menampilkan respons dari backend -->
            <?php if (isset($response)): ?>
            <div class="response assistant-message">
                <strong>Asisten: </strong><?= esc($response) ?>
            </div>
            <?php endif; ?>
        </div>

        <form action="/chatbot/chat" method="post">
            <div class="input-group">
                <input type="text" name="message" id="userInput" placeholder="Tanyakan sesuatu..." required>
                <button type="submit">Kirim</button>
            </div>
        </form>
    </div>
</body>
</html>
