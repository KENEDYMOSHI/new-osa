<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <style>
        .nav-pills .nav-link {
            font-size: 0.85rem;
            padding: 0.4rem 0.6rem;
            white-space: nowrap;
        }
        
        /* Modern Document Card Styling */
        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
        }
        .document-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .document-icon-lg {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-danger-light {
            background-color: #ffe5e5;
        }
        .bg-warning-light {
            background-color: #fff3cd;
        }
        .bg-success-light {
            background-color: #d4edda;
        }
        .bg-info-light {
            background-color: #d1ecf1;
        }
        .bg-primary-light {
            background-color: #cfe2ff;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .gap-2 {
            gap: 0.5rem;
        }
        .transition {
            transition: all 0.3s ease;
        }
        .border-left-warning {
            border-left: 3px solid #ffc107 !important;
        }
        .border-success {
            border-left-color: #28a745 !important;
        }
        .border-warning {
            border-left-color: #ffc107 !important;
        }
        .border-info {
            border-left-color: #17a2b8 !important;
        }
        .border-primary {
            border-left-color: #007bff !important;
        }
        
        /* Returned Document Styling */
        .returned-document {
            border: 2px dashed #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.05) !important;
            border-radius: 0.25rem !important;
        }
        .returned-document:hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
            box-shadow: 0 0.125rem 0.5rem rgba(220, 53, 69, 0.2) !important;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse-animation {
            animation: pulse 2s ease-in-out infinite;
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
                            <b>App Fee CN</b> <a class="float-right"><?= $application->control_number ?? 'N/A' ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>License Fee CN</b> 
                            <a class="float-right">
                                <?php 
                                    $licCn = 'Not Generated';
                                    if (!empty($application->license_items) && is_array($application->license_items)) {
                                        // key 0 usually holds the main license item if singular
                                        if (!empty($application->license_items[0]->control_number)) {
                                            $licCn = $application->license_items[0]->control_number;
                                        }
                                    }
                                    echo $licCn;
                                ?>
                            </a>
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

            <!-- License Number Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">License Number</h3>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-center bg-light border border-dashed rounded" style="min-height: 120px;">
                        <?php if (!empty($application->license_number)): ?>
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="fas fa-id-card text-success fa-3x"></i>
                                </div>
                                <h4 class="font-weight-bold text-primary mb-1"><?= $application->license_number ?></h4>
                                <small class="text-muted">License Generated</small>
                            </div>
                        <?php else: ?>
                            <span class="text-muted font-italic">License Number will be generated here</span>
                        <?php endif; ?>
                    </div>
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
                        <li class="nav-item"><a class="nav-link" href="#documents" data-toggle="tab"><i class="fas fa-paperclip mr-1"></i> Attachments Uploaded</a></li>
                         <li class="nav-item"><a class="nav-link" href="#license" data-toggle="tab"><i class="fas fa-id-card mr-1"></i> License</a></li>
                         
                         <?php 
                            // Condition: Show only if Applicant has submitted completion data
                            // Statuses: Applicant_Submission, Pending_DTS, Pending_CEO, Approved, Approved_DTS, etc.
                            // Basically anything AFTER 'Approved_Surveillance' and triggering 'Applicant_Submission'
                            $showTools = in_array($application->status, ['Applicant_Submission', 'Pending_DTS', 'Pending_CEO', 'Approved_DTS', 'Approved_CEO', 'Approved', 'Rejected']);
                         ?>
                         <?php if ($showTools): ?>
                            <li class="nav-item"><a class="nav-link" href="#tools" data-toggle="tab"><i class="fas fa-tools mr-1"></i> Tools & Qualifications</a></li>
                         <?php endif; ?>

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
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Ward</label>
                                        <span class="h6"><?= $application->business_ward ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Postal Code</label>
                                        <span class="h6"><?= $application->postal_code ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group border-bottom pb-2">
                                        <label class="d-block text-muted small mb-1">Street / Village</label>
                                        <span class="h6"><?= $application->business_street ?? 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <!-- Documents (Combined Attachments & Qualifications) -->
                        <div class="tab-pane" id="documents">
                            <h5 class="mb-3 text-primary"><i class="fas fa-paperclip mr-2"></i>Required Attachments</h5>
                            <style>
                                .returned-document {
                                    border: 2px dashed #dc3545 !important;
                                    background-color: #fff5f5 !important;
                                }
                                .returned-document .document-icon {
                                    background-color: #f8d7da !important;
                                }
                                .returned-document .alert-danger {
                                    background-color: #ffebec;
                                    border-color: #f5c6cb;
                                }
                            </style>
                            <div class="row">
                                <?php if (!empty($application->attachments)): ?>
                    <?php foreach ($application->attachments as $doc): ?>
                        <?php if (!isset($doc->category) || $doc->category != 'qualification'): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border-0 shadow-sm h-100 hover-shadow transition <?= (isset($doc->status) && $doc->status == 'Returned') ? 'returned-document' : '' ?>">
                                <div class="card-body p-4">
                                    <!-- Header with Icon and Badge -->
                                    <div class="d-flex align-items-start mb-3">
                                        <?php if (isset($doc->status) && $doc->status == 'Returned'): ?>
                                        <!-- Pulsing Warning Icon for Returned Documents -->
                                        <div class="document-icon bg-danger rounded-circle p-3 mr-3 pulse-animation">
                                            <i class="fas fa-exclamation-triangle text-white fa-lg"></i>
                                        </div>
                                        <?php else: ?>
                                        <div class="document-icon bg-danger-light rounded-circle p-3 mr-3">
                                            <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                        </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <?php 
                                            $badgeClass = 'badge-success'; // Default green for UPLOADED
                                            $badgeText = 'UPLOADED';
                                            $badgeIcon = 'fa-check-circle';
                                            $badgeStyle = '';
                                            
                                            if (isset($doc->status)) {
                                                if ($doc->status == 'Returned') {
                                                    $badgeClass = 'badge-danger';
                                                    $badgeText = 'RETURNED';
                                                    $badgeIcon = 'fa-exclamation-triangle';
                                                    $badgeStyle = 'font-weight: bold; font-size: 0.75rem; padding: 0.4rem 0.8rem;';
                                                } elseif ($doc->status == 'Resubmitted') {
                                                    $badgeClass = 'badge-info';
                                                    $badgeText = 'RESUBMITTED';
                                                    $badgeIcon = 'fa-sync';
                                                } elseif ($doc->status == 'Approved') {
                                                    $badgeClass = 'badge-primary';
                                                    $badgeText = 'APPROVED';
                                                    $badgeIcon = 'fa-check-double';
                                                }
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?> badge-pill px-3 py-1 mb-2" style="<?= $badgeStyle ?>">
                                                <i class="fas <?= $badgeIcon ?> mr-1"></i> <?= $badgeText ?>
                                            </span>
                                            <h6 class="font-weight-bold text-dark mb-1 line-clamp-2">
                                                <?= $doc->document_type ?? $doc->type ?? $doc->document_name ?? $doc->file_name ?? 'Document' ?>
                                            </h6>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-file mr-1"></i> <?= $doc->file_name ?? $doc->original_name ?? 'File' ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-3">
                                        <a href="javascript:void(0)" 
                                           onclick="viewDocument('<?= $doc->id ?? '' ?>', '<?= addslashes($doc->document_type ?? $doc->type ?? 'Document') ?>')" 
                                           class="btn btn-primary btn-sm flex-fill">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        <?php if (isset($doc->status) && $doc->status == 'Resubmitted'): ?>
                                        <button onclick="acceptDocument('<?= $doc->id ?? '' ?>')" 
                                                class="btn btn-success btn-sm flex-fill">
                                            <i class="fas fa-check mr-1"></i> Accept
                                        </button>
                                        <?php endif; ?>
                                        <button onclick="showReturnModal('<?= $doc->id ?? '' ?>', '<?= addslashes($doc->document_name ?? $doc->file_name ?? 'Document') ?>')" 
                                                class="btn btn-warning btn-sm flex-fill">
                                            <i class="fas fa-undo mr-1"></i> Return
                                        </button>
                                    </div>
                                    
                                    <!-- Rejection Reason Alert -->
                                    <?php if (isset($doc->rejection_reason) && !empty($doc->rejection_reason)): ?>
                                    <div class="alert alert-danger mt-3 mb-0 py-2 px-3 border-left" style="border-left: 4px solid #dc3545 !important;">
                                        <small class="font-weight-bold">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <strong>Reason:</strong> <?= $doc->rejection_reason ?>
                                        </small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center text-muted py-4">No attachments available</div>
                                <?php endif; ?>
                            </div>

                            <!-- Divider -->
                            <hr class="my-4">

                            <!-- Qualification Documents Section -->
                             <h5 class="mb-3 text-primary"><i class="fas fa-graduation-cap mr-2"></i>Qualification Documents</h5>
                             <style>
                                .returned-document {
                                    border: 2px dashed #dc3545 !important;
                                    background-color: #fff5f5 !important;
                                }
                                .returned-document .document-icon {
                                    background-color: #f8d7da !important;
                                }
                                .returned-document .alert-danger {
                                    background-color: #ffebec;
                                    border-color: #f5c6cb;
                                    border-left: 4px solid #dc3545;
                                }
                             </style>
                             <div class="row">
                                <?php if (!empty($application->attachments)): ?>
                                    <?php foreach ($application->attachments as $doc): ?>
                                        <?php if (isset($doc->category) && $doc->category == 'qualification'): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                             <div class="card hover-shadow mb-3 <?= (isset($doc->status) && $doc->status == 'Returned') ? 'returned-document' : '' ?>" 
                                      data-doc-id="<?= $doc->id ?? '' ?>">
                                                <div class="card-body p-4">
                                                    <!-- Header with Icon and Badge -->
                                                    <div class="d-flex align-items-start mb-3">
                                                        <?php if (isset($doc->status) && $doc->status == 'Returned'): ?>
                                                        <!-- Pulsing Warning Icon for Returned Documents -->
                                                        <div class="document-icon bg-danger rounded-circle p-3 mr-3 pulse-animation">
                                                            <i class="fas fa-exclamation-triangle text-white fa-lg"></i>
                                                        </div>
                                                        <?php else: ?>
                                                        <div class="document-icon bg-warning-light rounded-circle p-3 mr-3">
                                                            <i class="fas fa-certificate text-warning fa-lg"></i>
                                                        </div>
                                                        <?php endif; ?>
                                                        <div class="flex-grow-1">
                                                            <?php 
                                                            $badgeClass = 'badge-info'; // Default blue for QUALIFICATION
                                                            $badgeText = 'QUALIFICATION';
                                                            $badgeIcon = 'fa-graduation-cap';
                                                            $badgeStyle = '';
                                                            
                                                            if (isset($doc->status)) {
                                                                if ($doc->status == 'Returned') {
                                                                    $badgeClass = 'badge-danger';
                                                                    $badgeText = 'RETURNED';
                                                                    $badgeIcon = 'fa-exclamation-triangle';
                                                                    $badgeStyle = 'font-weight: bold; font-size: 0.75rem; padding: 0.4rem 0.8rem;';
                                                                } elseif ($doc->status == 'Resubmitted') {
                                                                    $badgeClass = 'badge-info';
                                                                    $badgeText = 'RESUBMITTED';
                                                                    $badgeIcon = 'fa-sync';
                                                                } elseif ($doc->status == 'Approved') {
                                                                    $badgeClass = 'badge-primary';
                                                                    $badgeText = 'APPROVED';
                                                                    $badgeIcon = 'fa-check-double';
                                                                }
                                                            }
                                                            ?>
                                                            <span class="badge <?= $badgeClass ?> badge-pill px-3 py-1 mb-2" style="<?= $badgeStyle ?>">
                                                                <i class="fas <?= $badgeIcon ?> mr-1"></i> <?= $badgeText ?>
                                                            </span>
                                                            <h6 class="font-weight-bold text-dark mb-1 line-clamp-2">
                                                                <?= $doc->document_type ?? $doc->type ?? $doc->document_name ?? $doc->file_name ?? 'Qualification' ?>
                                                            </h6>
                                                            <p class="text-muted small mb-0">
                                                                <i class="fas fa-file mr-1"></i> <?= $doc->file_name ?? $doc->original_name ?? 'File' ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Action Buttons -->
                                                    <div class="d-flex gap-2 mt-3">
                                                        <a href="javascript:void(0)" 
                                                           onclick="viewDocument('<?= $doc->id ?? '' ?>', '<?= addslashes($doc->document_type ?? $doc->type ?? 'Qualification') ?>')" 
                                                           class="btn btn-primary btn-sm flex-fill">
                                                            <i class="fas fa-eye mr-1"></i> View
                                                        </a>
                                                        <?php if (isset($doc->status) && $doc->status == 'Resubmitted'): ?>
                                                        <button onclick="acceptDocument('<?= $doc->id ?? '' ?>')" 
                                                                class="btn btn-success btn-sm flex-fill">
                                                            <i class="fas fa-check mr-1"></i> Accept
                                                        </button>
                                                        <?php endif; ?>
                                                        <button onclick="showReturnModal('<?= $doc->id ?? '' ?>', '<?= addslashes($doc->document_name ?? $doc->file_name ?? 'Document') ?>')"
                                                                class="btn btn-warning btn-sm flex-fill">
                                                            <i class="fas fa-undo mr-1"></i> Return
                                                        </button>
                                                    </div>

                                                    <!-- Rejection Reason Alert -->
                                                    <?php if (isset($doc->rejection_reason) && !empty($doc->rejection_reason)): ?>
                                                    <div class="alert alert-danger mt-3 mb-0 py-2 px-3 border-left" style="border-left: 4px solid #dc3545 !important;">
                                                        <small class="font-weight-bold">
                                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                                            <strong>Reason:</strong> <?= $doc->rejection_reason ?>
                                                        </small>
                                                    </div>
                                                    <?php endif; ?>
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
                        <!-- /.tab-pane -->

                         <!-- License -->
                        <!-- License -->
                        <!-- License -->
                        <div class="tab-pane" id="license">
                            <div class="card shadow-sm mb-4 border-top-primary">
                                <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-contract mr-2"></i>License Details</h6>
                                    <span class="badge badge-primary badge-pill px-3">Total Items: <?= count($application->license_items ?? []) + 1 ?></span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light text-uppercase small text-muted">
                                                <tr>
                                                    <th class="py-3 pl-4" style="min-width: 250px;">Item Description</th>
                                                    <th class="py-3 text-center">Control Number</th>
                                                    <th class="py-3 text-right">Amount (TZS)</th>
                                                    <th class="py-3 text-center pr-4">Payment Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Application Fee Row -->
                                                <tr style="background-color: #f8f9fc;">
                                                    <td class="pl-4 py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-circle bg-primary text-white mr-3">
                                                                <i class="fas fa-file-invoice-dollar"></i>
                                                            </div>
                                                            <div>
                                                                <span class="font-weight-bold text-gray-800 d-block">Application Fee</span>
                                                                <small class="text-muted">Processing fee</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center py-3">
                                                        <?php 
                                                            $controlNumber = '-';
                                                            if (!empty($application->license_items)) {
                                                                $controlNumber = $application->license_items[0]->control_number ?? '-';
                                                            }
                                                        ?>
                                                        <?php if ($controlNumber !== '-'): ?>
                                                            <div class="bg-white border rounded px-2 py-1 d-inline-block shadow-sm">
                                                                <code class="text-primary font-weight-bold" style="font-size: 0.95rem;"><?= $controlNumber ?></code>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted small font-italic">Not Generated</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right py-3 font-weight-bold text-dark h6 mb-0">
                                                        <?php 
                                                            $billAmount = 0;
                                                            if (!empty($application->license_items)) {
                                                                $billAmount = $application->license_items[0]->application_fee ?? 0;
                                                            }
                                                            echo number_format($billAmount, 2);
                                                        ?>
                                                    </td>
                                                    <td class="text-center py-3 pr-4">
                                                        <?php 
                                                            $payStatus = 'Pending';
                                                            if (!empty($application->license_items)) {
                                                                $payStatus = $application->license_items[0]->payment_status ?? 'Pending';
                                                            }
                                                            $statusBadge = 'badge-warning';
                                                            $statusIcon = 'fa-clock';
                                                            if ($payStatus === 'Paid') {
                                                                $statusBadge = 'badge-success';
                                                                $statusIcon = 'fa-check-circle';
                                                            } elseif ($payStatus === 'Partially Paid') {
                                                                $statusBadge = 'badge-info';
                                                                $statusIcon = 'fa-adjust';
                                                            }
                                                        ?>
                                                        <span class="badge <?= $statusBadge ?> badge-pill px-3 py-2">
                                                            <i class="fas <?= $statusIcon ?> mr-1"></i> <?= $payStatus ?>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <!-- License Items Rows -->
                                                <?php if (!empty($application->license_items)): ?>
                                                    <?php foreach ($application->license_items as $item): ?>
                                                    <tr>
                                                        <td class="pl-4 py-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon-circle bg-gray-200 text-gray-600 mr-3">
                                                                    <i class="fas fa-certificate"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="font-weight-bold text-dark d-block"><?= $item->license_name ?? $item->type ?? $item->name ?? 'License' ?></span>
                                                                    <small class="text-muted">License Class</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center py-3">
                                                            <span class="badge badge-light text-muted border border-dashed font-weight-normal px-3 py-2">
                                                                <i class="fas fa-hourglass-start mr-1 text-gray-400"></i> Pending Approval
                                                            </span>
                                                        </td>
                                                        <td class="text-right py-3 font-weight-bold text-dark h6 mb-0"><?= number_format($item->amount ?? $item->fee ?? 0, 2) ?></td>
                                                        <td class="text-center py-3 pr-4">
                                                            <span class="badge badge-light text-warning border border-warning px-3 py-2 rounded-pill">
                                                                <i class="fas fa-clock mr-1"></i> Pending
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-5">
                                                            <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                                                            <p class="mb-0">No license items found for this application.</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tools & Qualifications Tab -->
                        <div class="tab-pane" id="tools">
                             <!-- Previous Licenses (Moved to Top) -->
                             <div class="mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-history mr-2"></i>Previous Licenses</h5>
                                <?php if (!empty($application->previous_licenses_list)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>License Number</th>
                                                    <th>Type</th>
                                                    <th>Issued Date</th>
                                                    <th>Expiry Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($application->previous_licenses_list as $lic): ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                            // Handle both string values and object values for license number
                                                            if (is_string($lic)) {
                                                                echo htmlspecialchars($lic);
                                                            } elseif (is_object($lic)) {
                                                                echo htmlspecialchars($lic->number ?? '-');
                                                            } elseif (is_array($lic)) {
                                                                echo htmlspecialchars($lic['number'] ?? '-');
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            // Get the license type being renewed from license_items
                                                            $licenseType = '-';
                                                            if (!empty($application->license_items)) {
                                                                // Get the first license item type (or you could match by some criteria)
                                                                $firstItem = is_array($application->license_items) ? $application->license_items[0] : $application->license_items;
                                                                if (is_object($firstItem)) {
                                                                    $licenseType = $firstItem->license_type ?? $firstItem->name ?? '-';
                                                                } elseif (is_array($firstItem)) {
                                                                    $licenseType = $firstItem['license_type'] ?? $firstItem['name'] ?? '-';
                                                                }
                                                            }
                                                            echo htmlspecialchars($licenseType);
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            // Issued Date
                                                            if (is_object($lic)) {
                                                                echo htmlspecialchars($lic->issued ?? $lic->issued_date ?? '-');
                                                            } elseif (is_array($lic)) {
                                                                echo htmlspecialchars($lic['issued'] ?? $lic['issued_date'] ?? '-');
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            // Expiry Date
                                                            if (is_object($lic)) {
                                                                echo htmlspecialchars($lic->expiry ?? $lic->expiry_date ?? '-');
                                                            } elseif (is_array($lic)) {
                                                                echo htmlspecialchars($lic['expiry'] ?? $lic['expiry_date'] ?? '-');
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted font-italic">No previous licenses listed.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Tools -->
                            <div class="mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-hammer mr-2"></i>Tools and Equipment</h5>
                                <?php if (!empty($application->tools_list)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Tool Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($application->tools_list as $tool): ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                            // Handle both string values and object values
                                                            if (is_string($tool)) {
                                                                echo htmlspecialchars($tool);
                                                            } elseif (is_object($tool)) {
                                                                echo htmlspecialchars($tool->name ?? '-');
                                                            } elseif (is_array($tool)) {
                                                                echo htmlspecialchars($tool['name'] ?? '-');
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted font-italic">No tools listed.</p>
                                <?php endif; ?>
                            </div>

                             <!-- Qualifications -->
                            <div class="mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-user-graduate mr-2"></i>Technical Qualifications</h5>
                                <?php if (!empty($application->qualifications_list)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Qualification</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($application->qualifications_list as $qual): ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                            // Handle both string values and object values
                                                            if (is_string($qual)) {
                                                                echo htmlspecialchars($qual);
                                                            } elseif (is_object($qual)) {
                                                                echo htmlspecialchars($qual->name ?? '-');
                                                            } elseif (is_array($qual)) {
                                                                echo htmlspecialchars($qual['name'] ?? '-');
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted font-italic">No qualifications listed.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Experience -->
                            <div class="mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-briefcase mr-2"></i>Work Experience</h5>
                                <?php if (!empty($application->experience_list)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Work Experience</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($application->experience_list as $exp): ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                            // Handle both string values and object values
                                                            if (is_string($exp)) {
                                                                echo htmlspecialchars($exp);
                                                            } elseif (is_object($exp)) {
                                                                $company = $exp->company ?? '-';
                                                                $position = $exp->position ?? '-';
                                                                echo htmlspecialchars("$position at $company");
                                                            } elseif (is_array($exp)) {
                                                                $company = $exp['company'] ?? '-';
                                                                $position = $exp['position'] ?? '-';
                                                                echo htmlspecialchars("$position at $company");
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted font-italic">No experience listed.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Approvals -->
                        <!-- Approvals Tab (Reference Design: Clean Timeline) -->
                        <div class="tab-pane" id="approvals">
                            <style>
                                /* Clean Timeline CSS */
                                .clean-timeline {
                                    position: relative;
                                    padding-left: 30px; /* Space for marker */
                                    margin-left: 10px;
                                    border-left: 1px solid #e3e6f0; /* Thin gray connector */
                                }
                                .ct-item {
                                    position: relative;
                                    margin-bottom: 2.5rem; /* Spacious */
                                    padding-left: 15px;
                                }
                                .ct-marker {
                                    position: absolute;
                                    left: -36px; /* Align with border(-30px - 6px half width) */
                                    top: 0;
                                    width: 12px;
                                    height: 12px;
                                    border-radius: 50%;
                                    background: #fff;
                                    border: 2px solid #d1d3e2; /* Default Gray */
                                    z-index: 2;
                                }
                                .ct-marker.active { border-color: #4e73df; } /* Blue for Active */
                                .ct-marker.completed { border-color: #1cc88a; } /* Green for Done */
                                .ct-marker.rejected { border-color: #e74a3b; } /* Red for Rejected */
                                .ct-marker.review { border-color: #e74a3b; } /* Red ring style from image */

                                .ct-header {
                                    display: flex;
                                    align-items: center;
                                    flex-wrap: wrap;
                                    margin-bottom: 0.5rem;
                                    font-size: 1rem;
                                    color: #333;
                                }
                                .ct-badge {
                                    font-size: 0.75rem;
                                    font-weight: 700;
                                    padding: 0.25rem 0.6rem;
                                    border-radius: 0.2rem;
                                    margin-right: 0.8rem;
                                    text-transform: uppercase;
                                    letter-spacing: 0.5px;
                                }
                                .badge-review { background-color: #eaecf4; color: #5a5c69; }
                                .badge-approve { background-color: #d1e7dd; color: #0f5132; } /* Soft Green */
                                .badge-reject { background-color: #f8d7da; color: #842029; } /* Soft Red */
                                
                                .ct-meta {
                                    font-size: 0.9rem;
                                    color: #5a5c69;
                                }
                                .ct-meta strong { color: #2e344e; font-weight: 700; }
                                .ct-meta .dot { margin: 0 0.5rem; color: #bdc3c7; }
                                .ct-date { color: #858796; }

                                .ct-content {
                                    font-size: 0.95rem;
                                    color: #2e344e;
                                    line-height: 1.6;
                                }
                                .ct-content.pending { color: #858796; font-style: italic; }
                                
                                /* Action Form Inline */
                                .action-box {
                                    background: #f8f9fc;
                                    border: 1px solid #e3e6f0;
                                    border-radius: 0.35rem;
                                    padding: 1.5rem;
                                    margin-top: 1rem;
                                }
                            </style>

                            <!-- Flash Messages -->
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            <?php endif; ?>
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle mr-2"></i><?= session()->getFlashdata('error') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            <?php endif; ?>

                            <h5 class="mb-5 text-gray-800 font-weight-bold ml-2">Application Timeline</h5>

                            <div class="clean-timeline">
                                <?php
                                    // Helper Logic
                                    $rmStatus = $application->region_manager_status ?? 'Pending';
                                    $svStatus = $application->surveillance_status ?? 'Pending';
                                    $dtsStatus = $application->dts_status ?? 'Pending';
                                    $ceoStatus = $application->ceo_status ?? 'Pending';
                                    $appStatus = $application->application_status ?? 'Pending';
                                    
                                    // Helpers for Data
                                    $rmComment = ''; $svComment = ''; $dtsComment = ''; $ceoComment = '';
                                    $rmDate = ''; $svDate = ''; $dtsDate = ''; $ceoDate = '';
                                    $rmApprover = ''; $svApprover = ''; $dtsApprover = ''; $ceoApprover = '';
                                    
                                    if (!empty($application->approvals)) {
                                        foreach ($application->approvals as $ap) {
                                            $stageName = $ap->stage ?? '';
                                            // Backend sends 'Manager'/'Surveillance', safely check both
                                            if ($stageName == 'Manager' || $stageName == 1) { 
                                                $rmComment = $ap->comment ?? ''; 
                                                $rmDate = $ap->date ?? ($ap->created_at ?? ''); 
                                                $rmApprover = $ap->approver ?? '';
                                            }
                                            if ($stageName == 'Surveillance' || $stageName == 2) { 
                                                $svComment = $ap->comment ?? ''; 
                                                $svDate = $ap->date ?? ($ap->created_at ?? ''); 
                                                $svApprover = $ap->approver ?? '';
                                            }
                                            if ($stageName == 'DTS' || $stageName == 3) { 
                                                $dtsComment = $ap->comment ?? ''; 
                                                $dtsDate = $ap->date ?? ($ap->created_at ?? ''); 
                                                $dtsApprover = $ap->approver ?? '';
                                            }
                                            if ($stageName == 'CEO' || $stageName == 4) { 
                                                $ceoComment = $ap->comment ?? ''; 
                                                $ceoDate = $ap->date ?? ($ap->created_at ?? ''); 
                                                $ceoApprover = $ap->approver ?? '';
                                            }
                                        }
                                    }

                                    // 1. Region Manager State
                                    $rmMarker = 'active'; // default ring
                                    $rmBadgeClass = 'badge-review';
                                    $rmBadgeText = 'REVIEW';
                                    
                                    if ($rmStatus == 'Approved') { 
                                        $rmMarker = 'completed'; $rmBadgeClass = 'badge-approve'; $rmBadgeText = 'APPROVED'; 
                                    } elseif ($rmStatus == 'Rejected') { 
                                        $rmMarker = 'rejected'; $rmBadgeClass = 'badge-reject'; $rmBadgeText = 'REJECTED'; 
                                    }
                                ?>

                                <!-- 1. Region Manager Item -->
                                <div class="ct-item">
                                    <div class="ct-marker <?= $rmMarker ?>"></div>
                                    <div class="ct-header">
                                        <span class="ct-badge <?= $rmBadgeClass ?>"><?= $rmBadgeText ?></span>
                                        <div class="ct-meta">
                                            by <strong>Region Manager<?= $rmApprover ? ' - ' . $rmApprover : '' ?></strong> 
                                            <?php if ($rmDate): ?>
                                                <span class="dot">•</span> <span class="ct-date"><?= date('M d, Y', strtotime($rmDate)) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="ct-content <?= ($rmStatus == 'Pending') ? 'pending' : '' ?>">
                                        <?php if ($rmStatus == 'Pending'): ?>
                                            <?php if ($user->inGroup('manager')): ?>
                                                <!-- Action Form for Manager -->
                                                 <div class="action-box">
                                                    <h6 class="font-weight-bold text-primary mb-3">Action Required</h6>
                                                    <p class="mb-3">Please review the application and provide your decision.</p>
                                                    <button type="button" class="btn btn-primary btn-sm px-4" data-toggle="modal" data-target="#approveModal">Approve</button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm px-4" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                                 </div>
                                            <?php else: ?>
                                                Status: Pending Review
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?= $rmComment ? $rmComment : 'No remarks provided.' ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php
                                    // 2. Surveillance State
                                    $svMarker = ''; 
                                    $svBadgeClass = 'badge-review';
                                    $svBadgeText = 'PENDING';
                                    
                                    if ($rmStatus == 'Pending' || $rmStatus == 'Rejected') {
                                        $svMarker = ''; // Default gray
                                    } else {
                                         if ($svStatus == 'Approved') { 
                                            $svMarker = 'completed'; $svBadgeClass = 'badge-approve'; $svBadgeText = 'APPROVED'; 
                                        } elseif ($svStatus == 'Rejected') { 
                                            $svMarker = 'rejected'; $svBadgeClass = 'badge-reject'; $svBadgeText = 'REJECTED'; 
                                        } elseif ($svStatus == 'Pending') {
                                            $svMarker = 'active'; $svBadgeText = 'REVIEW';
                                        }
                                    }
                                ?>

                                <!-- 2. Surveillance Item -->
                                <div class="ct-item">
                                    <div class="ct-marker <?= $svMarker ?>"></div>
                                    <div class="ct-header">
                                        <span class="ct-badge <?= ($svStatus == 'Pending' && $rmStatus != 'Pending' && $rmStatus != 'Rejected') ? 'badge-review' : (($svStatus == 'Pending') ? 'badge-secondary' : $svBadgeClass) ?>">
                                            <?= $svBadgeText ?>
                                        </span>
                                        <div class="ct-meta">
                                            by <strong>Surveillance Officer<?= $svApprover ? ' - ' . $svApprover : '' ?></strong>
                                             <?php if ($svDate): ?>
                                                <span class="dot">•</span> <span class="ct-date"><?= date('M d, Y', strtotime($svDate)) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="ct-content <?= ($svStatus == 'Pending') ? 'pending' : '' ?>">
                                         <?php if ($rmStatus == 'Pending' || $rmStatus == 'Rejected'): ?>
                                            Waiting for previous stage...
                                         <?php elseif ($svStatus == 'Pending'): ?>
                                             <?php if ($user->inGroup('surveillance')): ?>
                                                 <!-- Action Form for Surveillance -->
                                                  <div class="action-box">
                                                    <h6 class="font-weight-bold text-primary mb-3">Action Required</h6>
                                                    <p class="mb-3">Region Manager endorsed. Please verify details.</p>
                                                    <button type="button" class="btn btn-primary btn-sm px-4" data-toggle="modal" data-target="#approveModal">Approve</button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm px-4" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                                 </div>
                                             <?php else: ?>
                                                Status: Pending Verification
                                             <?php endif; ?>
                                         <?php else: ?>
                                            <?= $svComment ? $svComment : 'No remarks provided.' ?>
                                         <?php endif; ?>
                                    </div>
                                </div>

                                <!-- 3. Applicant Submission Item -->
                                <?php
                                    $appSubmitted = false;
                                    $appMarker = '';
                                    $appBadgeClass = 'badge-secondary';
                                    $appBadgeText = 'WAITING';
                                    
                                    // Check if submitted (Status is NOT Pending, Approved_Manager, Approved_Surveillance)
                                    // Actually, if status is 'Applicant_Submission' or it has moved PAST that.
                                    // Statuses: Applicant_Submission, Approved_DTS, Approved_CEO, Verified, License_Generated
                                    $submittedStatuses = ['Applicant_Submission', 'Approved_DTS', 'Approved_CEO', 'Approved', 'License_Generated', 'Completed'];
                                    
                                    if (in_array($appStatus, $submittedStatuses)) {
                                        $appSubmitted = true;
                                        $appMarker = 'completed';
                                        $appBadgeClass = 'badge-approve';
                                        $appBadgeText = 'SUBMITTED';
                                    } elseif ($svStatus == 'Approved') {
                                        $appMarker = 'active'; // Waiting for applicant
                                        $appBadgeClass = 'badge-warning';
                                        $appBadgeText = 'PENDING SUBMISSION';
                                    }
                                ?>
                                <div class="ct-item">
                                    <div class="ct-marker <?= $appMarker ?>"></div>
                                    <div class="ct-header">
                                        <span class="ct-badge <?= $appBadgeClass ?>"><?= $appBadgeText ?></span>
                                        <div class="ct-meta">by <strong>Applicant</strong></div>
                                    </div>
                                    <div class="ct-content <?= (!$appSubmitted) ? 'pending' : '' ?>">
                                        <?php if ($appSubmitted): ?>
                                            Applicant has submitted the complete license application.
                                        <?php elseif ($svStatus == 'Approved'): ?>
                                            Waiting for applicant to fill particulars and submit.
                                        <?php else: ?>
                                            Waiting for surveillance approval...
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- 4. Technical Director (DTS) Item -->
                                <?php if ($appSubmitted): ?>
                                    <?php
                                        $dtsMarker = '';
                                        $dtsBadgeClass = 'badge-review';
                                        $dtsBadgeText = 'PENDING';
                                        
                                        if ($dtsStatus == 'Approved') {
                                            $dtsMarker = 'completed'; $dtsBadgeClass = 'badge-approve'; $dtsBadgeText = 'APPROVED';
                                        } elseif ($dtsStatus == 'Rejected') {
                                            $dtsMarker = 'rejected'; $dtsBadgeClass = 'badge-reject'; $dtsBadgeText = 'REJECTED';
                                        } elseif ($dtsStatus == 'Pending') {
                                            $dtsMarker = 'active'; $dtsBadgeText = 'REVIEW';
                                        }
                                    ?>
                                    <div class="ct-item">
                                        <div class="ct-marker <?= $dtsMarker ?>"></div>
                                        <div class="ct-header">
                                            <span class="ct-badge <?= ($dtsStatus == 'Pending') ? 'badge-review' : $dtsBadgeClass ?>"><?= $dtsBadgeText ?></span>
                                            <div class="ct-meta">
                                                by <strong>Technical Director<?= $dtsApprover ? ' - ' . $dtsApprover : '' ?></strong>
                                                 <?php if ($dtsDate): ?>
                                                    <span class="dot">•</span> <span class="ct-date"><?= date('M d, Y', strtotime($dtsDate)) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="ct-content <?= ($dtsStatus == 'Pending') ? 'pending' : '' ?>">
                                            <?php if ($dtsStatus == 'Pending'): ?>
                                                <?php if ($user->inGroup('dts')): ?>
                                                     <div class="action-box">
                                                        <h6 class="font-weight-bold text-primary mb-3">Action Required</h6>
                                                        <p class="mb-3">Applicant submission received. Please endorse.</p>
                                                        <button type="button" class="btn btn-primary btn-sm px-4" data-toggle="modal" data-target="#approveModal">Approve</button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm px-4" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                                     </div>
                                                <?php else: ?>
                                                    Status: Pending Review
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?= $dtsComment ? $dtsComment : 'No remarks provided.' ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- 5. CEO Item -->
                                <?php if ($appSubmitted && ($dtsStatus == 'Approved' || $dtsStatus == 'Rejected')): ?>
                                    <?php
                                        $ceoMarker = '';
                                        $ceoBadgeClass = 'badge-review';
                                        $ceoBadgeText = 'PENDING';
                                        
                                        if ($dtsStatus == 'Rejected') {
                                            $ceoMarker = ''; // Gray
                                        } else {
                                            if ($ceoStatus == 'Approved') {
                                                $ceoMarker = 'completed'; $ceoBadgeClass = 'badge-approve'; $ceoBadgeText = 'APPROVED';
                                            } elseif ($ceoStatus == 'Rejected') {
                                                $ceoMarker = 'rejected'; $ceoBadgeClass = 'badge-reject'; $ceoBadgeText = 'REJECTED';
                                            } elseif ($ceoStatus == 'Pending') {
                                                $ceoMarker = 'active'; $ceoBadgeText = 'REVIEW';
                                            }
                                        }
                                    ?>
                                    <div class="ct-item">
                                        <div class="ct-marker <?= $ceoMarker ?>"></div>
                                        <div class="ct-header">
                                            <span class="ct-badge <?= ($ceoStatus == 'Pending' && $dtsStatus != 'Rejected') ? 'badge-review' : (($ceoStatus == 'Pending') ? 'badge-secondary' : $ceoBadgeClass) ?>">
                                                <?= $ceoBadgeText ?>
                                            </span>
                                            <div class="ct-meta">
                                                by <strong>Chief Executive Officer<?= $ceoApprover ? ' - ' . $ceoApprover : '' ?></strong>
                                                 <?php if ($ceoDate): ?>
                                                    <span class="dot">•</span> <span class="ct-date"><?= date('M d, Y', strtotime($ceoDate)) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="ct-content <?= ($ceoStatus == 'Pending') ? 'pending' : '' ?>">
                                            <?php if ($dtsStatus == 'Rejected'): ?>
                                                Process terminated at Technical Director stage.
                                            <?php elseif ($ceoStatus == 'Pending'): ?>
                                                <?php if ($user->inGroup('ceo')): ?>
                                                     <div class="action-box">
                                                        <h6 class="font-weight-bold text-primary mb-3">Action Required</h6>
                                                        <p class="mb-3">Technical Director endorsed. Final approval required.</p>
                                                        <button type="button" class="btn btn-primary btn-sm px-4" data-toggle="modal" data-target="#approveModal">Approve</button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm px-4" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                                     </div>
                                                <?php else: ?>
                                                    Status: Pending Final Approval
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?= $ceoComment ? $ceoComment : 'No remarks provided.' ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- 6. Final Item -->
                                <div class="ct-item">
                                    <div class="ct-marker <?= ($ceoStatus == 'Approved') ? 'completed' : (($rmStatus == 'Rejected' || $svStatus == 'Rejected' || $dtsStatus == 'Rejected' || $ceoStatus == 'Rejected') ? 'rejected' : '') ?>"></div>
                                    <div class="ct-header">
                                        <?php if ($ceoStatus == 'Approved'): ?>
                                            <span class="ct-badge badge-approve">LICENSE GENERATED</span>
                                        <?php elseif ($rmStatus == 'Rejected' || $svStatus == 'Rejected' || $dtsStatus == 'Rejected' || $ceoStatus == 'Rejected'): ?>
                                            <span class="ct-badge badge-reject">TERMINATED</span>
                                        <?php else: ?>
                                            <span class="ct-badge badge-secondary">WAITING</span>
                                        <?php endif; ?>

                                        <div class="ct-meta">by <strong>System</strong></div>
                                    </div>
                                    <div class="ct-content">
                                        <?php if ($ceoStatus == 'Approved'): ?>
                                            License successfully generated and issued to applicant.
                                        <?php elseif ($rmStatus == 'Rejected' || $svStatus == 'Rejected' || $dtsStatus == 'Rejected' || $ceoStatus == 'Rejected'): ?>
                                            Application process terminated due to rejection.
                                        <?php else: ?>
                                            Workflow in progress.
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>

                            <!-- Approve Modal (Restored for Comments) -->
                            <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?= base_url('osaApproveApplication') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="application_id" value="<?= $application->id ?>">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title" id="approveModalLabel"><i class="fas fa-check-circle mr-2"></i>Confirm Approval</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>You are about to approve this application. Please include your remarks.</p>
                                                <div class="form-group">
                                                    <label for="approveComment" class="font-weight-bold">Remarks / Comment <span class="text-muted">(Optional)</span></label>
                                                    <textarea class="form-control" id="approveComment" name="comment" rows="3" placeholder="Enter endorsement remarks..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Approve</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Reject Modal (Shared) -->
                            <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?= base_url('osaRejectApplication') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="application_id" value="<?= $application->id ?>">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="rejectModalLabel">Reject Application</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="rejectReason">Reason for Rejection <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="rejectReason" name="comment" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </div>
                                        </form>
                                    </div>
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

<!-- Document Viewer Modal -->
<div class="modal fade" id="documentViewerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf mr-2"></i><span id="viewerDocName">Document</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="documentFrame" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Return Document Modal -->
<div class="modal fade" id="returnDocumentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-undo mr-2"></i>Return Document
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Document:</strong> <span id="returnDocName"></span></p>
                <div class="form-group">
                    <label for="returnReason">Reason for Returning <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="returnReason" rows="4" placeholder="Enter reason why this document is being returned..." required></textarea>
                    <small class="form-text text-muted">Please provide a clear reason so the applicant knows what to correct.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-warning" onclick="submitReturn()">
                    <i class="fas fa-paper-plane mr-1"></i> Submit Return
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentDocId = null;

function acceptDocument(docId) {
    if (!docId) return;
    
    if (confirm('Are you sure you want to accept this document? The status will be updated to Uploaded.')) {
        // Show loading
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        fetch('http://localhost:8080/api/approval/document/' + docId + '/accept', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-KEY': '<?= $apiKey ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Document accepted successfully!');
                location.reload();
            } else {
                alert('Failed to accept document: ' + (data.message || 'Unknown error'));
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        })
        .catch(err => {
            console.error('Error accepting document:', err);
            alert('Error connecting to server.');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }
}

// Helper function to check if document has been viewed
function isDocumentViewed(docId) {
    const viewedDocs = JSON.parse(localStorage.getItem('viewedDocuments') || '[]');
    return viewedDocs.includes(docId.toString());
}

// Helper function to mark document as viewed
function markDocumentAsViewed(docId) {
    const viewedDocs = JSON.parse(localStorage.getItem('viewedDocuments') || '[]');
    if (!viewedDocs.includes(docId.toString())) {
        viewedDocs.push(docId.toString());
        localStorage.setItem('viewedDocuments', JSON.stringify(viewedDocs));
    }
}

function viewDocument(docId, docName) {
    if (!docId) {
        alert('Document ID is missing');
        return;
    }
    
    // Set document name in modal
    document.getElementById('viewerDocName').textContent = docName || 'Document';
    
    // Set iframe source to backend API endpoint
    const url = 'http://localhost:8080/api/admin/document/' + docId + '/view';
    document.getElementById('documentFrame').src = url;
    
    // Mark document as viewed
    markDocumentAsViewed(docId);
    
    // Show modal
    $('#documentViewerModal').modal('show');
    
    // Update UI after modal is shown - remove red border
    $('#documentViewerModal').on('shown.bs.modal', function () {
        // Find the document card and remove returned-document class
        const docCard = document.querySelector(`[onclick*="viewDocument('${docId}'"]`).closest('.returned-document');
        if (docCard) {
            docCard.classList.remove('returned-document');
            // Also remove the pulsing icon and update to normal icon
            const pulsingIcon = docCard.querySelector('.pulse-animation');
            if (pulsingIcon) {
                pulsingIcon.classList.remove('pulse-animation', 'bg-danger');
                pulsingIcon.classList.add('bg-danger-light');
                const icon = pulsingIcon.querySelector('i');
                if (icon) {
                    icon.classList.remove('text-white', 'fa-exclamation-triangle');
                    icon.classList.add('text-danger', 'fa-file-pdf');
                }
            }
        }
    });
}

function showReturnModal(docId, docName) {
    if (!docId) {
        alert('Document ID is missing');
        return;
    }
    
    currentDocId = docId;
    document.getElementById('returnDocName').textContent = docName;
    document.getElementById('returnReason').value = '';
    $('#returnDocumentModal').modal('show');
}

function submitReturn() {
    const reason = document.getElementById('returnReason').value.trim();
    
    if (!reason) {
        alert('Please enter a reason for returning this document');
        return;
    }
    
    if (!currentDocId) {
        alert('Document ID is missing');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Submitting...';
    
    // Send return request to backend
    fetch('http://localhost:8080/api/admin/document/return', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            document_id: currentDocId,
            rejection_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if (data.status === 'success' || data.message) {
            // Close modal
            $('#returnDocumentModal').modal('hide');
            
            // Show success message
            alert('Document has been returned successfully');
            
            // Update document badge and status without reloading
            const docCards = document.querySelectorAll('[data-doc-id="' + currentDocId + '"]');
            docCards.forEach(card => {
                // Update badge
                const badge = card.querySelector('.badge');
                if (badge) {
                    badge.className = 'badge badge-danger badge-pill px-3 py-1 mb-2';
                    badge.style = 'font-weight: bold; font-size: 0.75rem; padding: 0.4rem 0.8rem;';
                    badge.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> RETURNED';
                }
                
                // Add rejection reason alert if not exists
                const existingAlert = card.querySelector('.alert-danger');
                if (!existingAlert) {
                    const actionButtons = card.querySelector('.d-flex.gap-2');
                    if (actionButtons) {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger mt-3 mb-0 py-2 px-3 border-left';
                        alertDiv.style = 'border-left: 4px solid #dc3545 !important;';
                        alertDiv.innerHTML = `
                            <small class="font-weight-bold">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <strong>Reason:</strong> ${reason}
                            </small>
                        `;
                        actionButtons.parentNode.appendChild(alertDiv);
                    }
                }
            });
            
            // Clear the form
            document.getElementById('returnReason').value = '';
            currentDocId = null;
        } else {
            alert('Failed to return document: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        console.error('Error:', error);
        alert('An error occurred while returning the document');
    });
}

// On page load, check which documents have been viewed and update UI
document.addEventListener('DOMContentLoaded', function() {
    // Get all document cards with returned status
    const returnedCards = document.querySelectorAll('.returned-document');
    
    returnedCards.forEach(card => {
        // Find the view button to get document ID
        const viewButton = card.querySelector('[onclick*="viewDocument"]');
        if (viewButton) {
            const onclickAttr = viewButton.getAttribute('onclick');
            const match = onclickAttr.match(/viewDocument\('([^']+)'/);
            if (match && match[1]) {
                const docId = match[1];
                
                // Check if this document has been viewed
                if (isDocumentViewed(docId)) {
                    // Remove returned-document class
                    card.classList.remove('returned-document');
                    
                    // Update icon from pulsing warning to normal PDF icon
                    const pulsingIcon = card.querySelector('.pulse-animation');
                    if (pulsingIcon) {
                        pulsingIcon.classList.remove('pulse-animation', 'bg-danger');
                        pulsingIcon.classList.add('bg-danger-light');
                        const icon = pulsingIcon.querySelector('i');
                        if (icon) {
                            icon.classList.remove('text-white', 'fa-exclamation-triangle');
                            icon.classList.add('text-danger', 'fa-file-pdf');
                        }
                    }
                    
                    // For qualification documents, update to certificate icon
                    const qualIcon = card.querySelector('.bg-warning-light');
                    if (qualIcon && qualIcon.classList.contains('pulse-animation')) {
                        qualIcon.classList.remove('pulse-animation', 'bg-danger');
                        qualIcon.classList.add('bg-warning-light');
                        const icon = qualIcon.querySelector('i');
                        if (icon) {
                            icon.classList.remove('text-white', 'fa-exclamation-triangle');
                            icon.classList.add('text-warning', 'fa-certificate');
                        }
                    }
                }
            }
        }
    });
});

// Print Modal Functions
function openPrintModal() {
    document.getElementById('printModal').style.display = 'block';
    // Load print content via iframe
    document.getElementById('printFrame').src = '<?= base_url('print-application/' . $application->id) ?>';
}

function closePrintModal() {
    document.getElementById('printModal').style.display = 'none';
    document.getElementById('printFrame').src = '';
}

function printContent() {
    document.getElementById('printFrame').contentWindow.print();
}

function downloadPDF() {
    // Trigger print dialog with save as PDF option
    const iframe = document.getElementById('printFrame');
    iframe.contentWindow.focus();
    iframe.contentWindow.print();
}
</script>

<!-- Print Modal -->
<div id="printModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8);">
    <div style="position: relative; background-color: #fefefe; margin: 3% auto; padding: 0; width: 85%; max-width: 1200px; height: 85%; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
        <!-- Modal Header -->
        <div style="background: #2c5f2d; color: white; padding: 15px 20px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px;">
                <i class="fas fa-eye mr-2"></i>View Application
            </h3>
            <div>
                <button onclick="downloadPDF()" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-right: 10px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-file-pdf mr-1"></i>Download PDF
                </button>
                <button onclick="printContent()" style="background: white; color: #2c5f2d; border: none; padding: 8px 16px; border-radius: 4px; margin-right: 10px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-print mr-1"></i>Print
                </button>
                <button onclick="closePrintModal()" style="background: transparent; color: white; border: 2px solid white; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
        
        <!-- Modal Body - iframe -->
        <iframe id="printFrame" style="width: 100%; height: calc(100% - 60px); border: none; border-radius: 0 0 8px 8px;"></iframe>
    </div>
</div>

<script>
// Force hide any loading overlays when page is fully loaded
$(document).ready(function() {
    // Hide common loading overlay classes
    $('.preloader').fadeOut();
    $('.overlay').fadeOut();
    $('.loading-overlay').fadeOut();
    $('[data-widget="pushmenu"]').PushMenu('collapse');
    
    // Remove any overlay elements
    setTimeout(function() {
        $('.preloader, .overlay, .loading-overlay').remove();
    }, 500);
});

// Also try on window load
$(window).on('load', function() {
    $('.preloader, .overlay, .loading-overlay').fadeOut().remove();
});
</script>

<?= $this->endSection(); ?>
