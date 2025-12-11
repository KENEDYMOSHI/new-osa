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
                            <?php if ($scaleResults) : ?>
                            <table id="example1" class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th class="head">Full Name</th>
                                        <th class="head">Gender</th>
                                        <th class="head">City</th>
                                        <th class="head">District</th>
                                        <th class="head">Ward</th>
                                        <th class="head">Postal Address</th>
                                        <th class="head">Phone Number</th>
                                        <th class="head">Date</th>
                                        <th class="head">Scale Type</th>
                                        <th class="head">Scale Capacity</th>
                                        <th class="head">Quantity</th>
                                        <th class="head">Sets Of Weight</th>
                                        <th class="head">Verification Capacity</th>
                                        <th class="head">Sticker Number</th>
                                        <th class="head">Control Number</th>
                                        <th class="head" id="amount">Amount</th>
                                        <th class="head">Payment</th>
                                        <th class="head">Status</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($scaleResults as $scale) : ?>
                                    <tr>
                                        <td><?= $scale->first_name . '  ' . $scale->last_name ?></td>
                                        <td><?= $scale->gender ?></td>
                                        <td><?= $scale->city ?></td>
                                        <td><?= $scale->district ?></td>
                                        <td><?= $scale->ward ?></td>
                                        <td><?= $scale->postal_address ?></td>
                                        <td><?= $scale->phone_number ?></td>
                                        <td><?= date("d M Y", strtotime($scale->date)) ?></td>
                                        <td>
                                            <?php if ($scale->trade_scale_type) : ?>
                                            <?= $scale->trade_scale_type ?>
                                            <?php elseif ($scale->stone_scale_type) : ?>
                                            <?= $scale->stone_scale_type ?>
                                            <?php else : ?>
                                            <?= $scale->lab_scale_type ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($scale->trade_scale_capacity) : ?>
                                            <?= $scale->trade_scale_capacity ?>
                                            <?php elseif ($scale->stone_scale_capacity) : ?>
                                            <?= $scale->stone_scale_capacity ?>
                                            <?php else : ?>
                                            <?= $scale->lab_scale_capacity ?>
                                            <?php endif; ?>
                                            Kilograms
                                        </td>
                                        <td>
                                            <?php if ($scale->trade_scale_quantity) : ?>
                                            <?= $scale->trade_scale_quantity ?>
                                            <?php elseif ($scale->stone_scale_quantity) : ?>
                                            <?= $scale->stone_scale_quantity ?>
                                            <?php else : ?>
                                            <?= $scale->lab_scale_quantity ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($scale->lower_class_sets) : ?>
                                            <?= $scale->trade_lower_class_sets ?>
                                            <?php elseif ($scale->higher_class_sets) : ?>
                                            <?= $scale->higher_class_sets ?>
                                            <?php else : ?>
                                            <?= $scale->metric_sets ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($scale->customer_capacity) : ?>
                                            <?= $scale->customer_capacity ?>

                                            <?php else : ?>
                                            <?= $scale->verification_capacity ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $scale->sticker_number ?></td>

                                        <td>
                                            <?= $scale->control_number ?>

                                        </td>

                                        <td>
                                            <?= $scale->amount ?>

                                        </td>
                                        <td>
                                            <?php if ($scale->payment == 'Paid') : ?>
                                            <span class="badge badge-primary">Paid</span>
                                            <?php else : ?>
                                            <span class="badge badge-danger">Pending</span>
                                            <?php endif; ?>

                                        </td>


                                        <td>
                                            <?php if ($scale->status == 'Pass') : ?>
                                            <span class="badge badge-success">Pass</span>
                                            <?php else : ?>
                                            <span class="badge badge-warning">Rejected</span>
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
                                        <th>District</th>
                                        <th>Ward</th>
                                        <th>Postal Address</th>
                                        <th>Phone Number</th>
                                        <th>Date</th>
                                        <th>Scale Type</th>
                                        <th>Scale Capacity</th>
                                        <th>Quantity</th>
                                        <th>Sets Of Weighs</th>
                                        <th>Measuring Capacity</th>
                                        <th>Sticker Number</th>
                                        <th>Control Number</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                        <th>Status</th>

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