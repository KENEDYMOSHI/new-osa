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
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Filter Requests</h3>
            </div>
            <form method="get" action="<?= base_url('requestedFormD') ?>">
                <div class="card-body">
                    <div class="row">
                        <!-- Row 1: Basic Search -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Applicant Name</label>
                                <input type="text" name="applicant_name" class="form-control" placeholder="Name" value="<?= esc($filters['applicant_name'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                             <div class="form-group">
                                <label>License Number</label>
                                <input type="text" name="license_number" class="form-control" placeholder="License No." value="<?= esc($filters['license_number'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                             <div class="form-group">
                                <label>Seal Number</label>
                                <input type="text" name="seal_number" class="form-control" placeholder="Seal No." value="<?= esc($filters['seal_number'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <!-- Row 2: Status & Inspector -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="Pending" <?= ($filters['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Approved" <?= ($filters['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="Rejected" <?= ($filters['status'] ?? '') == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label>Inspector</label>
                                <select name="inspector_id" class="form-control select2" style="width: 100%;">
                                    <option value="">All Inspectors</option>
                                    <?php if(!empty($inspectors)): ?>
                                        <?php foreach($inspectors as $inspector): ?>
                                            <option value="<?= $inspector->id ?>" <?= ($filters['inspector_id'] ?? '') == $inspector->id ? 'selected' : '' ?>>
                                                <?= $inspector->first_name . ' ' . $inspector->last_name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Date Filters -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Month</label>
                                <input type="month" name="month" class="form-control" value="<?= esc($filters['month'] ?? '') ?>">
                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label>Date Range (Start - End)</label>
                                <div class="d-flex">
                                    <input type="date" name="start_date" class="form-control mr-1" value="<?= esc($filters['start_date'] ?? '') ?>">
                                    <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
                    <a href="<?= base_url('requestedFormD') ?>" class="btn btn-secondary"> <i class="fas fa-sync"></i> Reset</a>
                </div>
            </form>
        </div>

        <!-- Requests Table -->
        <div class="card">
            <div class="card-body">
                <table id="formDTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Applicant Name</th>
                            <th>License Number</th>
                            <th>Seal Number</th>
                            <th>Company</th>
                            <th>Activities</th>
                            <th>Date</th>
                            <th>Form D</th>
                            <th>Action/Decision</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($requests)): ?>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?= esc($request['practitioner_name']) ?></td>
                                    <td><?= esc($request['license_number']) ?></td>
                                    <td><?= esc($request['seal_number']) ?></td>
                                    <td><?= esc($request['company_name']) ?></td>
                                    <td><?= esc($request['certification_action']) ?></td>
                                    <td><?= esc($request['created_at']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#previewModal<?= $request['id'] ?>" title="Preview Form D">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#actionModal<?= $request['id'] ?>" title="Take Action">
                                            <i class="fas fa-tasks mr-1"></i> Action
                                        </button>
                                    </td>
                                </tr>

                                <!-- Preview Modal (Form D Document Only) -->
                                <div class="modal fade" id="previewModal<?= $request['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="fas fa-file-alt mr-2"></i>Form D Preview - <?= esc($request['form_d_number'] ?? 'ID: ' . $request['id']) ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <iframe src="<?= base_url('printFormDRequest/' . $request['id']) ?>?token=<?= $request['print_token'] ?? '' ?>" 
                                                        style="width: 100%; height: 80vh; border: none;" 
                                                        title="Form D Document"></iframe>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action/Decision Modal -->
                                <div class="modal fade" id="actionModal<?= $request['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-gradient-success text-white">
                                                <h5 class="modal-title"><i class="fas fa-clipboard-check mr-2"></i>Action / Decision - Request #<?= esc($request['form_d_number'] ?? $request['id']) ?></h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Applicant Information Summary -->
                                                <div class="card card-outline card-primary mb-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title font-weight-bold"><i class="fas fa-user mr-1"></i> Applicant Information</h3>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><small class="text-muted">Practitioner:</small><br><strong><?= esc($request['practitioner_name']) ?></strong></p>
                                                                <p class="mb-1"><small class="text-muted">License:</small><br><?= esc($request['license_number']) ?></p>
                                                                <p class="mb-0"><small class="text-muted">Seal Number:</small><br><?= esc($request['seal_number']) ?></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><small class="text-muted">Company:</small><br><?= esc($request['company_name']) ?></p>
                                                                <p class="mb-1"><small class="text-muted">Activity:</small><br><?= esc($request['certification_action']) ?></p>
                                                                <p class="mb-0"><small class="text-muted">Submitted:</small><br><?= date('d M Y', strtotime($request['created_at'])) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Current Status -->
                                                <div class="alert <?= $request['status'] == 'Pending' ? 'alert-warning' : ($request['status'] == 'Approved' ? 'alert-success' : 'alert-danger') ?> mb-3">
                                                    <strong>Current Status:</strong> 
                                                    <span class="badge badge-<?= $request['status'] == 'Pending' ? 'warning' : ($request['status'] == 'Approved' ? 'success' : 'danger') ?> ml-2">
                                                        <?= esc($request['status']) ?>
                                                    </span>
                                                </div>


                                                <!-- Previous Decision Info (if already processed) -->
                                                <?php if($request['status'] != 'Pending'): ?>
                                                    <div class="alert alert-info mb-3">
                                                        <i class="fas fa-info-circle mr-2"></i>
                                                        <strong>Previous Decision:</strong>
                                                        <?php if($request['status'] == 'Approved'): ?>
                                                            <p class="mb-0 mt-2"><strong>Inspector ID:</strong> <?= esc($request['inspector_id']) ?></p>
                                                            <p class="mb-0"><strong>Verification Date:</strong> <?= esc($request['verification_date']) ?></p>
                                                            <?php if(!empty($request['assignment_notes'])): ?>
                                                                <p class="mb-0"><strong>Notes:</strong> <?= nl2br(esc($request['assignment_notes'])) ?></p>
                                                            <?php endif; ?>
                                                        <?php elseif($request['status'] == 'Rejected'): ?>
                                                            <p class="mb-0 mt-2"><strong>Rejection Reason:</strong> <?= esc($request['rejection_reason']) ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Action Form (Unified with Decision Selector) -->
                                                <div class="card card-outline card-primary">
                                                    <div class="card-header bg-gradient-primary">
                                                        <h3 class="card-title font-weight-bold text-white"><i class="fas fa-clipboard-check mr-1"></i> Take Action on Request</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <form action="<?= base_url('processFormDRequest') ?>" method="post" id="actionForm<?= $request['id'] ?>">
                                                            <input type="hidden" name="id" value="<?= $request['id'] ?>">
                                                            
                                                            <!-- Decision Selector -->
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Decision <span class="text-danger">*</span></label>
                                                                <select name="decision" id="decision<?= $request['id'] ?>" class="form-control" required onchange="toggleDecisionFields<?= $request['id'] ?>(this.value)">
                                                                    <option value="">-- Select Decision --</option>
                                                                    <option value="approve" <?= $request['status'] == 'Approved' ? 'selected' : '' ?>>✓ Approve & Assign Inspector</option>
                                                                    <option value="reject" <?= $request['status'] == 'Rejected' ? 'selected' : '' ?>>✗ Reject Request</option>
                                                                </select>
                                                            </div>

                                                            <!-- Approve Fields (Hidden by default) -->
                                                            <div id="approveFields<?= $request['id'] ?>" style="display: <?= $request['status'] == 'Approved' ? 'block' : 'none' ?>;">
                                                                <div class="alert alert-success">
                                                                    <i class="fas fa-check-circle mr-1"></i> <strong>Approval Details</strong>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label>Select Inspector <span class="text-danger">*</span></label>
                                                                    <select name="inspector_id" id="inspector<?= $request['id'] ?>" class="form-control select2" style="width: 100%;">
                                                                        <option value="">-- Choose Inspector --</option>
                                                                        <?php if(!empty($inspectors)): ?>
                                                                            <?php foreach($inspectors as $inspector): ?>
                                                                                <option value="<?= $inspector->id ?>" <?= (isset($request['inspector_id']) && $request['inspector_id'] == $inspector->id) ? 'selected' : '' ?>>
                                                                                    <?= $inspector->first_name . ' ' . $inspector->last_name ?> - <?= $inspector->phone ?? 'No Phone' ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    </select>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Inspection Date <span class="text-danger">*</span></label>
                                                                    <input type="date" name="inspection_date" id="inspectionDate<?= $request['id'] ?>" class="form-control" value="<?= $request['verification_date'] ?? date('Y-m-d') ?>">
                                                                </div>
                                                            </div>

                                                            <!-- Reject Fields (Hidden by default) -->
                                                            <div id="rejectFields<?= $request['id'] ?>" style="display: <?= $request['status'] == 'Rejected' ? 'block' : 'none' ?>;">
                                                                <div class="alert alert-danger">
                                                                    <i class="fas fa-times-circle mr-1"></i> <strong>Rejection Details</strong>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label>Rejection Reason <span class="text-danger">*</span></label>
                                                                    <textarea name="rejection_reason" id="rejectionReason<?= $request['id'] ?>" class="form-control" rows="3" placeholder="Provide a clear reason for rejection..."><?= $request['rejection_reason'] ?? '' ?></textarea>
                                                                </div>
                                                            </div>

                                                            <!-- Comments (Always Visible) -->
                                                            <div class="form-group">
                                                                <label>Additional Comments / Notes</label>
                                                                <textarea name="comments" class="form-control" rows="3" placeholder="Enter any additional comments or notes..."><?= $request['assignment_notes'] ?? '' ?></textarea>
                                                            </div>

                                                            <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold">
                                                                <i class="fas fa-paper-plane mr-1"></i> SUBMIT DECISION
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>

                                                <script>
                                                function toggleDecisionFields<?= $request['id'] ?>(decision) {
                                                    const approveFields = document.getElementById('approveFields<?= $request['id'] ?>');
                                                    const rejectFields = document.getElementById('rejectFields<?= $request['id'] ?>');
                                                    const inspector = document.getElementById('inspector<?= $request['id'] ?>');
                                                    const inspectionDate = document.getElementById('inspectionDate<?= $request['id'] ?>');
                                                    const rejectionReason = document.getElementById('rejectionReason<?= $request['id'] ?>');
                                                    
                                                    if (decision === 'approve') {
                                                        approveFields.style.display = 'block';
                                                        rejectFields.style.display = 'none';
                                                        inspector.required = true;
                                                        inspectionDate.required = true;
                                                        rejectionReason.required = false;
                                                    } else if (decision === 'reject') {
                                                        approveFields.style.display = 'none';
                                                        rejectFields.style.display = 'block';
                                                        inspector.required = false;
                                                        inspectionDate.required = false;
                                                        rejectionReason.required = true;
                                                    } else {
                                                        approveFields.style.display = 'none';
                                                        rejectFields.style.display = 'none';
                                                        inspector.required = false;
                                                        inspectionDate.required = false;
                                                        rejectionReason.required = false;
                                                    }
                                                }
                                                </script>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Approve/Assign Modal -->
                                <div class="modal fade" id="approveModal<?= $request['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('approveFormDRequest') ?>" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Assign Inspector</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $request['id'] ?>">
                                                    <p>Assign an inspector to verify this request.</p>
                                                    
                                                    <div class="form-group">
                                                        <label>Select Inspector</label>
                                                        <select name="inspector_id" class="form-control select2" style="width: 100%;" required>
                                                            <option value="">Select Inspector</option>
                                                            <?php if(!empty($inspectors)): ?>
                                                                <?php foreach($inspectors as $inspector): ?>
                                                                    <option value="<?= $inspector->id ?>"><?= $inspector->first_name . ' ' . $inspector->last_name ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Inspection Date</label>
                                                        <input type="date" name="inspection_date" class="form-control" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Assignment Notes</label>
                                                        <textarea name="assignment_notes" class="form-control" rows="3" placeholder="Enter instructions or notes..."></textarea>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Assign & Approve</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal<?= $request['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('rejectFormDRequest') ?>" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Request</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $request['id'] ?>">
                                                    <p>Are you sure you want to reject this request?</p>
                                                    <div class="form-group">
                                                        <label>Rejection Reason</label>
                                                        <textarea name="rejection_reason" class="form-control" required></textarea>
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

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        // Initialize Select2 Elements
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        // Initialize Select2 for modals when they are shown
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.select2').select2({
                theme: 'bootstrap4',
                dropdownParent: $(this),
                placeholder: '-- Choose Inspector --',
                allowClear: true
            });
        });

        $("#formDTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            "ordering": false // Disable initial ordering to keep PHP sort order if desired, or enable
        });
    });
</script>
<?= $this->endSection(); ?>
