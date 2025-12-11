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
    <!-- Ionicons -->
 
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/adminlte.min.css">

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <!-- Main content -->
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
                                <h5 class="text-center"><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                                <h5 class="text-center">WEIGHTS AND MEASURES AGENCY </h5>
                                <h5 class="text-center">REGIONAL COLLECTION REPORT</h5>
                                <p class="text-center"><?= date("d M Y") ?></p>

                            </div>
                            <div class="col-md-4 align-right"><img class="float-right"
                                    src="<?= base_url() ?>/assets/images/wma1.png" alt="">
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- info row -->

                        <!-- /.row -->

                        <!-- Table row -->
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>

                                            <th>Region</th>
                                            <th>Paid Amount</th>
                                            <th>Pending Amount</th>
                                            <th>Total</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach ($fullReport as $report) : ?>

                                        <tr>
                                            <td><?= $report['region'] ?></td>
                                            <td>Tsh <?= number_format($report['paid']) ?></td>
                                            <td>Tsh <?= number_format($report['pending']) ?></td>
                                            <td>Tsh <?= number_format($report['total']) ?></td>

                                        </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <div class="row">
                            <!-- accepted payments column -->


                            <?php
                            $paidAmount = 0;
                            $pendingAmount = 0;
                            $totalAmount = 0;

                            ?>
                            <?php foreach ($fullReport as $rpt) : ?>

                            <?php if ($rpt['paid']) : ?>
                            <?php $paidAmount += $rpt['paid'] ?>
                            <?php endif; ?>
                            <?php if ($rpt['pending']) : ?>
                            <?php $pendingAmount += $rpt['pending'] ?>
                            <?php endif; ?>
                            <?php if ($rpt['total']) : ?>
                            <?php $totalAmount += $rpt['total'] ?>
                            <?php endif; ?>

                            <?php endforeach; ?>


                            <!-- /.col -->
                            <div class="col-6">
                                <p class="lead">Collection Due <?= date('d M Y') ?></p>

                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Paid:</th>
                                            <td>Tsh <?= number_format($paidAmount) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Pending:</th>
                                            <td>Tsh <?= number_format($pendingAmount) ?></td>
                                        </tr>

                                        <tr>
                                            <th>Total Collection:</th>
                                            <td>Tsh <?= number_format($totalAmount) ?></td>
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
                        <div class="row no-print">
                            <div class="col-12">
                                <a href="<?= base_url() ?>/printFullReport" id="print" target="_blank"
                                    class="btn btn-success pull-right"><i class="fas fa-print"></i> Print</a>

                            </div>
                        </div>
                    </div>
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
        <!-- /.content -->
    </div>
    <!-- ./wrapper -->

    <script type="text/javascript">
    window.addEventListener("load", window.print());
    </script>
</body>

<!-- Mirrored from adminlte.io/themes/v3/pages/examples/invoice-print.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 24 Jul 2020 16:50:03 GMT -->

</html>