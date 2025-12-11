<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/AdminDashboard">Dashboard</a></li>
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
        <?php if ($pageSession->getFlashdata('Success')) : ?>
            <div id="message" class="alert alert-success text-center" role="alert">
                <?= $pageSession->getFlashdata('Success'); ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"> <i class="far fa-user x-3"></i><?= $user->first_name . ' ' . $user->last_name ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <?= form_open(base_url() . '/admin/updateUser/' . $user->unique_id) ?>

                <div class="form-group">
                    <label for="">user Role</label>
                    <select class="form-control" name="role" id="">
                        <option selected disabled>Select Role</option>
                        <option value="7">Admin</option>
                        <option value="3">Head Of Section</option>
                        <option value="2">Manager</option>
                        <option value="1">Officer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="">Region</label>
                    <select class="form-control select2bs4" name="region">
                        <option disabled selected>Select Region</option>
                        <?php foreach (renderRegions() as $region) : ?>
                            <option value="<?= $region['region'] ?>"><?= $region['region'] ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>
                <div class="form-group">
                    <label for="">Password</label>
                    <input type="new-password" autocomplete="off" class="form-control" name="password" id="" placeholder="New Password">

                </div>
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                <?= form_close() ?>
            </div>
            <!-- /.card-body -->
        </div>


    </div>
    <!-- /.card -->

    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable();
        });
    </script>


</section>

<?= $this->endSection(); ?>