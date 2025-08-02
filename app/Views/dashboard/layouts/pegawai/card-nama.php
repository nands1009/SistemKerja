<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <style>
        .container-body {
            position: relative;
            background-color: white;
            height: 384px;
            top: 50px;
            width: 95%;
            left: 3%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        h1 {
            position: relative;
            top: -41px;
            font-size: 35px;
            text-align: justify;
            margin-top: 50px;
            color: #333;
            font-family: 'Arial Narrow', sans-serif;
            font-weight: bold;
            right: 0px;
        }


        .container-name {
            position: absolute;
            top: 34px;
            right: 55%;
            width: 682px;
            height: 16%;
            /* padding: 20px; */
            /* border-radius: 8px; */
        }



        td[class="px-4 py-2"] {
            background-color: #28a745;
        }

        h2 {
            font-family: 'Arial Narrow', sans-serif;
            font-size: 50px;
        }

        .card-body {
            position: relative;
    width: 537px;
    height: 192px;
    padding: 6px;
    background-color: white;
    border-radius: 8px;
    box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
        }



        .working-hours p {
            position: relative;
            top: -16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 0px 6px;
            border-radius: 8px;
            max-width: 500px;
            margin: 20px auto;
        }

        .waktu {
            color: white;
            font-family: 'Arial Narrow', sans-serif;
        }

        .jam-kerja {
            color: white;
            font-family: 'Arial Narrow', sans-serif;
        }

        .card-footer {
            position: relative;
            width: 536px;
            top: -13px;
            height: 56px;
            padding: 14px;
            background-color: #FF2E00;
            text-align: center;
            font-weight: bold;
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
            font-weight: bold;
            right: 170px;
        }

        h5 {
            font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #333;
    position: relative;
    right: -21px;
    font-family: 'Arial Narrow', sans-serif;
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
            color: #333333;
            font-family: 'Arial Narrow', sans-serif;

        }

        .border-b img {
            position: relative;
    right: -424px;
    top: 20px;
        }
        
        .border-b{
            border-bottom-width: 0px;
        }

        h2[class="name"] {
            position: relative;
    right: -4%;
    top: -58px;
    color: #333333;
    font-family: 'Arial Narrow', sans-serif;
    font-weight: bold;
    z-index: 1;
    FONT-SIZE: 22PX;
    text-align: left;
    text-transform: uppercase;
        }
        
    </style>
</head>

<body>
    <div class="container-body">

        <div class="container-name">
            <h1>Dashboard</h1>
            <div class="card-body">
                <h5 class="text-lg font-semibold mb-4">Selamat Datang</h5>




                <!-- Table untuk menampilkan data laporan -->
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">

                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($nama)) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($nama as $row) : ?>
                                <h1 class="border-b">
                                    <img width="70" height="70" src="https://img.icons8.com/parakeet-line/96/user.png" alt="user" />
                                    <h2 class="name"><?= esc($row['username']) ?></h2>
                                </h1>

                            <?php endforeach; ?>
                        <?php else : ?>

                            <tr>
                                <td colspan="2" class="px-4 py-2 text-center text-gray-500">Tidak ada data yang tersedia.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent border-success">
                <div class="working-hours">
                    <p>
                        <span class="waktu"><?= esc($currentTime) ?></span>
                        <span class="jam-kerja">Jam kerja: 08:00:00 - 17:00:00</span>
                    </p>
                </div>



            </div>

        </div>
    </div>

</body>

</html>