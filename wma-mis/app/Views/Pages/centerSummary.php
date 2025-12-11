<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= $page['heading'] ?></h1>
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
            <div class="card-header">COLLECTION BY COLLECTION CENTER</div>
            <div class="card-body">
                <table class="table table-sm table-hover ">

                    <thead class="thead-dark">
                        <tr>
                            <th>Center Number</th>
                            <th>Center Name</th>
                            <th>Paid</th>
                            <th>Pending</th>
                            <th>Partial</th>
                            <th>Total</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($collectionData as $data) : ?>

                            <tr onclick="window.location='<?= base_url('activitiesSummary/' . $data->center . '/view') ?>';" style="cursor: pointer;">
                                <td><?= $data->center ?></td>
                                <td><?= $data->centerName ?></td>
                                <td><?= number_format($data->paid) ?></td>
                                <td><?= number_format($data->pending) ?></td>
                                <td><?= number_format($data->partial) ?></td>
                                <td><?= number_format($data->total) ?></td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Tsh <?= number_format($paidSum) ?></th>
                            <th>Tsh <?= number_format($pendingSum) ?></th>
                            <th>Tsh <?= number_format($partialSum) ?></th>
                            <th>Tsh <?= number_format($total) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <a href="<?=base_url('downloadCentersSummary')?>" target="_blank" type="button" class="btn btn-sm btn-primary"><i class="far fa-download" aria-hidden="true"></i> Download</a>
            </div>
        </div>
        <!-- 
    <pre>
        <?php print_r($collectionData) ?>

      
    </pre> -->

    </div><!-- /.container-fluid -->

</section>
<?= $this->endSection(); ?>