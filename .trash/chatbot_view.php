<<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Laporan Karyawan</title>
</head>
<body>
    <div>
        <h1>Chatbot Laporan Karyawan</h1>
        <textarea id="message" rows="4" cols="50"></textarea><br>
        <button onclick="sendMessage()">Kirim</button>
    </div>

    <div id="response">
        <h3>Response:</h3>
        <p id="chatResponse"></p>
    </div>

    <script>
        function sendMessage() {
            let message = document.getElementById("message").value;

            fetch('<?= base_url('chatbot/getResponse') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("chatResponse").textContent = data.response;
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>