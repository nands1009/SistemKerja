<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        h1 {
            position: relative;
            top: -41px;
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: Arial, Helvetica, sans-serif;

            right: 0px;
        }


        .container-card-sp {
            position: absolute;
            top: 146px;
            right: -11%;
            width: 705px;
            height: 16%;
            padding: 20px;
            border-radius: 8px;
        }

        .container-card-penghargaan {
            position: absolute;
            top: 146px;
            right: 18%;
            width: 705px;
            height: 16%;
            padding: 20px;
            border-radius: 8px;
        }





        td[class="px-4 py-2"] {
            background-color: #28a745;
        }

        h2 {
            color: black;
            font-size: 50px;
        }

        .card-body-sp {
            position: relative;
            width: 447px;
            height: 237px;
            padding: 25px;
            background-color: white;
            border-radius: 8px;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
        }

        .card-body-penghargaan {
            position: relative;
            width: 447px;
            height: 237px;
            padding: 25px;
            background-color: white;
            border-radius: 8px;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
        }




        .working-sp p {
            position: relative;
            top: -19px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 0px 6px;
            border-radius: 8px;
            max-width: 500px;
            margin: 20px auto;
            text-align: center;
            right: -43px;
        }

        .working-penghargaan p {
            position: relative;
            top: -19px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 0px 6px;
            border-radius: 8px;
            max-width: 500px;
            margin: 20px auto;
            text-align: center;
            right: -43px;
        }


        .jumalah-sp {
            position: absolute;
            right: 34%;
            color: white;
            font-weight: bold;
            text-align: center;
            top: 0px;
            font-size: 14px;
            font-family: 'Arial Narrow', sans-serif;
            text-transform: uppercase;

        }

        .jumalah-penghargaan {
            position: absolute;
            right: 36%;
            color: white;
            font-weight: bold;
            text-align: center;
            top: 3px;
            font-size: 14px;
            font-family: 'Arial Narrow', sans-serif;
            text-transform: uppercase;
        }


        .card-footer-sp {
            position: relative;
            width: 448px;
            top: -56px;
            height: 56px;
            padding: 14px;
            background-color: #FF2E00;
            text-align: center;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            border-radius: 0px 0px 8px 8px;
            right: 0%;
        }

        .card-footer-penghargaan {
            position: relative;
            width: 448px;
            top: -56px;
            height: 56px;
            padding: 14px;
            background-color: #00BFA6;
            text-align: center;
            color: white;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            border-radius: 0px 0px 8px 8px;
            right: 0%;
        }

        .container-name .h1 {
            position: relative;
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;

            right: 170px;
        }

        h5 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8f8f8;
            color: #333;
            font-weight: bold;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #e6f7ff;
        }

        .text-center {
            text-align: center;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .px-4,
        .py-2 {
            padding-left: 0rem;
            padding-right: 1rem;
            padding-top: -2.5rem;
            padding-bottom: -2.5rem;
        }

        .sp p {
            position: absolute;
            font-family: 'Arial Narrow', sans-serif;
            font-size: 59px;
            left: 109px;
            text-align: left;
            top: 14px;
        }

        .penghargaan p {
            position: absolute;
            font-family: 'Arial Narrow', sans-serif;
            font-size: 59px;
            left: 106px;
            text-align: left;
            top: 14px;
        }


        .sp i {
            position: absolute;
            right: -17px;
            z-index: 1;
            top: -20px;
            font-size: 50px;

        }

        .penghargaan i {
            position: absolute;
            right: -15px;
            z-index: 1;
            top: -19px;
            font-size: 50px;
        }

        span[class="spterima"] {
            position: relative;
            color: #333333;
            top: 99px;
            /* font-size: 25px; */
            right: -70px;
            /* position: relative; */
            /* top: 15px; */
            font-size: 18px;
            /* right: -265px; */
            color: #333333;
            font-family: 'Arial Narrow', sans-serif;
        }

        span[class="penghargaanterima"] {
            position: relative;
            color: #333333;
            top: 99px;
            font-size: 18px;
            right: -61px;
            font-family: 'Arial Narrow', sans-serif;
        }

        span[class="sptolak"] {
            position: relative;
            top: 15px;
            font-size: 18px;
            right: -265px;
            color: #333333;
            font-family: 'Arial Narrow', sans-serif;
        }

        span[class="penghargaantolak"] {
            position: relative;
            top: 15px;
            font-size: 18px;
            right: -268px;
            color: #333333;
            font-family: 'Arial Narrow', sans-serif;
        }


        #approved {
            font-size: 62px;
            margin-inline: -21px;
            top: 46px;
        }

        #rejected {
            font-size: 62px;
            margin-inline: 173px;
            top: -38px;
        }

        #approvedpenghargaan {
            font-size: 62px;
            margin-inline: -22px;
            top: 49px;
        }

        #rejectedpenghargaan {
            font-size: 62px;
            margin-inline: 179px;
            top: -34px;
        }
    </style>
</head>

<body>


    <div class="container-card-sp">

        <div class="card-body-sp">
            <h5 class="text-lg font-semibold mb-4"></h5>
            <!-- Table untuk menampilkan data laporan -->
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">
                    </tr>
                </thead>
                <tbody>
                    <h1 class="sp">
                        <i class="fa-solid fa-file-circle-exclamation"></i>
                        <p id="approved"><?= $spapproved; ?></p>
                        <span class="spterima">Approved</span>
                    </h1>
                    <h1 class="sp">
                        <p id="rejected"><?= $sprejected; ?></p>
                        <span class="sptolak">Rejected</span>
                    </h1>
                </tbody>
            </table>
        </div>
        <div class="card-footer-sp bg-transparent border-success">
            <div class="working-sp">
                <p>
                    <span class="jumalah-sp">Notifikasi SURAT PERINGATAN </span>
                </p>
            </div>
        </div>
    </div>


    <div class="container-card-penghargaan">

        <div class="card-body-penghargaan">
            <h5 class="text-lg font-semibold mb-4"></h5>
            <!-- Table untuk menampilkan data laporan -->
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">
                    </tr>
                </thead>
                <tbody>
                    <h1 class="penghargaan">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <p id="approvedpenghargaan"><?= $penghargaanapproved; ?></p>
                        <span class="penghargaanterima">Approved</span>
                    </h1>
                    <h1 class="penghargaan">
                        <p id="rejectedpenghargaan">
                            <?= $penghargaanrejected; ?>
                        </p>
                        <span class="penghargaantolak">Rejected</span>
                    </h1>

                    </h1>
                </tbody>
            </table>
        </div>
        <div class="card-footer-penghargaan bg-transparent border-success">
            <div class="working-penghargaan">
                <p>
                    <span class="jumalah-penghargaan">Notifikasi SURAT PENGHARGAAN </span>
                </p>
            </div>
        </div>
    </div>


</body>

</html>