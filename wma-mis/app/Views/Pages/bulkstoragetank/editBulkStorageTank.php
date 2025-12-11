<?= $this->extend('layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
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
<!-- /.content-header -->

<!-- Main content -->
<section class="content body">
    <div class="container-fluid">
        <?php if ($pageSession->getFlashdata('Success')) : ?>
        <div id="message" class="alert alert-success text-center" role="alert">
            <?= $pageSession->getFlashdata('Success'); ?>
        </div>
        <?php endif; ?>
        <?php if ($pageSession->getTempdata('error')) : ?>
        <div class="alert alert-danger text-center" role="alert">
            <?= $pageSession->getTempdata('error'); ?>
        </div>
        <?php endif; ?>

        <?= form_open_multipart(base_url('BulkStorageTank/updateBulkStorageTank/' . $record->id)) ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"> <i class="far fa-user icon"></i> Personal Details</h3>
                        <div class="card-tools">
                            <!-- Buttons, labels, and many other things can be placed here! -->
                            <!-- Here is a label for example -->

                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <?= $this->include('Components/personalDetailsFormEdit'); ?>
                    </div>
                    <!-- /.card-body -->

                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </div>

            <!-- Technical details -->
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="far fa-cogs icon"></i>Fuel Pump Technical Details</h3>
                        <div class="card-tools">
                            <!-- Buttons, labels, and many other things can be placed here! -->
                            <!-- Here is a label for example -->

                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <?= $this->include('Components/BulkStorageTank/bstTechnicalDetailsEdit'); ?>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="form-group">

                            <!-- <button class="btn btn-primary"><i class="far fa-save"></i> Save</button> -->
                            <input type="submit" name="submit" value="Update" class="btn btn-primary">
                        </div>
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </div>
        </div>

        <?= form_close() ?>
        <!-- /.card -->


    </div>
    <!-- /.card -->

    </div>
    </div>

</section>

<?= $this->endSection(); ?>