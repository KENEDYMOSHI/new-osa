<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->


<!-- Main content -->
<section class="content body">
<!-- <h1> <?= count($vtv)?></h4>
    <pre>
        <?php print_r($vtv)?>
    </pre> -->
    <div class="container-fluid">
  
        <!-- Scales -->
        <div class="row">
           
            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-box"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-number">Pre Package</span>
                        
                        <span class="">Paid: Tsh<?= paidAmount($prePackage); ?></span><br>
                        <span class="">Pending: Tsh<?= pendingAmount($prePackage); ?></span>
                        <span class="info-box-number">Total: Tsh <?= totalAmount($prePackage); ?></span>
                    </div>

                </div>

            </div>
            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-truck-container"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-number">VTC</span>
                        <span class="">Paid: Tsh<?= paidAmount($vtv); ?></span><br>
                        <span class="">Pending: Tsh<?= pendingAmount($vtv); ?></span>
                        <span class="info-box-number">Total: Tsh <?= totalAmount($vtv); ?></span>
                    </div>

                </div>

            </div>
            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-truck"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-number">SBL</span>
                        <span class="">Paid: Tsh <?= paidAmount($sbl); ?></span><br>
                        <span class="">Pending: Tsh <?= pendingAmount($sbl); ?></span>
                        <span class="info-box-number">Total: Tsh <?= totalAmount($sbl); ?></span>
                    </div>

                </div>

            </div>




            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-ring"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-number">Meter</span>
                        <span class="">Paid: Tsh <?= paidAmount($waterMeter); ?></span><br>
                        <span class="">Pending: Tsh <?= pendingAmount($waterMeter); ?></span>
                        <span class="info-box-number">Total: Tsh <?= totalAmount($waterMeter); ?></span>
                    </div>

                </div>

            </div>
            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-ship"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-number">Metrological Supervision</span>
                        <span class="">Paid: Tsh <?= '0'; ?></span><br>
                        <span class="">Pending: Tsh <?= '0'; ?></span>
                        <span class="info-box-number">Total: Tsh <?= '0'; ?></span>
                    </div>

                </div>

            </div>
        </div>


        <div class="card">
            <!-- <div class="card-header">
                Collection Summary
            </div> -->
            <div class="card-body">
                <div id="dataChart" class="mt-5"></div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-4 col-6">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span> -->
                            <h5 id="paid" class="description-header"></h5>
                            <span class="description-text">PAID AMOUNT</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 col-6">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                                0%</span> -->
                            <h5 id="pending" class="description-header"></h5>
                            <span class="description-text">PENDING AMOUNT</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 col-6">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span> -->
                            <h5 id="total" class="description-header"></h5>
                            <span class="description-text">TOTAL AMOUNT</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->


        <!-- ======================================= -->

    </div>

</section>

<?= $this->endSection(); ?>