<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        /* @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300;400&display=swap'); */
        /* @import url('http://localhost/vipimo/dist/css/fonts.css'); */

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

        table {
            font-size: 12px;
            border-collapse: collapse;
        }

        table td {
            padding: 3px;
        }

        .mainTable {
            width: 100%;
            border-collapse: collapse;
            color: #222;
            /* font-size: 14px; */

        }

        .mainTable td {
            padding: 0 3px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .able td {
            padding: 0 3px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .mainTable th {
            padding: 4px;
            text-align: left;
            background: #333;
            color: #fff;
        }


        .summary table {

            width: 40%;
            position: absolute;
            right: 0;
            border-collapse: collapse;
            color: #222;

        }


        .summary table td {
            /* padding: 5px; */
            text-align: left;
            border-bottom: 1px solid #333;

        }

        .contacts {
            font-size: 12px;
        }
    </style>

</head>

<body>


   
<header>
        <section class="left">
            <img src='<?= getImage('assets/images/emblem.png') ?>' alt="">
        </section>

        <section class="middle">
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>
            </div>
        </section>

        <section class="right">
            <img src='<?= getImage('assets/images/wma1.png') ?>' alt="">
        </section>
    </header>
    <div class="contacts">
        <p class="center contacts"><?= $contacts ?></p>

    </div>
    <br>
    <h4 style="text-align: center;"><?= $reportTitle ?></h4>
    <div class="container">
        <table cellspacing="0" class=" mainTable table-bordered table-sm" border="" style="width: 100%; margin:0;padding:0;">
            <!-- <table class="table1  table-bordered1" border="1"> -->
            <thead class="dark">
                <tr>
                    <th>Date</th>
                    <th>Name Of Client</th>
                    <th>Region</th>
                    <th>Location</th>
                    <th>Product</th>
                    <th>Batch Number</th>
                    <th>Fees</th>
                    <th>Results</th>
                    <th>Control Number</th>
                    <th>Payment</th>
                    <?php if ($role == 2 || $role == 3 || $role == 7) : ?>
                        <th>Officer</th>
                    <?php endif; ?>
                    <!-- <th>Measures Taken</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prePackageData as $data) : ?>
                    <!-- 
                        <tr>

                            <td style="margin:0;padding:0;">${data.date}</td>
                            <td style="margin:0;padding:0;">${data.customer}</td>
                            <td style="margin:0;padding:0;">${data.region}</td>
                            <td style="margin:0;padding:0;">${data.location}</td>
                            <td style="margin:0;padding:0;">

                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderProducts(data.productData)}
                                </table>

                            </td>
                            <td style="margin:0;padding:0;">

                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderProductStatus(data.productData)}

                                </table>

                            </td>
                            <td style="margin:0;padding:0;">

                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderProductFee(data.productData)}
                                </table>

                            </td>

                            <td style="margin:0;padding:0;">
                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderControlNumber(data.productData)}
                                </table>
                            </td>
                            <td style="margin:0;padding:0;">
                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderPaymentStatus(data.productData)}
                                </table>
                            </td>
                            <td style="margin:0;padding:0;"> - </td>
                        </tr> -->


                    <tr>

                        <td style="margin:0;padding:0;"><?= $data['date'] ?></td>
                        <td style="margin:0;padding:0;"><?= $data['customer'] ?></td>
                        <td style="margin:0;padding:0;"><?= $data['region'] ?></td>
                        <td style="margin:0;padding:0;"><?= $data['location'] ?></td>
                        <td style="margin:0;padding:0;">

                            <table cellspacing="0" border="1" style="width: 100%;">
                                <?php foreach ($data['productData'] as $product) : ?>
                                    <tr>
                                        <td style="margin:0;padding:0;"><?= $product['commodity'] ?></td>
                                    </tr>
                                <?php endforeach; ?>

                            </table>

                        </td>
                        <td style="margin:0;padding:0;">

                            <table cellspacing="0" border="1" style="width: 100%;">
                                <?php foreach ($data['productData'] as $product) : ?>
                                    <tr>
                                        <td style="margin:0;padding:0;"><?= $product['batchNumber'] ?></td>
                                    </tr>
                                <?php endforeach; ?>

                            </table>

                        </td>
                        <td style="margin:0;padding:0;">

                            <table cellspacing="0" border="1" style="width: 100%;">
                                <?php foreach ($data['productData'] as $product) : ?>
                                    <tr>
                                        <td style="margin:0;padding:0; ">Tsh<?= number_format($product['amount']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- status goes here -->
                            </table>

                        </td>
                        <td style="margin:0;padding:0;">

                            <table cellspacing="0" border="1" style="width: 100%;">
                                <?php foreach ($data['productData'] as $product) : ?>
                                    <tr>
                                        <td style="margin:0;padding:0;"><?= $product['status'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- fee goes here -->
                            </table>

                        </td>
                        <td style="margin:0;padding:0;">

                            <table cellspacing="0" border="1" style="width: 100%;">
                                <?php foreach ($data['productData'] as $product) : ?>
                                    <tr>
                                        <td style="margin:0;padding:0;"><?= $product['controlNumber'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- fee goes here -->
                            </table>

                        </td>
                        <td style="margin:0;padding:0;">

                            <table cellspacing="0" border="1" style="width: 100%;">
                                <?php foreach ($data['productData'] as $product) : ?>
                                    <tr>
                                        <td style="margin:0;padding:0;"><?= $product['payment'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- fee goes here -->
                            </table>

                        </td>
                        <?php if ($role == 2 || $role == 3 || $role == 7) : ?>
                            <td style="margin:0;padding:0;">

                                <table cellspacing="0" border="1" style="width: 100%;">
                                    <?php foreach ($data['productData'] as $officer) : ?>
                                        <tr>
                                            <td style="margin:0;padding:0px;"><?= $officer['officer'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!-- fee goes here -->
                                </table>

                            </td>
                        <?php endif; ?>
                        <!-- <td style="margin:0;padding:0;"><?= $data['controlNumber'] ?></td> -->
                        <!-- <td style="margin:0;padding:0;"> - </td> -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        <div class="summary">

            <table>
                <tr>
                    <td style="padding: 0;"><b>
                            <h4>Collection Summary</h4>
                        </b></td>
                    <td style="padding: 0;"></td>
                    <td style="padding: 0;"></td>

                </tr>

                <?php if ($prePackageSummary['paidPrePackage'] > 0 && $prePackageSummary['pendingPrePackage'] > 0) : ?>
                    <tr>
                        <td><b>Paid Amount</b></td>
                        <td>Tsh <?= number_format($prePackageSummary['paidPrePackage']) ?></td>
                        

                    </tr>
                    <tr>
                        <td><b>Pending Amount</b></td>
                        <td>Tsh <?= number_format($prePackageSummary['pendingPrePackage']) ?></td>
                  
                    </tr>
                    <tr>
                        <td><b>Total Amount</b></td>
                        <td>Tsh <?= number_format($prePackageSummary['totalPrePackage']) ?></td>
                       
                    </tr>

                <?php elseif ($prePackageSummary['paidPrePackage'] > 0) : ?>
                    <tr>
                        <td><b>Paid Amount</b></td>
                        <td>Tsh <?= number_format($prePackageSummary['paidPrePackage'])?></td>
                       

                    </tr>
                <?php elseif ($prePackageSummary['pendingPrePackage'] > 0) : ?>
                    <tr>
                        <td><b>Pending Amount</b></td>
                        <td>Tsh <?= number_format($prePackageSummary['pendingPrePackage']) ?></td>
                        
                    </tr>

                <?php endif; ?>




            </table>
        </div>
    </div>






</body>

</html>