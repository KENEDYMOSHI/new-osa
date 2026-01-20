<!DOCTYPE html>
<html>

<!-- Mirrored from adminlte.io/themes/v3/pages/examples/invoice-print.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 24 Jul 2020 16:50:03 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $page['title'] ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 -->

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url() ?>/plugins/fontawesome-free/css/all.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/adminlte.min.css">

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <!-- title row -->
                        <div class="row mb-5">
                            <div class="col-md-4"> <img src="<?= base_url() ?>/assets/images/emblem.png" alt=""></div>
                            <div class="col-md-4">
                                <h5 class="text-center"><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                                <h5 class="text-center"><b>MINISTRY OF INDUSTRIES AND TRADE </b></h5>
                                <h5 class="text-center">WEIGHTS AND MEASURES AGENCY </h5>
                                <h5 class="text-center"><?= strtoupper($region) ?> COLLECTION REPORT</h5>
                                <p class="text-center"><?= date("d M Y") ?></p>

                            </div>
                            <div class="col-md-4 align-right"><img class="float-right" src="<?= base_url() ?>/assets/images/wma1.png" alt="">
                            </div>
                            <!-- /.col -->
                        </div>

                        <!-- info row -->

                        <!-- /.row -->

                        <!-- Table row -->

                        <?php
                        // print_r($fuelPumpCollection);

                        // exit;
                        ?>


                        <div class="row">
                            <?php
                            $scalesTotal = 0;
                            $scalesPaid = 0;
                            $scalesPending = 0;
                            ?>
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>

                                            <th>Activity</th>
                                            <th>Paid Amount</th>
                                            <th>Pending Amount</th>
                                            <th>Total</th>
                                           

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Inspection & Verification of Scales</td>
                                            <td>Tsh <?= paidAmount($scaleCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($scaleCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($scaleCollection) ?></td>
                                           
                                        </tr>
                                        <tr>
                                            <td>Inspection & Verification of Fuel Pumps</td>
                                            <td>Tsh <?= paidAmount($fuelPumpCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($fuelPumpCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($fuelPumpCollection) ?></td>
                                            
                                        </tr>
                                        <tr>
                                            <td> Pre Package</td>
                                            <td>Tsh <?= paidAmount($prePackageCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($prePackageCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($prePackageCollection) ?></td>
                                            
                                        </tr>
                                        <tr>
                                            <td>Vehicle Tank Verification</td>
                                            <td>Tsh <?= paidAmount($vtcCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($vtcCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($vtcCollection) ?></td>
                                            
                                        </tr>
                                        <tr>
                                            <td>Sandy & Ballast Lorries </td>
                                            <td>Tsh <?= paidAmount($sblCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($sblCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($sblCollection) ?></td>
                                            
                                        </tr>
                                        <tr>
                                            <td>Bulk Storage Tank Calibration</td>
                                            <td>Tsh <?= paidAmount($bstCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($bstCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($bstCollection) ?></td>
                                            
                                        </tr>
                                        <tr>
                                            <td>Fixed Storage Tank Calibration</td>
                                            <td>Tsh <?= paidAmount($fstCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($fstCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($fstCollection) ?></td>
                                         
                                        </tr>
                                        <tr>
                                            <td>Inspection Of Flow Meter</td>
                                            <td>Tsh <?= paidAmount($flowMeterCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($flowMeterCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($flowMeterCollection) ?></td>
                                            
                                        </tr>
                                        <tr>
                                            <td>Inspection Of Meter</td>
                                            <td>Tsh <?= paidAmount($waterMeterCollection) ?></td>
                                            <td>
                                                Tsh <?= pendingAmount($waterMeterCollection) ?>
                                            </td>
                                            <td>Tsh <?= totalAmount($waterMeterCollection) ?></td>
                                          
                                        </tr>




                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <div class="row">
                            <!-- accepted payments column -->

                            <?php
                            //===================Get all paid amount in a region=========================
                            $paid = paidSum($scaleCollection) + paidSum($fuelPumpCollection)  + paidSum($vtcCollection) + paidSum($sblCollection) + paidSum($bstCollection) + paidSum($fstCollection) + paidSum($flowMeterCollection) + paidSum($waterMeterCollection);

                            //===================Get all pending amount in a region=========================
                            $pending = pendingSum($scaleCollection) + pendingSum($fuelPumpCollection)  + pendingSum($vtcCollection) + pendingSum($sblCollection) + pendingSum($bstCollection) + pendingSum($fstCollection) + pendingSum($flowMeterCollection) + pendingSum($waterMeterCollection);

                            //===================Get all total collection in a region=========================
                            $totalCollection = totalCollection($scaleCollection) + totalCollection($fuelPumpCollection) +  +totalCollection($vtcCollection) + totalCollection($sblCollection) + totalCollection($bstCollection) + totalCollection($fstCollection) + totalCollection($flowMeterCollection) + totalCollection($waterMeterCollection);
                            ?>




                            <!-- /.col -->
                            <div class="col-6">
                                <p class="lead">Collection Due <?= date('d M Y') ?></p>

                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Paid:</th>
                                            <td>Tsh <?= number_format($paid)  ?></td>
                                        </tr>
                                        <tr>
                                            <th>Pending:</th>
                                            <td>Tsh <?= number_format($pending)  ?></td>
                                        </tr>

                                        <tr>
                                            <th>Total Collection:</th>
                                            <td>Tsh <?= number_format($totalCollection) ?></td>
                                        </tr>
                                        <!-- <tr>
                                            <th>Target:</th>
                                            <td>$265.24</td>
                                        </tr>
                                        <tr>
                                            <th>Evaluation:</th>
                                            <td>20% Increase</td>
                                        </tr> -->
                                    </table>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- this row will not appear when printing -->

                    </div>
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->

    </section>
    <!-- ./wrapper -->

    <script type="text/javascript">
        window.addEventListener("load", window.print());
    </script>
</body>

<!-- Mirrored from adminlte.io/themes/v3/pages/examples/invoice-print.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 24 Jul 2020 16:50:03 GMT -->

</html>