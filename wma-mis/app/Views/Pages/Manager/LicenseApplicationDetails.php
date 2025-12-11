<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>

<style>
    .disable {
        pointer-events: none;
        cursor: default;
        background: gray;
        border: 1px solid gray;

    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
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
        <?= Success() ?>

    </div>

    <div class="card">
        <div class="card-header"><b>LICENSE APPLICATION DETAILS</b></div>


        <div class="card-body">
            <table class="table display product-overview mb-30" id="support_table5">
                <!-- <thead>
                            <tr>
                                <th>Name</th>
                                <th>Details</th>

                            </tr>
                        </thead> -->
                <tbody>
                    <tr class="shade">

                        <td colspan="2">PERSONAL PARTICULARS</td>
                    </tr>
                    <tr>
                        <td>Applicant Name</td>
                        <td><?= $particulars->applicant_name ?></td>
                    </tr>
                    <tr>
                        <td>Nationality</td>
                        <td><?= $particulars->nationality ?></td>
                    </tr>
                    <?php if ($particulars->nationality == 'Tanzanian') : ?>
                        <tr>
                            <td>NIDA Number</td>
                            <td><?= $particulars->nida_number ?></td>
                        </tr>
                    <?php else : ?>
                        <tr>
                            <td>Passport Number</td>
                            <td><?= $particulars->passport ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td>Mobile Number</td>
                        <td><?= $particulars->mobile_number ?></td>
                    </tr>
                    <tr>
                        <td>Email Address</td>
                        <td><?= $particulars->email ?></td>
                    </tr>
                    <tr>
                        <td>Region</td>
                        <td><?= $particulars->region ?></td>
                    </tr>
                    <tr>
                        <td>District</td>
                        <td><?= $particulars->district ?></td>
                    </tr>
                    <tr>
                        <td>Postal Address</td>
                        <td><?= $particulars->postal_address ?></td>
                    </tr>
                    <tr>
                        <td>Ward</td>
                        <td><?= $particulars->ward ?></td>
                    </tr>
                    <tr>
                        <td>Postal Code</td>
                        <td><?= $particulars->postal_code ?></td>
                    </tr>
                    <tr>
                        <td>Physical Address</td>
                        <td><?= $particulars->physical_address ?></td>
                    </tr>
                    <?php if ($particulars->company_registration_number != '') : ?>
                        <tr>
                            <td>Company Registration Number</td>
                            <td><?= $particulars->company_registration_number ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr class="shade">

                        <td colspan="2">APPLICANT QUALIFICATIONS</td>
                    </tr>
                    <?php foreach ($qualifications as $qualification) : ?>
                        <tr>
                            <td><?= $qualification->qualification ?></td>
                            <td>Duration: <?= $qualification->duration ?> Years</td>

                        </tr>
                    <?php endforeach; ?>
                    <!-- *********************************************** -->
                    <tr class="shade">

                        <td colspan="2">LICENSE TYPE</td>
                    </tr>
                    <?php foreach ($licenseTypes as $license) : ?>
                        <tr>
                            <td><?= $license->type ?></td>
                            <td></td>

                        </tr>
                    <?php endforeach; ?>
                    <!-- *************************************************** -->
                    <tr class="shade">

                        <td colspan="2">TOOLS/EQUIPMENTS OR FACILITY</td>
                    </tr>
                    <?php foreach ($tools as $tool) : ?>
                        <tr>
                            <td><?= $tool->tool ?></td>
                            <td></td>

                        </tr>
                    <?php endforeach; ?>
                    <!-- *************************************************** -->
                    <tr class="shade">

                        <td colspan="2">ATTACHMENTS</td>
                    </tr>
                    <?php foreach ($attachments as $attachment) : ?>
                        <tr>
                            <td><?= $attachment->document ?></td>
                            <td>

                                <a href="<?= $attachment->path ?>" class="btn btn-primary btn-xs" download="<?= $attachment->path ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>



                        </tr>
                    <?php endforeach; ?>








                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="<?= base_url('download-application/' . $applicationId) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fal fa-download" aria-hidden="true"></i> Download</a>
        </div>

    </div>

    </div>
    </div>
</section>

<?= $this->endSection(); ?>