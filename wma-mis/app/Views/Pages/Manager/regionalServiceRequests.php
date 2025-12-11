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
        <?php if ($pageSession->getFlashdata('success')) : ?>
            <?= Success() ?>
    </div>
<?php endif; ?>
<div class="card">
    <div class="card-header"><b>SUBMITTED SERVICE REQUESTS</b></div>
    <?php if (empty($requests)) : ?>
        <div class="card-body">
            <h6>No Requests Available</h6>
        </div>
    <?php else : ?>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Service</th>
                        <th>Region</th>
                        <th>District</th>
                        <th>Ward</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>


                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request) : ?>

                        <tr>
                            <td><?= $request->name ?></td>
                            <td><?= $request->services ?></td>
                            <td><?= $request->region ?></td>
                            <td><?= $request->district ?></td>
                            <td><?= $request->ward ?></td>
                            <td><?= dateFormatter($request->created_at) ?></td>

                            <?php if ($request->status == 1) : ?>
                                <td>

                                    <span class="badge badge-pill badge-success">Seen</span>
                                </td>
                            <?php else : ?>
                                <td>
                                    <span class="badge badge-pill badge-danger">Not Seen</span>
                                </td>
                            <?php endif; ?>
                            <td>
                                <a href="<?= base_url('confirm-service-request/' . $request->id) ?>" class="btn btn-primary btn-xs <?= $request->status == 1 ? 'disable' : '4400' ?>">
                                    <i class="fal fa-check"></i>
                                </a>
                                <a href="<?= base_url('download-service-request/' . $request->id) ?>" class="btn btn-primary btn-xs " target="_blank">
                                    <i class="fal fa-download"></i>
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