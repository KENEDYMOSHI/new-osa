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

                            <?php if ($FlowMeterResults) : ?>
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
                                        <th class="head">Oil Company</th>
                                        <th class="head">Flow Meter Type</th>
                                        <th class="head">Model Number</th>
                                        <th class="head">Serial Number</th>
                                        <th class="head">Flow Rete</th>
                                        <th class="head">Product</th>
                                        <th class="head">Standard Capacity</th>
                                        <th class="head">Status</th>
                                        <th class="head">Sticker Number</th>
                                        <th class="head">Control Number</th>
                                        <th class="head" id="amount">Amount</th>
                                        <th class="head">Payment</th>
                                        <?php if ($role == 1 || $role == 2) : ?>

                                        <th class="head">Action</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($FlowMeterResults as $FlowMeter) : ?>
                                    <tr>
                                        <td><?= $FlowMeter->first_name . '  ' . $FlowMeter->last_name ?></td>
                                        <td><?= $FlowMeter->gender ?></td>
                                        <td><?= $FlowMeter->region ?></td>
                                        <td><?= $FlowMeter->ward ?></td>
                                        <td><?= $FlowMeter->postal_address ?></td>
                                        <td><?= $FlowMeter->phone_number ?></td>
                                        <td><?= dateFormatter($FlowMeter->date) ?></td>
                                        <td><?= $FlowMeter->oil_company ?></td>
                                        <td><?= $FlowMeter->flow_meter_type ?></td>
                                        <td><?= $FlowMeter->model_number ?></td>
                                        <td><?= $FlowMeter->serial_number ?></td>
                                        <td><?= $FlowMeter->flow_rate . ' ' ?>Liters</td>
                                        <td><?= $FlowMeter->product ?></td>
                                        <td><?= $FlowMeter->standard_capacity . ' ' ?>Liters</td>

                                        <td>
                                            <?php if ($FlowMeter->status == 'Pass') : ?>
                                            <span class="badge badge-success">Pass</span>
                                            <?php elseif ($FlowMeter->status == 'Rejected') : ?>
                                            <span class="badge badge-warning">Rejected</span>
                                            <?php else : ?>
                                            <span class="badge badge-danger">Condemned</span>
                                            <?php endif; ?>

                                        </td>



                                        <td><?php if ($FlowMeter->sticker_number) : ?>
                                            <?= $FlowMeter->sticker_number ?>
                                            <?php else : ?>
                                            None
                                            <?php endif; ?></td>

                                        <td>
                                            <?= $FlowMeter->control_number ?>
                                        </td>
                                        <td>
                                            <?= $FlowMeter->amount ?>
                                        </td>
                                        <td>
                                            <?php if ($FlowMeter->payment == 'Paid') : ?>
                                            <span class="badge badge-primary">Paid</span>
                                            <?php else : ?>
                                            <span class="badge badge-danger">Pending</span>
                                            <?php endif; ?>
                                        </td>

                                        <?php if ($role == 1 || $role == 2) : ?>


                                        <td>
                                            <a href="<?= base_url() ?>/editFlowMeter/<?= $FlowMeter->id ?>"
                                                class="btn btn-primary btn-sm"><i class="far fa-edit"></i></a>
                                            <a href="<?= base_url() ?>/deleteFlowMeter/<?= $FlowMeter->id ?>"
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
                                        <th>Date</th>
                                        <th>Oil Company</th>
                                        <th>Flow Meter Type</th>
                                        <th>Model Number</th>
                                        <th>Serial Number</th>
                                        <th>Flow Rete</th>
                                        <th>Product</th>
                                        <th>Standard Capacity</th>
                                        <th>Status</th>
                                        <th>Sticker Number</th>
                                        <th>Control Number</th>
                                        <th id="amount">Amount</th>
                                        <th>Payment</th>
                                        <?php if ($role == 1 || $role == 2) : ?>

                                        <th>Action</th>
                                        <?php endif; ?>

                                    </tr>


                                </tfoot>
                            </table> <?php else : ?> <h3>There Are No Records Currently Available</h3>
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