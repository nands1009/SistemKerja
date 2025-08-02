<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
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

        #chat-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 500px;
        }

        #chatbox {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            border-bottom: 1px solid #ddd;
        }

        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            max-width: 80%;
        }

        .user {
            background-color: #007bff;
            color: white;
            align-self: flex-start;
        }

        .bot {
            background-color: #f1f1f1;
            color: black;
            align-self: flex-end;
        }

        input[type="text"] {
            width: calc(100% - 90px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin: 10px;
            font-size: 16px;
            outline: none;
        }

        button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div id="chat-container">
        <div id="chatbox">
            <!-- Pesan chat akan ditambahkan di sini -->
        </div>
        <div style="display: flex; padding: 10px;">
            <input type="text" id="question" placeholder="Tanyakan sesuatu..." />
            <button onclick="askQuestion()">Kirim</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
      function askQuestion() {
    var question = $('#question').val(); // Ambil nilai input dari pengguna
    if (question.trim() === "") {
        alert("Pertanyaan tidak boleh kosong!");
        return;
    }

    var questionId = new Date().getTime(); // Membuat ID unik untuk pertanyaan menggunakan timestamp

    // Menambahkan pesan pengguna ke chatbox
    $('#chatbox').append('<div class="message user" id="user-' + questionId + '"><strong>Anda:</strong> ' + question + '</div>');
    $('#chatbox').append('<div class="message bot" id="bot-' + questionId + '"><strong>Bot:</strong> <em>Menunggu jawaban...</em></div>');
    $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);

    // Mengirimkan pertanyaan dan questionId ke server menggunakan AJAX
    $.ajax({
        url: '/chatbot/chat', // Pastikan URL ini sesuai dengan route di Routes.php
        type: 'POST',
        data: { question: question, id: questionId },
        dataType: 'json',
        success: function(response) {
            // Mengubah "Menunggu jawaban..." dengan jawaban dari admin atau bot
            if (response.status === 'waiting_for_admin') {
                $('#chatbox').find('div.message.bot#bot-' + questionId + ' em').text('Jawaban sedang ditunggu dari admin...');
            } else {
                $('#chatbox').find('div.message.bot#bot-' + questionId + ' em').text(response.answer);
            }
            $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
            $('#question').val('');
        },
        error: function(xhr, status, error) {
            console.log("Error: ", error);
            alert("Terjadi kesalahan. Coba lagi nanti.");
        }
    });
}
        // Interval untuk cek apakah admin sudah memberikan jawaban
   setInterval(function() {
    var questionId = $('#chatbox').find('.message.bot em').last().parent().attr('id').split('-')[1];

    // Pastikan questionId ada
    if (!questionId) return;

    $.ajax({
        url: '/chatbot/getLatestAnswer', // Pastikan URL ini sesuai dengan route di Routes.php
        type: 'GET',
        data: { questionId: questionId }, // Mengirimkan questionId ke server
        dataType: 'json',
        success: function(response) {
            // Tangani response sukses dan update jawaban jika tersedia
            if (response.status === 'answered') {
                $('#chatbox').find('div.message.bot#bot-' + questionId + ' em').text(response.answer);
                $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight); // Scroll ke bawah
            } else if (response.status === 'waiting_for_admin') {
                $('#chatbox').find('div.message.bot#bot-' + questionId + ' em').text(response.answer);
                $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight); // Scroll ke bawah
            }
        },
        error: function(xhr, status, error) {
            console.log("Error: ", error);
            alert("Terjadi kesalahan. Coba lagi nanti.");
        }
    });
}, 5000000); // Cek setiap 5 detik
    </script>
</body>

</html>