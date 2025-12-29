<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Filter Card -->
    <div class="card">
        <div class="card-header bg-white">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Applications</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('examRemark') ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-user mr-1"></i> Applicant Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter name" value="<?= $filters['name'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt mr-1"></i> Region</label>
                            <select class="form-control select2" name="region" style="width: 100%;">
                                <option value="" selected>Select Region</option>
                                <option <?= ($filters['region'] ?? '') == 'Dar es Salaam' ? 'selected' : '' ?>>Dar es Salaam</option>
                                <option <?= ($filters['region'] ?? '') == 'Arusha' ? 'selected' : '' ?>>Arusha</option>
                                <option <?= ($filters['region'] ?? '') == 'Mwanza' ? 'selected' : '' ?>>Mwanza</option>
                                <option <?= ($filters['region'] ?? '') == 'Dodoma' ? 'selected' : '' ?>>Dodoma</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-id-card mr-1"></i> License Type</label>
                            <select class="form-control select2" name="license_type" style="width: 100%;">
                                <option value="" selected>Select License Type</option>
                                <option <?= ($filters['license_type'] ?? '') == 'Class A' ? 'selected' : '' ?>>Class A</option>
                                <option <?= ($filters['license_type'] ?? '') == 'Class B' ? 'selected' : '' ?>>Class B</option>
                                <option <?= ($filters['license_type'] ?? '') == 'Class C' ? 'selected' : '' ?>>Class C</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-calendar mr-1"></i> Year</label>
                            <select class="form-control select2" name="year" style="width: 100%;">
                                <option value="" selected>Select Year</option>
                                <option <?= ($filters['year'] ?? '') == '2025' ? 'selected' : '' ?>>2025</option>
                                <option <?= ($filters['year'] ?? '') == '2026' ? 'selected' : '' ?>>2026</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="far fa-calendar-alt mr-1"></i> Date Range</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control float-right" name="dateRange" id="dateRange" autocomplete="off" value="<?= $filters['dateRange'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <div class="form-group w-100 text-right">
                             <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /.card -->

    <!-- Applications List Card -->
    <div class="card">
        <div class="card-header bg-white border-0">
            <h3 class="card-title">Applications List</h3>
            <div class="card-tools">
                <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-download"></i> Export
                </a>
                <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-bars"></i>
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
            <table id="approvalTable" class="table table-hover table-bordered table-striped text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 10px">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="selectAll">
                                <label for="selectAll" class="custom-control-label"></label>
                            </div>
                        </th>
                        <th>Applicant Name</th>
                        <th>License Type</th>
                        <th>Region</th>
                        <th>Theory</th>
                        <th>Practical</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $app): ?>
                        <tr id="row-<?= $app->id ?>">
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="check<?= $app->id ?>">
                                        <label for="check<?= $app->id ?>" class="custom-control-label"></label>
                                    </div>
                                </td>
                                <td>
                                    <?= $app->first_name ?> <?= $app->last_name ?>
                                    <?php if (!empty($app->company_name)): ?>
                                        <br><small class="text-muted"><?= $app->company_name ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= $app->license_type ?></td>
                                <td><?= $app->region ?></td>
                                
                                <!-- Theory Score -->
                                <td>
                                    <span class="view-mode-<?= $app->id ?>"><?= $app->theory_score == 0 ? '-' : (float)$app->theory_score ?></span>
                                    <input type="number" 
                                           class="form-control form-control-sm edit-mode-<?= $app->id ?> d-none" 
                                           id="theory-<?= $app->id ?>"
                                           value="<?= (float)$app->theory_score ?>" 
                                           step="0.01" 
                                           style="width: 80px;"
                                           oninput="calculateTotal('<?= $app->id ?>')">
                                </td>
                                
                                <!-- Practical Score -->
                                <td>
                                    <span class="view-mode-<?= $app->id ?>"><?= $app->practical_score == 0 ? '-' : (float)$app->practical_score ?></span>
                                    <input type="number" 
                                           class="form-control form-control-sm edit-mode-<?= $app->id ?> d-none" 
                                           id="practical-<?= $app->id ?>"
                                           value="<?= (float)$app->practical_score ?>" 
                                           step="0.01" 
                                           style="width: 80px;"
                                           oninput="calculateTotal('<?= $app->id ?>')">
                                </td>
                                
                                <!-- Total Score (Auto-calculated) -->
                                <td>
                                    <span id="total-display-<?= $app->id ?>"><?= $app->total_score == 0 ? '-' : (float)$app->total_score ?></span>
                                </td>

                                <!-- Status (Auto-calculated) -->
                                <td>
                                    <?php 
                                        $theory = $app->theory_score;
                                        $practical = $app->practical_score;
                                        
                                        if ($theory !== null || $practical !== null) {
                                            $totalScore = ((float)$theory) + ((float)$practical);
                                            $status = $totalScore >= 50 ? 'PASS' : 'FAIL';
                                            $badgeClass = $totalScore >= 50 ? 'badge-success' : 'badge-danger';
                                        } else {
                                            $status = '-';
                                            $badgeClass = '';
                                        }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>" id="status-badge-<?= $app->id ?>"><?= $status ?></span>
                                </td>

                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-icon btn-sm btn-light btn-hover-primary" 
                                            id="action-btn-<?= $app->id ?>"
                                            onclick="handleAction('<?= $app->id ?>')"
                                            title="Edit">
                                        <i class="fas fa-edit text-primary" id="action-icon-<?= $app->id ?>"></i>
                                    </button>
                                </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

</div>

<!-- Hidden Form for submission -->
<form id="submissionForm" action="<?= base_url('saveExamRemark') ?>" method="post" style="display: none;">
    <input type="hidden" name="application_id" id="form-application-id">
    <input type="hidden" name="theory_score" id="form-theory-score">
    <input type="hidden" name="practical_score" id="form-practical-score">
</form>

<script>
    function handleAction(id) {
        var btn = $('#action-btn-' + id);
        var icon = $('#action-icon-' + id);
        var isEditing = !$('.edit-mode-' + id).first().hasClass('d-none');

        if (isEditing) {
            // Currently editing, so this is a SAVE action
            saveRow(id);
        } else {
            // Currently viewing, so this is an EDIT action
             $('.view-mode-' + id).addClass('d-none');
             $('.edit-mode-' + id).removeClass('d-none');
             
             // Change icon to Save
             btn.removeClass('btn-light btn-hover-primary').addClass('btn-success');
             icon.removeClass('fa-edit text-primary').addClass('fa-save text-white');
             btn.attr('title', 'Save Changes');
        }
    }

    function calculateTotal(id) {
        var theoryInput = $('#theory-' + id).val();
        var practicalInput = $('#practical-' + id).val();
        
        // If both inputs are empty, clear status
        if (theoryInput === '' && practicalInput === '') {
            $('#total-display-' + id).text('-');
            $('#status-badge-' + id).removeClass('badge-success badge-danger').text('-');
            return;
        }

        var theory = parseFloat(theoryInput) || 0;
        var practical = parseFloat(practicalInput) || 0;
        
        var total = theory + practical;
        
        // Remove decimals if whole number
        var displayTotal = (total % 1 === 0) ? total : total.toFixed(2);
        
        // Update Total Display
        $('#total-display-' + id).text(displayTotal);
        
        // Update Status Badge
        var badge = $('#status-badge-' + id);
        if (total >= 50) {
            badge.removeClass('badge-danger').addClass('badge-success').text('PASS');
        } else {
            badge.removeClass('badge-success').addClass('badge-danger').text('FAIL');
        }
    }

    function saveRow(id) {
        var theory = $('#theory-' + id).val();
        var practical = $('#practical-' + id).val();
        
        $('#form-application-id').val(id);
        $('#form-theory-score').val(theory);
        $('#form-practical-score').val(practical);
        
        $('#submissionForm').submit();
    }

    $(document).ready(function() {
        // Initialize DataTables
        $('#approvalTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true
        });

        // Select All Checkbox functionality
        $('#selectAll').change(function() {
            var isChecked = $(this).prop('checked');
            $('#approvalTable tbody input[type="checkbox"]').prop('checked', isChecked);
        });

        // Initialize Date Range Picker
        $('#dateRange').daterangepicker();
    });
</script>

<?= $this->endSection(); ?>