<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php

use App\Models\VtcModel;

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
                    <?= form_open() ?>
                    <div class="d-flex justify-content-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <select id="year" class="form-control select2bs4" name="years" required>
                                    <option disabled selected value="">Financial Year</option>
                                    <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                        <option <?= isset($selectedYear) && $selectedYear == ($i . '_' . $i + 1) ? 'selected' : '' ?> value="<?= $i . '_' . $i + 1 ?>"><?= $i . '/' . $i + 1 ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="far fa-search    "></i> Search</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    </form>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title"><?= " Financial Year ($year) : " . $page['heading']  ?><b>(<?= count($vtvResults) ?>)</b> &nbsp; &nbsp; &nbsp; Total Amount: <b class="">Tsh <?= totalAmount($vtvResults) ?></b></h5>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">


                            <!-- <pre>
                            <?php print_r([]); ?>
                            </pre> -->



                            <!-- <h5>Total Amount: <span class="">Tsh <?= totalAmount($vtvResults) ?></span></h5> -->

                            <?php if ($vtvResults) : ?>
                                <table id="example1" class="table table-sm table-bordered ">
                                    <thead>
                                        <tr>
                                            <th class="head">Owner</th>

                                            <th class="head">Region</th>
                                            <th class="head">Ward</th>
                                            <th class="head">Postal Address</th>
                                            <th class="head">Owners Contact</th>
                                            <th class="head">Activity</th>
                                            <!-- <th class="head">Verified By</th> -->
                                            <!-- <th class="head">Supervisors Contact</th> -->
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
                                            <th class="head">Chart</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach ($vtvResults as $vtv) : ?>
                                            <?php
                                            $id = vehicleId($vtv->vehicleId);
                                            $hasChart = (new VtcModel())->getChartIfo($id);
                                            if ($hasChart) {
                                                $href = base_url('downloadCalibrationChart/' . $id);
                                                $chartLink = <<<HTML
                                                     <a href="$href" class="btn btn-primary btn-sm" target="_blank"> <i class="far fa-download"></i> Download Chart</a>        
                                                HTML;
                                            } else {
                                                $chartLink = <<<HTML
                                                <a href="#" class="btn btn-default btn-sm" > <i class="far fa-download"></i> Download Chart</a>        
                                           HTML;
                                            }

                                            ?>
                                            <tr>
                                                <td><?= $vtv->PyrName ?></td>

                                                <td><?= $vtv->region ?></td>
                                                <td><?= $vtv->ward ?></td>
                                                <td><?= $vtv->postal_address ?></td>
                                                <td><?= $vtv->PyrCellNum ?></td>
                                                <td><?= $vtv->Task ?></td>
                                                <!-- <td><?= $vtv->officer ?></td> -->
                                                <td><?= $vtv->tin_number ?></td>
                                                <td><?= $vtv->driver_name ?></td>
                                                <td><?= $vtv->driver_license ?></td>
                                                <td><?= $vtv->vehicle_brand ?></td>
                                                <td><?= $vtv->trailer_plate_number ?></td>
                                                <td><?= $vtv->capacity ?>Liters</td>
                                                <td><?= dateFormatter($vtv->registration_date) ?></td>
                                                <td><?= dateFormatter($vtv->next_calibration) ?></td>


                                                <td>
                                                    <?php if ($vtv->calibrationStatus == 'Valid') : ?>
                                                        <span class="badge-pill badge-primary">Valid</span>
                                                    <?php else : ?>
                                                        <span class="badge-pill badge-danger">Not Valid</span>


                                                    <?php endif; ?>

                                                </td>
                                                <td><?= $vtv->sticker_number ?></td>
                                                <td><?= $vtv->PayCntrNum ?></td>
                                                <td><?= $vtv->amount ?></td>
                                                <td>
                                                    <?php if ($vtv->PaymentStatus == 'Paid') : ?>
                                                        <span class="badge-pill badge-success">Paid</span>
                                                    <?php else : ?>
                                                        <span class="badge-pill badge-warning">Pending</span>
                                                    <?php endif; ?>

                                                </td>

                                                <td>
                                                    <?= $chartLink ?>
                                                </td>









                                            </tr>
                                        <?php endforeach; ?>


                                    </tbody>
                                    <tfoot>
                                        <!-- <tr>
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



                                        </tr> -->
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