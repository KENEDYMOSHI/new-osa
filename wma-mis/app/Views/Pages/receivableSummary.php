<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php $year = str_replace('/', '_', $financialYear) ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1><?= $page['heading'] ?></h1> -->
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <form action="<?= base_url('receivableSummary') ?>" method="POST">
                    <div class="d-flex justify-content-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <select id="year" class="form-control select2bs4" name="year" required>
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
            </div>
            <div class="card-body">

                <h5 class="text-center">DEBTORS ANALYSIS REPORT (<?= $financialYear ?>)</h5>
                <h6 class="text-center">Aged Analysis Of Debtors As at <?= dateFormatter(date('Y-m-d')) ?></h6>
                <table class="table table-sm table-hover" id="receivables">
                    <thead class="thead-dark">
                        <tr>
                            <th>S/N</th>
                            <th>Collection Center</th>
                            <th>Total Amount</th>
                            <th>1-30 Days</th>
                            <th>31-61 Days</th>
                            <th>61-90 Days</th>
                            <th>91-120 Days</th>
                            <th>121-365 Days</th>
                            <th>Above 365 Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n = 1; ?>
                        <?php foreach ($collectionData as $collection) : ?>
                            <?php $link = $collection->total != 0 ? base_url("regionDebt/$collection->center/$year") : '#' ?>
                            <tr>
                                <td><?= $n++ ?></td>
                                <td><a href="<?= $link ?>"><?= $collection->centerName ?></a></td>
                                <td><a href="<?= $link ?>"><?= number_format($collection->total) ?></a></td>
                                <td><a href="<?= $link ?>"><?= number_format($collection->_1_30) ?></a></td>
                                <td><a href="<?= $link ?>"><?= number_format($collection->_31_60) ?></a></td>
                                <td><a href="<?= $link ?>"><?= number_format($collection->_61_90) ?></a></td>
                                <td><a href="<?= $link ?>"><?= number_format($collection->_91_120) ?></a></td>
                                <td><a href="<?= $link ?>"><?= number_format($collection->_121_365) ?></a></td>
                                <td><a href="<?= $link ?>"><?= number_format($collection->_above365) ?></a></td>
                            </tr>


                        <?php endforeach; ?>

                    </tbody>
                </table>

                <br>
                <br>
                <div class="col-4">
                    <table class="table table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Days</th>
                                <th>TZS</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> 1-30 Days</td>
                                <td><?= number_format($total_1_30) ?></td>
                            </tr>
                            <tr>
                                <td> 31-60 Days</td>
                                <td><?= number_format($total_31_60) ?></td>
                            </tr>
                            <tr>
                                <td> 61-90 Days</td>
                                <td><?= number_format($total_61_90) ?></td>
                            </tr>
                            <tr>
                                <td> 91-120 Days</td>
                                <td><?= number_format($total_91_120) ?></td>
                            </tr>
                            <tr>
                                <td> 121-365 Days</td>
                                <td><?= number_format($total_121_365) ?></td>
                            </tr>
                            <tr>
                                <td> Above Days</td>
                                <td><?= number_format($total_above365) ?></td>
                            </tr>
                            <tr>
                                <td><b>TOTAL</b></td>
                                <td><b><?= number_format($total_1_30 + $total_31_60 + $total_61_90 + $total_above365 + $total_121_365 + $total_91_120) ?></b></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
              
                <a href="<?= base_url("downloadReceivableSummary/$year") ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fal fa-download"></i> Download</a>
            </div>
        </div>



    </div><!-- /.container-fluid -->
    <script>
        $(document).ready(function() {
            $('#receivables').DataTable({
                dom: '<"top"lBfrtip>',
                buttons: [
                    'excel',
                ],
                lengthMenu: [33],
                "responsive": true,
                "autoWidth": false,
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,

            });
        });
    </script>

</section>
<?= $this->endSection(); ?>-