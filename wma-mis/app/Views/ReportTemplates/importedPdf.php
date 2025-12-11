<?php


?>
<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<div class="report">
    <div class="card-header">
        <h5 class="text-center"> <?= $title ?></h5>
    </div>
    <div class="card-body">

        <?php if ($imported) : ?>

            <table id="imported" class="table-bordered table-sm" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Region</th>
                        <th>Importer</th>
                        <th>Product</th>
                        <th>Transad Number</th>
                        <th>F.O.B</th>
                        <th>Fees</th>
                        <th>Control Number</th>
                        <th>Payment Status</th>
                        <th>Phone Number</th>
                        <th>Decision</th>
                    </tr>
                </thead>
                <tbody id="imported-table">
                    <?php foreach ($imported as $item) : ?>
                        <tr>
                            <td><?= dateFormatter($item->createdAt) ?></td>
                            <td><?= str_replace('Wakala Wa Vipimo', '', wmaCenter($item->center)->centerName) ?></td>
                            <td><?= $item->customer ?></td>
                            <td><?= $item->product ?></td>
                            <td><?= $item->tansardNumber ?></td>
                            <td><?= $item->fob ?></td>
                            <td><?= number_format($item->amount) ?></td>
                            <td><?= $item->controlNumber ?></td>
                            <td><?= $item->PaymentStatus ?></td>
                            <td><?= $item->phoneNumber ?></td>
                            <td><?= $item->Status ?></td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>



        <?php else : ?>
            <h5>There Are No Records Currently Available</h5>
        <?php endif; ?>
    </div>
</div>


<?= $this->endSection(); ?>