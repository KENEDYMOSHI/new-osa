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
                    <li class="breadcrumb-item active"><?= $page['heading']  ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<script>

</script>
<!-- Main content -->
<section class="content body">
    <di class="container-fluid">

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
        <?= $this->include('widgets/customerOptions.php') ?>
        <?= $this->include('components/Customers') ?>


        <?= form_open_multipart() ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="selectedCustomerDetails"></div>

                </div>
                <div class="card">

                    <ul class="list-group" id="customerScaleList">

                    </ul>

                    <div id="totalAmount" class="list-group-item"></div>
                </div>
                <!-- /.card -->
            </div>

            <!-- Technical details -->
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="far fa-cogs icon"></i>Scale Technical Details</h3>
                        <div class="card-tools">
                            <!-- Buttons, labels, and many other things can be placed here! -->
                            <!-- Here is a label for example -->

                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">

                            <input id="customerHash" class="form-control" type="text" name="customer_hash" hidden>
                        </div>
                        <button type="button" id="addScale" class="btn btn-info mb-2" data-toggle="modal"
                            data-target="#newScale">
                            <i class="far fa-plus"></i> Add New Scale
                        </button>
                        <!-- Technical details foe the scale -->
                        <?= $this->include('Components/Scales/scaleTechnicalDetails'); ?>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="form-group">

                            <!-- <button class="btn btn-primary"><i class="far fa-save"></i> Save</button> -->
                            <input type="submit" name="submit" value="Save" class="btn btn-primary">
                        </div>
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        </form>
        <?= form_close(); ?>
        <!-- /.card -->


        </div>
        <!-- /.card -->

        </div>
        </div>

</section>
<!-- New Customer -->
<div class="modal fade" id="newScale">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Scale</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $this->include('Components/Scales/AddScale') ?>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="addNewScale" class="btn btn-primary">Add</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<?= $this->endSection(); ?>