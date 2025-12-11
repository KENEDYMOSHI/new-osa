<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <style>
        * {
            padding: 0px;
            margin: 0px;
            line-height: 1;
            box-sizing: border-box;
        }

        body {
            /* margin: 10px; */
            font-family: sans-serif !important;
            /* font-family: 'Shantell Sans', cursive; */
            color: #333333;
            font-size: 9px;
        }

        .bold {
            font-weight: bold;
        }

        h5 {
            /* padding: 0;
            margin: 0;
            line-height: 1.3; */
            text-align: center;

        }

        header {
            /* margin-bottom: 10px; */
            border-bottom: 1px solid #777;

        }

        header:after {
            content: "";
            display: table;
            clear: both;
        }

        header .left,
        header .right {
            float: left;
            width: 20%;

        }

        header .right img {
            float: right;


        }

        header .middle {
            padding-top: 7px;
            float: left;
            width: 60%;
            text-align: center;

        }

        header .middle h5 {
            text-align: center;
            padding: 0;
            margin: 0;
            line-height: 1.2;


        }





        table {
            width: 100%;
            border-collapse: collapse;
            /* font-size: 12px; */
        }

        table,
        th,
        td {
            /* border: 1px solid #333; */
            padding: 1px;
        }

        thead th {
            background-color: #333333;
            color: #ffffff;
            font-weight: bold;
        }

        .detailsTable {}

        .qrCode {

            width: 90px !important;

        }

        .qrImg {
            width: 90px !important;
        }



        .line {
            border: 1px solid #777;
        }

        header h6,
        header p {
            margin: 0;
            padding: 2px 0;

        }

        .seal {
            width: 100px;
            height: 100px;
            display: inline-block;
            text-align: center;
            line-height: 100px;
            border: 1px solid #333333;
        }

        #ullageTable td {
            font-size: 11.5px;
        }

        #ullageSummary {
            width: 70%;

        }

        #ullageSummary td {
            font-size: 11.5px;
        }

        .note td {
            padding: 10px;
        }
    </style>

</head>

<body>
    <header>
        <section class="left">
            <img src='<?= getImage('assets/images/emblem.png') ?>' alt="">
        </section>

        <section class="middle">

            <h5 class="bold"><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
            <h5 class="bold"><b>MINISTRY OF INDUSTRY AND TRADE</b></h5>
            <h5 class="bold">WEIGHTS AND MEASURES AGENCY </h5>
            <h6 class="bold"><?= $center->centerName ?></h6>
            <p class="contacts"><?= $center->contacts ?></p>

        </section>

        <section class="right">
            <img src='<?= getImage('assets/images/wma1.png') ?>' alt="">
        </section>
    </header>
    <div class="contacts">
        <p class="center"></p>

    </div>

    <div class="content">

        <?= $this->renderSection('content'); ?>

    </div>


</body>

</html>