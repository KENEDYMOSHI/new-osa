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

                            <h4>Total Amount: <span class="total"></span></h4>

                            <?php if ($vtcResults) : ?>
                            <table id="example1" class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th class="head">Owner</th>
                                        <th class="head">Gender</th>
                                        <th class="head">Region</th>
                                        <th class="head">Ward</th>
                                        <th class="head">Postal Address</th>
                                        <th class="head">Owners Contact</th>
                                        <th class="head">Activity</th>
                                        <th class="head">Supervisors Name</th>
                                        <th class="head">Supervisors Contact</th>
                                        <th class="head">Tin Number</th>
                                        <th class="head">Driver's Name</th>
                                        <th class="head">Driver's License</th>
                                        <th class="head">Vehicle Brand</th>
                                        <th class="head">Plate Number</th>
                                        <th class="head">Capacity</th>
                                        <th class="head">Calibrated On</th>
                                        <th class="head">Next Calibration</th>


                                        <th class="head">Status</th>
                                        <th class="head">Sticker Number</th>
                                        <th class="head">Control Number</th>
                                        <th class="head" id="amount">Amount</th>
                                        <th class="head">Payment</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($vtcResults as $vtc) : ?>
                                    <tr>
                                        <td><?= $vtc->first_name . '  ' . $vtc->last_name ?></td>
                                        <td><?= $vtc->gender ?></td>
                                        <td><?= $vtc->region ?></td>
                                        <td><?= $vtc->ward ?></td>
                                        <td><?= $vtc->postal_address ?></td>
                                        <td><?= $vtc->phone_number ?></td>
                                        <td><?= $vtc->activity ?></td>
                                        <td><?= $vtc->supervisor ?></td>
                                        <td><?= $vtc->supervisor_phone ?></td>
                                        <td><?= $vtc->tin_number ?></td>
                                        <td><?= $vtc->driver_name ?></td>
                                        <td><?= $vtc->driver_license ?></td>
                                        <td><?= $vtc->vehicle_brand ?></td>
                                        <td><?= $vtc->plate_number ?></td>
                                        <td><?= $vtc->capacity ?>Liters</td>
                                        <td><?= dateFormatter($vtc->registration_date) ?></td>
                                        <td><?= $vtc->next_calibration ?></td>


                                        <td>
                                            <?php if ($vtc->status == 'Valid') : ?>
                                            <span class="badge-pill badge-primary">Valid</span>
                                            <?php else : ?>
                                            <span class="badge-pill badge-danger">Not Valid</span>


                                            <?php endif; ?>

                                        </td>
                                        <td><?= $vtc->sticker_number ?></td>
                                        <td><?= $vtc->control_number ?></td>
                                        <td><?= $vtc->amount ?></td>
                                        <td>
                                            <?php if ($vtc->payment == 'Paid') : ?>
                                            <span class="badge-pill badge-success">Paid</span>
                                            <?php else : ?>
                                            <span class="badge-pill badge-warning">Pending</span>
                                            <?php endif; ?>

                                        </td>









                                    </tr>
                                    <?php endforeach; ?>


                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="head">Owner</th>
                                        <th>Gender</th>
                                        <th>Region</th>
                                        <th>Ward</th>
                                        <th>Postal Address</th>
                                        <th>Owners Contact</th>
                                        <th>Activity</th>
                                        <th>Supervisors Name</th>
                                        <th>Supervisors Contact</th>
                                        <th>Tin Number</th>
                                        <th>Driver's Name</th>
                                        <th>Driver's License</th>
                                        <th>Vehicle Brand</th>
                                        <th>Plate Number</th>
                                        <th>Capacity</th>
                                        <th>Calibrated On</th>
                                        <th>Next Calibration</th>


                                        <th>Status</th>
                                        <th>Sticker Number</th>
                                        <th>Control Number</th>
                                        <th>Payment</th>
                                        <th id="amount">Amount</th>



                                    </tr>
                                </tfoot>
                            </table>
                            <?php else : ?>
                            <h2>There Are No Records Currently Available</h2>
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