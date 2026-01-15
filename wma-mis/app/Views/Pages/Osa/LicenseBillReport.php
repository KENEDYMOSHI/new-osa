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
                    <li class="breadcrumb-item active">License Bill Report</li>
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
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filter Report</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('licenseBillReport') ?>">
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
                                    if (!empty($licenseTypes)) {
                                        foreach ($licenseTypes as $type) {
                                            $typeName = $type['name'];
                                            $selected = (isset($filters['license_type']) && $filters['license_type'] == $typeName) ? 'selected' : '';
                                            echo "<option value='$typeName' $selected>$typeName</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                    <i class="fas fa-search mr-1"></i> Filter
                                </button>
                           </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- License Bills Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    License Billing Report
                </h3>
                 <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table id="licenseBillTable" class="table table-hover table-striped table-sm text-nowrap" style="font-size: 0.9rem;">
                    <thead class="bg-light">
                        <tr>
                            <th>Payer Name</th>
                            <th>License Type</th>
                            <th>Fee Type</th>
                            <th>Payment Status</th>
                            <th>Date</th>
                            <th>Control Number</th>
                            <th>Amount (TZS)</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($licenses)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No records found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($licenses as $l): ?>
                                <tr>
                                    <td class="align-middle">
                                        <?= ucwords(strtolower($l->payer_name ?? ($l->applicant_name ?? ($l->first_name . ' ' . $l->last_name)))) ?>
                                    </td>
                                    <td class="align-middle text-primary font-weight-bold">
                                        <?= $l->license_type ?? 'N/A' ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-light border">
                                            <?= $l->fee_type ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                            $status = $l->payment_status ?? 'Pending';
                                            $badgeClass = 'badge-secondary';
                                            if ($status === 'Paid') $badgeClass = 'badge-success';
                                            elseif ($status === 'Partial') $badgeClass = 'badge-warning';
                                            elseif ($status === 'Pending') $badgeClass = 'badge-secondary';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <?= $l->bill_date ?>
                                    </td>
                                    <td class="align-middle"><?= $l->control_number ?? '-' ?></td>
                                    <td class="align-middle"><?= number_format($l->bill_amount ?? 0, 2) ?></td>
                                    <td class="align-middle text-right">
                                        <button class="btn btn-sm btn-outline-primary" title="View Bill" onclick="window.location.href='#'">
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
        $("#licenseBillTable").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "print"],
            "dom": 'Bfrtip',
            "order": [], 
            "pageLength": 20
        });
    });
</script>
<?= $this->endSection(); ?>
