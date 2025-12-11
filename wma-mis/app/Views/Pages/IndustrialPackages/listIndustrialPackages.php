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
                            <!-- <h4>Total Amount: <span class="total"></span></h4> -->

                            <?php if ($prePackageData) : ?>
                                <table id="example1" class=" mainTable table-bordered table-sm" style="width: 100%;">
                                    <!-- <table class="table1  table-bordered1" > -->
                                    <thead class="dark">
                                        <tr>
                                            <th>Date</th>
                                            <th>Name Of Client</th>
                                            <th>Region</th>
                                            <th>Location</th>
                                            <th>Product</th>
                                            <th>Batch Number</th>
                                            <th>Fees</th>
                                            <th>Results</th>
                                            <th>Control Number</th>
                                            <th>Payment</th>
                                            <?php if ($role == 2 || $role == 3 || $role == 7) : ?>
                                                <th>Officer</th>
                                            <?php endif; ?>
                                            <!-- <th>Measures Taken</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prePackageData as $data) : ?>
                                            <!-- 
                        <tr>

                            <td style="margin:0;padding:0;">${data.date}</td>
                            <td style="margin:0;padding:0;">${data.customer}</td>
                            <td style="margin:0;padding:0;">${data.region}</td>
                            <td style="margin:0;padding:0;">${data.location}</td>
                            <td style="margin:0;padding:0;">

                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderProducts(data.productData)}
                                </table>

                            </td>
                            <td style="margin:0;padding:0;">

                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderProductStatus(data.productData)}

                                </table>

                            </td>
                            <td style="margin:0;padding:0;">

                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderProductFee(data.productData)}
                                </table>

                            </td>

                            <td style="margin:0;padding:0;">
                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderControlNumber(data.productData)}
                                </table>
                            </td>
                            <td style="margin:0;padding:0;">
                                <table cellspacing="0" border="0" style="width: 100%;">
                                    ${renderPaymentStatus(data.productData)}
                                </table>
                            </td>
                            <td style="margin:0;padding:0;"> - </td>
                        </tr> -->


                                            <tr>

                                                <td style="margin:0;padding:0;"><?= $data['date'] ?></td>
                                                <td style="margin:0;padding:0;"><?= $data['customer'] ?></td>
                                                <td style="margin:0;padding:0;"><?= $data['region'] ?></td>
                                                <td style="margin:0;padding:0;"><?= $data['location'] ?></td>
                                                <td style="margin:0;padding:0;">

                                                    <table cellspacing="0" style="width: 100%;">
                                                        <?php foreach ($data['productData'] as $product) : ?>
                                                            <tr>
                                                                <td style="margin:0;padding:0;"><?= $product['commodity'] ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>

                                                    </table>

                                                </td>
                                                <td style="margin:0;padding:0;">

                                                    <table cellspacing="0" style="width: 100%;">
                                                        <?php foreach ($data['productData'] as $product) : ?>
                                                            <tr>
                                                                <td style="margin:0;padding:0;"><?= $product['batchNumber'] ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>

                                                    </table>

                                                </td>
                                                <td style="margin:0;padding:0;">

                                                    <table cellspacing="0" style="width: 100%;">
                                                        <?php foreach ($data['productData'] as $product) : ?>
                                                            <tr>
                                                                <td style="margin:0;padding:0; ">Tsh<?= number_format($product['amount']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <!-- status goes here -->
                                                    </table>

                                                </td>
                                                <td style="margin:0;padding:0;">

                                                    <table cellspacing="0" style="width: 100%;">
                                                        <?php foreach ($data['productData'] as $product) : ?>
                                                            <tr>
                                                                <td style="margin:0;padding:0;"><?= $product['status'] ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <!-- fee goes here -->
                                                    </table>

                                                </td>
                                                <td style="margin:0;padding:0;">

                                                    <table cellspacing="0" style="width: 100%;">
                                                        <?php foreach ($data['productData'] as $product) : ?>
                                                            <tr>
                                                                <td style="margin:0;padding:0;"><?= $product['controlNumber'] ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <!-- fee goes here -->
                                                    </table>

                                                </td>
                                                <td style="margin:0;padding:0;">

                                                    <table cellspacing="0" style="width: 100%;">
                                                        <?php foreach ($data['productData'] as $product) : ?>
                                                            <tr>
                                                                <td style="margin:0;padding:0;"><?= $product['payment'] ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <!-- fee goes here -->
                                                    </table>

                                                </td>
                                                <?php if ($role == 2 || $role == 3 || $role == 7) : ?>
                                                    <td style="margin:0;padding:0;">

                                                        <table cellspacing="0" style="width: 100%;">
                                                            <?php foreach ($data['productData'] as $officer) : ?>
                                                                <tr>
                                                                    <td style="margin:0;padding:0px;"><?= $officer['officer'] ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            <!-- fee goes here -->
                                                        </table>

                                                    </td>
                                                <?php endif; ?>
                                                <!-- <td style="margin:0;padding:0;"><?= $data['controlNumber'] ?></td> -->
                                                <!-- <td style="margin:0;padding:0;"> - </td> -->
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else : ?>
                                <h3>There Are No Records Currently Available</h3>
                            <?php endif; ?>
                            <!-- <table id="example1" class="my-table " > -->

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