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
                    <th>Accumulated Actual Collection - A </th>
                    <th>Estimates <?= $monthAndYear ?> - B</th>
                    <th>Variance (TZS)</th>
                    <th>Variance (%) (A-B)/B</th>


                </tr>
            </thead>
            <tbody>
                <?php foreach ($collections as $collection) : ?>
                    <?php
                    $today = number_format($collection->today ?? 0);
                    $accumulated = number_format($collection->accumulated ?? 0);
                    $estimate = number_format($collection->estimate ?? 0);
                    $variance = number_format($collection->variance ?? 0);
                    $variancePercentage = number_format($collection->variancePercentage ?? 0);

                    ?>
                    <tr>
                        <td><?= $collection->region ?></td>
                        <td><?= $today ?></td>
                        <td><?= $accumulated ?></td>
                        <td><?= $estimate ?></td>
                        <td><?= $variance ?></td>
                        <td><?= $variancePercentage ?> %</td>

                    </tr>

                <?php endforeach; ?>

            </tbody>
            <tfoot>
                <?php
                $totalToday = (new ArrayLibrary($collections))->reduce(fn ($x, $y) => $x + $y->today)->get();
                $totalAccumulated = (new ArrayLibrary($collections))->reduce(fn ($x, $y) => $x + $y->accumulated)->get();
                $totalEstimate = (new ArrayLibrary($collections))->reduce(fn ($x, $y) => $x + $y->estimate)->get();
                $totalVariance = (new ArrayLibrary($collections))->reduce(fn ($x, $y) => $x + $y->variance)->get();
                $totalVariancePercentage =   $totalVariancePercentage = ($totalVariance / $totalEstimate) * 100;
                ?>
                <tr>
                    <th><b>TOTAL</b></th>
                    <th><?= number_format($totalToday) ?></th>
                    <th><?= number_format($totalAccumulated) ?></th>
                    <th><?= number_format($totalEstimate) ?></th>
                    <th><?= number_format($totalVariance) ?></th>
                    <th> <?= number_format($totalVariancePercentage) ?>%</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


<?= $this->endSection(); ?>