<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <style>
        .nav-pills .nav-link {
            font-size: 0.85rem;
            padding: 0.4rem 0.6rem;
            white-space: nowrap;
        }
    </style>
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('initialApplicationApproval') ?>">Applications</a></li>
                    <li class="breadcrumb-item active">Application Details</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Left Column: Profile Card & Actions -->
        <div class="col-md-3">

            <!-- Profile Image & Name -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="img-circle elevation-2 d-flex justify-content-center align-items-center bg-light mx-auto" style="width: 100px; height: 100px; font-size: 40px; color: #ccc;">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <h3 class="profile-username text-center mt-3"><?= $application->first_name ?? 'N/A' ?> <?= $application->last_name ?? '' ?></h3>
                    <p class="text-muted text-center">
                        <?php 
                        if (!empty($application->license_items) && is_array($application->license_items)) {
                            echo $application->license_items[0]->type ?? 'License Application';
                        } else {
                            echo 'License Application';
                        }
                        ?>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Citizenship Status</b> <a class="float-right"><?= $application->nationality ?? 'N/A' ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Application Type</b> <a class="float-right"><?= $application->application_type ?? 'New' ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Control #</b> <a class="float-right"><?= $application->control_number ?? 'N/A' ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b> 
                            <a class="float-right">
                                <span class="badge badge-<?= ($application->application_status ?? '') == 'Approved' ? 'success' : 'warning' ?>">
                                    <?= $application->application_status ?? 'Pending' ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body p-3">
                    <button class="btn btn-success btn-block mb-2">
                        <i class="fas fa-check mr-1"></i> Approve
                    </button>
                    <button class="btn btn-danger btn-block mb-2">
                        <i class="fas fa-times mr-1"></i> Reject
                    </button>
                    <button class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-undo mr-1"></i> Return for Revision
                    </button>
                    <button class="btn btn-info btn-block">
                        <i class="fas fa-print mr-1"></i> Print Application
                    </button>
                </div>
            </div>

            <!-- Back Button -->
             <a href="<?= base_url('initialApplicationApproval') ?>" class="btn btn-secondary btn-block mb-3">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>

        </div>
        <!-- /.col -->

        <!-- Right Column: Tabs -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#personal" data-toggle="tab"><i class="fas fa-user mr-1"></i> Personal Information</a></li>
                        <li class="nav-item"><a class="nav-link" href="#business" data-toggle="tab"><i class="fas fa-building mr-1"></i> Company Information</a></li>
                        <li class="nav-item"><a class="nav-link" href="#attachments" data-toggle="tab"><i class="fas fa-file-alt mr-1"></i> Required Attachments</a></li>
                        <li class="nav-item"><a class="nav-link" href="#qualifications" data-toggle="tab"><i class="fas fa-graduation-cap mr-1"></i> Qualification Documents</a></li>
                        <li class="nav-item"><a class="nav-link" href="#license" data-toggle="tab"><i class="fas fa-id-card mr-1"></i> License</a></li>
                         <li class="nav-item"><a class="nav-link" href="#approvals" data-toggle="tab"><i class="fas fa-tasks mr-1"></i> Approvals</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        
                        <!-- Personal Info -->
                        <div class="active tab-pane" id="personal">
                            <h5 class="mb-3 text-primary"><i class="fas fa-user-circle mr-2"></i>Personal Information</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Full Name</label>
                                        <span class="h6"><?= $application->first_name ?? 'N/A' ?> <?= $application->middle_name ?? '' ?> <?= $application->last_name ?? '' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Gender</label>
                                        <span class="h6"><?= $application->gender ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Date of Birth</label>
                                        <span class="h6"><?= !empty($application->dob) ? date('d M Y', strtotime($application->dob)) : 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">
                                            <?php 
                                            // Show NIDA for Tanzanian citizens, Passport for others
                                            if (isset($application->nationality) && stripos($application->nationality, 'Tanzania') !== false) {
                                                echo 'NIDA Number';
                                            } else {
                                                echo 'Passport Number';
                                            }
                                            ?>
                                        </label>
                                        <span class="h6"><?= $application->identity_number ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Phone Number</label>
                                        <span class="h6"><?= $application->phone_number ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Email Address</label>
                                        <span class="h6"><?= $application->email ?? 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="mb-3 mt-4 text-primary"><i class="fas fa-map-marker-alt mr-2"></i>Physical Address</h5>
                             <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Region</label>
                                        <span class="h6"><?= $application->region ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">District</label>
                                        <span class="h6"><?= $application->district ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Ward</label>
                                        <span class="h6"><?= $application->ward ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Street / Village</label>
                                        <span class="h6"><?= $application->street ?? 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <!-- Business Info -->
                        <div class="tab-pane" id="business">
                             <h5 class="mb-3 text-primary"><i class="fas fa-building mr-2"></i>Business Details</h5>
                             <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Company Name</label>
                                        <span class="h6"><?= $application->company_name ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">TIN Number</label>
                                        <span class="h6"><?= $application->tin_number ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">BRELA Registration</label>
                                        <span class="h6"><?= $application->registration_number ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Company Phone</label>
                                        <span class="h6"><?= $application->company_phone ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Company Email</label>
                                        <span class="h6"><?= $application->company_email ?? 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="mb-3 mt-4 text-primary"><i class="fas fa-map mr-2"></i>Business Location</h5>
                             <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Region</label>
                                        <span class="h6"><?= $application->business_region ?? $application->region ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">District</label>
                                        <span class="h6"><?= $application->business_district ?? $application->district ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Postal Address / Physical Location</label>
                                        <span class="h6"><?= $application->business_address ?? $application->postal_address ?? 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <!-- Attachments -->
                        <div class="tab-pane" id="attachments">
                            <h5 class="mb-3 text-primary">Required Attachments</h5>
                            <div class="row">
                                <?php if (!empty($application->attachments)): ?>
                    <?php foreach ($application->attachments as $doc): ?>
                        <?php if (!isset($doc->category) || $doc->category != 'qualification'): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card bg-light">
                                <div class="card-body pt-2 pb-2">
                                    <div class="text-right">
                                         <span class="badge badge-success" style="font-size: 0.7rem;">UPLOADED</span>
                                    </div>
                                    <h6 class="text-truncate mb-2" title="<?= $doc->document_name ?? $doc->file_name ?? 'Document' ?>">
                                        <i class="fas fa-file-pdf text-danger mr-1"></i>
                                        <?= $doc->document_name ?? $doc->file_name ?? 'Document' ?>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-tag mr-1"></i> <?= $doc->document_type ?? $doc->type ?? 'Document' ?>
                                    </p>
                                    <div class="text-center">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye mr-1"></i> View Document
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center text-muted py-4">No attachments available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        
                        <!-- Qualifications -->
                        <div class="tab-pane" id="qualifications">
                             <h5 class="mb-3 text-primary">Qualification Documents</h5>
                             <div class="row">
                                <?php if (!empty($application->attachments)): ?>
                                    <?php foreach ($application->attachments as $doc): ?>
                                        <?php if (isset($doc->category) && $doc->category == 'qualification'): ?>
                                        <div class="col-md-6 col-lg-4">
                                             <div class="card bg-light">
                                                <div class="card-body pt-2 pb-2">
                                                    <div class="text-right">
                                                         <span class="badge badge-info" style="font-size: 0.7rem;">QUALIFICATION</span>
                                                    </div>
                                                    <h6 class="text-truncate mb-2" title="<?= $doc->document_name ?? $doc->file_name ?? 'Document' ?>">
                                                        <i class="fas fa-file-certificate text-warning mr-1"></i>
                                                        <?= $doc->document_name ?? $doc->file_name ?? 'Document' ?>
                                                    </h6>
                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-tag mr-1"></i> <?= $doc->document_type ?? $doc->type ?? 'Qualification' ?>
                                                    </p>
                                                    <div class="text-center">
                                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye mr-1"></i> View Document
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center text-muted py-4">No qualification documents available</div>
                                <?php endif; ?>
                            </div>
                        </div>

                         <!-- License -->
                        <div class="tab-pane" id="license">
                             <h5 class="mb-3 text-primary">License Selection</h5>
                             <div class="table-responsive">
                                 <table class="table table-bordered">
                                     <thead class="thead-light">
                                         <tr>
                                             <th>License Class</th>
                                             <th>Description</th>
                                             <th class="text-right">Fee (TZS)</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         <?php if (!empty($application->license_items)): ?>
                                             <?php foreach ($application->license_items as $item): ?>
                                             <tr>
                                                 <td><span class="text-bold text-primary"><?= $item->license_name ?? $item->type ?? $item->name ?? 'License' ?></span></td>
                                                 <td><?= $item->description ?? $item->details ?? '-' ?></td>
                                                 <td class="text-right font-weight-bold"><?= number_format($item->amount ?? $item->fee ?? 0, 2) ?></td>
                                             </tr>
                                             <?php endforeach; ?>
                                         <?php else: ?>
                                             <tr>
                                                 <td colspan="3" class="text-center text-muted">No license items found</td>
                                             </tr>
                                         <?php endif; ?>
                                     </tbody>
                                 </table>
                             </div>
                        </div>

                        <!-- Approvals -->
                        <div class="tab-pane" id="approvals">
                             <h5 class="mb-3 text-primary">Approval Workflow Status</h5>
                             <div class="timeline timeline-inverse">
                                <!-- Region Manager -->
                                <div>
                                    <i class="fas fa-user-tie bg-<?= ($application->region_manager_status ?? '') == 'Approved' ? 'success' : (($application->region_manager_status ?? '') == 'Rejected' ? 'danger' : 'warning') ?>"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header"><a href="#">Region Manager</a> Verification</h3>
                                        <div class="timeline-body">
                                            Current Status: 
                                            <span class="badge badge-<?= ($application->region_manager_status ?? '') == 'Approved' ? 'success' : (($application->region_manager_status ?? '') == 'Rejected' ? 'danger' : 'warning') ?>">
                                                <?= $application->region_manager_status ?? 'Pending' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Surveillance -->
                                <div>
                                    <i class="fas fa-eye bg-<?= ($application->surveillance_status ?? '') == 'Approved' ? 'success' : (($application->surveillance_status ?? '') == 'Rejected' ? 'danger' : 'warning') ?>"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header"><a href="#">Surveillance</a> Verification</h3>
                                        <div class="timeline-body">
                                             Current Status: 
                                            <span class="badge badge-<?= ($application->surveillance_status ?? '') == 'Approved' ? 'success' : (($application->surveillance_status ?? '') == 'Rejected' ? 'danger' : 'warning') ?>">
                                                <?= $application->surveillance_status ?? 'Pending' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>

<?= $this->endSection(); ?>
