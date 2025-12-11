<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/directorDashboard">Dashboard</a></li>
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
                            <h5 class="text-center"><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                            <h5 class="text-center">WEIGHTS AND MEASURES AGENCY </h5>
                            <h5 class="text-center">REGIONAL COLLECTION REPORT</h5>
                            <p class="text-center"><?= date("d M Y") ?></p>

                        </div>
                        <div class="col-md-4 align-right"><img class="float-right" src="<?= base_url() ?>/assets/images/wma1.png" alt="">
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->

                    <!-- /.row -->

                    <!-- Table row -->

                    <pre>
                        <?php
                       // print_r($xxx);
                        ?>
                   </pre>
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>

                                        <th>Region</th>
                                        <th>Paid Amount</th>
                                        <th>Pending Amount</th>
                                        <th>Total</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($fullReport as $report) : ?>

                                        <tr>
                                            <td><?= $report['region'] ?></td>
                                            <td>Tsh <?= number_format($report['paid']) ?></td>
                                            <td>Tsh <?= number_format($report['pending']) ?></td>
                                            <td>Tsh <?= number_format($report['total']) ?></td>
                                            <td> <a href="<?= base_url() ?>/regionReport/<?= $report['region'] ?>">
                                                    <div class="btn btn-primary btn-sm"><i class="far fa-eye"></i></div>
                                                </a></td>
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
                                        <td>Tsh <?= number_format($paidAmount + $pendingAmount) ?></td>
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
                            <a href="<?= base_url() ?>/printFullReport" id="print" target="_blank" class="btn btn-primary pull-right"><i class="far fa-print"></i> Print</a>

                        </div>
                    </div>
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->

</section>
<?= $this->endSection(); ?>