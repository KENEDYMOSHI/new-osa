<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<div class="card-body">

    <h6 class="text-center">WEIGHTS AND MEASURES AGENCY</h6>
    <h6 class="text-center"><?= strtoupper(str_replace('Wakala Wa Vipimo', ' Regional', $centerName . ' Office')) ?></h6>
    <h6 class="text-center">DEBTORS ANALYSIS REPORT (<?= str_replace('_','/',$financialYear) ?>)</h6>
    <h6 class="text-center">Aged Analysis Of Debtors As at <?= dateFormatter(date('Y-m-d')) ?></h6>
    <table class="table table-sm table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>S/N</th>
                <th>Debtor Name</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Control Number</th>
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
                    <td><?= $collection->customer ?></td>
                    <td><?= dateFormatter($collection->CreatedAt) ?></td>
                    <td><?= number_format($collection->amount) ?></td>
                    <td><?= $collection->controlNumber ?></td>
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