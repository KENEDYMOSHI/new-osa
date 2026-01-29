<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filter Statistics</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= base_url('licenseStatistics') ?>" method="get">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Year</label>
                                <select name="year" class="form-control select2">
                                    <?php 
                                    $current = date('Y');
                                    $selected = $selectedYear ?? $current;
                                    for ($i = $current; $i >= $current - 5; $i--) {
                                        $sel = ($i == $selected) ? 'selected' : '';
                                        echo "<option value='$i' $sel>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                             <div class="form-group mb-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                <a href="<?= base_url('licenseStatistics') ?>" class="btn btn-default ml-2"><i class="fas fa-sync"></i> Reset</a>
                             </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Financial Year Recap Report</h3>
                    </div>
                    <div class="card-body">
                         <div class="chart">
                             <canvas id="monthlyRecapChart" style="min-height: 250px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
           <!-- License Type Statistics -->
           <div class="col-lg-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">License Type Statistics</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>License Class</th>
                                        <th class="text-center">Applicants</th>
                                        <th>Popularity</th>
                                        <th class="text-right">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($licenseStats)): ?>
                                        <tr><td colspan="4" class="text-center">No data available</td></tr>
                                    <?php else: ?>
                                        <?php 
                                            // Show only top 5 here for brevity, full list in modal
                                            $topLicenses = array_slice($licenseStats, 0, 5); 
                                            foreach($topLicenses as $stat): 
                                        ?>
                                        <tr>
                                            <td><?= $stat['name'] ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-success"><?= $stat['count'] ?></span>
                                            </td>
                                            <td>
                                                <div class="progress progress-xs">
                                                    <?php 
                                                        $color = 'bg-success';
                                                        if($stat['percent'] < 20) $color = 'bg-warning'; 
                                                        if($stat['percent'] >= 20 && $stat['percent'] < 50) $color = 'bg-primary'; 
                                                    ?>
                                                    <div class="progress-bar <?= $color ?>" style="width: <?= $stat['percent'] ?>%"></div>
                                                </div>
                                            </td>
                                            <td class="text-right"><span class="badge badge-light"><?= $stat['percent'] ?>%</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="javascript:void(0)" class="text-muted" data-toggle="modal" data-target="#modal-all-licenses">View All Licenses <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
           </div>

           <!-- Top Regions -->
           <div class="col-lg-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Top Regions (Mikoa)</h3>
                    </div>
                    <div class="card-body">
                         <?php if(empty($regionStats)): ?>
                            <p class="text-center text-muted">No regional data available</p>
                        <?php else: ?>
                            <?php foreach($regionStats as $index => $stat): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0"><i class="fas fa-map-marker-alt text-danger mr-2"></i> <?= $stat['name'] ?></h6>
                                    <span class="badge badge-dark"><?= $stat['count'] ?></span>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Performance: <?= $stat['percent'] ?>%</small>
                                    <div class="progress progress-xs">
                                         <div class="progress-bar <?= $index == 0 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= $stat['percent'] ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                    </div>
                     <div class="card-footer text-center">
                         <!-- Changed from javascript:void(0) to trigger modal -->
                        <a href="javascript:void(0)" class="text-muted" data-toggle="modal" data-target="#modal-all-regions">View All Regions <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
           </div>
        </div>

        <!-- Financial Stats (Placeholder to match screenshot) -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm">
                    <div class="info-box-content text-center">
                        <span class="info-box-text text-success font-weight-bold"><i class="fas fa-arrow-up"></i> 17%</span>
                        <span class="info-box-number">TZS <?= number_format($financials['total_amount'] ?? 0) ?></span>
                        <span class="info-box-text text-muted">TOTAL AMOUNT</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm">
                    <div class="info-box-content text-center">
                        <span class="info-box-text text-warning font-weight-bold"><i class="fas fa-arrow-left"></i> 0%</span>
                        <span class="info-box-number">TZS <?= number_format($financials['application_fee'] ?? 0) ?></span>
                        <span class="info-box-text text-muted">APPLICATION FEE</span>
                    </div>
                </div>
            </div>
             <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm">
                    <div class="info-box-content text-center">
                        <span class="info-box-text text-warning font-weight-bold"><i class="fas fa-arrow-left"></i> 0%</span>
                        <span class="info-box-number">TZS <?= number_format($financials['license_fee'] ?? 0) ?></span>
                        <span class="info-box-text text-muted">LICENSE FEE</span>
                    </div>
                </div>
            </div>
             <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm">
                    <div class="info-box-content text-center">
                        <span class="info-box-text text-success font-weight-bold"><i class="fas fa-arrow-up"></i> 20%</span>
                        <span class="info-box-number">TZS <?= number_format($financials['paid_fee'] ?? 0) ?></span>
                        <span class="info-box-text text-muted">PAID FEE</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: All Licenses -->
<div class="modal fade" id="modal-all-licenses">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">All License Statistics</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped" id="table-all-licenses">
                    <thead>
                        <tr>
                            <th>License Class</th>
                            <th class="text-center">Applicants</th>
                            <th>Popularity</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php foreach($licenseStats as $stat): ?>
                        <tr>
                            <td><?= $stat['name'] ?></td>
                            <td class="text-center">
                                <span class="badge badge-success"><?= $stat['count'] ?></span>
                            </td>
                            <td>
                                <div class="progress progress-xs">
                                    <?php 
                                        $color = 'bg-success';
                                        if($stat['percent'] < 20) $color = 'bg-warning'; 
                                        if($stat['percent'] >= 20 && $stat['percent'] < 50) $color = 'bg-primary'; 
                                    ?>
                                    <div class="progress-bar <?= $color ?>" style="width: <?= $stat['percent'] ?>%"></div>
                                </div>
                            </td>
                            <td class="text-right"><span class="badge badge-light"><?= $stat['percent'] ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: All Regions -->
<div class="modal fade" id="modal-all-regions">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">All Regional Statistics</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                 <table class="table table-bordered table-striped" id="table-all-regions">
                     <thead>
                        <tr>
                            <th>Region</th>
                            <th class="text-center">Count</th>
                            <th>Performance</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach($allRegions as $stat): ?>
                            <tr>
                                <td><?= $stat['name'] ?></td>
                                <td class="text-center"><span class="badge badge-dark"><?= $stat['count'] ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2"><?= $stat['percent'] ?>%</span>
                                        <div class="progress progress-xs flex-grow-1">
                                             <div class="progress-bar bg-info" style="width: <?= $stat['percent'] ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                     </tbody>
                 </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Script for Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Ensure Chart.js is loaded -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById('monthlyRecapChart').getContext('2d');
        
        var monthlyDataCurrent = <?= json_encode($monthlyStats['currentYear']) ?>;
        var monthlyDataLast = <?= json_encode($monthlyStats['lastYear']) ?>;
        var labels = <?= json_encode($monthlyStats['labels']) ?>;

        var monthlyRecapChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '<?= $years['current'] ?>',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: monthlyDataCurrent,
                        fill: true
                    },
                    {
                        label: '<?= $years['last'] ?>',
                        backgroundColor: 'rgba(210, 214, 222, 1)',
                        borderColor: 'rgba(210, 214, 222, 1)',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: monthlyDataLast,
                        fill: true
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: true
                },
                 scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                }
            }
        });

        // Auto-open Modals based on URL parameters
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('modal')) {
            var modalType = urlParams.get('modal');
            if (modalType === 'licenses') {
                $('#modal-all-licenses').modal('show');
            } else if (modalType === 'regions') {
                $('#modal-all-regions').modal('show');
            }
        }
    });
</script>

<?= $this->endSection(); ?>
