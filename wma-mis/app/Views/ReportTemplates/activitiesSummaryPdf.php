<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<h5 class="text-center mt-2"><?=strtoupper($centerName) ?></h5>
<table class="table table-sm table-bordered">

    <thead class="thead-dark">
        <tr>
            <th>Activity Name</th>
            <th>Paid</th>
            <th>Pending</th>
            <th>Partial</th>
            <th>Total</th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($collectionData as $data) : ?>
            <tr>
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
<?= $this->endSection(); ?>