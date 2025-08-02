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


        .container-card-riwayat {
            position: absolute;
    top: 212px;
    right: 12%;
    width: 689px;
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

        .card-body-riwayat {
            position: relative;
            width: 328px;
            height: 136px;
            padding: 25px;
            background-color: white;
            border-radius: 8px;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
        }



        .working-laporan p {
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


        .jumalah-laporan {
            color: white;
    font-weight: bold; 
    text-align: center;
    font-size: 14px;
    font-family: 'Arial Narrow', sans-serif;
    text-transform: uppercase;
        }

        .card-footer-riwayat {
            position: relative;
            width: 329px;
            top: -22px;
            height: 56px;
            padding: 14px;
            background-color: #fbca1f;
            text-align: center;

            color: white;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            border-radius: 0px 0px 8px 8px;

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

        .card-body-riwayat h2 {
            color: black;
            text-align: center;
            margin-top: -42px;
            margin-right: -15px;
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

        .rejectCount p {
            font-family: 'Arial Narrow', sans-serif;
            font-size: 55px;
            margin-left: 4px;
            text-align: left;
            margin-top: -36px;
        }

        .rejectCount img {
            position: relative;
            right: -232px;
            font-family: 'Arial Narrow', sans-serif;
        }
    </style>
</head>

<body>


    <div class="container-card-riwayat">

        <div class="card-body-riwayat">
            <h5 class="text-lg font-semibold mb-4"></h5>




            <!-- Table untuk menampilkan data laporan -->
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">

                    </tr>
                </thead>
                <tbody>

                    <h1 class="rejectCount">
                        <img width="50" height="50" src="https://img.icons8.com/pastel-glyph/64/document--v1.png" alt="document--v1" />
                        <p> <?= $rejectCount; ?><i class="fa-solid fa-file-exclamation"></i></i></p>
                    </h1>

                </tbody>
            </table>
        </div>
        <div class="card-footer-riwayat bg-transparent border-success">
            <div class="working-laporan">
                <p>
                    <span class="jumalah-laporan">Jumlah Laporan Kerja Di Tolak</span>
                </p>
            </div>



        </div>

    </div>

</body>

</html>