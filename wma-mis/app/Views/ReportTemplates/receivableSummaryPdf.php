<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<div class="card-body">

<h5 class="text-center">DEBTORS ANALYSIS REPORT (<?= str_replace('_','/',$financialYear) ?>)</h5>
<h6 class="text-center">Aged Analysis Of Debtors As at <?= dateFormatter(date('Y-m-d')) ?></h6>
<table class="table table-sm table-bordered" style="width: 100%;">
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

            <tr>
                <td><?= $n++ ?></td>
                <td><?= $collection->centerName ?></td>
                <td><?= number_format($collection->total) ?></td>
                <td><?= number_format($collection->_1_30) ?></td>
                <td><?= number_format($collection->_31_60) ?></td>
                <td><?= number_format($collection->_61_90) ?></td>
                <td><?= number_format($collection->_91_120) ?></td>
                <td><?= number_format($collection->_121_365) ?></td>
                <td><?= number_format($collection->_above365) ?></td>
            </tr>


        <?php endforeach; ?>

    </tbody>
</table>

<br>

<div style="width:50%;float:right">
    <table class="table table-sm table-bordered" >
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
<?= $this->endSection(); ?>