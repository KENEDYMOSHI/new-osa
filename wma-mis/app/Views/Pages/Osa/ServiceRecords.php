<?= $this->extend('Layouts/coreLayout') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark" style="font-weight: 700; color: #343a40;">Technicians’ Registry</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('osaDashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Technicians’ Registry</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <?php if (!empty($technicianProfile)): ?>
        <!-- Technician Profile Section (Only visible if a specific technician is selected/found) -->
        <div class="card mb-4" style="border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none;">
            <div class="card-header" style="background-color: #fff9f0; border-bottom: 2px solid #f39c12; border-radius: 8px 8px 0 0;">
                <h3 class="card-title" style="font-weight: 700; color: #333; margin: 0;"><i class="fas fa-id-card mr-2" style="color: #f39c12;"></i>Technician Profile</h3>
            </div>
            <div class="card-body" style="background-color: #fffcf7;">
                <div class="row">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <small class="text-muted font-weight-bold" style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Technician Name</small>
                        <h5 class="font-weight-bold mt-1" style="color: #2c3e50; font-family: 'Segoe UI', sans-serif;"><?= esc($technicianProfile['name'] ?? 'N/A') ?></h5>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <small class="text-muted font-weight-bold" style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Phone Number</small>
                        <h5 class="font-weight-bold mt-1" style="color: #2c3e50; font-family: 'Segoe UI', sans-serif;"><?= esc($technicianProfile['phone'] ?? 'N/A') ?></h5>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <small class="text-muted font-weight-bold" style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Company Name</small>
                        <h5 class="font-weight-bold mt-1" style="color: #2c3e50; font-family: 'Segoe UI', sans-serif;"><?= esc($technicianProfile['company'] ?? 'N/A') ?></h5>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted font-weight-bold" style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Seal Number</small>
                        <h5 class="font-weight-bold mt-1" style="color: #2c3e50; font-family: 'Segoe UI', sans-serif;"><?= esc($technicianProfile['seal_number'] ?? 'N/A') ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filter Registry Section -->
        <div class="card mb-4" style="border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e0e0e0;">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #e9ecef;">
                <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.95rem;"><i class="fas fa-filter mr-2" style="color: #495057;"></i>Filter Service Records</h6>
            </div>
            <div class="card-body bg-white px-4 py-4">
                <form method="GET" action="<?= base_url('serviceRecords') ?>">
                    <div class="row">
                        <!-- Technician Name -->
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-600 mb-2" style="color: #495057; font-size: 0.875rem;">Technician Name</label>
                            <input type="text" name="technician_name" class="form-control" placeholder="Technician Name" value="<?= $filters['technician_name'] ?? '' ?>" style="border-color: #ced4da; font-size: 0.875rem; height: 38px;">
                        </div>

                        <!-- Customer Name -->
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-600 mb-2" style="color: #495057; font-size: 0.875rem;">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Customer Name" value="<?= $filters['customer_name'] ?? '' ?>" style="border-color: #ced4da; font-size: 0.875rem; height: 38px;">
                        </div>

                        <!-- License Number -->
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-600 mb-2" style="color: #495057; font-size: 0.875rem;">License Number</label>
                            <input type="text" name="license_number" class="form-control" placeholder="License No." value="<?= $filters['license_number'] ?? '' ?>" style="border-color: #ced4da; font-size: 0.875rem; height: 38px;">
                        </div>

                        <!-- Service Date -->
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-600 mb-2" style="color: #495057; font-size: 0.875rem;">Service Date</label>
                            <input type="date" name="service_date" class="form-control" placeholder="dd/mm/yyyy" value="<?= $filters['service_date'] ?? '' ?>" style="border-color: #ced4da; font-size: 0.875rem; height: 38px;">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Year -->
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-600 mb-2" style="color: #495057; font-size: 0.875rem;">Year</label>
                            <select name="year" class="form-control" style="border-color: #ced4da; font-size: 0.875rem; height: 38px; color: #6c757d;">
                                <option value="all">All Years</option>
                                <?php foreach ($availableYears as $year): ?>
                                    <option value="<?= $year ?>" <?= ($filters['year'] == $year) ? 'selected' : '' ?>><?= $year ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-600 mb-2" style="color: #495057; font-size: 0.875rem;">Date Range</label>
                            <input type="text" name="date_range" class="form-control" id="reservation" placeholder="YYYY-MM-DD - YYYY-MM-DD" value="<?= $filters['date_range'] ?? '' ?>" style="border-color: #ced4da; font-size: 0.875rem; height: 38px; color: #6c757d;">
                            <input type="hidden" name="date_from" value="<?= $filters['date_from'] ?? '' ?>">
                            <input type="hidden" name="date_to" value="<?= $filters['date_to'] ?? '' ?>">
                        </div>

                        <!-- Region -->
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-600 mb-2" style="color: #495057; font-size: 0.875rem;">Region</label>
                            <select name="region" class="form-control" style="border-color: #ced4da; font-size: 0.875rem; height: 38px; color: #6c757d;">
                                <option value="all">All Regions</option>
                                <option value="Dar es Salaam" <?= ($filters['region'] ?? '') == 'Dar es Salaam' ? 'selected' : '' ?>>Dar es Salaam</option>
                                <option value="Arusha" <?= ($filters['region'] ?? '') == 'Arusha' ? 'selected' : '' ?>>Arusha</option>
                                <option value="Mwanza" <?= ($filters['region'] ?? '') == 'Mwanza' ? 'selected' : '' ?>>Mwanza</option>
                                <option value="Dodoma" <?= ($filters['region'] ?? '') == 'Dodoma' ? 'selected' : '' ?>>Dodoma</option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-600 mb-2" style="color: transparent; font-size: 0.875rem;">Actions</label>
                            <div class="d-flex" style="gap: 8px;">
                                <button type="submit" class="btn btn-success flex-fill" style="height: 38px; font-weight: 600; font-size: 0.875rem; border-radius: 4px;">
                                    <i class="fas fa-search mr-1"></i> Filter
                                </button>
                                <a href="<?= base_url('serviceRecords') ?>" class="btn btn-outline-secondary" style="height: 38px; width: 38px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 4px;" title="Reset">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Service Records Table -->
        <div class="card" style="border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.08); border: 1px solid #dee2e6;">
            <div class="card-header d-flex justify-content-between align-items-center py-3" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0 mr-4" style="font-weight: 600; font-size: 1rem; color: #495057;"><i class="fas fa-clipboard-list mr-2"></i>Service Records</h6>
                    <span class="text-muted mr-2" style="font-size: 0.875rem;">Show</span>
                    <select class="form-control form-control-sm" style="width: 70px; font-size: 0.875rem;">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    <span class="text-muted ml-2" style="font-size: 0.875rem;">entries</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-muted mr-2" style="font-size: 0.875rem;">Search:</span>
                    <input type="text" class="form-control form-control-sm mr-3" placeholder="" style="width: 200px; font-size: 0.875rem;">
                    <button class="btn btn-outline-secondary btn-sm" style="font-size: 0.875rem; margin-left: 5px;"><i class="fas fa-file-pdf mr-1"></i>PDF</button>
                    <button class="btn btn-outline-secondary btn-sm" style="font-size: 0.875rem; margin-left: 5px;"><i class="fas fa-file-excel mr-1"></i>Excel</button>
                    <button class="btn btn-outline-secondary btn-sm" style="font-size: 0.875rem; margin-left: 5px;"><i class="fas fa-file-csv mr-1"></i>CSV</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                        <thead style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                            <tr>
                                <th style="font-weight: 600; color: #495057; padding: 12px; border-top: none; border-right: 1px solid #dee2e6; white-space: nowrap;"><i class="far fa-calendar-alt mr-1 text-muted"></i>Service Date</th>
                                <th style="font-weight: 600; color: #495057; padding: 12px; border-top: none; border-right: 1px solid #dee2e6; white-space: nowrap;"><i class="far fa-user mr-1 text-muted"></i>Technician</th>
                                <th style="font-weight: 600; color: #495057; padding: 12px; border-top: none; border-right: 1px solid #dee2e6; white-space: nowrap;"><i class="far fa-building mr-1 text-muted"></i>Customer Name</th>
                                <th style="font-weight: 600; color: #495057; padding: 12px; border-top: none; border-right: 1px solid #dee2e6; white-space: nowrap;"><i class="fas fa-map-marker-alt mr-1 text-muted"></i>Region</th>
                                <th style="font-weight: 600; color: #495057; padding: 12px; border-top: none; border-right: 1px solid #dee2e6; white-space: nowrap;"><i class="fas fa-tools mr-1 text-muted"></i>Instrument</th>
                                <th style="font-weight: 600; color: #495057; padding: 12px; border-top: none; border-right: 1px solid #dee2e6; white-space: nowrap;"><i class="fas fa-tag mr-1 text-muted"></i>Sticker Number</th>
                                <th style="font-weight: 600; color: #495057; padding: 12px; border-top: none; border-right: 1px solid #dee2e6; min-width: 180px;"><i class="fas fa-exclamation-triangle mr-1 text-muted"></i>Issue/Problem</th>
                                <th style="font-weight: 600; color: #495057; padding: 12px; border-top: none; min-width: 200px;"><i class="fas fa-wrench mr-1 text-muted"></i>Work Performed</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($serviceRecords)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5" style="background-color: #f8f9fa;">
                                        <i class="fas fa-inbox fa-4x mb-3" style="color: #dee2e6;"></i><br>
                                        <span style="color: #6c757d; font-size: 1rem;">No service records found.</span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($serviceRecords as $record): ?>
                                    <tr style="border-bottom: 1px solid #f0f0f0;">
                                        <td style="vertical-align: middle; padding: 12px; border-right: 1px solid #dee2e6; white-space: nowrap;">
                                            <span style="color: #495057; font-weight: 500;">
                                                <?= date('d M Y', strtotime($record->service_date)) ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle; padding: 12px; border-right: 1px solid #dee2e6; font-weight: 600; color: #495057;">
                                            <?= esc($record->technician_name ?? 'N/A') ?>
                                        </td>
                                        <td style="vertical-align: middle; padding: 12px; border-right: 1px solid #dee2e6; font-weight: 500; color: #333;"><?= esc($record->customer_name) ?></td>
                                        <td style="vertical-align: middle; padding: 12px; border-right: 1px solid #dee2e6;">
                                            <span class="badge badge-light" style="border: 1px solid #dee2e6; color: #495057; padding: 5px 10px; font-weight: 500;">
                                                <?= esc($record->region ?? '-') ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle; padding: 12px; border-right: 1px solid #dee2e6;">
                                            <span class="badge badge-light" style="border: 1px solid #dee2e6; color: #495057; padding: 5px 10px; font-weight: 500;">
                                                <?= esc($record->instrument ?? '-') ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle; padding: 12px; border-right: 1px solid #dee2e6; font-family: 'Courier New', monospace; font-weight: 600; color: #6c757d; font-size: 0.9rem;">
                                            <?= esc($record->sticker_number ?? '-') ?>
                                        </td>
                                        <td style="vertical-align: middle; padding: 12px; border-right: 1px solid #dee2e6; color: #6c757d; line-height: 1.5;"><?= esc($record->issue_problem ?? '-') ?></td>
                                        <td style="vertical-align: middle; padding: 12px; color: #495057; line-height: 1.5;"><?= esc($record->work_performed) ?></td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Date Range Picker Script -->
<script>
$(function() {
    $('#reservation').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    });

    $('#reservation').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $('input[name="date_from"]').val(picker.startDate.format('YYYY-MM-DD'));
        $('input[name="date_to"]').val(picker.endDate.format('YYYY-MM-DD'));
    });

    $('#reservation').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('input[name="date_from"]').val('');
        $('input[name="date_to"]').val('');
    });
});
</script>

<?= $this->endSection() ?>
