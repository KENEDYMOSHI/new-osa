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
                                <label>Company Name</label>
                                <input type="text" name="company_name" class="form-control" placeholder="Search by company" value="<?= $filters['company_name'] ?? '' ?>">
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
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i>Filter</button>
                            <a href="<?= base_url('licenseReport') ?>" class="btn btn-secondary"><i class="fas fa-redo mr-1"></i>Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Licenses Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-id-card mr-2"></i>Issued Licenses (<?= count($licenses) ?>)</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>License Number</th>
                            <th>Applicant Name</th>
                            <th>Company Name</th>
                            <th>License Type</th>
                            <th>Region</th>
                            <th>Issue Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
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
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <strong class="text-primary"><?= $license->license_number ?></strong>
                                    </td>
                                    <td><?= $license->applicant_name ?? ($license->first_name . ' ' . $license->last_name) ?></td>
                                    <td><?= $license->company_name ?? $license->business_name ?? 'N/A' ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= $license->license_type ?></span>
                                    </td>
                                    <td><?= $license->region ?? 'N/A' ?></td>
                                    <td><?= date('d M Y', strtotime($license->created_at)) ?></td>
                                    <td><?= date('d M Y', strtotime($license->expiry_date)) ?></td>
                                    <td>
                                        <?php
                                        $today = date('Y-m-d');
                                        $expiry = date('Y-m-d', strtotime($license->expiry_date));
                                        if ($expiry < $today) {
                                            echo '<span class="badge badge-danger">Expired</span>';
                                        } else {
                                            $daysLeft = floor((strtotime($expiry) - strtotime($today)) / 86400);
                                            if ($daysLeft <= 30) {
                                                echo '<span class="badge badge-warning">Expiring Soon (' . $daysLeft . ' days)</span>';
                                            } else {
                                                echo '<span class="badge badge-success">Active</span>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewLicense('<?= $license->id ?>')">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button class="btn btn-primary" onclick="printLicense('<?= $license->id ?>')">
                                                <i class="fas fa-print"></i> Print
                                            </button>
                                        </div>
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

<script>
function viewLicense(licenseId) {
    alert('View license: ' + licenseId);
    // TODO: Implement license view modal or redirect
}

function printLicense(licenseId) {
    alert('Print license: ' + licenseId);
    // TODO: Implement license print functionality
}
</script>

<?= $this->endSection(); ?>
