<?= $this->extend('Layouts/coreLayout'); ?>
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

                            <?php if ($pumpResults) : ?>
                            <table id="example1" class="table table-bordered ">
                                <h4>Total Amount <span class="total"></span></h4>
                                <thead>
                                    <tr>
                                        <th class="head">Full Name</th>
                                        <th class="head">Gender</th>
                                        <th class="head">City</th>
                                        <th class="head">Ward</th>
                                        <th class="head">Postal Address</th>
                                        <th class="head">Phone Number</th>
                                        <th class="head">Date</th>
                                        <th class="head">Petrol Station</th>
                                        <th class="head">Product</th>
                                        <th class="head">Fuel Pump Type</th>
                                        <th class="head">Capacity</th>
                                        <th class="head">Number Of Fuel Nozzles</th>
                                        <th class="head">Status</th>
                                        <th class="head">Sticker Number</th>
                                        <th class="head">Control Number</th>
                                        <th class="head" id="amount">Amount</th>
                                        <th class="head">Payment</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($pumpResults as $pump) : ?>
                                    <tr>
                                        <td><?= $pump->first_name . '  ' . $pump->last_name ?></td>
                                        <td><?= $pump->gender ?></td>
                                        <td><?= $pump->region ?></td>
                                        <td><?= $pump->ward ?></td>
                                        <td><?= $pump->postal_address ?></td>
                                        <td><?= $pump->phone_number ?></td>
                                        <td><?= date("d M Y", strtotime($pump->date)) ?></td>
                                        <td><?= $pump->petrol_station ?></td>
                                        <td><?= $pump->product ?></td>
                                        <td><?= $pump->pump_type ?></td>
                                        <td><?= $pump->capacity . ' ' ?>Liters</td>
                                        <td><?= $pump->dispensers . ' ' ?>Dispensers</td>

                                        <td>
                                            <?php if ($pump->status == 'Pass') : ?>
                                            <span class="badge badge-success">Pass</span>
                                            <?php else : ?>
                                            <span class="badge badge-warning">Rejected</span>
                                            <?php endif; ?>



                                        </td>



                                        <td><?php if ($pump->sticker_number) : ?>
                                            <?= $pump->sticker_number ?>
                                            <?php else : ?>
                                            None
                                            <?php endif; ?></td>

                                        <td>
                                            <?= $pump->control_number ?>
                                        </td>
                                        <td>
                                            <?= $pump->amount ?>
                                        </td>
                                        <td>
                                            <?php if ($pump->payment == 'Paid') : ?>
                                            <span class="badge badge-primary">Paid</span>
                                            <?php else : ?>
                                            <span class="badge badge-danger">Pending</span>
                                            <?php endif; ?>
                                        </td>





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
                                        <th>Date</th>
                                        <th>Petrol Station</th>
                                        <th>Product</th>
                                        <th>Fuel Pump Type</th>
                                        <th>Capacity</th>
                                        <th>Number Of Fuel Dispensers</th>
                                        <th>Status</th>
                                        <th>Sticker Number</th>
                                        <th>Control Number</th>
                                        <th>Amount</th>
                                        <th>Payment</th>

                                    </tr>


                                </tfoot>
                            </table> <?php else : ?>
                            <h3>There Are No Records Currently Available</h3>
                            <?php endif; ?>
                            <!-- <table id="example1" class="my-table " border="1"> -->

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