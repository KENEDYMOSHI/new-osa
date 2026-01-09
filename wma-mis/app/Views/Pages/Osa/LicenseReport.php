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
                    <li class="breadcrumb-item"><a href="<?= base_url('osaDashboard') ?>">OSA Dashboard</a></li>
                    <li class="breadcrumb-item active">License Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filter Licenses</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('licenseReport') ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Applicant Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Search by name" value="<?= $filters['name'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date Range</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control float-right" id="reservation" name="dateRange" value="<?= $filters['dateRange'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Region</label>
                                <select name="region" class="form-control">
                                    <option value="">All Regions</option>
                                    <?php
                                    $regions = ['Dar es Salaam', 'Arusha', 'Dodoma', 'Geita', 'Iringa', 'Kagera', 'Katavi', 'Kigoma', 'Kilimanjaro', 'Lindi', 'Manyara', 'Mara', 'Mbeya', 'Morogoro', 'Mtwara', 'Mwanza', 'Njombe', 'Pwani', 'Rukwa', 'Ruvuma', 'Shinyanga', 'Simiyu', 'Singida', 'Songwe', 'Tabora', 'Tanga'];
                                    foreach ($regions as $region) {
                                        $selected = (isset($filters['region']) && $filters['region'] == $region) ? 'selected' : '';
                                        echo "<option value='$region' $selected>$region</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>License Type</label>
                                <select name="license_type" class="form-control">
                                    <option value="">All Types</option>
                                    <?php
                                    $types = ['Gas Meter Calibration', 'Fixed Storage Tanks Verification', 'Vehicle Tank Calibration', 'Water Meter Verification', 'Pre-Package Verification'];
                                    foreach ($types as $type) {
                                        $selected = (isset($filters['license_type']) && $filters['license_type'] == $type) ? 'selected' : '';
                                        echo "<option value='$type' $selected>$type</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Year</label>
                                <select name="year" class="form-control">
                                    <option value="">All Years</option>
                                    <?php
                                    for ($y = date('Y'); $y >= 2020; $y--) {
                                        $selected = (isset($filters['year']) && $filters['year'] == $y) ? 'selected' : '';
                                        echo "<option value='$y' $selected>$y</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <div>
                                <button type="submit" class="btn btn-primary shadow-sm px-4">
                                    <i class="fas fa-search mr-1"></i> Filter
                                </button>
                                <a href="<?= base_url('licenseReport') ?>" class="btn btn-outline-secondary shadow-sm ml-2 px-3">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Licenses Table -->
        <?php
        $activeCount = 0;
        $expiredCount = 0;
        $today = date('Y-m-d');
        if (!empty($licenses)) {
            foreach($licenses as $l) {
                $expiry = date('Y-m-d', strtotime($l->expiry_date));
                if ($expiry < $today) {
                    $expiredCount++;
                } else {
                    $activeCount++;
                }
            }
        }
        ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-id-card mr-2"></i>
                    Issued Licenses
                    <span class="badge badge-info ml-2" style="font-size: 0.9rem;"><?= count($licenses) ?> Total</span>
                    <span class="badge badge-success ml-1" style="font-size: 0.9rem;"><?= $activeCount ?> Active</span>
                    <span class="badge badge-danger ml-1" style="font-size: 0.9rem;"><?= $expiredCount ?> Expired</span>
                </h3>
            </div>
            <div class="card-body table-responsive">
                <table id="licenseTable" class="table table-hover table-striped table-sm text-nowrap" style="font-size: 0.9rem;">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>License Number</th>
                            <th>Applicant Name</th>
                            <th>License Type</th>
                            <th>Region</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Control #</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($licenses)): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No licenses found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($licenses as $license): ?>
                                <tr>
                                    <td class="align-middle"><?= $i++ ?></td>
                                    <td class="align-middle">
                                        <strong class="text-primary"><?= $license->license_number ?></strong>
                                    </td>
                                    <td class="align-middle">
                                        <?= ucwords(strtolower($license->applicant_name ?? ($license->first_name . ' ' . $license->last_name))) ?>
                                    </td>
                                    <td class="align-middle text-truncate" style="max-width: 150px;" title="<?= $license->license_type ?>">
                                        <?= $license->license_type ?>
                                    </td>
                                    <td class="align-middle"><?= $license->region ?? 'N/A' ?></td>
                                    <td class="align-middle">
                                        <?php
                                        $today = date('Y-m-d');
                                        $expiry = date('Y-m-d', strtotime($license->expiry_date));
                                        if ($expiry < $today) {
                                            echo '<span class="badge badge-danger">Expired</span>';
                                        } else {
                                            $daysLeft = floor((strtotime($expiry) - strtotime($today)) / 86400);
                                            if ($daysLeft <= 30) {
                                                // Calculate expiring date string for tooltip or context if needed, but not displaying column
                                                echo '<span class="badge badge-warning">Expiring Soon</span>';
                                            } else {
                                                echo '<span class="badge badge-success">Active</span>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($license->payment_status == 'Paid'): ?>
                                            <span class="badge badge-success">Paid</span>
                                        <?php elseif ($license->payment_status == 'Pending'): ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= $license->payment_status ?? 'N/A' ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle"><?= $license->control_number ?? '-' ?></td>
                                    <td class="align-middle"><?= number_format($license->total_amount ?? 0) ?></td>
                                    <td class="align-middle">
                                            <button class="btn btn-success btn-xs" onclick="viewLicense('<?= $license->license_number ?>')" title="View" data-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced License View Modal -->
<div class="modal fade" id="licenseModal" tabindex="-1" role="dialog" aria-labelledby="licenseModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title font-weight-bold" id="licenseModalLabel"><i class="fas fa-certificate text-primary mr-2"></i>License Preview</h5>
                <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light text-center p-0">
                <div id="licenseLoading" class="py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading license...</p>
                </div>
                
                <div class="license-wrapper p-4 d-none" id="licenseContainer" style="overflow-y: auto; max-height: 80vh;">
                    <img id="licenseImage" src="" class="img-fluid shadow-sm" alt="License Image" style="border: 1px solid #dee2e6; max-width: 100%; border-radius: 4px;">
                </div>

                <div id="licenseError" class="py-5 d-none">
                    <i class="fas fa-exclamation-circle text-danger fa-3x mb-3"></i>
                    <p class="text-danger font-weight-bold">License image not found or failed to load.</p>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0 d-flex justify-content-between">
                 <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
                 <div>
                     <a href="#" id="downloadBtn" class="btn btn-success px-4 mr-2" download>
                        <i class="fas fa-download mr-1"></i> Download
                     </a>
                     <button type="button" class="btn btn-primary px-4" id="printBtn">
                        <i class="fas fa-print mr-1"></i> Print
                     </button>
                 </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>
function viewLicense(licenseNumber) {
    if (!licenseNumber) {
        alert('Invalid License Number');
        return;
    }

    $('#licenseModal').modal('show');
    
    // Reset state
    $('#licenseLoading').removeClass('d-none');
    $('#licenseContainer').addClass('d-none');
    $('#licenseError').addClass('d-none');

    // Construct URL pointing to the smart endpoint
    // We encode the license number to handle slashes/spaces safely in URL
    var imageUrl = 'http://localhost:8080/api/license/view-image/' + encodeURIComponent(licenseNumber);
    
    // Set verify and load
    var img = new Image();
    img.onload = function() {
        $('#licenseLoading').addClass('d-none');
        $('#licenseContainer').removeClass('d-none');
        $('#licenseImage').attr('src', imageUrl);
        
        // Setup Download Button
        $('#downloadBtn').attr('href', imageUrl);
        
        // Setup Print Button
        $('#printBtn').off('click').on('click', function() {
            printImage(imageUrl);
        });
    };
    img.onerror = function() {
        $('#licenseLoading').addClass('d-none');
        $('#licenseError').removeClass('d-none');
        // Retry logic or explicit error message could go here
    };
    img.src = imageUrl;
}

function printImage(url) {
    var win = window.open('');
    win.document.write('<html><head><title>Print License</title></head><body style="margin:0; text-align:center;">');
    win.document.write('<img src="' + url + '" style="max-width:100%;" onload="window.print();window.close()" />');
    win.document.write('</body></html>');
    win.focus();
}

function exportToCsv() {
    // Collect current filter values
    var params = new URLSearchParams(window.location.search);
    var url = '<?= base_url('license/export') ?>' + '?' + params.toString();
    window.location.href = url;
}
</script>

</script>
<!-- Date Range Picker Script -->
<script>
    $(function() {
        // Initialize Date Range Picker
        $('#reservation').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoUpdateInput: false
        });

        $('#reservation').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#reservation').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Initialize DataTable with Buttons
        $("#licenseTable").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "print"],
            "dom": 'Bfrtip',
            "order": [], // Disable initial sort if needed, or let it sort by first column
            "pageLength": 20
        });
    });
</script>
<?= $this->endSection(); ?>
