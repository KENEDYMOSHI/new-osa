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
            <form method="get" action="<?= base_url('initialApplicationApproval') ?>">
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
                        <th>Region Approve</th>
                        <th>Surveillance Approval</th>
                        <th>Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $app): ?>
                        <tr>
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
                             <td>
                                <?php 
                                    $rStatus = $app->region_manager_status ?? 'Pending';
                                    if($rStatus == 'Approved'): ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php elseif($rStatus == 'Rejected'): ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php endif; ?>
                             </td>
                             <td>
                                <?php 
                                    $sStatus = $app->surveillance_status ?? 'Pending';
                                    if($sStatus == 'Approved'): ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php elseif($sStatus == 'Rejected'): ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php endif; ?>
                             </td>
                            <td><?= date('d M Y', strtotime($app->applied_date)) ?></td>
                            <td class="text-center">
                                 <a href="<?= base_url('viewApplication/' . ($app->application_id ?? $app->id)) ?>" 
                                    class="btn btn-icon btn-light btn-hover-primary btn-sm" 
                                    title="View"
                                    onclick="event.preventDefault(); window.location.href=this.href; return false;">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <!-- manager -->
    <!-- surveillance -->
    <!--  -->
    <?php if ($user->inGroup('manager')): ?>

    <?php elseif ($user->inGroup('surveillance')): ?>

    <?php else: ?>

    <?php endif; ?>


    <!-- your ui goes hoere -->

</div>
</div>


<script>
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