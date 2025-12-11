<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>

<style>
    .disable {
        pointer-events: none;
        cursor: default;
        background: gray;
        border: 1px solid gray;

    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content body">

    <div class="container-fluid">
        <?= Success() ?>
      
    </div>

<div class="card">
    <div class="card-header"><b>SUBMITTED LICENSE APPLICATIONS</b></div>
    <?php if (empty($applications)) : ?>
        <div class="card-body">
            <h6>No Applications Available</h6>
        </div>
    <?php else : ?>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>License Type</th>
                        <th>Region</th>
                        <th>District</th>
                        <th>Date</th>
                        <th>View</th>


                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application) : ?>

                        <tr>
                            <td><?= $application->applicant_name ?></td>
                            <td><?= $application->type ?></td>
                            <td><?= $application->region ?></td>
                            <td><?= $application->district ?></td>
                            <td><?= dateFormatter($application->created_at) ?></td>

                            
                          
                            <td>
                                <a href="<?= base_url('view-application/' . $application->application_id) ?>" class="btn btn-primary btn-xs ">
                                    <i class="fal fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>


                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</div>
</div>
</section>

<?= $this->endSection(); ?>