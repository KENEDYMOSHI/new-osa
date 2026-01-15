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
                                <label>Payer Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Search by name" value="<?= $filters['name'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Control Number</label>
                                <input type="text" name="control_number" class="form-control" placeholder="Enter Control Number" value="<?= $filters['control_number'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bill Description (License Type)</label>
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
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                             <div class="form-group">
                                <label>Fee Type</label>
                                <select name="fee_type" class="form-control">
                                    <option value="">All</option>
                                    <option value="Application Fee" <?= (isset($filters['fee_type']) && $filters['fee_type'] == 'Application Fee') ? 'selected' : '' ?>>Application Fee</option>
                                    <option value="License Fee" <?= (isset($filters['fee_type']) && $filters['fee_type'] == 'License Fee') ? 'selected' : '' ?>>License Fee</option>
                                </select>
                             </div>
                        </div>
                        <div class="col-md-3">
                             <div class="form-group">
                                <label>Payment Status</label>
                                <select name="payment_status" class="form-control">
                                    <option value="">All</option>
                                    <option value="Paid" <?= (isset($filters['payment_status']) && $filters['payment_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                    <option value="Pending" <?= (isset($filters['payment_status']) && $filters['payment_status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                </select>
                             </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                <table id="licenseBillTable" class="table table-hover table-striped table-sm text-nowrap" style="font-size: 0.8rem;">
                    <thead class="bg-light" style="font-size: 0.75rem;">
                        <tr>
                            <th>Payer Name</th>
                            <th>License Type</th>
                            <th>Fee Type</th>
                            <th>Payment Status</th>
                            <th>Date</th>
                            <th>Control Number</th>
                            <th>Amount (TZS)</th>
                            <th>Paid Amount (TZS)</th>
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
                                    <td class="align-middle">
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
                                    <td class="align-middle"><?= number_format($l->paid_amount ?? 0, 2) ?></td>
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
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "buttons": ["copy", "csv", "excel", "print"],
            "dom": 'lBfrtip',
            "order": [], 
            "pageLength": 20
        });
    });
</script>
<?= $this->endSection(); ?>
