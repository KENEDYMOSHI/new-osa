<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?? 'License Setting' ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/osaDashboard">OSA</a></li>
                    <li class="breadcrumb-item active">License Setting</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">
    <!-- Main Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="license-setting-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="application-fee-tab" data-toggle="pill" href="#application-fee" role="tab" aria-controls="application-fee" aria-selected="true">
                                <i class="fal fa-money-bill-wave mr-2"></i>Application Type Setting
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="license-application-tab" data-toggle="pill" href="#license-application" role="tab" aria-controls="license-application" aria-selected="false">
                                <i class="fal fa-file-certificate mr-2"></i>License Application Fee Configuration
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="license-setting-tabContent">
                        <!-- Application Type Setting Tab -->
                        <div class="tab-pane fade show active" id="application-fee" role="tabpanel" aria-labelledby="application-fee-tab">
                            
                            <?php if (isset($table_error) && $table_error): ?>
                            <!-- Database Error Alert -->
                            <div class="alert alert-danger alert-dismissible fade show" style="border-left: 4px solid #dc3545;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Database Table Not Found!</h5>
                                <p class="mb-2">The <code>application_type_fees</code> table does not exist in your database.</p>
                                <p class="mb-0">
                                    <strong>Action Required:</strong> Please run the SQL script in <code>create_table.sql</code> to create the table.
                                    <br>
                                    <small class="text-muted">You can find this file in the artifacts folder or run the SQL directly in phpMyAdmin.</small>
                                </p>
                            </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Clean Modern Card -->
                                    <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                                        <div class="card-header bg-white border-bottom" style="border-radius: 10px 10px 0 0;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="color: #2c3e50; font-weight: 600; font-size: 18px;">
                                                    <i class="fas fa-list-alt text-primary mr-2"></i>
                                                    Application Type Configuration
                                                </h4>
                                                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#addFeeModal" style="border-radius: 5px; padding: 6px 16px;">
                                                    <i class="fas fa-plus mr-1"></i> Add New
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0" id="feesTable" style="border-collapse: separate; border-spacing: 0;">
                                                    <thead style="background-color: #f8f9fa;">
                                                        <tr>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">#</th>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">
                                                                <i class="fas fa-tag mr-1"></i> Application Type
                                                            </th>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">
                                                                <i class="fas fa-flag mr-1"></i> Nationality
                                                            </th>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">
                                                                <i class="fas fa-money-bill-wave mr-1"></i> Amount
                                                            </th>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">
                                                                <i class="fas fa-calendar mr-1"></i> Date Added
                                                            </th>
                                                            <th class="border-0 text-muted text-center" style="padding: 15px; font-weight: 600;">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="applicationTypeFeesTableBody">
                                                        <tr>
                                                            <td colspan="6" class="text-center" style="padding: 30px;">
                                                                <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                                                <p class="text-muted">Loading application type fees...</p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light border-top-0" style="border-radius: 0 0 10px 10px;">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Manage application types and their associated fees for different nationalities
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- License Application Setting Tab -->
                        <div class="tab-pane fade" id="license-application" role="tabpanel" aria-labelledby="license-application-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Clean Modern Card -->
                                    <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                                        <div class="card-header bg-white border-bottom" style="border-radius: 10px 10px 0 0;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="color: #2c3e50; font-weight: 600; font-size: 18px;">
                                                    <i class="fas fa-certificate text-primary mr-2"></i>
                                                    License Type Configuration
                                                </h4>
                                                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#addLicenseTypeModal" style="border-radius: 5px; padding: 6px 16px;">
                                                    <i class="fas fa-plus mr-1"></i> Add New
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0" id="licenseTypesTable" style="border-collapse: separate; border-spacing: 0;">
                                                    <thead style="background-color: #f8f9fa;">
                                                        <tr>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">#</th>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">
                                                                <i class="fas fa-tag mr-1"></i> License Type Name
                                                            </th>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">
                                                                <i class="fas fa-file-alt mr-1"></i> Description
                                                            </th>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">
                                                                <i class="fas fa-money-bill-wave mr-1"></i> License Fee
                                                            </th>
                                                            <th class="border-0 text-muted text-center" style="padding: 15px; font-weight: 600;">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="licenseTypesTableBody">
                                                        <tr>
                                                            <td colspan="5" class="text-center" style="padding: 30px;">
                                                                <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                                                <p class="text-muted">Loading license types...</p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light border-top-0" style="border-radius: 0 0 10px 10px;">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Manage license types and their associated fees
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Fee Modal -->
<div class="modal fade" id="addFeeModal" tabindex="-1" role="dialog" aria-labelledby="addFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div class="modal-header bg-white border-bottom" style="border-radius: 10px 10px 0 0;">
                <h5 class="modal-title" id="addFeeModalLabel" style="color: #2c3e50; font-weight: 600;">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Add Application Type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addFeeForm">
                <div class="modal-body" style="padding: 25px;">
                    <div class="form-group">
                        <label for="application_type" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-tag text-primary mr-1"></i>
                            Application Type <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="application_type" name="application_type" required style="border-radius: 8px; padding: 10px;">
                            <option value="">Select Application Type</option>
                            <option value="New License">New License</option>
                            <option value="Renew License">Renew License</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nationality" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-flag text-success mr-1"></i>
                            Nationality <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="nationality" name="nationality" required style="border-radius: 8px; padding: 10px;">
                            <option value="">Select Nationality</option>
                            <option value="Citizen">Citizen</option>
                            <option value="Non-Citizen">Non-Citizen</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label for="amount" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-money-bill-wave text-warning mr-1"></i>
                            Amount (TZS) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="border-radius: 8px 0 0 8px; background-color: #f8f9fa;">TZS</span>
                            </div>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required min="0" style="border-radius: 0 8px 8px 0; padding: 10px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top" style="border-radius: 0 0 10px 10px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px;">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 20px;">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Fee Modal -->
<div class="modal fade" id="editFeeModal" tabindex="-1" role="dialog" aria-labelledby="editFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div class="modal-header bg-white border-bottom" style="border-radius: 10px 10px 0 0;">
                <h5 class="modal-title" id="editFeeModalLabel" style="color: #2c3e50; font-weight: 600;">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Application Type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editFeeForm">
                <input type="hidden" id="edit_fee_id" name="fee_id">
                <div class="modal-body" style="padding: 25px;">
                    <div class="form-group">
                        <label for="edit_application_type" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-tag text-primary mr-1"></i>
                            Application Type <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="edit_application_type" name="application_type" required style="border-radius: 8px; padding: 10px;">
                            <option value="">Select Application Type</option>
                            <option value="New License">New License</option>
                            <option value="Renew License">Renew License</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_nationality" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-flag text-success mr-1"></i>
                            Nationality <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="edit_nationality" name="nationality" required style="border-radius: 8px; padding: 10px;">
                            <option value="">Select Nationality</option>
                            <option value="Citizen">Citizen</option>
                            <option value="Non-Citizen">Non-Citizen</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label for="edit_amount" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-money-bill-wave text-warning mr-1"></i>
                            Amount (TZS) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="border-radius: 8px 0 0 8px; background-color: #f8f9fa;">TZS</span>
                            </div>
                            <input type="number" class="form-control" id="edit_amount" name="amount" placeholder="Enter amount" required min="0" style="border-radius: 0 8px 8px 0; padding: 10px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top" style="border-radius: 0 0 10px 10px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px;">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 20px;">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add License Type Modal -->
<div class="modal fade" id="addLicenseTypeModal" tabindex="-1" role="dialog" aria-labelledby="addLicenseTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div class="modal-header bg-white border-bottom" style="border-radius: 10px 10px 0 0;">
                <h5 class="modal-title" id="addLicenseTypeModalLabel" style="color: #2c3e50; font-weight: 600;">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Add License Type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addLicenseTypeForm">
                <div class="modal-body" style="padding: 25px;">
                    <div class="form-group">
                        <label for="license_type_name" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-tag text-primary mr-1"></i>
                            License Type Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="license_type_name" name="name" placeholder="e.g., Weighbridge Operator" required style="border-radius: 8px; padding: 10px;">
                    </div>
                    <div class="form-group">
                        <label for="license_description" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-file-alt text-info mr-1"></i>
                            Description
                        </label>
                        <textarea class="form-control" id="license_description" name="description" rows="3" placeholder="Enter license type description..." style="border-radius: 8px; padding: 10px;"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label for="license_fee" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-money-bill-wave text-warning mr-1"></i>
                            License Fee (TZS) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="border-radius: 8px 0 0 8px; background-color: #f8f9fa;">TZS</span>
                            </div>
                            <input type="number" class="form-control" id="license_fee" name="fee" placeholder="Enter fee amount" required min="0" style="border-radius: 0 8px 8px 0; padding: 10px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top" style="border-radius: 0 0 10px 10px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px;">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 20px;">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit License Type Modal -->
<div class="modal fade" id="editLicenseTypeModal" tabindex="-1" role="dialog" aria-labelledby="editLicenseTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div class="modal-header bg-white border-bottom" style="border-radius: 10px 10px 0 0;">
                <h5 class="modal-title" id="editLicenseTypeModalLabel" style="color: #2c3e50; font-weight: 600;">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit License Type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editLicenseTypeForm">
                <input type="hidden" id="edit_license_type_id" name="id">
                <div class="modal-body" style="padding: 25px;">
                    <div class="form-group">
                        <label for="edit_license_type_name" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-tag text-primary mr-1"></i>
                            License Type Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="edit_license_type_name" name="name" placeholder="e.g., Weighbridge Operator" required style="border-radius: 8px; padding: 10px;">
                    </div>
                    <div class="form-group">
                        <label for="edit_license_description" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-file-alt text-info mr-1"></i>
                            Description
                        </label>
                        <textarea class="form-control" id="edit_license_description" name="description" rows="3" placeholder="Enter license type description..." style="border-radius: 8px; padding: 10px;"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label for="edit_license_fee" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-money-bill-wave text-warning mr-1"></i>
                            License Fee (TZS) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="border-radius: 8px 0 0 8px; background-color: #f8f9fa;">TZS</span>
                            </div>
                            <input type="number" class="form-control" id="edit_license_fee" name="fee" placeholder="Enter fee amount" required min="0" style="border-radius: 0 8px 8px 0; padding: 10px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top" style="border-radius: 0 0 10px 10px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px;">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 20px;">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // ==================== APPLICATION TYPE FEES MANAGEMENT ====================
    const APP_FEE_API_URL = '<?= base_url('licenseSetting/getFees') ?>';

    // Load application type fees on page load
    function loadApplicationTypeFees() {
        $.ajax({
            url: APP_FEE_API_URL,
            method: 'GET',
            dataType: 'json',
            success: function(fees) {
                renderApplicationTypeFeesTable(fees);
            },
            error: function(xhr) {
                $('#applicationTypeFeesTableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 30px;">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                            <p class="text-danger">Failed to load application type fees. Please check if the OSA application is running.</p>
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Render application type fees table
    function renderApplicationTypeFeesTable(fees) {
        const tbody = $('#applicationTypeFeesTableBody');
        
        if (fees.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px;">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No application types configured yet. Click "Add New" to get started.</p>
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        fees.forEach((fee, index) => {
            const iconColor = fee.application_type === 'New License' ? '#2196F3' : '#FF9800';
            const iconBg = fee.application_type === 'New License' ? '#e3f2fd' : '#fff3e0';
            const badgeBg = fee.nationality === 'Citizen' ? '#d4edda' : '#fff3cd';
            const badgeColor = fee.nationality === 'Citizen' ? '#155724' : '#856404';
            const amountColor = fee.nationality === 'Citizen' ? '#27ae60' : '#f39c12';
            
            html += `
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px; vertical-align: middle;">
                        <span class="badge badge-light" style="font-size: 14px;">${index + 1}</span>
                    </td>
                    <td style="padding: 15px; vertical-align: middle;">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle mr-2" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: ${iconBg};">
                                <i class="fas fa-certificate" style="color: ${iconColor};"></i>
                            </div>
                            <span style="font-weight: 500; color: #2c3e50;">${fee.application_type}</span>
                        </div>
                    </td>
                    <td style="padding: 15px; vertical-align: middle;">
                        <span class="badge" style="background-color: ${badgeBg}; color: ${badgeColor}; padding: 6px 12px; border-radius: 12px;">
                            ${fee.nationality}
                        </span>
                    </td>
                    <td style="padding: 15px; vertical-align: middle;">
                        <span style="font-weight: 600; color: ${amountColor}; font-size: 15px;">
                            TZS ${parseFloat(fee.amount).toLocaleString()}
                        </span>
                    </td>
                    <td style="padding: 15px; vertical-align: middle; color: #7f8c8d;">
                        ${new Date(fee.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}
                    </td>
                    <td style="padding: 15px; vertical-align: middle;" class="text-center">
                        <button class="btn btn-sm btn-outline-primary mr-1 edit-fee-btn" style="border-radius: 5px;" title="Edit" data-id="${fee.id}" data-type="${fee.application_type}" data-nationality="${fee.nationality}" data-amount="${fee.amount}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-fee-btn" style="border-radius: 5px;" title="Delete" data-id="${fee.id}" data-type="${fee.application_type}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.html(html);
    }

    // Load fees on page load
    loadApplicationTypeFees();

    // Handle Add Fee Form Submission
    $('#addFeeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        var formData = {
            application_type: $('#application_type').val(),
            nationality: $('#nationality').val(),
            amount: parseFloat($('#amount').val())
        };
        
        // Disable submit button to prevent double submission
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        
        $.ajax({
            url: '<?= base_url('licenseSetting/addFee') ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                swal({
                    title: 'Success!',
                    text: 'Fee added successfully',
                    icon: 'success',
                    timer: 2000,
                    buttons: false
                });
                $('#addFeeModal').modal('hide');
                $('#addFeeForm')[0].reset();
                loadApplicationTypeFees();
            },
            error: function(xhr) {
                // Re-enable submit button
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                var errorMessage = 'Failed to add fee. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.messages) {
                    errorMessage = Object.values(xhr.responseJSON.messages).join(', ');
                }
                
                swal({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error'
                });
            }
        });
    });


    // Edit fee functionality
    $(document).on('click', '.edit-fee-btn', function() {
        const feeId = $(this).data('id');
        const applicationType = $(this).data('type');
        const nationality = $(this).data('nationality');
        const amount = $(this).data('amount');
        
        // Populate the edit form
        $('#edit_fee_id').val(feeId);
        $('#edit_application_type').val(applicationType);
        $('#edit_nationality').val(nationality);
        $('#edit_amount').val(amount);
        
        // Show the modal
        $('#editFeeModal').modal('show');
    });

    // Handle Edit Fee Form Submission
    $('#editFeeForm').on('submit', function(e) {
        e.preventDefault();
        
        const feeId = $('#edit_fee_id').val();
        const formData = {
            application_type: $('#edit_application_type').val(),
            nationality: $('#edit_nationality').val(),
            amount: parseFloat($('#edit_amount').val())
        };
        
        // Disable submit button
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');
        
        $.ajax({
            url: '<?= base_url('licenseSetting/updateFee') ?>/' + feeId,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                swal({
                    title: 'Success!',
                    text: 'Fee updated successfully',
                    icon: 'success',
                    timer: 2000,
                    buttons: false
                });
                $('#editFeeModal').modal('hide');
                loadApplicationTypeFees();
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                let errorMessage = 'Failed to update fee. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.messages) {
                    errorMessage = Object.values(xhr.responseJSON.messages).join(', ');
                }
                
                swal({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error'
                });
            }
        });
    });


    // Delete fee functionality
    $(document).on('click', '.delete-fee-btn', function() {
        const feeId = $(this).data('id');
        
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    value: null,
                    visible: true,
                    className: 'btn btn-secondary',
                    closeModal: true
                },
                confirm: {
                    text: 'Yes, delete it!',
                    value: true,
                    visible: true,
                    className: 'btn btn-danger',
                    closeModal: true
                }
            },
            dangerMode: true
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '<?= base_url('licenseSetting/deleteFee') ?>/' + feeId,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        swal({
                            title: 'Deleted!',
                            text: 'Fee has been deleted.',
                            icon: 'success',
                            timer: 2000,
                            buttons: false
                        });
                        loadApplicationTypeFees();
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to delete fee. Please try again.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        swal({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Handle Application Fee Form Submission
    $('#application-fee-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('licenseSetting/updateApplicationFee') ?>',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    swal({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update application fee settings'
                });
            }
        });
    });

    // Handle License Application Form Submission
    $('#license-application-form').on('submit', function(e) {
        e.preventDefault();
        
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        
        $.ajax({
            url: '<?= base_url('licenseSetting/updateLicenseApplication') ?>',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                if (response.status === 'success') {
                    swal({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'License application settings updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                var errorMessage = 'Failed to update license application settings. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 0) {
                    errorMessage = 'Unable to connect to the server. Please check your internet connection.';
                }
                
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    });

    // ==================== LICENSE TYPE MANAGEMENT ====================
    const OSA_API_URL = '<?= base_url('licenseSetting/getLicenseTypes') ?>';

    // Load license types on page load
    function loadLicenseTypes() {
        $.ajax({
            url: OSA_API_URL,
            method: 'GET',
            dataType: 'json',
            success: function(licenseTypes) {
                renderLicenseTypesTable(licenseTypes);
            },
            error: function(xhr) {
                $('#licenseTypesTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 30px;">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                            <p class="text-danger">Failed to load license types. Please check if the OSA application is running.</p>
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Render license types table
    function renderLicenseTypesTable(licenseTypes) {
        const tbody = $('#licenseTypesTableBody');
        
        if (licenseTypes.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="5" class="text-center" style="padding: 30px;">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No license types configured yet. Click "Add New" to get started.</p>
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        licenseTypes.forEach((type, index) => {
            const description = type.description || 'No description';
            
            html += `
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px; vertical-align: middle;">
                        <span class="badge badge-light" style="font-size: 14px;">${index + 1}</span>
                    </td>
                    <td style="padding: 15px; vertical-align: middle;">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle mr-2" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: #e3f2fd;">
                                <i class="fas fa-certificate text-primary"></i>
                            </div>
                            <span style="font-weight: 500; color: #2c3e50;">${type.name}</span>
                        </div>
                    </td>
                    <td style="padding: 15px; vertical-align: middle; max-width: 400px;">
                        <div style="line-height: 1.6; color: #555; font-size: 14px;">
                            ${description}
                        </div>
                    </td>
                    <td style="padding: 15px; vertical-align: middle;">
                        <span style="font-weight: 600; color: #27ae60; font-size: 15px;">
                            TZS ${parseFloat(type.fee).toLocaleString()}
                        </span>
                    </td>
                    <td style="padding: 15px; vertical-align: middle;" class="text-center">
                        <button class="btn btn-sm btn-outline-primary mr-1 edit-license-type-btn" style="border-radius: 5px;" title="Edit" data-id="${type.id}" data-name="${type.name}" data-description="${type.description || ''}" data-fee="${type.fee}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-license-type-btn" style="border-radius: 5px;" title="Delete" data-id="${type.id}" data-name="${type.name}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.html(html);
    }

    // Add License Type
    $('#addLicenseTypeForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#license_type_name').val(),
            description: $('#license_description').val(),
            fee: parseFloat($('#license_fee').val())
        };
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        
        $.ajax({
            url: '<?= base_url('licenseSetting/addLicenseType') ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                swal({
                    title: 'Success!',
                    text: 'License type added successfully',
                    icon: 'success',
                    timer: 2000,
                    buttons: false
                });
                $('#addLicenseTypeModal').modal('hide');
                $('#addLicenseTypeForm')[0].reset();
                loadLicenseTypes();
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                let errorMessage = 'Failed to add license type. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.messages) {
                    errorMessage = Object.values(xhr.responseJSON.messages).join(', ');
                }
                
                swal({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error'
                });
            }
        });
    });

    // Edit License Type - Open Modal
    $(document).on('click', '.edit-license-type-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const description = $(this).data('description');
        const fee = $(this).data('fee');
        
        $('#edit_license_type_id').val(id);
        $('#edit_license_type_name').val(name);
        $('#edit_license_description').val(description);
        $('#edit_license_fee').val(fee);
        
        $('#editLicenseTypeModal').modal('show');
    });

    // Edit License Type - Submit
    $('#editLicenseTypeForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#edit_license_type_id').val();
        const formData = {
            name: $('#edit_license_type_name').val(),
            description: $('#edit_license_description').val(),
            fee: parseFloat($('#edit_license_fee').val())
        };
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');
        
        $.ajax({
            url: '<?= base_url('licenseSetting/updateLicenseType') ?>/' + id,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                swal({
                    title: 'Success!',
                    text: 'License type updated successfully',
                    icon: 'success',
                    timer: 2000,
                    buttons: false
                });
                $('#editLicenseTypeModal').modal('hide');
                loadLicenseTypes();
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                let errorMessage = 'Failed to update license type. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.messages) {
                    errorMessage = Object.values(xhr.responseJSON.messages).join(', ');
                }
                
                swal({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error'
                });
            }
        });
    });

    // Delete License Type
    $(document).on('click', '.delete-license-type-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        swal({
            title: 'Are you sure?',
            text: `You are about to delete "${name}". This action cannot be undone!`,
            icon: 'warning',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    value: null,
                    visible: true,
                    className: 'btn btn-secondary',
                    closeModal: true
                },
                confirm: {
                    text: 'Yes, delete it!',
                    value: true,
                    visible: true,
                    className: 'btn btn-danger',
                    closeModal: true
                }
            },
            dangerMode: true
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '<?= base_url('licenseSetting/deleteLicenseType') ?>/' + id,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        swal({
                            title: 'Deleted!',
                            text: 'License type has been deleted.',
                            icon: 'success',
                            timer: 2000,
                            buttons: false
                        });
                        loadLicenseTypes();
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to delete license type. Please try again.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        swal({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Load license types when tab is shown
    $('a[href="#license-application"]').on('shown.bs.tab', function() {
        loadLicenseTypes();
    });

    // Load on page load if tab is active
    if ($('#license-application').hasClass('active')) {
        loadLicenseTypes();
    }
});
</script>
<?= $this->endSection(); ?>
