<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre Packages</title>
</head>

<body>
    <style>

        *{
            padding: 0;
            margin:0;
            box-sizing: border-box;
        }
        body {
            font-family: sans-serif;
        }

        .container {
            width: 90%;
            margin: 0 auto;
        }

        table {
            width: 100%;
            color: #333;
            border-collapse: collapse;
            font-size: 14px;
        }


        th {
            background-color: #333;
            color: #fff;
        }

        th,
        td {
            padding: 5px;
        }

        .logo-left {
            position: absolute;
            left: 50px;
        }

        .logo-right {
            position: absolute;
            right: 50px;
            top: 0;
        }

        .headings {
            text-align: center;
            line-height: 2;
            color: #3a3a3a;
            margin-bottom: 50px;
        }

        .headings h5 {
            margin: 0;
            padding: 0;
        }

        .left {
            text-align: left;
        }

        .pageBreak {
            page-break-before: always;
        }
    </style>
    <header>
        <div class="wrapper">
            <div class="logo-left">
                <img src='data:image/jpeg;base64,<?= coatOfArm() ?>' alt="">
            </div>
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>
                <h5>PRE PACKED GOODS REPORT</h5>

            </div>
            <div class="logo-right">
                <img src='data:image/jpeg;base64,<?= wmaLogo() ?>' alt="">
            </div>
        </div>

    </header>
    <?php

    function getAllowedErrorLimit($lot)
    {
        if ($lot > 100 && $lot <= 500) {
            return 3;
        } else if ($lot > 501 && $lot <= 3200) {
            return 3;
        } else if ($lot > 3200) {
            return 7;
        }
    }
    function evaluateProductStatus($measurementData, $declaredQuantity, $lotSize)
    {


        $withT1error = array_filter($measurementData, function ($data) {
            return $data['status'] == 1;
        });

        // filter t2
        $withT2error = array_filter($measurementData, function ($data) {
            return $data['status'] == 2;
        });

        $netQuantities = array_map(function ($net) use ($declaredQuantity) {
            return (int)$net['net_weight'] - (int)$declaredQuantity;
        }, $measurementData);

        // calculate individual error
        $individualError = array_reduce($netQuantities, function ($prev, $next) {
            return $prev + $next;
        });

        if (count($withT1error) > getAllowedErrorLimit($lotSize) && count($withT2error) > 0) {
            return 'Failed';
        } else {
            return 'Pass';
        }
    }

    ?>



    <div class="container">
        <pre>
            <?php
            // print_r($prePackageData);

            // exit;

            ?>
        </pre>
        <table cellspacing="0" border="1" style="width: 100%;">
            <table class="table1  table-bordered1" border="1"> -
                <thead class="dark">
                    <tr>
                        <th>Date</th>
                        <th>Name Of Client</th>
                        <th>Region</th>
                        <th>Location</th>
                        <th>Product</th>
                        <th>Results</th>
                        <th>Fees</th>
                        <th>Control Number</th>
                        <th>Measures Taken</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prePackageData as $data) : ?>


                        <tr>

                            <td><?= $data['date'] ?></td>
                            <td><?= $data['customer'] ?></td>
                            <td><?= $data['region'] ?></td>
                            <td><?= $data['location'] ?></td>
                            <td>

                                <table cellspacing="0" border="1" style="width: 100%;">
                                    <?php foreach ($data['productData'] as $product) : ?>
                                        <tr>
                                            <td><?= $product['commodity'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                </table>

                            </td>
                            <td>

                                <table cellspacing="0" border="1" style="width: 100%;">
                                    <?php foreach ($data['productData'] as $product) : ?>
                                        <tr>
                                            <td>Tsh<?= $product['amount'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!-- status goes here -->
                                </table>

                            </td>
                            <td>

                                <table cellspacing="0" border="1" style="width: 100%;">
                                    <?php foreach ($data['productData'] as $product) : ?>
                                        <tr>
                                            <td><?= $product['status'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!-- fee goes here -->
                                </table>

                            </td>
                            <td><?= $data['controlNumber'] ?></td>
                            <td> - </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            `




    </div>


</body>

</html>