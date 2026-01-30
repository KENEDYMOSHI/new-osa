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
                <h3 class="card-title">Filter Reports</h3>
            </div>
            <form method="get" action="<?= base_url('reportFormDRequest') ?>">
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
                                <label>Date Range (Start - End)</label>
                                <div class="d-flex">
                                    <input type="date" name="start_date" class="form-control mr-1" value="<?= esc($filters['start_date'] ?? '') ?>">
                                    <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Processed</option>
                                    <option value="Approved" <?= ($filters['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="Rejected" <?= ($filters['status'] ?? '') == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
                    <a href="<?= base_url('reportFormDRequest') ?>" class="btn btn-secondary"> <i class="fas fa-sync"></i> Reset</a>
                </div>
            </form>
        </div>

        <!-- Requests Table -->
        <div class="card">
            <div class="card-body">
                <table id="reportTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Applicant Name</th>
                            <th>License Number</th>
                            <th>Seal Number</th>
                            <th>Company</th>
                            <th>Activities</th>
                            <th>Date Processed</th>
                            <th>Status</th>
                            <th>Original Form D</th>
                            <th>Decision Details</th>
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
                                    <td><?= esc($request['updated_at']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $request['status'] == 'Approved' ? 'success' : 'danger' ?>">
                                            <?= esc($request['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#previewModal<?= $request['id'] ?>" title="View Form D">
                                            <i class="fas fa-file-alt"></i> View
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailsModal<?= $request['id'] ?>" title="View Decision">
                                            <i class="fas fa-info-circle"></i> Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No processed requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals Section (Outside Table) -->
<?php if(!empty($requests)): ?>
    <?php foreach ($requests as $request): ?>
        
        <!-- Preview Modal (Detailed Certificate View) -->
        <div class="modal fade" id="previewModal<?= $request['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form D Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4 font-serif">
                        <!-- Certificate Header -->
                        <div class="text-center mb-4">
                            <h5 class="font-weight-bold mb-0">WEIGHTS AND MEASURES AGENCY</h5>
                            <p class="small font-weight-bold">P.O BOX 313 DAR ES SALAAM</p>
                            <h4 class="font-weight-bold text-uppercase mt-2" style="text-decoration: underline;">FORM D</h4>
                        </div>
                        <!-- Certificate Body -->
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-md-6"><strong>Company:</strong> <?= esc($request['company_name']) ?></div>
                                <div class="col-md-6 text-right"><strong>Date:</strong> <?= date('d M Y', strtotime($request['created_at'])) ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6"><strong>Practitioner:</strong> <?= esc($request['practitioner_name']) ?></div>
                                <div class="col-md-6 text-right"><strong>License No:</strong> <?= esc($request['license_number']) ?></div>
                            </div>
                            <hr>
                            <div class="row mb-3"><div class="col-12"><p>I hereby certify that the under-mentioned instrument has been <strong><?= esc($request['certification_action']) ?></strong>:</p></div></div>
                            <div class="row mb-2">
                                <div class="col-md-4"><strong>Instrument:</strong> <?= esc($request['instrument_name']) ?></div>
                                <div class="col-md-4"><strong>Serial No:</strong> <?= esc($request['serial_number'] ?? 'N/A') ?></div>
                                <div class="col-md-4"><strong>Seal No:</strong> <?= esc($request['seal_number'] ?? 'N/A') ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6"><strong>Capacity/Quantity:</strong> <?= esc($request['capacity'] ?? $request['quantity'] ?? '-') ?></div>
                                <div class="col-md-6"><strong>Location:</strong> <?= esc($request['region']) ?>, <?= esc($request['district']) ?></div>
                            </div>
                            <hr>
                            <div class="row mb-2"><div class="col-12"><p><strong>Declaration:</strong> I/We <?= esc($request['declarant_name'] ?? $request['practitioner_name']) ?> being the user(s) request verification.</p></div></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div class="modal fade" id="detailsModal<?= $request['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-info text-white">
                        <h5 class="modal-title"><i class="fas fa-info-circle mr-2"></i>Decision Details - Request #<?= esc($request['form_d_number'] ?? $request['id']) ?></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert <?= $request['status'] == 'Approved' ? 'alert-success' : 'alert-danger' ?>">
                            <h5 class="mb-0"><i class="fas <?= $request['status'] == 'Approved' ? 'fa-check' : 'fa-times' ?>"></i> Status: <?= esc($request['status']) ?></h5>
                        </div>

                        <?php if($request['status'] == 'Approved'): ?>
                            <div class="card card-outline card-success">
                                <div class="card-header"><h3 class="card-title">Approval Information</h3></div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Assigned Inspector</dt>
                                        <dd class="col-sm-8"><?= !empty($request['inspector_first_name']) ? esc($request['inspector_first_name'] . ' ' . $request['inspector_last_name']) : esc($request['inspector_id']) ?></dd>
                                        <dt class="col-sm-4">Inspection Date</dt>
                                        <dd class="col-sm-8"><?= esc($request['verification_date']) ?></dd>
                                        <dt class="col-sm-4">Assignment Notes</dt>
                                        <dd class="col-sm-8"><?= nl2br(esc($request['assignment_notes'] ?? 'None')) ?></dd>
                                    </dl>
                                </div>
                            </div>
                        <?php elseif($request['status'] == 'Rejected'): ?>
                            <div class="card card-outline card-danger">
                                <div class="card-header"><h3 class="card-title">Rejection Information</h3></div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Rejection Reason</dt>
                                        <dd class="col-sm-8"><?= nl2br(esc($request['rejection_reason'])) ?></dd>
                                        <dt class="col-sm-4">Notes</dt>
                                        <dd class="col-sm-8"><?= nl2br(esc($request['assignment_notes'] ?? 'None')) ?></dd>
                                    </dl>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
<?php endif; ?>

<script>
    $(function () {
        $("#reportTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            "ordering": false 
        });
    });
</script>
<?= $this->endSection(); ?>
