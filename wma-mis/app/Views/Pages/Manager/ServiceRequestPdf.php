<!DOCTYPE html>
<html lang="en">

<title></title>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <style>
        * {
            /* padding: 0;
        margin: 0; */
            box-sizing: border-box;
            font-family: sans-serif;
        }

        header,
        section,
        aside {
            margin: 0 1.5% 10px 1.5%;
        }

        section {
            float: left;
            width: 30%;
        }


        .contacts {
            clear: both;
            margin-bottom: 0;
        }

        .left,
        .right {
            width: 20%;
        }

        .right img {
            float: right;
        }

        .middle {
            width: 50%;
        }

        .headings {
            /* margin-top: 10px; */
            text-align: center;
            line-height: 1;
        }

        .headings h5 {
            margin: 5px;
        }

        .center {
            text-align: center;
        }

        .contacts {
            width: 100%;



        }

        .contacts p {
            margin: 0;
            padding: 0;
            margin-top: -30px;
        }

        .contacts h5 {
            margin: 0;
            padding: 0;
            /* margin-top: -30px; */
        }

        .report {
            width: 100%;
            /* margin-top: 60px; */
        }

        .mainTable {

            margin-top: 100px;

        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: #222;
            font-size: 12px;
            margin-bottom: 10px;


        }

        table td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .mainTable th {
            padding: 5px;
            text-align: left;
            background: #e6e6e6;
        }




        .shade {
            background: #555;
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- <header>
        <code>&#60;header&#62;</code>
        <?= base_url() ?>/
    </header> -->

    <header>
        <section class="left">
            <img src='data:image/jpeg;base64,<?= coatOfArm() ?>' alt="">
        </section>

        <section class="middle">
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>
                <h5>CUSTOMER SERVICE REQUEST </h5>
            </div>
        </section>

        <section class="right">
            <img src='data:image/jpeg;base64,<?= wmaLogo() ?>' alt="">
        </section>
    </header>

    <div class="container">
        <table border="1" class="mainTable">
            <tbody>
                <tr class="shade">

                    <td colspan="2">PERSONAL PARTICULARS</td>
                </tr>
                <tr>
                    <td>Customer Name</td>
                    <td><?= $request->name ?></td>
                </tr>


                <tr>
                    <td>Mobile Number</td>
                    <td><?= $request->mobile_number ?></td>
                </tr>

                <tr>
                    <td>Region</td>
                    <td><?= $request->region ?></td>
                </tr>
                <tr>
                    <td>District</td>
                    <td><?= $request->district ?></td>
                </tr>
                <tr>
                    <td>Postal Address</td>
                    <td><?= $request->postal_address ?></td>
                </tr>
                <tr>
                    <td>Ward</td>
                    <td><?= $request->ward ?></td>
                </tr>
                <tr>
                    <td>Postal Code</td>
                    <td><?= $request->postal_code ?></td>
                </tr>
                <tr>
                    <td>Physical Address</td>
                    <td><?= $request->physical_address ?></td>
                </tr>




            </tbody>
        </table>



        <table border="1">
            <tr class="shade">

                <td colspan="2">SERVICE DETAILS</td>
            </tr>
            <tr>
                <td>Service Name</td>
                <td><?= $request->services ?></td>
            </tr>
            <tr>
                <td>Date Requested</td>
                <td><?= dateFormatter($request->created_at) ?></td>
            </tr>
            <?php if ($request->addition_info != '') : ?>
                <tr>
                    <td>Additional information</td>
                    <td><?= $request->addition_info ?></td>
                </tr>
            <?php endif; ?>
        </table>


    </div>


</body>

</html>