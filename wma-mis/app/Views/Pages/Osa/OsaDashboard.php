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
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">

    <!-- Top Info Boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">TOTAL APPLICATIONS</span>
                    <span class="info-box-number"><?= $dashboard_stats['total_applications'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">APPROVED APPLICATIONS</span>
                    <span class="info-box-number"><?= $dashboard_stats['approved_applications'] ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">PENDING APPLICATIONS</span>
                    <span class="info-box-number"><?= $dashboard_stats['pending_applications'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">REJECTED APPLICATIONS</span>
                    <span class="info-box-number"><?= $dashboard_stats['rejected_applications'] ?></span>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!-- Licenses Stats & Metrics Row -->
    <div class="row">
        <!-- Active Licenses Card -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card mb-3">
                <div class="card-body text-center py-3"> <!-- Reduced padding -->
                    <span class="fa-stack fa-2x text-orange mb-1">
                        <i class="far fa-circle fa-stack-2x"></i>
                        <i class="fas fa-check fa-stack-1x"></i>
                    </span>
                    <h4 class="mb-0 font-weight-bold text-secondary"><?= $dashboard_stats['active_licenses'] ?></h4>
                    <span class="text-muted text-sm">Active licenses</span>
                </div>
            </div>
        </div>

        <!-- Expired Licenses Card -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card mb-3">
                <div class="card-body text-center py-3"> <!-- Reduced padding -->
                    <span class="fa-stack fa-2x text-orange mb-1">
                        <i class="far fa-circle fa-stack-2x"></i>
                        <i class="fas fa-exclamation-triangle fa-stack-1x"></i>
                    </span>
                    <h4 class="mb-0 font-weight-bold text-secondary"><?= $dashboard_stats['expired_licenses'] ?></h4>
                    <span class="text-muted text-sm">Expired Licenses</span>
                </div>
            </div>
        </div>
        
        <!-- Application Status Metric Card -->
        <div class="col-md-6">
             <div class="card mb-3">
                <div class="card-body d-flex flex-column justify-content-center py-3"> <!-- Reduced padding -->
                    <h6 class="text-muted mb-3 font-weight-bold" style="font-size: 0.9rem;">Application Status Metric</h6>
                    
                    <?php 
                        $total = $dashboard_stats['total_applications'] > 0 ? $dashboard_stats['total_applications'] : 1;
                        $success_pct = ($dashboard_stats['approved_applications'] / $total) * 100;
                        $rejected_pct = ($dashboard_stats['rejected_applications'] / $total) * 100;
                    ?>

                    <div class="progress mb-2" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-orange" role="progressbar" style="width: <?= $success_pct ?>%; background-color: #fd7e14;" aria-valuenow="<?= $success_pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 1%; background-color: #fff;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100"></div> <!-- Spacer look -->
                        <div class="progress-bar bg-orange" role="progressbar" style="width: <?= $rejected_pct ?>%; opacity: 0.7; background-color: #fd7e14;" aria-valuenow="<?= $rejected_pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="d-flex align-items-center" style="font-size: 0.8rem;">
                         <div class="mr-4">
                            <i class="fas fa-circle text-orange mr-1" style="font-size: 0.5rem;"></i> 
                            <span class="text-muted">Successful (<?= $dashboard_stats['approved_applications'] ?>)</span>
                         </div>
                         <div>
                            <i class="fas fa-circle text-orange mr-1" style="opacity: 0.7; font-size: 0.5rem;"></i>
                            <span class="text-muted">Rejected (<?= $dashboard_stats['rejected_applications'] ?>)</span>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Main Report Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h5 class="card-title">Monthly Recap Report</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-center">
                                <strong>Financial Year Recap Report</strong>
                            </p>
                            <div class="chart">
                                <!-- Sales Chart Canvas -->
                                <canvas id="salesChart" height="180" style="height: 180px;"></canvas>
                            </div>
                            <!-- /.chart-responsive -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <hr>

                    <!-- New Row for License Stats and Top Regions -->
                    <div class="row mt-4">
                         <!-- License Type Statistics (Left) -->
                         <div class="col-md-8">
                            <p class="text-center">
                                <strong>License Type Statistics</strong>
                            </p>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>License Class</th>
                                            <th class="text-center">Applicants</th>
                                            <th>Popularity</th>
                                            <th style="width: 40px" class="text-center">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dashboard_stats['license_stats'] as $license): ?>
                                            <tr>
                                                <td><?= $license['name'] ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $license['color'] ?>"><?= $license['count'] ?></span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar bg-<?= $license['color'] ?>" style="width: <?= $license['percent'] ?>%"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center"><span class="badge bg-<?= $license['color'] ?>"><?= $license['percent'] ?>%</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <a href="#" class="text-muted text-sm">View All Licenses <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                        </div>

                        <!-- Top Regions (Right) -->
                        <div class="col-md-4 border-left">
                            <p class="text-center">
                                <strong>Top Regions (Mikoa)</strong>
                            </p>

                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                <?php foreach ($dashboard_stats['regions'] as $region): ?>
                                    <li class="item">
                                        <div class="product-img">
                                            <div class="d-flex justify-content-center align-items-center bg-light rounded-circle" style="width: 45px; height: 45px;">
                                                <i class="fas fa-map-marker-alt text-<?= $region['color'] ?> fa-lg"></i>
                                            </div>
                                        </div>
                                        <div class="product-info">
                                            <a href="javascript:void(0)" class="product-title text-secondary">
                                                <?= $region['name'] ?>
                                                <span class="badge badge-<?= $region['color'] ?> float-right"><?= $region['count'] ?></span>
                                            </a>
                                            <span class="product-description text-sm">
                                                Performance: <?= $region['percent'] ?>%
                                                <div class="progress progress-xxs mt-1">
                                                    <div class="progress-bar bg-<?= $region['color'] ?>" style="width: <?= $region['percent'] ?>%"></div>
                                                </div>
                                            </span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <div class="mt-3 text-center">
                                <a href="#" class="text-muted text-sm">View All Regions <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>

                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- ./card-body -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-3 col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span>
                                <h5 class="description-header">TZS <?= number_format($dashboard_stats['financials']['total_amount']) ?></h5>
                                <span class="description-text">TOTAL AMOUNT</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-2 col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                                <h5 class="description-header">TZS <?= number_format($dashboard_stats['financials']['application_fee']) ?></h5>
                                <span class="description-text">APPLICATION FEE</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-2 col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                                <h5 class="description-header">TZS <?= number_format($dashboard_stats['financials']['license_fee']) ?></h5>
                                <span class="description-text">LICENSE FEE</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-2 col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span>
                                <h5 class="description-header">TZS <?= number_format($dashboard_stats['financials']['paid_fee']) ?></h5>
                                <span class="description-text">PAID FEE</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-6">
                            <div class="description-block">
                                <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> 18%</span>
                                <h5 class="description-header">TZS <?= number_format($dashboard_stats['financials']['pending_fee']) ?></h5>
                                <span class="description-text">PENDING FEE</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- PIPING CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(function () {
        'use strict'

        // Get context with jQuery - using jQuery's .get() method.
        var salesChartCanvas = $('#salesChart').get(0).getContext('2d')

        var salesChartData = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [
                {
                    label: '2025/2026',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    pointRadius: false,
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: [28, 48, 40, 19, 86, 27, 90, 45, 60, 35, 78, 92],
                    fill: true,
                    tension: 0.4
                },
                {
                    label: '2026/2027',
                    backgroundColor: 'rgba(210, 214, 222, 1)',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    pointRadius: false,
                    pointColor: 'rgba(210, 214, 222, 1)',
                    pointStrokeColor: '#c1c7d1',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data: [65, 59, 80, 81, 56, 55, 40, 70, 45, 65, 30, 85],
                    fill: true,
                    tension: 0.4
                }
            ]
        }

        var salesChartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: true,
                position: 'left',
                align: 'center',
                labels: {
                     boxWidth: 12
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }

        // This will get the first returned node in the jQuery collection.
        // eslint-disable-next-line no-unused-vars
        var salesChart = new Chart(salesChartCanvas, {
            type: 'line',
            data: salesChartData,
            options: salesChartOptions
        })
    })
</script>
<?= $this->endSection(); ?>
