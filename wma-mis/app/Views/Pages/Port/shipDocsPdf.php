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
    <link rel="stylesheet" href="<?=base_url()?>/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->

    <link rel="stylesheet" href="<?=base_url()?>/dist/css/adminlte.min.css">

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

                                            <th>SHIP ship</th>
                                            <th>REMARK</th>
                                            <th>APPENDIX</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr id="ship">
                                            <?php foreach ($documents as $ship) : ?>


                                        <tr>
                                            <td>STOWAGE PLAN</td>
                                            <td><?=$ship->StowagePlan ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>SHIP PARTICULARS</td>
                                            <td><?=$ship->ShipParticulars ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>TANKS CALIBRATION CERTIFICATE</td>
                                            <td><?=$ship->TankCalibrationCertificate ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>BILL OF LADING</td>
                                            <td><?=$ship->BillOfLading ?></td>
                                            <td></td>
                                        </tr>

                                        <td>CARGO MANIFEST</td>
                                        <td><?=$ship->CargoManifest ?></td>
                                        <td></td>
                                        </tr>
                                        <tr>
                                            <td>ULLAGE REPORT OF THE LAST LOADING POSTS</td>
                                            <td><?=$ship->UllageReportOfLoadingPorts ?></td>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>ULLAGE TEMPERATURE INTERFACE CALIBRATION CERTIFICATE</td>
                                            <td><?=$ship->UllageTemperatureInterfaceCalibrationCertificate ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>CERTIFICATES OF QUANTITY</td>
                                            <td><?=$ship->CertificateOfQuantity ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>CERTIFICATES OF QUALITY</td>
                                            <td><?=$ship->CertificateOfQuality ?></td>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>NOTICES OF READINESS SIGNED BY CARGO RECEIVER</td>
                                            <td><?=$ship->NoticeOfReadinessSignedByCargoReceiver ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>VESSEL EXPERIENCE FACTOR(V.E.F)</td>
                                            <td><?=$ship->VesselExperienceFactor ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>LAST/ ARRIVAL PORT BUNKER</td>
                                            <td><?=$ship->LastArrivalPortBunker ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>DISCHARGING ORDER/INSTRUCTION</td>
                                            <td><?=$ship->CargoDischargingOrder ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>CERTIFICATE OF ORIGIN</td>
                                            <td><?=$ship->CertificateOfOrigin ?></td>
                                            <td></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        </tr>



                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- /.row -->

                        <!-- this row will not appear when printing -->
                        <div class="row no-print">
                            <div class="col-12">
                                <a href="<?= base_url() ?>/printPage" id="print" target="_blank"
                                    class="btn btn-success pull-right"><i class="fas fa-download"></i> Download</a>

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


</body>

<!-- Mirrored from adminlte.io/themes/v3/pages/examples/invoice-print.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 24 Jul 2020 16:50:03 GMT -->

</html>