<!DOCTYPE html>
<html>
<head>
    <title>Chatbot CI4</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --user-msg-color: #4361ee;
            --bot-msg-color: #f1f3f5;
            --border-radius: 12px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .chat-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .chat-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            position: relative;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .chat-header-icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .chat-box {
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background-color: #fff;
            scrollbar-width: thin;
            scrollbar-color: #ccc #f5f5f5;
        }
        
        .chat-box::-webkit-scrollbar {
            width: 6px;
        }
        
        .chat-box::-webkit-scrollbar-track {
            background: #f5f5f5;
        }
        
        .chat-box::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 20px;
        }
        
        .message-group {
            display: flex;
            margin-bottom: 20px;
            position: relative;
        }
        
        .user-group {
            justify-content: flex-end;
        }
        
        .message {
            padding: 12px 18px;
            border-radius: 18px;
            max-width: 70%;
            position: relative;
            line-height: 1.5;
            font-size: 0.95rem;
            word-wrap: break-word;
        }
        
        .user-message {
            background-color: var(--user-msg-color);
            color: white;
            border-bottom-right-radius: 4px;
            margin-left: auto;
        }
        
        .bot-message {
            background-color: var(--bot-msg-color);
            color: #333;
            border-bottom-left-radius: 4px;
            margin-right: auto;
        }
        
        .bot-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
            font-size: 1.2rem;
        }
        
        .loading-message {
            background-color: #e9ecef;
            color: #6c757d;
        }
        
        .loading-dots::after {
            content: '';
            animation: dots 1.5s infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }
        
        .chat-form {
            padding: 15px;
            background-color: #fff;
            border-top: 1px solid #eee;
        }
        
        .input-group {
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .form-control {
            border: none;
            padding: 15px 20px;
            font-size: 1rem;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            box-shadow: none;
            background-color: #f8f9fa;
        }
        
        .btn-send {
            border-radius: 0 30px 30px 0;
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding-left: 25px;
            padding-right: 25px;
            transition: all 0.2s ease;
        }
        
        .btn-send:hover, .btn-send:focus {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-send i {
            margin-left: 5px;
        }
        
        .time-stamp {
            font-size: 0.7rem;
            color: rgba(0,0,0,0.4);
            margin-top: 5px;
            text-align: right;
        }
        
        .user-time {
            text-align: right;
            color: rgba(255,255,255,0.7);
        }
        
        .typing-indicator {
            display: flex;
            align-items: center;
        }
        
        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: #6c757d;
            border-radius: 50%;
            margin: 0 2px;
            animation: typing-bounce 1.4s infinite ease-in-out both;
        }
        
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        
        @keyframes typing-bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
        
        @media (max-width: 576px) {
            .chat-container {
                border-radius: 0;
                height: 100vh;
                margin: 0;
                display: flex;
                flex-direction: column;
            }
            
            .chat-box {
                flex: 1;
                height: auto;
            }
            
            .message {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="chat-container">
            <div class="chat-header">
                <i class="fas fa-robot chat-header-icon"></i>Chatbot CodeIgniter 4
            </div>
            
            <div class="chat-box" id="chatBox">
                <div class="message-group">
                    <div class="bot-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div>
                        <div class="message bot-message">
                            Halo! Saya adalah chatbot. Ada yang bisa saya bantu?
                        </div>
                        <div class="time-stamp">
                            Baru saja
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="chat-form">
                <form id="chatForm">
                    <div class="input-group">
                        <input type="text" class="form-control" id="questionInput" placeholder="Ketik pertanyaan Anda di sini..." required autocomplete="off">
                        <button class="btn btn-send" type="submit">Kirim <i class="fas fa-paper-plane"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Function untuk menambahkan pesan ke chat box
            function addMessage(message, isUser = false) {
                const currentTime = new Date();
                const hours = currentTime.getHours().toString().padStart(2, '0');
                const minutes = currentTime.getMinutes().toString().padStart(2, '0');
                const timeString = `${hours}:${minutes}`;
                
                if (isUser) {
                    // Template untuk pesan pengguna
                    const userMsgTemplate = `
                        <div class="message-group user-group">
                            <div>
                                <div class="message user-message">${message}</div>
                                <div class="time-stamp user-time">${timeString}</div>
                            </div>
                        </div>
                    `;
                    $('#chatBox').append(userMsgTemplate);
                } else {
                    // Template untuk pesan bot
                    const botMsgTemplate = `
                        <div class="message-group">
                            <div class="bot-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div>
                                <div class="message bot-message">${message}</div>
                                <div class="time-stamp">${timeString}</div>
                            </div>
                        </div>
                    `;
                    $('#chatBox').append(botMsgTemplate);
                }
                
                // Auto-scroll ke pesan terbaru
                $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
            }
            
            // Function untuk menampilkan loading indicator
            function showLoadingIndicator() {
                const loadingTemplate = `
                    <div class="message-group loading-group" id="loadingMessage">
                        <div class="bot-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <div class="message bot-message loading-message">
                                <div class="typing-indicator">
                                    <div class="typing-dot"></div>
                                    <div class="typing-dot"></div>
                                    <div class="typing-dot"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#chatBox').append(loadingTemplate);
                $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
            }
            
            // Function untuk menghapus loading indicator
            function removeLoadingIndicator() {
                $('#loadingMessage').remove();
            }
            
            // Form submission handler
            $('#chatForm').submit(function(e) {
                e.preventDefault();
                
                const question = $('#questionInput').val().trim();
                
                if (question) {
                    // Tambahkan pesan pengguna ke chat box
                    addMessage(question, true);
                    
                    // Disable input dan tampilkan loading
                    $('#questionInput').prop('disabled', true);
                    showLoadingIndicator();
                    
                    // Kirim pertanyaan ke controller
                    $.ajax({
                        url: '<?= base_url('chatbot/send-question') ?>',
                        type: 'POST',
                        data: {
                            question: question
                        },
                        dataType: 'json',
                        success: function(response) {
                            // Remove loading message
                            removeLoadingIndicator();
                            
                            if (response.status) {
                                // Tambahkan jawaban dari bot
                                addMessage(response.answer, false);
                            } else {
                                // Tambahkan pesan error
                                addMessage('Maaf, terjadi kesalahan: ' + response.message, false);
                            }
                            
                            // Re-enable input dan clear
                            $('#questionInput').prop('disabled', false).val('').focus();
                        },
                        error: function(xhr, status, error) {
                            // Remove loading message
                            removeLoadingIndicator();
                            
                            // Parse error message jika ada
                            let errorMsg = 'Maaf, terjadi kesalahan dalam berkomunikasi dengan server';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response && response.message) {
                                    errorMsg = 'Maaf, terjadi kesalahan: ' + response.message;
                                }
                            } catch (e) {
                                console.error('Error parsing error response:', e);
                            }
                            
                            // Tambahkan pesan error
                            addMessage(errorMsg, false);
                            
                            // Re-enable input
                            $('#questionInput').prop('disabled', false).focus();
                        },
                        // Untuk demo, jika tidak terkoneksi ke server
                        // Hilangkan baris berikut saat implementasi sebenarnya
                        complete: function() {
                            // Simulasi respons jika tidak ada koneksi ke server
                            if (location.hostname === "localhost" || location.hostname === "127.0.0.1") {
                                setTimeout(function() {
                                    removeLoadingIndicator();
                                    
                                    // Contoh respons bot sederhana untuk demo
                                    let response = "Maaf, server tidak dapat dijangkau. Ini adalah pesan demo.";
                                    
                                    if (question.toLowerCase().includes('halo') || question.toLowerCase().includes('hai')) {
                                        response = "Halo! Ada yang bisa saya bantu?";
                                    } else if (question.toLowerCase().includes('bantuan') || question.toLowerCase().includes('help')) {
                                        response = "Saya bisa membantu Anda dengan berbagai pertanyaan. Silakan bertanya!";
                                    } else if (question.toLowerCase().includes('terima kasih') || question.toLowerCase().includes('thanks')) {
                                        response = "Sama-sama! Senang bisa membantu.";
                                    } else {
                                        response = "Saya menerima pertanyaan Anda: \"" + question + "\". Ini adalah pesan demo karena tidak ada koneksi ke server.";
                                    }
                                    
                                    addMessage(response, false);
                                    $('#questionInput').prop('disabled', false).val('').focus();
                                }, 1500);
                            }
                        }
                    });
                }
            });
            
            // Focus pada input saat halaman dimuat
            $('#questionInput').focus();
        });
    </script>
</body>
</html>