<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<pre>
<?php
// print_r($vtcCollection);
// exit;
?>

</pre>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/fullReport">Full Report</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
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
                                        <th>View</th>

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
                                        <td>
                                            <a href="<?= base_url() ?>/listAllScales/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Inspection & Verification of Fuel Pumps</td>
                                        <td>Tsh <?= paidAmount($fuelPumpCollection) ?></td>
                                        <td>
                                            Tsh <?= pendingAmount($fuelPumpCollection) ?>
                                        </td>
                                        <td>Tsh <?= totalAmount($fuelPumpCollection) ?></td>
                                        <td>
                                            <a href="<?= base_url() ?>/listAllFuelPumps/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> Pre Package</td>
                                        <td>Tsh <?= paidAmount($prePackageCollection) ?></td>
                                        <td>
                                            Tsh <?= pendingAmount($prePackageCollection) ?>
                                        </td>
                                        <td>Tsh <?= totalAmount($prePackageCollection) ?></td>
                                        <td>
                                            <a href="<?= base_url() ?>/listAllPrePackage/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Vehicle Tank Verification</td>
                                        <td>Tsh <?= paidAmount($vtcCollection) ?></td>
                                        <td>
                                            Tsh <?= pendingAmount($vtcCollection) ?>
                                        </td>
                                        <td>Tsh <?= totalAmount($vtcCollection) ?></td>
                                        <td>
                                            <a href="<?= base_url() ?>/listAllVehicleTanks/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sandy & Ballast Lorries </td>
                                        <td>Tsh <?= paidAmount($sblCollection) ?></td>
                                        <td>
                                            Tsh <?= pendingAmount($sblCollection) ?>
                                        </td>
                                        <td>Tsh <?= totalAmount($sblCollection) ?></td>
                                        <td>
                                            <a href="<?= base_url() ?>/listAllLorries/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bulk Storage Tank Calibration</td>
                                        <td>Tsh <?= paidAmount($bstCollection) ?></td>
                                        <td>
                                            Tsh <?= pendingAmount($bstCollection) ?>
                                        </td>
                                        <td>Tsh <?= totalAmount($bstCollection) ?></td>
                                        <td>
                                            <a href="<?= base_url() ?>/listAllBulkStorageTanks/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Fixed Storage Tank Calibration</td>
                                        <td>Tsh <?= paidAmount($fstCollection) ?></td>
                                        <td>
                                            Tsh <?= pendingAmount($fstCollection) ?>
                                        </td>
                                        <td>Tsh <?= totalAmount($fstCollection) ?></td>
                                        <td>
                                            <a href="<?= base_url() ?>/listAllFixedStorageTanks/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Inspection Of Flow Meter</td>
                                        <td>Tsh <?= paidAmount($flowMeterCollection) ?></td>
                                        <td>
                                            Tsh <?= pendingAmount($flowMeterCollection) ?>
                                        </td>
                                        <td>Tsh <?= totalAmount($flowMeterCollection) ?></td>
                                        <td>
                                            <a href="<?= base_url() ?>/listAllFlowMeters/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Inspection Of Water Meter</td>
                                        <td>Tsh <?= paidAmount($waterMeterCollection) ?></td>
                                        <td>
                                            Tsh <?= pendingAmount($waterMeterCollection) ?>
                                        </td>
                                        <td>Tsh <?= totalAmount($waterMeterCollection) ?></td>
                                        <td>
                                            <a href="<?= base_url() ?>/listAllWaterMeters/<?= $region ?>" class="btn btn-success btn-sm"><i class="far fa-eye"></i></a>
                                        </td>
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
                    <div class="row no-print">
                        <div class="col-12">
                            <a href="<?= base_url() ?>/regionalReportPrint/<?= $region ?>" id="print" target="_blank" class="btn btn-success pull-right"><i class="far fa-print"></i>
                                Print</a>

                        </div>
                    </div>
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->

</section>
<?= $this->endSection(); ?>