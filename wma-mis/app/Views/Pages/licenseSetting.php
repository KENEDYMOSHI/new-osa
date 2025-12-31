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
                        <li class="nav-item">
                            <a class="nav-link" id="support-help-tab" data-toggle="pill" href="#support-help" role="tab" aria-controls="support-help" aria-selected="false">
                                <i class="fal fa-headset mr-2"></i>Support/Help
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
                                                                <i class="fas fa-tools mr-1"></i> Instruments
                                                            </th>
                                                            <th class="border-0 text-muted" style="padding: 15px; font-weight: 600;">
                                                                <i class="fas fa-tasks mr-1"></i> Criteria
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

                        <!-- Support/Help Tab -->
                        <div class="tab-pane fade" id="support-help" role="tabpanel" aria-labelledby="support-help-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Display View -->
                                    <div class="card shadow-sm" id="supportDisplayCard" style="border-radius: 10px; border: none;">
                                        <div class="card-header bg-white border-bottom" style="border-radius: 10px 10px 0 0;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="color: #2c3e50; font-weight: 600; font-size: 18px;">
                                                    <i class="fas fa-headset text-primary mr-2"></i>
                                                    Support & Help Contact Information
                                                </h4>
                                                <button type="button" class="btn btn-outline-primary" id="editSupportBtn" style="border-radius: 5px; padding: 6px 16px;">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                                                    <tbody>
                                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                                            <td style="padding: 20px; width: 30%; vertical-align: middle; background-color: #f8f9fa;">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-circle mr-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: #e3f2fd;">
                                                                        <i class="fas fa-building text-primary"></i>
                                                                    </div>
                                                                    <span style="font-weight: 600; color: #495057;">Organization Details</span>
                                                                </div>
                                                            </td>
                                                            <td style="padding: 20px; vertical-align: middle;">
                                                                <p id="display_address" style="color: #2c3e50; margin: 0; line-height: 1.6; white-space: pre-line;">-</p>
                                                            </td>
                                                        </tr>
                                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                                            <td style="padding: 20px; width: 30%; vertical-align: middle; background-color: #f8f9fa;">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-circle mr-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: #e8f5e9;">
                                                                        <i class="fas fa-phone text-success"></i>
                                                                    </div>
                                                                    <span style="font-weight: 600; color: #495057;">Phone Number(s)</span>
                                                                </div>
                                                            </td>
                                                            <td style="padding: 20px; vertical-align: middle;">
                                                                <p id="display_phone" style="color: #2c3e50; margin: 0; font-weight: 500; white-space: pre-line;">-</p>
                                                            </td>
                                                        </tr>
                                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                                            <td style="padding: 20px; width: 30%; vertical-align: middle; background-color: #f8f9fa;">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-circle mr-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: #e1f5fe;">
                                                                        <i class="fas fa-envelope text-info"></i>
                                                                    </div>
                                                                    <span style="font-weight: 600; color: #495057;">Email (General Inquiries)</span>
                                                                </div>
                                                            </td>
                                                            <td style="padding: 20px; vertical-align: middle;">
                                                                <p id="display_email_general" style="color: #2c3e50; margin: 0;">-</p>
                                                            </td>
                                                        </tr>
                                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                                            <td style="padding: 20px; width: 30%; vertical-align: middle; background-color: #f8f9fa;">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-circle mr-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: #fff3e0;">
                                                                        <i class="fas fa-tools text-warning"></i>
                                                                    </div>
                                                                    <span style="font-weight: 600; color: #495057;">Email (Technical Support)</span>
                                                                </div>
                                                            </td>
                                                            <td style="padding: 20px; vertical-align: middle;">
                                                                <p id="display_email_tech" style="color: #2c3e50; margin: 0;">-</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 20px; width: 30%; vertical-align: middle; background-color: #f8f9fa;">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-circle mr-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: #e3f2fd;">
                                                                        <i class="fas fa-globe text-primary"></i>
                                                                    </div>
                                                                    <span style="font-weight: 600; color: #495057;">Website</span>
                                                                </div>
                                                            </td>
                                                            <td style="padding: 20px; vertical-align: middle;">
                                                                <p id="display_website" style="color: #2c3e50; margin: 0;">-</p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light border-top-0" style="border-radius: 0 0 10px 10px;">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                WMA contact information for public inquiries and support
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Edit Form (Hidden by default) -->
                                    <div class="card shadow-sm" id="supportEditCard" style="border-radius: 10px; border: none; display: none;">
                                        <div class="card-header bg-white border-bottom" style="border-radius: 10px 10px 0 0;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="color: #2c3e50; font-weight: 600; font-size: 18px;">
                                                    <i class="fas fa-edit text-primary mr-2"></i>
                                                    Edit Support & Help Configuration
                                                </h4>
                                                <button type="button" class="btn btn-outline-secondary" id="cancelEditBtn" style="border-radius: 5px; padding: 6px 16px;">
                                                    <i class="fas fa-times mr-1"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="padding: 30px;">
                                            <form id="supportHelpForm">
                                                <!-- Hidden field to store phone data -->
                                                <input type="hidden" id="osa_phone_data" value="">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="osa_address" style="font-weight: 500; color: #2c3e50;">
                                                                <i class="fas fa-building text-primary mr-1"></i>
                                                                Organization Details (Address)
                                                            </label>
                                                            <textarea class="form-control" id="osa_address" name="address" rows="5" placeholder="Enter organization address..." style="border-radius: 8px; padding: 15px;"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <label style="font-weight: 500; color: #2c3e50; margin-bottom: 0;">
                                                                    <i class="fas fa-phone text-success mr-1"></i>
                                                                    Phone Number(s)
                                                                </label>
                                                                <button type="button" class="btn btn-sm btn-success" id="addPhoneBtn" style="border-radius: 5px;">
                                                                    <i class="fas fa-plus mr-1"></i> Add Number
                                                                </button>
                                                            </div>
                                                            <div id="phoneNumbersContainer">
                                                                <!-- Phone number fields will be added here dynamically -->
                                                            </div>
                                                            <small class="text-muted">Click "Add Number" to add more phone numbers</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="osa_email_general" style="font-weight: 500; color: #2c3e50;">
                                                                <i class="fas fa-envelope text-info mr-1"></i>
                                                                Email (General Inquiries)
                                                            </label>
                                                            <input type="email" class="form-control" id="osa_email_general" name="email_general" placeholder="e.g. info@wma.go.tz" style="border-radius: 8px; padding: 10px;">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="osa_email_tech" style="font-weight: 500; color: #2c3e50;">
                                                                <i class="fas fa-tools text-warning mr-1"></i>
                                                                Email (Technical Support)
                                                            </label>
                                                            <input type="email" class="form-control" id="osa_email_tech" name="email_tech" placeholder="e.g. ictsupport@wma.go.tz" style="border-radius: 8px; padding: 10px;">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="osa_website" style="font-weight: 500; color: #2c3e50;">
                                                                <i class="fas fa-globe text-primary mr-1"></i>
                                                                Website
                                                            </label>
                                                            <input type="text" class="form-control" id="osa_website" name="website" placeholder="e.g. www.wma.go.tz" style="border-radius: 8px; padding: 10px;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-12 text-right">
                                                        <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 8px 25px;">
                                                            <i class="fas fa-save mr-1"></i> Save Changes
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
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
                    <div class="form-group">
                        <label for="add_instrument_input" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-tools text-secondary mr-1"></i>
                            Selected Instruments
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="add_instrument_input" placeholder="Type instrument name (e.g. Weighbridge)">
                            <div class="input-group-append">
                                <button class="btn btn-info" type="button" id="btn_add_instrument">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        <div id="add_instruments_container" class="d-flex flex-wrap p-2" style="background: #f8f9fa; border-radius: 5px; min-height: 50px;">
                            <!-- Instruments will appear here -->
                            <small class="text-muted w-100 text-center mt-1">No instruments added yet.</small>
                        </div>
                        <input type="hidden" id="license_selected_instruments" name="selected_instruments">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-tasks text-secondary mr-1"></i>
                            Criteria (Instrument Quantities)
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="license_min_instruments" style="font-size: 13px; color: #666;">Minimum Required</label>
                                <input type="number" class="form-control" id="license_min_instruments" min="0" placeholder="e.g. 1" style="border-radius: 8px; padding: 10px;">
                            </div>
                            <div class="col-md-6">
                                <label for="license_max_instruments" style="font-size: 13px; color: #666;">Maximum Allowed</label>
                                <input type="number" class="form-control" id="license_max_instruments" min="0" placeholder="e.g. 5" style="border-radius: 8px; padding: 10px;">
                            </div>
                        </div>
                        <p id="add_criteria_preview" class="text-muted mt-2 mb-0" style="font-size: 13px; font-style: italic; display: none;"></p>
                        <input type="hidden" id="license_criteria" name="criteria">
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
                    <div class="form-group">
                        <label for="edit_instrument_input" style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-tools text-secondary mr-1"></i>
                            Selected Instruments
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="edit_instrument_input" placeholder="Type instrument name">
                            <div class="input-group-append">
                                <button class="btn btn-info" type="button" id="btn_edit_add_instrument">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        <div id="edit_instruments_container" class="d-flex flex-wrap p-2" style="background: #f8f9fa; border-radius: 5px; min-height: 50px;">
                            <!-- Instruments will appear here -->
                            <small class="text-muted w-100 text-center mt-1">No instruments added yet.</small>
                        </div>
                        <input type="hidden" id="edit_license_selected_instruments" name="selected_instruments">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 500; color: #2c3e50;">
                            <i class="fas fa-tasks text-secondary mr-1"></i>
                            Criteria (Instrument Quantities)
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="edit_license_min_instruments" style="font-size: 13px; color: #666;">Minimum Required</label>
                                <input type="number" class="form-control" id="edit_license_min_instruments" min="0" placeholder="e.g. 1" style="border-radius: 8px; padding: 10px;">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_license_max_instruments" style="font-size: 13px; color: #666;">Maximum Allowed</label>
                                <input type="number" class="form-control" id="edit_license_max_instruments" min="0" placeholder="e.g. 5" style="border-radius: 8px; padding: 10px;">
                            </div>
                        </div>
                        <p id="edit_criteria_preview" class="text-muted mt-2 mb-0" style="font-size: 13px; font-style: italic; display: none;"></p>
                        <input type="hidden" id="edit_license_criteria" name="criteria">
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
            let instruments = '-';
            
            // Parse instruments JSON
            try {
                if (type.selected_instruments) {
                    const parsed = JSON.parse(type.selected_instruments);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        instruments = '<ul style="padding-left: 20px; margin-bottom: 0;">' + 
                                     parsed.map(i => `<li>${i}</li>`).join('') + 
                                     '</ul>';
                    } else if (typeof type.selected_instruments === 'string') {
                         // Fallback if it's just a string but not JSON
                        instruments = type.selected_instruments;
                    }
                }
            } catch (e) {
                instruments = type.selected_instruments || '-';
            }

            let criteria = '-';
            try {
                if (type.criteria) {
                    const parsedCriteria = JSON.parse(type.criteria);
                    criteria = generateCriteriaSentence(parsedCriteria.min, parsedCriteria.max);
                }
            } catch (e) {
                criteria = type.criteria || '-';
            }
            
            // Escape single quotes for data attributes
            const safeInstruments = (type.selected_instruments || '').replace(/'/g, "&apos;");
            const safeCriteria = (type.criteria || '').replace(/'/g, "&apos;");
            
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
                    <td style="padding: 15px; vertical-align: middle; max-width: 300px;">
                        <div style="line-height: 1.6; color: #555; font-size: 14px; white-space: normal;">
                            ${description}
                        </div>
                    </td>
                    <td style="padding: 15px; vertical-align: middle; max-width: 250px;">
                        <div style="line-height: 1.6; color: #555; font-size: 14px;">
                            ${instruments}
                        </div>
                    </td>
                    <td style="padding: 15px; vertical-align: middle; max-width: 250px;">
                        <div style="line-height: 1.6; color: #555; font-size: 14px; white-space: normal;">
                            ${criteria}
                        </div>
                    </td>
                    <td style="padding: 15px; vertical-align: middle; white-space: nowrap;">
                        <span style="font-weight: 600; color: #27ae60; font-size: 13px;">
                            TZS ${parseFloat(type.fee).toLocaleString()}
                        </span>
                    </td>
                    <td style="padding: 15px; vertical-align: middle; white-space: nowrap;" class="text-center">
                        <button class="btn btn-sm btn-outline-primary mr-1 edit-license-type-btn" style="border-radius: 5px;" title="Edit" 
                            data-id="${type.id}" 
                            data-name="${type.name}" 
                            data-description="${type.description || ''}" 
                            data-fee="${type.fee}"
                            data-selected-instruments='${safeInstruments}'
                            data-criteria='${safeCriteria}'>
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
        
        const criteriaData = {
            min: $('#license_min_instruments').val(),
            max: $('#license_max_instruments').val()
        };
        
        const formData = {
            name: $('#license_type_name').val(),
            description: $('#license_description').val(),
            selected_instruments: $('#license_selected_instruments').val(),
            criteria: JSON.stringify(criteriaData),
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
        const selectedInstruments = $(this).data('selected-instruments'); 
        const criteria = $(this).data('criteria');
        
        $('#edit_license_type_id').val(id);
        $('#edit_license_type_name').val(name);
        $('#edit_license_description').val(description);
        
        // Instruments
        let parsedInstruments = [];
        if (typeof selectedInstruments === 'object') {
            parsedInstruments = selectedInstruments;
        } else if (typeof selectedInstruments === 'string') {
            try {
                parsedInstruments = JSON.parse(selectedInstruments);
            } catch(e) {
                parsedInstruments = [];
            }
        }
        renderInstrumentList('edit_instruments_container', 'edit_license_selected_instruments', parsedInstruments);
        
        // Criteria (Min/Max)
        let minQuery = '';
        let maxQuery = '';
        if (criteria) {
             try {
                // If criteria is object (via data attribute parsing) or string
                const criteriaObj = (typeof criteria === 'object') ? criteria : JSON.parse(criteria);
                minQuery = criteriaObj.min || '';
                maxQuery = criteriaObj.max || '';
            } catch (e) {
                // Fallback if criteria is plain text
                console.log('Criteria is not JSON', criteria);
            }
        }
        $('#edit_license_min_instruments').val(minQuery);
        $('#edit_license_max_instruments').val(maxQuery);
        
        // Trigger preview update
        $('#edit_license_min_instruments').trigger('input');

        $('#edit_license_fee').val(fee);
        
        $('#editLicenseTypeModal').modal('show');
    });

    // Edit License Type - Submit
    $('#editLicenseTypeForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#edit_license_type_id').val();
         const criteriaData = {
            min: $('#edit_license_min_instruments').val(),
            max: $('#edit_license_max_instruments').val()
        };

        const formData = {
            name: $('#edit_license_type_name').val(),
            description: $('#edit_license_description').val(),
            selected_instruments: $('#edit_license_selected_instruments').val(),
            criteria: JSON.stringify(criteriaData),
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


    // ==================== SUPPORT & HELP SETTINGS ====================
    const SUPPORT_DETAILS_API_URL = '<?= base_url('licenseSetting/getSupportDetails') ?>';

    // Load support details
    function loadSupportDetails() {
        $.ajax({
            url: SUPPORT_DETAILS_API_URL,
            method: 'GET',
            dataType: 'json',
            success: function(details) {
                if (details && Object.keys(details).length > 0) {
                    // Populate display fields
                    $('#display_address').text(details.address || '-');
                    
                    // Format phone numbers with labels from separate columns
                    let phoneDisplay = '';
                    let phoneCount = 0;
                    
                    for (let i = 1; i <= 3; i++) {
                        const label = details[`phone_label_${i}`];
                        const number = details[`phone_number_${i}`];
                        
                        if (number && number.trim()) {
                            if (phoneCount > 0) phoneDisplay += '\n';
                            phoneDisplay += `${label || 'Phone'}: ${number}`;
                            phoneCount++;
                        }
                    }
                    
                    $('#display_phone').text(phoneDisplay || '-');
                    $('#display_email_general').text(details.email_general || '-');
                    $('#display_email_tech').text(details.email_tech || '-');
                    $('#display_website').text(details.website || '-');
                    
                    // Store phone data for editing
                    $('#osa_phone_data').val(JSON.stringify({
                        phone_label_1: details.phone_label_1 || '',
                        phone_number_1: details.phone_number_1 || '',
                        phone_label_2: details.phone_label_2 || '',
                        phone_number_2: details.phone_number_2 || '',
                        phone_label_3: details.phone_label_3 || '',
                        phone_number_3: details.phone_number_3 || ''
                    }));
                    
                    // Populate form fields (for editing)
                    $('#osa_address').val(details.address || '');
                    $('#osa_email_general').val(details.email_general || '');
                    $('#osa_email_tech').val(details.email_tech || '');
                    $('#osa_website').val(details.website || '');
                } else {
                    // No data - show placeholders
                    $('#display_address').text('-');
                    $('#display_phone').text('-');
                    $('#display_email_general').text('-');
                    $('#display_email_tech').text('-');
                    $('#display_website').text('-');
                }
            },
            error: function(xhr) {
                console.error('Failed to load support details', xhr);
            }
        });
    }

    // Toggle between display and edit modes
    $('#editSupportBtn').on('click', function() {
        $('#supportDisplayCard').hide();
        $('#supportEditCard').show();
    });

    $('#cancelEditBtn').on('click', function() {
        $('#supportEditCard').hide();
        $('#supportDisplayCard').show();
    });

    // Load settings when tab is shown
    $('a[href="#support-help"]').on('shown.bs.tab', function() {
        loadSupportDetails();
    });

    // Initial load if tab is active (though likely not active by default)
    if ($('#support-help').hasClass('active')) {
        loadSupportDetails();
    }

    // Phone number management
    let phoneFieldCounter = 0;

    function addPhoneField(label = '', number = '') {
        phoneFieldCounter++;
        const fieldHtml = `
            <div class="phone-field-group mb-3" data-phone-id="${phoneFieldCounter}" style="border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; background-color: #f8f9fa;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label style="font-size: 12px; color: #6c757d; font-weight: 600;">Label/Title</label>
                        <input type="text" class="form-control phone-label" placeholder="e.g. Office, Mobile, Fax" value="${label}" style="border-radius: 6px; padding: 8px; font-size: 14px;">
                    </div>
                    <div class="col-md-7 mb-2">
                        <label style="font-size: 12px; color: #6c757d; font-weight: 600;">Phone Number</label>
                        <input type="text" class="form-control phone-number" placeholder="e.g. +255 (26) 22610700" value="${number}" style="border-radius: 6px; padding: 8px; font-size: 14px;">
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-phone-btn w-100" data-phone-id="${phoneFieldCounter}" style="border-radius: 6px; padding: 8px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#phoneNumbersContainer').append(fieldHtml);
    }

    // Add phone number button click
    $('#addPhoneBtn').on('click', function() {
        addPhoneField();
    });

    // Remove phone number button click (delegated event)
    $(document).on('click', '.remove-phone-btn', function() {
        const phoneId = $(this).data('phone-id');
        $(`.phone-field-group[data-phone-id="${phoneId}"]`).remove();
    });

    // Initialize with at least one phone field when edit mode is opened
    $('#editSupportBtn').on('click', function() {
        // Clear existing fields
        $('#phoneNumbersContainer').empty();
        phoneFieldCounter = 0;
        
        // Add phone fields based on loaded data
        const phoneDataStr = $('#osa_phone_data').val();
        if (phoneDataStr && phoneDataStr.trim()) {
            try {
                const phoneData = JSON.parse(phoneDataStr);
                
                // Load from separate columns format
                for (let i = 1; i <= 3; i++) {
                    const label = phoneData[`phone_label_${i}`];
                    const number = phoneData[`phone_number_${i}`];
                    
                    if (number && number.trim()) {
                        addPhoneField(label || '', number);
                    }
                }
            } catch (e) {
                console.error('Error parsing phone data:', e);
            }
        }
        
        // Always ensure at least one field exists
        if ($('.phone-field-group').length === 0) {
            addPhoneField();
        }
    });


    // ==================== CRITERIA SENTENCE GENERATOR ====================
    function numberToWords(n) {
        const words = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 
                      'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty'];
        
        const num = parseInt(n);
        if (isNaN(num)) return n;
        if (num >= 0 && num <= 20) return words[num];
        return num; // Fallback for numbers > 20
    }

    function generateCriteriaSentence(min, max) {
        if (!min && !max) return '-';
        
        const minVal = parseInt(min);
        const maxVal = parseInt(max);
        
        if (minVal && (!maxVal || minVal === maxVal)) {
            // "Select a minimum of two measuring instruments"
            return `Select a minimum of ${numberToWords(minVal)} measuring instruments`;
        } else if (minVal && maxVal && minVal !== maxVal) {
            // "Select a minimum of two and a maximum of three instruments"
            return `Select a minimum of ${numberToWords(minVal)} and a maximum of ${numberToWords(maxVal)} instruments`;
        } else if (!minVal && maxVal) {
            // Edge case: Only max?
            return `Select a maximum of ${numberToWords(maxVal)} instruments`;
        }
        
        return '-';
    }

    // Live Preview Event Listeners (Add Modal)
    $('#license_min_instruments, #license_max_instruments').on('input', function() {
        const min = $('#license_min_instruments').val();
        const max = $('#license_max_instruments').val();
        const sentence = generateCriteriaSentence(min, max);
        
        const previewEl = $('#add_criteria_preview');
        if (sentence !== '-') {
            previewEl.text(sentence).show();
        } else {
            previewEl.hide();
        }
    });

    // Live Preview Event Listeners (Edit Modal)
    $('#edit_license_min_instruments, #edit_license_max_instruments').on('input', function() {
        const min = $('#edit_license_min_instruments').val();
        const max = $('#edit_license_max_instruments').val();
        const sentence = generateCriteriaSentence(min, max);
        
        const previewEl = $('#edit_criteria_preview');
        if (sentence !== '-') {
            previewEl.text(sentence).show();
        } else {
            previewEl.hide();
        }
    });

    // Handle Support Form Submission
    $('#supportHelpForm').on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        
        // Convert form to JSON object
        // Collect all phone numbers with labels from dynamic fields
        const formData = {
            address: $('#osa_address').val(),
            email_general: $('#osa_email_general').val(),
            email_tech: $('#osa_email_tech').val(),
            website: $('#osa_website').val()
        };
        
        // Add phone data to separate columns (max 3)
        $('.phone-field-group').each(function(index) {
            if (index < 3) { // Only save first 3 phone numbers
                const fieldNum = index + 1;
                const label = $(this).find('.phone-label').val().trim();
                const number = $(this).find('.phone-number').val().trim();
                
                formData[`phone_label_${fieldNum}`] = label || null;
                formData[`phone_number_${fieldNum}`] = number || null;
            }
        });

        $.ajax({
            url: '<?= base_url('licenseSetting/saveSupportDetails') ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                swal({
                    title: 'Success!',
                    text: 'Support details updated successfully',
                    icon: 'success',
                    timer: 2000,
                    buttons: false
                });

                // Reload data and switch to display mode
                loadSupportDetails();
                $('#supportEditCard').hide();
                $('#supportDisplayCard').show();
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                let errorMessage = 'Failed to save details. Please try again.';
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
    });
    // ==================== DYNAMIC INSTRUMENT LIST ====================
    
    // Function to render the instrument list
    function renderInstrumentList(containerId, hiddenInputId, instruments) {
        const container = $(`#${containerId}`);
        container.empty();
        
        if (!instruments || instruments.length === 0) {
            container.html('<small class="text-muted w-100 text-center mt-1">No instruments added yet.</small>');
            $(`#${hiddenInputId}`).val('');
            return;
        }
        
        // Update hidden input with JSON string
        $(`#${hiddenInputId}`).val(JSON.stringify(instruments));
        
        // Render items
        instruments.forEach((instrument, index) => {
            container.append(`
                <span class="badge badge-info mr-2 mb-2 p-2" style="font-size: 14px; position: relative; padding-right: 30px !important;">
                    ${instrument}
                    <span class="instrument-remove-btn" data-index="${index}" data-container="${containerId}" data-input="${hiddenInputId}" 
                          style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer; opacity: 0.7;">
                        <i class="fas fa-times"></i>
                    </span>
                </span>
            `);
        });
    }

    // Add Instrument Helper
    function handleAddInstrument(inputId, containerId, hiddenInputId) {
        const input = $(`#${inputId}`);
        const value = input.val().trim();
        
        if (!value) return;
        
        let currentData = $(`#${hiddenInputId}`).val();
        let instruments = [];
        
        try {
            instruments = currentData ? JSON.parse(currentData) : [];
        } catch (e) {
            // Fallback for comma separated legacy data?
            instruments = currentData.split(',').map(s => s.trim()).filter(s => s);
        }
        
        if (!Array.isArray(instruments)) instruments = [];
        
        // Avoid duplicates?
        if (!instruments.includes(value)) {
            instruments.push(value);
            renderInstrumentList(containerId, hiddenInputId, instruments);
            input.val('').focus();
        } else {
            swal('Info', 'This instrument is already in the list.', 'info');
        }
    }
    
    // Event Listeners for Add Buttons
    $('#btn_add_instrument').on('click', function() {
        handleAddInstrument('add_instrument_input', 'add_instruments_container', 'license_selected_instruments');
    });
    
    $('#add_instrument_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); // Prevent form submit
            handleAddInstrument('add_instrument_input', 'add_instruments_container', 'license_selected_instruments');
        }
    });

    $('#btn_edit_add_instrument').on('click', function() {
        handleAddInstrument('edit_instrument_input', 'edit_instruments_container', 'edit_license_selected_instruments');
    });

    $('#edit_instrument_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            handleAddInstrument('edit_instrument_input', 'edit_instruments_container', 'edit_license_selected_instruments');
        }
    });

    // Remove Instrument (Delegated)
    $(document).on('click', '.instrument-remove-btn', function() {
        const index = $(this).data('index');
        const containerId = $(this).data('container');
        const hiddenInputId = $(this).data('input');
        
        let currentData = $(`#${hiddenInputId}`).val();
        let instruments = [];
        try {
            instruments = currentData ? JSON.parse(currentData) : [];
        } catch (e) {
            instruments = [];
        }
        
        if (index > -1 && index < instruments.length) {
            instruments.splice(index, 1);
            renderInstrumentList(containerId, hiddenInputId, instruments);
        }
    });
    
    // Reset Add Form
    $('#addLicenseTypeModal').on('show.bs.modal', function () {
        $('#add_instrument_input').val('');
        renderInstrumentList('add_instruments_container', 'license_selected_instruments', []);
    });
});
</script>
<?= $this->endSection(); ?>
