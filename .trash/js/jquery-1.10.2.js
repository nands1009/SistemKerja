<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Kinerja</title>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

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
                    <li><a href="/laporan_kerja" class="waves-effect waves-dark"><i class="fa fa-desktop"></i>Lembar Laporan Kerja</a></li>
                    <li><a href="/laporan_kerja/riwayat" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Riwayat Laporan Kerja</a></li>
                    <li><a href="/rencana_kerja" class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Rencana Kerja</a></li>

                    <li><a class="waves-effect waves-dark"><i class="fa fa-sitemap"></i> Riwayat Evaluasi <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="/riwayat_evaluasi/penilaian">Riwayat Penilaian</a>
                            </li>
                            <li><a href=" /riwayat_evaluasi/penghargaan">Riwayat Penghargaan</a>
                            </li>
                            <li> <a href="/riwayat_evaluasi/sp/">Riwayat SP</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
    </div>
    <div id="chatbot-container">
        <div id="chatbot-icon">
            <img src="/img/robot-assistant.png" alt="Chat">
        </div>
        <div id="chatbot-popup" style="display: none;">
            <div id="chatbot-header">
                <h3></h3>
                <span id="chatbot-close">&times;</span>
            </div>
            <div id="chatbot-messages">
                <!-- Pesan-pesan akan ditampilkan di sini -->
            </div>
            <div id="chatbot-input-container">
                <input type="text" id="chatbot-input" placeholder="Ketik pesan...">
                <button id="chatbot-send">Kirim</button>
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