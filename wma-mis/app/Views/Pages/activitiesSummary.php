<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class=""><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Collection Centers</a></li>
                    <li class="breadcrumb-item active"><?= ucfirst($centerName) ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">
   

        <div class="card">
            <div class="card-header row" >
                <a href="<?=base_url('collectionSummary')?>" type="button" class="btn btn-primary btn-sm col-1" ><i class="far fa-arrow-left"></i> Back</a> 
                <h6 class="text-center col-11 mt-1"><?=strtoupper($centerName) ?></h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover">

                    <thead class="thead-dark">
                        <tr class="">
                            <th>Activity Name</th>
                            <th>Paid</th>
                            <th>Pending</th>
                            <th>Partial</th>
                            <th>Total</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($collectionData as $data) : ?>
                            <tr onclick="window.location='<?= base_url($data->url) ?>';" style="cursor: pointer;">
                                <td><?= $data->activity ?></td>
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

                            <th>Tsh <?= number_format($paidSum) ?></th>
                            <th>Tsh <?= number_format($pendingSum) ?></th>
                            <th>Tsh <?= number_format($partialSum) ?></th>
                            <th>Tsh <?= number_format($total) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <a href="<?=base_url("activitiesSummary/$centerCode/pdf")?>" target="_blank" type="button" class="btn btn-sm btn-primary"><i class="far fa-download" aria-hidden="true"></i> Download</a>
            </div>
        </div>

        <!-- <pre>
        <?php print_r($collectionData) ?>

      
    </pre> -->

    </div><!-- /.container-fluid -->

</section>
<?= $this->endSection(); ?>