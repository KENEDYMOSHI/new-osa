<?php

use App\Libraries\ArrayLibrary; ?>
<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
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
    <div class="container-fluid">
        <!-- Scales -->
        <div class="row">
            <!-- 
        <pre>
           
        </pre> -->

            <?php $center = $user->inGroup('officer', 'manager') ? $user->collection_center : 'all' ?>

            <div class="col-12 col-sm-6 col-md-4">
                <a href="<?= base_url('registeredPrepackages/' . $center) ?>">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-box"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-number">Pre Package(<?= instrumentQuantity($prePackage) ?>)</span>
                            <span class="">Paid: Tsh<?= paidAmount($prePackage); ?></span><br>
                            <span class="">Pending: Tsh<?= pendingAmount($prePackage); ?></span>
                            <span class="info-box-number">Total: Tsh <?= totalAmount($prePackage); ?></span>
                        </div>

                    </div>
                </a>


            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="<?= base_url('listVehicleTanks/' . $center) ?>">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-truck-container"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-number">VTV(<?= instrumentQuantity($vtv) ?>)</span>
                            <span class="">Paid: Tsh<?= paidAmount($vtv); ?></span><br>
                            <span class="">Pending: Tsh<?= pendingAmount($vtv); ?></span>
                            <span class="info-box-number">Total: Tsh <?= totalAmount($vtv); ?></span>
                        </div>

                    </div>
                </a>

            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <a href="<?= base_url('listLorries/' . $center) ?>">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-truck"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-number">SBL(<?= instrumentQuantity($sbl) ?>)</span>
                            <span class="">Paid: Tsh <?= paidAmount($sbl); ?></span><br>
                            <span class="">Pending: Tsh <?= pendingAmount($sbl); ?></span>
                            <span class="info-box-number">Total: Tsh <?= totalAmount($sbl); ?></span>
                        </div>

                    </div>
                </a>

            </div>



            <div class="col-12 col-sm-6 col-md-4">
                <a href="<?= base_url('WaterMeterList/' . $center) ?>">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-ring"></i></span>
                        <?php $meterCount = (new ArrayLibrary($waterMeter))->map(fn($meter) => $meter->quantity)->reduce(fn($x, $y) => $x + $y)->get(); ?>
                        <div class="info-box-content">
                            <span class="info-box-number">Water Meters(<?= $meterCount ?>)</span>
                            <span class="">Paid: Tsh <?= paidAmount($waterMeter); ?></span><br>
                            <span class="">Pending: Tsh <?= pendingAmount($waterMeter); ?></span>
                            <span class="info-box-number">Total: Tsh <?= totalAmount($waterMeter); ?></span>
                        </div>

                    </div>
                </a>


            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-cabinet-filing"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-number">Others(<?= count($others); ?>)</span>
                        <span class="">Paid: Tsh <?= paidAmount($others); ?></span><br>
                        <span class="">Pending: Tsh <?= pendingAmount($others); ?></span>
                        <span class="info-box-number">Total: Tsh <?= totalAmount($others); ?></span>
                    </div>

                </div>

            </div>
            <!-- <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-2 p-10"><i class="fal fa-ship"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-number">Metrlogical Supervision(<?= '0'; ?>)</span>
                        <span class="">Paid: Tsh <?= '0'; ?></span><br>
                        <span class="">Pending: Tsh <?= '0'; ?></span>
                        <span class="info-box-number">Total: Tsh <?= '0'; ?></span>
                    </div>

                </div>

            </div> -->
        </div>



        <div class="card">
            <!-- <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" onclick="getCollections()">Check</button>
            </div> -->
            <div class="card-body">

                <div id="dataChart" class="mt-5">
                    <div class="text-center" id="spinner" style="display:none">
                        <div class="spinner-border text-primary" role="status">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span> -->
                            <span class="description-text">PAID AMOUNT</span>
                            <h5 id="paid" class="description-header"><?= $annualTotal ?></h5>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                                0%</span> -->
                            <span class="description-text">PENDING AMOUNT</span>
                            <h5 id="pending" class="description-header"><?= $pendingUnpaid ?></h5>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                                0%</span> -->
                            <span class="description-text">PARTIAL AMOUNT</span>
                            <h5 id="partial" class="description-header"><?= $partialUnpaid ?></h5>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span> -->
                            <span class="description-text">TOTAL AMOUNT</span>
                            <h5 id="total" class="description-header"><?= $all ?></h5>
                        </div>
                        <!-- /.description-block -->
                    </div>

                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span> -->
                            <span class="description-text">COLLECTION FOR <?= dateFormatter(date('Y-m-d')) ?></span>
                            <h5 id="" class="description-header">TZS <?= number_format($today) ?></h5>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                                0%</span> -->
                            <span class="description-text">ACCUMULATED ACTUAL COLLECTION <?= $monthYear ?></span>
                            <h5 id="" class="description-header">TZS <?= number_format($accumulated) ?></h5>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                                0%</span> -->
                            <span class="description-text">ESTIMATE <?= $monthYear ?></span>
                            <h5 id="" class="description-header">TZS <?= number_format($estimate) ?></h5>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="description-block border-right">
                                    <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span> -->
                                    <span class="description-text">VARIANCE</span>
                                    <h5 id="" class="description-header">
                                        <?php if ($variance > 0): ?>
                                            <i class="far fa-arrow-up" style="color:green"></i>
                                        <?php else: ?>
                                            <i class="far fa-arrow-down" style="color:crimson"></i>
                                        <?php endif; ?>
                                        TZS <?= number_format($variance) ?>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="description-block border-right">
                                    <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span> -->
                                    <span class="description-text">VARIANCE PERCENTAGE</span>
                                    <h5 id="" class="description-header">
                                        <?php if ($percent > 0): ?>
                                            <i class="far fa-arrow-up" style="color:green"></i>
                                        <?php else: ?>
                                            <i class="far fa-arrow-down" style="color:crimson"></i>
                                        <?php endif; ?>

                                        <?= $percent ?> %
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <!-- /.description-block -->
                    </div>

                </div>
                <!-- /.row -->
            </div>
        </div>

        <!-- /.col -->

        <!-- fix for small devices only -->


        <!-- ======================================= -->

    </div>
    <script>
        const formatAmount = (value) => {
            return new Intl.NumberFormat().format(value)
        }
        let spinner = document.querySelector('#spinner')

        function loading() {
            spinner.style.display = 'block'


        }

        //remove loading animation
        function done() {

            spinner.style.display = 'none'

        }

        loading()
        fetch('dataChart', {
                method: 'GET',


            }).then(response => response.json())
            .then(data => {
                done()
                console.log(data)
                const {
                    amounts,
                    total,
                    paid,
                    pending,
                    partial
                } = data
                // document.querySelector('#total').innerHTML = total
                // document.querySelector('#paid').innerHTML = paid
                // document.querySelector('#pending').innerHTML = pending
                // document.querySelector('#partial').innerHTML = partial
                renderChart(amounts)
            })

        function renderChart(chartData) {



            // console.log(chartData)
            var options = {
                series: [{
                    name: 'Collection',
                    data: chartData,
                    color: '#DB611E',
                }, ],
                chart: {
                    type: 'bar',
                    height: 350,
                },

                title: {
                    text: 'Monthly Collection In All Activities',
                    align: 'center',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        dataLabels: {
                            position: 'top', // top, center, bottom
                        },
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return 'TZS ' + formatAmount(val);
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ['#333'],
                    },
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['transparent'],
                },
                xaxis: {
                    categories: [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec',

                    ],
                },
                yaxis: {
                    title: {
                        text: 'Collection in TZS',
                    },
                },
                fill: {
                    opacity: 1,
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'TZS ' + formatAmount(val);
                        },
                    },
                },
            };
            const dataChart = document.querySelector("#dataChart")
            dataChart.innerHTML = ''
            var chart = new ApexCharts(dataChart, options);
            chart.render();



        }
    </script>
</section>

<?= $this->endSection(); ?>