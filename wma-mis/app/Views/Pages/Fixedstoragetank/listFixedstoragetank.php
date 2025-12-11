<?= $this->extend('layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1 class="m-0 text-dark"></h1> -->
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <?php if ($role == 3) : ?>
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/fullReport">Full Report</a></li>
                    <?php else : ?>
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <?php endif; ?>
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
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <?php if ($pageSession->getFlashdata('Success')) : ?>
                    <div id="message" class="alert alert-success text-center" role="alert">
                        <?= $pageSession->getFlashdata('Success'); ?>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= $page['heading'] ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <h4>Total Amount: <span class="total"></span></h4>


                            <?php if ($FixedStorageTankResults) : ?>
                            <table id="example1" class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th class="head">Full Name</th>
                                        <th class="head">Gender</th>
                                        <th class="head">City</th>
                                        <th class="head">Ward</th>
                                        <th class="head">Postal Address</th>
                                        <th class="head">Phone Number</th>
                                        <th class="head">Calibrated On</th>
                                        <th class="head">Next Calibration</th>
                                        <th class="head">Filling Station</th>
                                        <th class="head">Number Of Tanks</th>
                                        <th class="head">Vehicle Tank Capacity</th>
                                        <th class="head">Product</th>
                                        <th class="head">Status</th>
                                        <th class="head">Control Number</th>
                                        <th class="head" id="amount">Amount</th>
                                        <th class="head">Payment</th>

                                        <?php if ($role == 1 || $role == 2) : ?>

                                        <th class="head">Action</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($FixedStorageTankResults as $fst) : ?>
                                    <tr>
                                        <td><?= $fst->first_name . '  ' . $fst->last_name ?></td>
                                        <td><?= $fst->gender ?></td>
                                        <td><?= $fst->region ?></td>
                                        <td><?= $fst->ward ?></td>
                                        <td><?= $fst->postal_address ?></td>
                                        <td><?= $fst->phone_number ?></td>
                                        <td><?= dateFormatter($fst->date) ?></td>
                                        <td><?= $fst->next_calibration ?></td>
                                        <td><?= $fst->filling_station ?></td>
                                        <td><?= $fst->number_of_tanks ?></td>
                                        <td><?= $fst->capacity ?>Litters</td>
                                        <td><?= $fst->product ?></td>
                                        <td>
                                            <?php if ($fst->status == 'Valid') : ?>
                                            <span class="badge badge-success">Valid</span>
                                            <?php else : ?>
                                            <span class="badge badge-warning">Not Valid</span>
                                            <?php endif; ?>

                                        </td>
                                        <td><?= $fst->control_number ?></td>
                                        <td><?= $fst->amount ?></td>
                                        <td><?= $fst->payment ?></td>


                                        <?php if ($role == 1 || $role == 2) : ?>

                                        <td>
                                            <a href="<?= base_url() ?>/editFixedStorageTank/<?= $fst->id ?>"
                                                class="btn btn-primary btn-sm"><i class="far fa-edit"></i></a>
                                            <a href="<?= base_url() ?>/deleteFixedStorageTank/<?= $fst->id ?>"
                                                class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>
                                        </td>
                                        <?php endif; ?>



                                    </tr>
                                    <?php endforeach; ?>


                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Gender</th>
                                        <th>City</th>
                                        <th>Ward</th>
                                        <th>Postal Address</th>
                                        <th>Phone Number</th>
                                        <th>Calibrated On</th>
                                        <th>Next Calibration</th>
                                        <th>Filling Station</th>
                                        <th>Number Of Tanks</th>
                                        <th>Vehicle Tank Capacity</th>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Control Number</th>
                                        <th id="amount">Amount</th>
                                        <th>Payment</th>

                                        <?php if ($role == 1 || $role == 2) : ?>
                                        <th>Action</th>

                                        <?php endif; ?>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php else : ?>
                            <h3>There Are No Records Currently Available</h3>
                            <?php endif; ?>
                            <!-- <table id=" example1" class="my-table " border="1"> -->

                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>

    </div>
    <!-- /.card -->

    </div>
    </div>

</section>


<?= $this->endSection(); ?>