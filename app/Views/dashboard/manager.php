<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Kinerja</title>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/css/materialize.min.css" media="screen,projection" />
    <!-- Bootstrap Styles-->
    <link href="/css/bootstrap.css" rel="stylesheet" />
    <!-- FontAwesome Styles-->
    <link href="/css/font-awesome.css" rel="stylesheet" />
    <!-- Morris Chart Styles-->
    
    <!-- Custom Styles-->

    <!-- Google Fonts-->

    <link rel="stylesheet" href="/css/style.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Raphael.js (Required for Morris.js) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>

    <!-- Include Morris.js (Chart library) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

    <!-- Include Morris.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<style>
        /* Gaya untuk container chatbot */
        #chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        /* Gaya untuk ikon chatbot */
        #chatbot-icon {
width: 80PX;
    height: 80PX;
    border-radius: 50%;

    display: flex
;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
        }

        #chatbot-icon:hover {
            transform: scale(1.05);

        }

        #chatbot-icon img {
    width: 50PX;
    height: 50PX;
        }

        /* Gaya untuk popup chatbot */
        #chatbot-popup {
            position: absolute;
            bottom: 75px;
            right: 0;
            width: 350px;
            height: 450px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* Header chatbot */
        #chatbot-header {
            background-color: #FF2E00;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #chatbot-header h3 {
            margin: 0;
            font-size: 16px;
        }

        #chatbot-close {
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }

        /* Area pesan chatbot */
        #chatbot-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }

        .message-container {
            margin-bottom: 10px;
            display: flex;
        }

        .user-message {
            justify-content: flex-end;
        }

        .bot-message {
            justify-content: flex-start;
        }

        .message {
            max-width: 80%;
            padding: 8px 12px;
            border-radius: 15px;
            font-size: 14px;
            word-wrap: break-word;
        }

        .user-message .message {
            background-color: #dcf8c6;
            border: 1px solid #c7edb5;
        }

        .bot-message .message {
            background-color: #fff;
            border: 1px solid #e1e1e1;
        }

        .timestamp {
            font-size: 10px;
            color: #999;
            margin-top: 4px;
            display: block;
        }

        /* Input chatbot */
        #chatbot-input-container {
            display: flex;
            padding: 10px;
            background-color: #f0f0f0;
            border-top: 1px solid #e1e1e1;
        }

        #chatbot-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
        }

        #chatbot-send {
            border: none;
            background-color: #FF2E00;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            margin-left: 8px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #chatbot-send:hover {
            background-color: #235a8c;
        }

        /* Animasi untuk typing indicator */
        .typing-indicator {
            padding: 8px 12px;
            background-color: #eee;
            border-radius: 15px;
            width: 40px;
            text-align: center;
            margin-bottom: 10px;
            display: none;
        }

        /* Responsif untuk perangkat mobile */
        @media screen and (max-width: 480px) {
            #chatbot-popup {
                width: 300px;
                right: 0;
            }
        }
    </style>
<body>
    <div id="wrapper">

        <!-- Navbar -->
        <nav class="navbar navbar-default top-navbar" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle waves-effect waves-dark" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand"><img src="/img/armindo.png" alt="Logo"></a>
            </div>

            <ul class="nav navbar-top-links navbar-right">
                <!-- Notifications Icon -->
                <!-- User Profile Dropdown -->
                <li><a class="dropdown-button waves-effect waves-dark" href="#!" data-activates="dropdown1"><i class="fa-solid fa-bars"></i> <i class="material-icons right">arrow_drop_down</i></a></li>
            </ul>
        </nav>


        <!-- Dropdown for User Profile and Logout -->
        <ul id="dropdown1" class="dropdown-content">
            <!-- Logout link -->
            <li><a href="<?= site_url('/logout') ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
        </ul>

        <!-- Dropdown for Notifications -->
        <ul id="dropdown2" class="dropdown-content w250">
            <li>
                <div>
                    <i class="fa fa-comment fa-fw"></i> New Comment
                    <span class="pull-right text-muted small">4 min</span>
                </div>
            </li>
            <li class="divider"></li>
            <li>
                <div>
                    <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                    <span class="pull-right text-muted small">12 min</span>
                </div>
            </li>
            <li class="divider"></li>
            <li>
                <div>
                    <i class="fa fa-envelope fa-fw"></i> Message Sent
                    <span class="pull-right text-muted small">4 min</span>
                </div>
            </li>
            <li class="divider"></li>
            <li>
                <div>
                    <i class="fa fa-tasks fa-fw"></i> New Task
                    <span class="pull-right text-muted small">4 min</span>
                </div>
            </li>
            <li class="divider"></li>
            <li>
                <div>
                    <i class="fa fa-upload fa-fw"></i> Server Rebooted
                    <span class="pull-right text-muted small">4 min</span>
                </div>
            </li>
            <li class="divider"></li>
            <li><a class="text-center" href="#"><strong>See All Alerts</strong><i class="fa fa-angle-right"></i></a></li>
        </ul>

        <!-- Sidebar -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li><a href="/dashboard" class="waves-effect waves-dark"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="/laporan_kerja_manager/create" class="waves-effect waves-dark"><i class="fa fa-desktop"></i>Input Laporan Kerja</a></li>
                    <li><a href="/laporan_kerja_manager/riwayat" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Riwayat Laporan Kerja</a></li>
                    <li><a href="/rencana-kerja/input" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Rencana Kerja</a></li>
                    <li><a href="/rencana-kerja/riwayat" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Riwayat Rencana Kerja</a></li>
                    <li><a href="/approval" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Persetujuan Laporan Kerja</a></li>
                    <li><a href="/penilaian" class="waves-effect waves-dark"><i class="fa fa-desktop"></i> Penilaian</a></li>
                    <li><a href="/pengajuan/ajukan" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Pengajuan Penghargaan atau SP</a></li>
                    <li><a href="/pengajuan/riwayat-pengajuan" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Riwayat Pengajuan</a></li>
                    <li><a href="/riwayat-penilaian" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Riwayat Penilaian</a></li>
                </ul>
                </li>
                </ul>
            </div>
        </div>
<div id="chatbot-container">
        <div id="chatbot-icon">
            <img src="/img/robot-assistant.png" alt="Chat Assistant">
        </div>
        <div id="chatbot-popup" style="display: none;">
            <div id="chatbot-header">
                <h3>Asisten Virtual ARMINDO</h3>
                <span id="chatbot-close">&times;</span>
            </div>
            <div id="chatbot-messages">
                <!-- Pesan bot awal -->
                <div class="message-container bot-message">
                    <div class="message">
                        Halo! Saya adalah asisten virtual Anda. Ada yang bisa saya bantu?
                    </div>
                </div>
                <div class="typing-indicator" id="typing-indicator">...</div>
            </div>
            <div id="chatbot-input-container">
                <input type="text" id="chatbot-input" placeholder="Ketik pesan Anda...">
                <button id="chatbot-send">→</button>
            </div>
        </div>
    </div>
        </nav>

            </div>
        </nav>

        <!-- Page Content -->
        <!-- Page Content -->
        <div id="page-wrapper">
            <!-- Main content will be rendered here -->
            <?= $this->renderSection('content') ?>
            


        </div>
        <footer class="footer">
            <p></p>
            <!-- Menampilkan gambar avatar di footer -->

            <p>&copy;</p>
    </div>
    </footer>
    
        

            <!-- /. WRAPPER  -->
            <?= $this->renderSection('content') ?>
            <!-- JS Scripts-->
            <!-- jQuery Js -->

 <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen-elemen chatbot
            const chatbotIcon = document.getElementById('chatbot-icon');
            const chatbotPopup = document.getElementById('chatbot-popup');
            const chatbotClose = document.getElementById('chatbot-close');
            const chatbotInput = document.getElementById('chatbot-input');
            const chatbotSend = document.getElementById('chatbot-send');
            const chatbotMessages = document.getElementById('chatbot-messages');
            const typingIndicator = document.getElementById('typing-indicator');

            // Toggle chatbot popup ketika ikon diklik
            chatbotIcon.addEventListener('click', function() {
                if (chatbotPopup.style.display === 'none' || chatbotPopup.style.display === '') {
                    chatbotPopup.style.display = 'flex';
                    chatbotInput.focus();
                    // Scroll ke pesan terakhir
                    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
                } else {
                    chatbotPopup.style.display = 'none';
                }
            });

            // Tutup chatbot popup ketika tombol close diklik
            chatbotClose.addEventListener('click', function() {
                chatbotPopup.style.display = 'none';
            });

            // Fungsi untuk mendapatkan timestamp
            function getTimestamp() {
                const now = new Date();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                return `${hours}:${minutes}`;
            }

            // Fungsi untuk menampilkan pesan pengguna
            function addUserMessage(message) {
                const userMessageHtml = `
                    <div class="message-container user-message">
                        <div class="message">
                            ${message}
                            <span class="timestamp">${getTimestamp()}</span>
                        </div>
                    </div>
                `;
                chatbotMessages.insertAdjacentHTML('beforeend', userMessageHtml);
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }

            // Fungsi untuk menampilkan pesan bot
            function addBotMessage(message) {
                const botMessageHtml = `
                    <div class="message-container bot-message">
                        <div class="message">
                            ${message}
                            <span class="timestamp">${getTimestamp()}</span>
                        </div>
                    </div>
                `;
                chatbotMessages.insertAdjacentHTML('beforeend', botMessageHtml);
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }

            // Fungsi untuk menampilkan indikator typing
            function showTypingIndicator() {
                typingIndicator.style.display = 'block';
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }

            // Fungsi untuk menyembunyikan indikator typing
            function hideTypingIndicator() {
                typingIndicator.style.display = 'none';
            }

            // Fungsi untuk mengirim pesan
            function sendMessage() {
                const message = chatbotInput.value.trim();
                if (message === '') return;

                // Tambahkan pesan pengguna ke chatbox
                addUserMessage(message);
                
                // Bersihkan input
                chatbotInput.value = '';
                
                // Tampilkan indikator typing
                showTypingIndicator();

                // Kirim pesan ke server
                fetch('/chatbot/getResponse', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'message=' + encodeURIComponent(message)
                })
                .then(response => response.json())
                .then(data => {
                    // Sembunyikan indikator typing
                    hideTypingIndicator();
                    
                    // Tambahkan pesan bot
                    setTimeout(() => {
                        addBotMessage(data.message);
                    }, 500); // Sedikit delay untuk efek typing
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideTypingIndicator();
                    
                    // Tampilkan pesan error
                    setTimeout(() => {
                        addBotMessage('Maaf, terjadi kesalahan dalam memproses pesan Anda. Silakan coba lagi.');
                    }, 500);
                });
            }

            // Event listener untuk tombol kirim
            chatbotSend.addEventListener('click', sendMessage);

            // Event listener untuk tombol Enter
            chatbotInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        });
    </script>
          <!-- Bootstrap Js -->
          <script src="/js/bootstrap.min.js"></script>

<script src="/js/materialize.min.js"></script>

<!-- Metis Menu Js -->
<script src="/js/jquery.metisMenu.js"></script>
<!-- Morris Chart Js -->





<!-- Custom Js -->
<script src="/js/custom-scripts.js"></script>


</body>

</html>