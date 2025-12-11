<?php

use App\Libraries\ArrayLibrary;
?>
<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<div class="report">
    <div class="card-header">
        <h5 class="text-center"> <?= $title ?></h5>
    </div>
    <div class="card-body">
        <table border="1" style="font-size: 10px;">
                <thead class="thead-dark">
                    <tr>
                        <th>Region</th>
                        <th>Collection For <?= $currentDate ?></th>
                        <th>Consolidation Fund Contribution 15% For TR</th>
                        <th>Net Collection For WMA</th>
                        <th>Accumulative Collection 100% </th>



                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($collections as $collection) : ?>
                        <?php

                        $today = number_format($collection->today ?? 0);
                        $trContribution = number_format($collection->trContribution ?? 0);
                        $wmaNet = number_format($collection->wmaNet ?? 0);
                        $accumulative = number_format($collection->accumulative ?? 0);


                        ?>
                        <tr>
                            <td><?= $collection->region ?></td>
                            <td><?= $today ?></td>
                            <td><?= $trContribution ?></td>
                            <td><?= $wmaNet ?></td>
                            <td><?= $accumulative ?></td>


                        </tr>


                    <?php endforeach; ?>
                    <?php
                    $totalToday = (new ArrayLibrary($collections))->reduce(fn($x, $y) => $x + $y->today)->get();
                    $totalTrContribution = (new ArrayLibrary($collections))->reduce(fn($x, $y) => $x + $y->trContribution)->get();
                    $totalWmaNet = (new ArrayLibrary($collections))->reduce(fn($x, $y) => $x + $y->wmaNet)->get();
                    $totalAccumulative = (new ArrayLibrary($collections))->reduce(fn($x, $y) => $x + $y->accumulative)->get();

                    ?>
                    <tr>
                        <th><b>TOTAL</b></th>
                        <th><?= number_format($totalToday) ?></th>
                        <th><?= number_format($totalTrContribution) ?></th>
                        <th><?= number_format($totalWmaNet) ?></th>
                        <th><?= number_format($totalAccumulative) ?></th>

                    </tr>

                </tbody>

            </table>
    </div>
</div>


<?= $this->endSection(); ?>