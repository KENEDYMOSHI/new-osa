<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php

use App\Models\LorriesModel;

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
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>

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
                    <?php
                    //print_r($lorryResults);
                    ?>
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
                            <!-- <h3 class="card-title"><?= $page['heading'] ?></h3> -->
                            <h5 class="card-title"><?= "Financial Year ($year) : " . $page['heading']  ?><b>(<?= count($lorryResults) ?>)</b> &nbsp; &nbsp; &nbsp; Total Amount: <b class="">Tsh <?= totalAmount($lorryResults) ?></b></h5>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- <h4>Total Amount: <span class="total"></span></h4> -->


                            <?php if ($lorryResults) : ?>
                                <table id="example1" class="table table-sm table-bordered ">
                                    <thead>
                                        <tr>
                                            <th class="head">Owner's Name</th>

                                            <th class="head">City</th>
                                            <th class="head">Ward</th>
                                            <th class="head">Postal Address</th>
                                            <th class="head">Phone Number</th>
                                            <th class="head">Task</th>
                                            <th class="head">Verified By</th>
                                            <th class="head">Tin Number</th>
                                            <th class="head">Driver's Name</th>
                                            <th class="head">Driver's License</th>
                                            <th class="head">Lorry Brand</th>
                                            <th class="head">Registration Plate</th>
                                            <th class="head">Lorry Capacity</th>
                                            <th class="head">Calibrated On</th>
                                            <th class="head"> Next Calibration</th>
                                            <th class="head">Status</th>
                                            <th class="head">Trailers</th>
                                            <th class="head">Sticker Number</th>
                                            <th class="head">Control Number</th>
                                            <th class="head" id="amount">Amount</th>
                                            <th class="head">Payment</th>
                                            <th class="head">Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach ($lorryResults as $lorry) : ?>
                                            <tr>
                                                <td><?= $lorry->PyrName ?></td>

                                                <td><?= $lorry->region ?></td>
                                                <td><?= $lorry->ward ?></td>
                                                <td><?= $lorry->postal_address ?></td>
                                                <td><?= $lorry->PyrCellNum ?></td>
                                                <td><?= $lorry->Task ?></td>
                                                <td><?= $lorry->officer ?></td>
                                                <td><?= $lorry->tin_number ?></td>
                                                <td><?= $lorry->driver_name ?></td>
                                                <td><?= $lorry->driver_license ?></td>
                                                <td><?= $lorry->vehicle_brand ?></td>
                                                <td><?= $lorry->plate_number ?></td>
                                                <td><?= $lorry->capacity ?>m<sup>3</sup></td>
                                                <td><?= dateFormatter($lorry->registration_date) ?></td>
                                                <td><?= $lorry->next_calibration ?></td>
                                                <td>


                                                    <?php if ($lorry->calibrationStatus == 'Valid') : ?>
                                                        <span class="badge-pill badge-primary">Valid</span>
                                                    <?php elseif ($lorry->calibrationStatus == 'Not Valid') : ?>
                                                        <span class="badge-pill badge-danger">Not Valid</span>
                                                    <?php elseif ($lorry->calibrationStatus == 'Condemned') : ?>
                                                        <span class="badge-pill badge-danger">Condemned</span>
                                                    <?php endif; ?>

                                                </td>
                                                <td>
                                                    <?php $trailers = (new LorriesModel())->getTrailers($lorry->id) ?>
                                                    <?php if (!empty($trailers)) : ?>
                                                        <ul>
                                                            <?php foreach ($trailers as $trailer) : ?>
                                                                <li> <b>Plate Number: </b><?= $trailer->trailerPlate ?> &nbsp; &nbsp; <b>Capacity:</b> <?= $trailer->trailerCapacity ?>m<sup>3</sup> &nbsp; &nbsp; <b><br></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else : ?>
                                                        No Extra Trailer
                                                    <?php endif; ?>

                                                </td>

                                                <td><?= $lorry->stickerNumber ?></td>
                                                <td><?= $lorry->PayCntrNum ?></td>
                                                <td><?= $lorry->amount ?></td>
                                                <td>
                                                    <?php if ($lorry->PaymentStatus == 'Paid') : ?>
                                                        <span class="badge-pill badge-success">Paid</span>
                                                    <?php elseif ($lorry->PaymentStatus == 'Pending') : ?>
                                                        <span class="badge-pill badge-warning">Pending</span>

                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <a href="<?= base_url("downloadLorryChart/$lorry->id") ?>" target="_blank" class="btn btn-primary btn-sm"><i class="far fa-file-download"></i> Chart</a>
                                                </td>


                                            </tr>
                                        <?php endforeach; ?>


                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="head">Owner's Name </th>

                                            <th>City</th>
                                            <th>Ward</th>
                                            <th>Postal Address</th>
                                            <th>Phone Number</th>
                                            <th>Supervisor's Name</th>
                                            <th>Supervisor's Contact</th>
                                            <th>Tin Number</th>
                                            <th>Driver's Name</th>
                                            <th>Driver's License</th>
                                            <th>Calibrated On</th>
                                            <th>Next Calibration</th>
                                            <th>Lorry Brand</th>
                                            <th>Registration Plate</th>
                                            <th>Lorry Capacity</th>
                                            <th>Status</th>
                                            <th>Trailers</th>
                                            <th>Sticker number</th>
                                            <th>Control Number</th>
                                            <th id="amount">Amount</th>
                                            <th>Payment</th>
                                            <th>Action</th>

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

    <?= $this->endSection(); ?>