<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Chatbot</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }
    
    .container {
      max-width: 700px;
      margin: 30px auto;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      background-color: #fff;
      overflow: hidden;
    }
    
    .chat-header {
      background-color: #2e75b6;
      color: white;
      padding: 15px 20px;
      font-size: 18px;
      font-weight: bold;
    }
    
    #chatbox {
      height: 450px;
      overflow-y: auto;
      padding: 20px;
      background-color: #f9f9f9;
    }
    
    .message-container {
      display: flex;
      margin-bottom: 15px;
    }
    
    .user-message {
      justify-content: flex-end;
    }
    
    .bot-message {
      justify-content: flex-start;
    }
    
    .message {
      max-width: 70%;
      padding: 10px 15px;
      border-radius: 18px;
      font-size: 14px;
      line-height: 1.5;
    }
    
    .user .message {
      background-color: #dcf8c6;
      border: 1px solid #c7edb5;
      text-align: right;
    }
    
    .bot .message {
      background-color: #fff;
      border: 1px solid #e1e1e1;
      text-align: left;
    }
    
    .info-message {
      background-color: #e3f2fd !important;
      border: 1px solid #bbdefb !important;
      padding: 12px 15px !important;
    }
    
    .typing-indicator {
      display: none;
      padding: 10px 15px;
      background-color: #eee;
      border-radius: 18px;
      width: 50px;
      text-align: center;
    }
    
    .input-container {
      display: flex;
      padding: 10px;
      background-color: #f0f0f0;
      border-top: 1px solid #e1e1e1;
    }
    
    #userInput {
      flex: 1;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 25px;
      outline: none;
      font-size: 14px;
    }
    
    #sendButton {
      border: none;
      background-color: #2e75b6;
      color: white;
      border-radius: 50%;
      width: 44px;
      height: 44px;
      margin-left: 8px;
      cursor: pointer;
      font-size: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    #sendButton:hover {
      background-color: #235a8c;
    }
    
    .timestamp {
      font-size: 11px;
      color: #999;
      margin-top: 5px;
      display: block;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="chat-header">
    Chatbot Asisten
  </div>
  
  <div id="chatbox">
    <div class="message-container bot-message">
      <div class="bot">
        <div class="message">
          Halo! Saya adalah chatbot asisten Anda. Ada yang bisa saya bantu hari ini?
        </div>
        <span class="timestamp">Hari ini, <?php echo date('H:i'); ?></span>
      </div>
    </div>
  </div>
  
  <div class="input-container">
    <input type="text" id="userInput" placeholder="Tulis pesan Anda di sini..." autocomplete="off">
    <button id="sendButton" onclick="sendMessage()">→</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Auto-scroll ke bawah saat halaman dimuat
  var chatbox = document.getElementById('chatbox');
  chatbox.scrollTop = chatbox.scrollHeight;
  
  // Event listener untuk tombol Enter
  document.getElementById('userInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      sendMessage();
    }
  });
});

// Fungsi untuk menambahkan format tanggal dan waktu
function getTimestamp() {
  const now = new Date();
  const hours = now.getHours().toString().padStart(2, '0');
  const minutes = now.getMinutes().toString().padStart(2, '0');
  return `Hari ini, ${hours}:${minutes}`;
}

// Fungsi untuk menampilkan indikator "typing"
function showTypingIndicator() {
  const typingHtml = `
    <div class="message-container bot-message" id="typing-indicator">
      <div class="bot">
        <div class="typing-indicator">
          <span>...</span>
        </div>
      </div>
    </div>
  `;
  document.getElementById('chatbox').insertAdjacentHTML('beforeend', typingHtml);
  document.getElementById('typing-indicator').style.display = 'flex';
  scrollToBottom();
}

// Fungsi untuk menyembunyikan indikator "typing"
function hideTypingIndicator() {
  const indicator = document.getElementById('typing-indicator');
  if (indicator) {
    indicator.remove();
  }
}

function sendMessage() {
  const userInput = document.getElementById('userInput');
  const message = userInput.value.trim();
  
  if (message === '') return;
  
  const chatbox = document.getElementById('chatbox');
  
  // Tambahkan pesan pengguna
  const userMessageHtml = `
    <div class="message-container user-message">
      <div class="user">
        <div class="message">${message}</div>
        <span class="timestamp">${getTimestamp()}</span>
      </div>
    </div>
  `;
  chatbox.insertAdjacentHTML('beforeend', userMessageHtml);
  
  // Bersihkan input
  userInput.value = '';
  
  // Tampilkan indikator "typing"
  showTypingIndicator();
  
  // Kirim pesan ke server
  fetch('/chatbot/getResponse', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'message=' + encodeURIComponent(message)
  })
  .then(response => response.json())
  .then(data => {
    // Sembunyikan indikator "typing"
    hideTypingIndicator();
    
    // Deteksi apakah ini adalah pesan informasi (mengandung kata "Maaf, saya belum memiliki jawaban")
    const isInfoMessage = data.message.includes("Maaf, saya belum memiliki jawaban");
    const messageClass = isInfoMessage ? 'message info-message' : 'message';
    
    // Tambahkan pesan bot
    const botMessageHtml = `
      <div class="message-container bot-message">
        <div class="bot">
          <div class="${messageClass}">${data.message}</div>
          <span class="timestamp">${getTimestamp()}</span>
        </div>
      </div>
    `;
    chatbox.insertAdjacentHTML('beforeend', botMessageHtml);
    
    // Scroll ke bawah untuk melihat pesan terbaru
    scrollToBottom();
  })
  .catch(error => {
    console.error('Error:', error);
    hideTypingIndicator();
    
    // Tampilkan pesan error
    const errorMessageHtml = `
      <div class="message-container bot-message">
        <div class="bot">
          <div class="message info-message">Maaf, terjadi kesalahan dalam memproses pesan Anda. Silakan coba lagi.</div>
          <span class="timestamp">${getTimestamp()}</span>
        </div>
      </div>
    `;
    chatbox.insertAdjacentHTML('beforeend', errorMessageHtml);
    scrollToBottom();
  });
}

function scrollToBottom() {
  const chatbox = document.getElementById('chatbox');
  chatbox.scrollTop = chatbox.scrollHeight;
}
</script>

</body>
</html>