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
                        <h5 class="modal-title">Form D Request Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4" style="background: #e9ecef; overflow-y: auto; max-height: 80vh;">
                        
                        <style>
                            /* Scoped Styles for Form D Paper */
                            #previewModal<?= $request['id'] ?> .form-d-paper {
                                font-family: 'Times New Roman', Times, serif;
                                color: #000;
                                background: #fff;
                                padding: 40px;
                                margin: 0 auto;
                                max-width: 800px;
                                box-shadow: 0 0 15px rgba(0,0,0,0.1);
                            }
                            #previewModal<?= $request['id'] ?> .form-header { text-align: center; margin-bottom: 20px; }
                            #previewModal<?= $request['id'] ?> .form-header h1 { font-size: 16px; font-weight: bold; margin: 0 0 5px 0; text-transform: uppercase; letter-spacing: 0.5px; }
                            #previewModal<?= $request['id'] ?> .form-header p { font-size: 11px; font-weight: bold; margin: 0; }
                            #previewModal<?= $request['id'] ?> .wma-logo { height: 80px; margin: 15px auto; display: block; }
                            
                            #previewModal<?= $request['id'] ?> .form-title-section { text-align: center; margin-bottom: 30px; }
                            #previewModal<?= $request['id'] ?> .form-title-section h2 { font-size: 18px; font-weight: bold; text-decoration: underline; margin: 0 0 5px 0; }
                            #previewModal<?= $request['id'] ?> .form-title-section p { font-size: 12px; font-weight: bold; margin: 0; text-transform: uppercase; }
                            #previewModal<?= $request['id'] ?> .form-subtitle { font-size: 11px; font-style: italic; font-weight: bold; margin-top: 5px; }

                            #previewModal<?= $request['id'] ?> .form-row { display: flex; align-items: baseline; margin-bottom: 12px; font-size: 13px; flex-wrap: wrap; }
                            #previewModal<?= $request['id'] ?> .form-label { font-weight: normal; margin-right: 10px; white-space: nowrap; }
                            #previewModal<?= $request['id'] ?> .form-value { 
                                border-bottom: 1px dotted #000; 
                                font-weight: bold; 
                                padding: 0 5px; 
                                flex-grow: 1; 
                                min-width: 50px; 
                            }
                            #previewModal<?= $request['id'] ?> .form-value-fixed {
                                border-bottom: 1px dotted #000;
                                font-weight: bold;
                                padding: 0 5px;
                                display: inline-block;
                            }

                            #previewModal<?= $request['id'] ?> .certify-text { margin: 25px 0; font-size: 13px; }
                            
                            #previewModal<?= $request['id'] ?> .actions-group { display: flex; justify-content: center; gap: 40px; margin: 20px 0; font-weight: bold; font-size: 13px; }
                            #previewModal<?= $request['id'] ?> .action-check { display: flex; align-items: center; }
                            #previewModal<?= $request['id'] ?> .tick-mark { font-size: 18px; margin-right: 5px; min-width: 20px; }

                            #previewModal<?= $request['id'] ?> .footer-certify-text { margin: 25px 0; font-size: 12px; text-align: justify; line-height: 1.4; }

                            #previewModal<?= $request['id'] ?> .auth-section { margin-top: 30px; border-top: 1px dotted #ccc; padding-top: 20px; }
                            #previewModal<?= $request['id'] ?> .auth-header { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555; margin-bottom: 10px; }
                            
                            #previewModal<?= $request['id'] ?> .inspection-report { background: #f8f9fa; padding: 15px; margin-top: 30px; border: 1px solid #ddd; }
                            #previewModal<?= $request['id'] ?> .inspection-report h5 { font-size: 13px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
                        </style>

                        <div class="form-d-paper">
                            <!-- Header -->
                            <div class="form-header">
                                <h1>WEIGHTS AND MEASURES AGENCY</h1>
                                <p>P.O BOX 313 DAR ES SALAAM</p>
                                <img src="<?= base_url('assets/images/wma1.png') ?>" alt="WMA Logo" class="wma-logo">
                            </div>

                            <!-- Form Title -->
                            <div class="form-title-section">
                                <h2>FORM D</h2>
                                <p>FORM OF CERTIFICATE TO BE USED BY A PUMP MECHANIC</p>
                                <p>AFTER SEALED/RE-SEALED</p>
                                <div class="form-subtitle">(Made under Regulation 12(d))</div>
                            </div>

                            <!-- Practitioner Info -->
                            <div class="form-row">
                                <span class="form-label">Company employing mechanic:</span>
                                <span class="form-value"><?= strtoupper($request['company_name'] ?? '') ?></span>
                            </div>
                            <div class="form-row">
                                <span class="form-label">License No:</span>
                                <span class="form-value-fixed" style="width: 200px; margin-right: 20px;"><?= $request['license_number'] ?? '' ?></span>
                                
                                <span class="form-label">Phone:</span>
                                <span class="form-value"><?= $request['practitioner_phone'] ?? '' ?></span>
                            </div>

                            <div class="certify-text">
                                I hereby certify that the under- mentioned liquid measuring pump has been
                            </div>

                            <!-- Actions -->
                            <div class="actions-group">
                                <div class="action-check">
                                    <span class="tick-mark"><?= ($request['certification_action'] ?? '') == 'Erected' ? '✔' : '&nbsp;&nbsp;' ?></span> *Erected
                                </div>
                                <div class="action-check">
                                    <span class="tick-mark"><?= ($request['certification_action'] ?? '') == 'Adjusted' ? '✔' : '&nbsp;&nbsp;' ?></span> Adjusted
                                </div>
                                <div class="action-check">
                                    <span class="tick-mark"><?= ($request['certification_action'] ?? '') == 'Repaired' ? '✔' : '&nbsp;&nbsp;' ?></span>  <span class="font-weight-bold">✓ Repaired</span>
                                </div>
                            </div>
                            <div style="text-align: center; font-style: italic; font-size: 10px; margin-bottom: 20px;">(*Delete where not applicable)</div>

                            <!-- Details -->
                            <div class="form-row">
                                <span class="form-label">By me and sealed with my seal No:</span>
                                <span class="form-value" style="flex-grow: 0; min-width: 100px;"><?= $request['seal_number'] ?? '' ?></span>
                            </div>
                            
                            <div class="form-row">
                                <span class="form-label">Name of user of pump:</span>
                                <span class="form-value"><?= strtoupper($request['company_name'] ?? '') ?></span>
                            </div>

                            <div class="form-row">
                                <span class="form-label">Location:</span>
                                <?php $loc = implode(', ', array_filter([$request['region'] ?? '', $request['district'] ?? '', $request['ward'] ?? ''])); ?>
                                <span class="form-value"><?= $loc ?></span>
                            </div>

                            <div class="form-row">
                                <span class="form-label">Make and type of pump:</span>
                                <span class="form-value"><?= $request['instrument_name'] ?? '' ?> (<?= $request['type_of_instrument'] ?? '' ?>)</span>
                            </div>

                            <div class="form-row">
                                <span class="form-label" style="width: 100px;">Product:</span>
                                <span class="form-value" style="margin-right: 20px;"><?= $request['product'] ?? 'N/A' ?></span>
                                
                                <span class="form-label">Capacity/Nozzles:</span>
                                <span class="form-value"><?= ($request['capacity'] ?? '') . ' ' . ($request['capacity_unit'] ?? '') ?></span>
                            </div>

                            <div class="form-row">
                                <span class="form-label" style="width: 100px;">Serial No:</span>
                                <span class="form-value" style="margin-right: 20px;"><?= $request['serial_number'] ?? 'N/A' ?></span>
                                
                                <span class="form-label">Sticker No:</span>
                                <span class="form-value"><?= $request['sticker_number'] ?? 'N/A' ?></span>
                            </div>

                            <div class="form-row">
                                <span class="form-label" style="width: 130px;">Date of sealing:</span>
                                <span class="form-value"><?= isset($request['verification_date']) ? date('F d, Y', strtotime($request['verification_date'])) : '' ?></span>
                            </div>
                            
                            <div class="form-row">
                                <span class="form-label" style="width: 130px;">Next Verification Date:</span>
                                <span class="form-value"><?= isset($request['next_verification_date']) ? date('F d, Y', strtotime($request['next_verification_date'])) : '' ?></span>
                            </div>

                            <div class="footer-certify-text">
                                I further certify that the above pump was fully tested against approved stamped measures and found correct within the permitted limits of error before sealed.
                            </div>

                            <div class="form-row" style="justify-content: flex-end;">
                                <span class="form-label">Certificate of Authorization No:</span>
                                <span class="form-value-fixed" style="width: 200px; text-align: center;"><?= $request['cert_auth_number'] ?? 'Quisquam ducimus te' ?></span> 
                            </div>

                            <!-- Declarant -->
                            <div class="form-row" style="margin-top: 30px;">
                                <span class="form-label">I / We</span>
                                <span class="form-value-fixed" style="width: 250px;"><?= $request['declarant_name'] ?? 'Veniam nulla minim' ?></span>
                            </div>
                            <div class="form-row">
                                <span class="form-label">Designation:</span>
                                <span class="form-value" style="margin-right: 20px;"></span>
                                <span class="form-label">Phone Number:</span>
                                <span class="form-value">---</span>
                            </div>
                            <div style="font-size: 11px; margin-top: 5px; margin-bottom: 20px; line-height: 1.3;">
                                Being the user(s) for trade purposes of the liquid measuring pump described above, which has been sealed/re-sealed by the pump mechanic, request the Inspector of Weights and Measures that arrangements may be made for its verification.
                            </div>

                            <!-- Official Verification (Bottom) -->
                            <div class="auth-section">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="auth-header">VERIFIED / APPROVED BY:</div>
                                        <div style="font-weight: bold; font-size: 14px; text-transform: uppercase;">
                                            <?php if (($request['status'] ?? '') === 'Approved'): ?>
                                                <?= !empty($request['inspector_first_name']) ? esc($request['inspector_first_name'] . ' ' . $request['inspector_last_name']) : 'MUSHI KASSIM' ?>
                                            <?php else: ?>
                                                ______________________
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-size: 11px; color: #666;">Inspector of Weights and Measures</div>
                                        
                                        <div class="form-row" style="margin-top: 15px;">
                                            <span class="form-label">Date:</span>
                                            <span class="form-value-fixed" style="width: 150px;">
                                                <?= (($request['status'] ?? '') === 'Approved' && isset($request['verification_date'])) ? date('d/m/Y', strtotime($request['verification_date'])) : '________________' ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-4 text-right">
                                        <div class="auth-header">DATE:</div>
                                        <div style="font-weight: bold; font-size: 14px;"><?= date('d M Y') ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Inspection Details -->
                            <div class="inspection-report">
                                <div class="auth-header" style="color: #2c3e50; font-size: 12px;">ADDITIONAL INSPECTION DETAILS</div>
                                <h5>Inspection Report:</h5>
                                <div style="font-size: 13px; background: #fff; padding: 10px; border: 1px solid #eee; border-radius: 4px;">
                                    <?= !empty($request['assignment_notes']) ? nl2br(esc($request['assignment_notes'])) : 'No specific report remarks.' ?>
                                </div>
                            </div>

                        </div> <!-- End form-d-paper -->
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                        <button onclick="printApplication('<?= base_url('printFormDRequest/' . $request['id']) ?>')" class="btn btn-outline-success">
                            <i class="fas fa-file-pdf mr-1"></i> Download PDF
                        </button>
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
<!-- Script for Printing -->
<script>
function printApplication(url) {
    var printWindow = window.open(url, '_blank');
    if (printWindow) {
        printWindow.focus();
    } else {
        alert('Please allow popups to print/download.');
    }
}
</script>

<?= $this->endSection(); ?>
